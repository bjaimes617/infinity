<?php

namespace App\Http\Controllers\cursos;

use App\Http\Controllers\Controller;
use App\Models\cursos\CursosModel;
use App\Models\cursos\ParticipantesModel;
use App\Models\cursos\AsistenciaModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\SedeModel;

use App\Models\User;
use DatePeriod;
use DateTime;
use DateInterval;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Exports\cursos\ReporteExport;
use App\Exports\cursos\IndicadoresExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use App\Http\Controllers\PoliceEstructure;

class ReportesController extends Controller {
    
    public function __construct() {
        $this->middleware(['auth', 'verified', 'check.password']);
        $this->nacademicos      = config('app.especialidad');
        $this->horariolab       = config('app.horariolaboral');
        $this->sedes            = SedeModel::where('active',1)->whereIn('id',[1,2,3,9])->get();
        $this->contractual      = config('app.contractual');
        $this->horariocursos    = config('app.horariocursos');
        $this->convocadocursos  = config('app.convocadocursos');
    }
    
    public function index()
    {
        
        $cursos = CursosModel::Groupby('skill_id')->orderby('skill_id','asc')->get();       
      
        return view('cursos.reportes.index')->with(["cursos"=>$cursos]);
    }
   
    public function create(Request $request) {

        $fecha = explode("-", $request->fecha); //Separo las dos fechas en dos arreglos

        $begin = Carbon::createfromFormat('d/m/Y', trim($fecha[0]))->startofDay();
        $finish = Carbon::createfromFormat('d/m/Y', trim($fecha[1]))->endofDay();
        
        try {
            switch ($request->tipo) {
                case 1://// reporte de participantes
                    if (empty($request->skill)) {
                        $cursos = CursosModel::whereBetween('inicio', array($begin->copy()->format('Y-m-d'), $finish->copy()->format('Y-m-d')))->get();
                    } else {
                        $cursos = CursosModel::whereBetween('inicio', array($begin->copy()->format('Y-m-d'), $finish->copy()->format('Y-m-d')))->where('skill_id', $request->skill)->get();
                    }
                    $results = $this->arrayParticipantes($cursos, $begin, $finish);
                   
                    return Excel::download(new ReporteExport($results), 'Reporte de Participantes de Cursos del '.$begin.' al '.$finish.'.xls');
                    break;
                case 2:/// indicadores de cursos
                    if (empty($request->skill)) {
                        $cursos = CursosModel::whereBetween('inicio', array("$begin", "$finish"))->get();
                    } else {
                        $cursos = CursosModel::whereBetween('inicio', array("$begin", "$finish"))->where('skill_id', $request->skill)->get();
                    }
                    $results = $this->arrayIndicadores($cursos, $begin, $finish);
                    //dd($results);
                    return Excel::download(new IndicadoresExport($results), 'Reporte consolidado de Cursos del '.$begin.' al '.$finish.'.xls');
                    break;
            }
        } catch (\Exception $e) {
            \Session::flash('error', 'Ocurrio un problema ' . $e->getMessage());
            return back();
        }
    }

    public function arrayParticipantes($cursos, $begin, $finish) {

        $row = array();
        $participantes = array();

        $finichs = new DateTime($finish);
        $finichs->modify('+ 1 day');
        $periodo = new DatePeriod(
                new DateTime($begin),
                new DateInterval('P1D'), $finichs);
        
     
        $cursos->load('RelationCartera', 'Relationskill', 'RelationPersonal', 'RelationAsistencias', 'RelationParticipantes');
        // dd($this->horariocursos);
        foreach ($cursos as $curs) {
            foreach ($curs->RelationParticipantes()->get() as $rs) {

                $participantes[] = $rs->id;
                $row[$rs->id]["curso"] = "DGC" . $curs->id;
                $row[$rs->id]["inicio"] = date('d/m/Y', strtotime($curs->inicio));
                $row[$rs->id]["finaliza"] = date('d/m/Y', strtotime($curs->finaliza));
                $row[$rs->id]["horariocurso"] = Arr::get($this->horariocursos, $curs->horario);
                $row[$rs->id]["convocado"] = Arr::get($this->convocadocursos, $curs->convodado);
                $row[$rs->id]["sede"] = SedeModel::where('nombre_sede',$curs->sede)->first()->real_name;
                 if ($curs->RelationEstado) {
                    $row[$rs->id]["region"] = $curs->RelationEstado->RelationRegion->nombre_region;
                    $row[$rs->id]["estado"] = $curs->RelationEstado->nombre_estado;
                    $row[$rs->id]["ciudad"] = $curs->RelationCiudad != null ? $curs->RelationCiudad->ciudad : "N/A";
                } else {
                    $row[$rs->id]["region"] = "N/A";
                    $row[$rs->id]["estado"] = "N/A";
                    $row[$rs->id]["ciudad"] = "N/A";
                }
                $row[$rs->id]["cedula"] = $rs->cedula_empleado;
                $row[$rs->id]["primer_nombre"] = $rs->primer_nombre;
                $row[$rs->id]["segundo_nombre"] = $rs->segundo_nombre;
                $row[$rs->id]["primer_apellido"] = $rs->primer_apellido;
                $row[$rs->id]["segundo_apellido"] = $rs->segundo_apellido;
                $row[$rs->id]["horarioparticipante"] = $rs->horario_operativo;
                $row[$rs->id]["local"] = $rs->telefono_casa;
                $row[$rs->id]["movil"] = $rs->telefono_movil;
                $row[$rs->id]["estatuscurso"] = $curs->active;
                $row[$rs->id]["estatusparticipante"] = $rs->RelationEstatus->estatus;
                $row[$rs->id]["campana"] = $curs->RelationCartera->nombre_cartera;
                $row[$rs->id]["skill"] = $curs->Relationskill->nombre_skill;
                $row[$rs->id]["cambio"] = $rs->cambiocampana;
                $row[$rs->id]["registrado"] = date('d/m/Y', strtotime($rs->created_at));
                $row[$rs->id]["facilitador"] = $curs->RelationPersonal->primer_nombre . " " . $curs->RelationPersonal->primer_apellido;
                $row[$rs->id]["referido"]  = $rs->referido == 1 ? "Reclutamiento" : ( $rs->referido == 2 ? "Operaciones" : null);
                foreach ($periodo as $key => $value) {
                    $row[$rs->id]["asistencia"][$value->format('Y-m-d')] = null;
                }
                $row[$rs->id]["observacion"] = $rs->relationObservacionesCurso->observaciones;
            }
            foreach ($curs->RelationAsistencias()->get() as $sistencia) {
                $row[$sistencia->rec_cparticipantes_id]["asistencia"][date('Y-m-d', strtotime($sistencia->created_at))] = $sistencia->prefijo;
            }
        }
        //dd($row);
        return [$row, $participantes, $periodo];
    }

    public function arrayIndicadores($cursos,$begin, $finish){
        
        $row = array();
        
        $finichs = new DateTime($finish);
        $finichs->modify('+ 1 day');
        $periodo = new DatePeriod(
                new DateTime($begin),
                new DateInterval('P1D'), $finichs);
         $listacursos = array();
         foreach ($cursos as $curs) {
            $listacursos[] = $curs->id;
            $row[$curs->id]["curso"] = "DGC" . $curs->id;
            $row[$curs->id]["estatuscurso"] = $curs->active;
            if ($curs->RelationEstado) {
                $row[$curs->id]["region"] = $curs->RelationEstado->RelationRegion->nombre_region;
                $row[$curs->id]["estado"] = $curs->RelationEstado->nombre_estado;
            } else {
                $row[$curs->id]["region"] = "N/A";
                $row[$curs->id]["estado"] = "N/A";
            }
            $row[$curs->id]["inicio"] = date('d/m/Y', strtotime($curs->inicio));
            $row[$curs->id]["finaliza"] = date('d/m/Y', strtotime($curs->finaliza));
            $row[$curs->id]["campana"] = $curs->RelationCartera->nombre_cartera;
            $row[$curs->id]["skill"] = $curs->Relationskill->nombre_skill;
            $row[$curs->id]["facilitador"] = $curs->RelationPersonal->primer_nombre . " " . $curs->RelationPersonal->primer_apellido;
            $row[$curs->id]["horariocurso"] = Arr::get($this->horariocursos, $curs->horario);
            $row[$curs->id]["convocado"]    = Arr::get($this->convocadocursos, $curs->convodado);
            $row[$curs->id]["sede"]         = SedeModel::where('nombre_sede',$curs->sede)->first()->real_name;
            $row[$curs->id]["requeridos"]   = $curs->requeridos;
            $row[$curs->id]["totalconvocado"] = ParticipantesModel::select(DB::raw('count(id) as total'))->where('rec_cursos_id', $curs->id)->first()->total;
            $row[$curs->id]["asistente"] = [];

            $asistentes = AsistenciaModel::select(DB::raw('count(id) as asistente, date_format(created_at,"%Y-%m-%d")as fecha'))
                            ->whereBetween('created_at', array("$begin 00:00:01", "$finish 23:59:59"))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('prefijo', ['A'])->groupBy('fecha')->get();

            foreach ($asistentes as $asistencia) {
                $row[$curs->id]["asistente"][date('d/m/Y', strtotime($asistencia->fecha))] = $asistencia->asistente;
            }
            

            $row[$curs->id]["abandono"] = ParticipantesModel::select(DB::raw('count(id) as abandono'))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('estatus_id', [13])->first()->abandono;

            $row[$curs->id]["desincorporado"] = ParticipantesModel::select(DB::raw('count(id) as desincorporado'))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('estatus_id', [15])->first()->desincorporado;

            $row[$curs->id]["reprobados"] = ParticipantesModel::select(DB::raw('count(id) as reprobado'))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('estatus_id', [16])->first()->reprobado;

            $row[$curs->id]["aprobados"] = ParticipantesModel::select(DB::raw('count(id) as aprobados'))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('estatus_id', [21])->first()->aprobados;

            $row[$curs->id]["noasistio"] = ParticipantesModel::select(DB::raw('count(id) as noasistio'))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('estatus_id', [17])->first()->noasistio;

            $row[$curs->id]["totalasistencia"] = $row[$curs->id]["totalconvocado"] - $row[$curs->id]["noasistio"];
            
           
            try {
                $row[$curs->id]["efectividad"] = round(($row[$curs->id]["totalasistencia"]/$row[$curs->id]["totalconvocado"])*100,2);
            } catch (\Exception $e) {                
                $row[$curs->id]["efectividad"] = 0;
            }            
            
            try {
                $row[$curs->id]["cumplimiento"] = round(($row[$curs->id]["totalasistencia"] / $curs->requeridos)*100 ,2);
            } catch (\Exception $e) {
                $row[$curs->id]["cumplimiento"] = 0;
            }
            // dd($row,$row[$curs->id]["totalasistencia"] , $curs->requeridos);
            $asistencia2dodia = date('Y-m-d', strtotime($curs->inicio.'+1 Days'));            
            $totalasistencia2dodia = AsistenciaModel::select(DB::raw('count(id) as asistente2dia'))
                            ->whereBetween('created_at', array("$asistencia2dodia 00:00:01", "$asistencia2dodia 23:59:59"))
                            ->where('rec_cursos_id', $curs->id)
                            ->whereIn('prefijo', ['A'])->first()->asistente2dia; 
            try {
                $row[$curs->id]["retencion"] = round((($row[$curs->id]["aprobados"] + $row[$curs->id]["reprobados"])/$totalasistencia2dodia)*100,2);
            } catch (\Exception $e) {
                $row[$curs->id]["retencion"] = 0;
            }                                       
        }
        //dd($row);//
        return [$listacursos,$row,$periodo];
    }
}
