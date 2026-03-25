<?php

namespace App\Http\Controllers\asistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\personal\PersonalModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\EstatusModel;
use App\Models\personal\CargoModel;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\EgresadosModel;
use App\Models\personal\ChangeCampaingModels;
use App\Models\personal\SkillModels;

use App\Models\asistencia\AsistenciaOperativaModel;
use App\Models\asistencia\ConvocadosModels;

use App\Exports\Asistencias\AsistenciasExport;
use App\Exports\Asistencias\IndicadoresExport;
use Maatwebsite\Excel\Facades\Excel;
use DatePeriod;
use DateTime;
use DateInterval;

use Carbon\Carbon;
use App\Http\Controllers\PoliceEstructure;


class ReportesController extends Controller {

    public $fechactual;
    public $supervisores;
    public $ejecutivo;
    public $coordinador;

    public function __construct($supervisores = null, $ejecutivo = null, $coordinador = null, $senior = null) {

        $supervisores = CargoModel::where('nombre_cargo', 'like', 'Supervisor%')
                        ->orwhere('nombre_cargo', 'like', 'Analista%')
                        ->orwhere('nombre_cargo', 'like', 'Formador%')->pluck('id');
        $this->supervisor = $supervisores;

        $ejecutivo = CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
                        ->orwhere('nombre_cargo', 'like', 'Team%')
                        ->orwhere('nombre_cargo', 'like', 'Validador%')
                        ->orwhere('nombre_cargo', 'like', 'Back%')
                        ->orwhere('nombre_cargo', 'like', 'Agente%')->pluck('id');
        $this->ejecutivo = $ejecutivo;

        $coordinador = CargoModel::where('nombre_cargo', 'like', 'Coordinador%')->pluck('id');
        $this->coordinador = $coordinador;

        $senior = CargoModel::Where('nombre_cargo', 'like', 'Senior%')->pluck('id');
        $this->senior = $senior;

        $this->otros = [];

        $this->itemsAsistencia = ["A" => "Asistente",
            "NL" => "No laborable",
            "CC" => "Cambio de Campaña",
            "DOM" => "Domingo",
            "FER" => "Feriado",
            "FI" => "Falta Injustificada",
            "FJ" => "Falta justificada",
            "PER" => "Permiso Remunerado",
            "PNR" => "Permiso No Remunerado",
            "SSO" => "Reposo",
            "VAC" => "Vacaciones"];
    }

    public function index() {

        $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);       
        
        if($nivel){            
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel,$user);         
        } else {
           return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }      

        return view('asistencia.reporte')->with(["areas" => $areas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        if (ENV('DEV_USER_CHANGE', false)) {
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
        try{
        
        $fecha = explode("-", $request->fecha);

        $begin = Carbon::createfromFormat('d/m/Y', trim($fecha[0]))->startofDay();
        $end = Carbon::createfromFormat('d/m/Y', trim($fecha[1]))->endofDay();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        $dato = AsistenciaOperativaModel::WhereBetween('created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')));

        switch ($nivel) {
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);

                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {

                    $result = $dato->wherein('personal_id', $idPersonals)
                                    ->whereIn('cartera_id', $carteraPermisada)
                                    ->whereIn('skill_id', $skillPermisado)->orderby('created_at', "ASC");
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {

                    $result = $dato->wherein('personal_id', $idPersonals)
                                    ->where('area_trabajo_id', $request->area_trabajo)
                                    ->whereIn('cartera_id', $carteraPermisada)
                                    ->whereIn('skill_id', $skillPermisado)->orderby('created_at', "ASC");
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {

                    $result = $dato->wherein('personal_id', $idPersonals)
                                    ->where('cartera_id', $request->carteras)
                                    ->whereIn('skill_id', $skillPermisado)->orderby('created_at', "ASC");
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {

                    $result = $dato->wherein('personal_id', $idPersonals)
                                    ->where('skill_id', $request->skill)->orderby('created_at', "ASC");
                }

                break;
            case 3:
                $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                ///SELECTORES EN TODOS
                if ($request->area_trabajo == null && $request->carteras == null) {
                    $result = $dato->WhereIn('skill_id', $skillsautorize)->orderby('created_at', "ASC");
                }
                /// DATOS EN GERENCIA Y SELECTORES EN TODOS
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)
                                ->WhereIn('skill_id', $skillsautorize)->orderby('created_at', "ASC");
                   
                }
                /// DATOS EN GERENCIA Y UNIDAD EL RESTO EN TODOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                                    ->WhereIn('skill_id', $skillsautorize)->orderby('created_at', "ASC");
                }
                ///DATOS ENGERENCIA, UNIDAD Y SKIL
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                        ->Where('skill_id', $request->skill)->orderby('created_at', "ASC");
                }                
                break;
            case 2:

                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills = $carterasPersonal[2];
                $personal = $carterasPersonal[3];

                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->whereIn('personal_id', $personal)
                                    ->wherein('skill_id', $skills)->orderby('created_at', "ASC");
                }
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->whereIn('personal_id', $personal)
                                    ->where('area_trabajo_id', $request->area_trabajo)
                                    ->wherein('skill_id', $skills)->orderby('created_at', "ASC");
                }
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                    $result = $dato->whereIn('personal_id', $personal)
                                    ->where('area_trabajo_id', $request->area_trabajo)
                                    ->where('cartera_id', $request->carteras)
                                    ->wherein('skill_id', $skills)->orderby('created_at', "ASC");
                }
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->whereIn('personal_id', $personal)
                                    ->where('area_trabajo_id', $request->area_trabajo)
                                    ->where('cartera_id', $request->carteras)
                                    ->where('skill_id', $request->skill)->orderby('created_at', "ASC");
                }

                break;
            case 1:
                ////4 SELECTORES EN TODOS
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->orderby('created_at', "ASC");
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)->orderby('created_at', "ASC");
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->carteras)->orderby('created_at', "ASC");
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->where('skill_id', $request->skill)->orderby('created_at', "ASC");
                }

                break;
            default:
                ///retorna el array vacio, no posee permisos.
                return json_encode(abort(403, "No cuenta los permisos para acceder a estos recursos."));
                break;
        }
     
        $sql = $result->get();
        $row = array();
        $personal = array();
        
        $finichs = new DateTime($end);
        $finichs->modify('+ 1 day');

        $period = new DatePeriod(
                new DateTime($begin),
                new DateInterval('P1D'), $finichs);
   
        foreach ($sql as $rs) {

            if (!array_key_exists($rs->personal_id, $row)) {
                $personal[]["personal"] = $rs->personal_id;
                $row[$rs->personal_id]["cedula"] = $rs->RelationPersonal->cedula_empleado;
                $row[$rs->personal_id]["primer_nombre"] = $rs->RelationPersonal->primer_nombre;
                $row[$rs->personal_id]["segundo_nombre"] = $rs->RelationPersonal->segundo_nombre;
                $row[$rs->personal_id]["primer_apellido"] = $rs->RelationPersonal->primer_apellido;
                $row[$rs->personal_id]["segundo_apellido"] = $rs->RelationPersonal->segundo_apellido;
                $row[$rs->personal_id]["cargo"] = $rs->RelationPersonal->RelationCargos != null ? $rs->RelationPersonal->RelationCargos->nombre_cargo : null;
                $row[$rs->personal_id]["nombre_campana"] = $rs->RelationPersonal->RelationAreas->nombre_campana;
                $row[$rs->personal_id]["nombre_cartera"] = $rs->RelationPersonal->RelationCartera->nombre_cartera;
                $row[$rs->personal_id]["nombre_skill"] = $rs->RelationPersonal->Relationskill->nombre_skill;
                $row[$rs->personal_id]["fecha_ingreso"] = $rs->RelationPersonal->fecha_ingreso != null ? date('d/m/Y', strtotime($rs->RelationPersonal->fecha_ingreso)) : null;
                $row[$rs->personal_id]["sede_actual"] = $rs->RelationPersonal->sede_laboral != null ? $rs->RelationPersonal->sede_laboral : "NO DISPONIBLE";
                $row[$rs->personal_id]["estatus_actual"] = $rs->RelationPersonal->RelationEstatus->estatus;
                $row[$rs->personal_id]["situacion_contractual"] = $rs->RelationPersonal->situacion_contractual;

                $row[$rs->personal_id]["asistente"] = 0;
                $row[$rs->personal_id]["faltainjustificada"] = 0;
                $row[$rs->personal_id]["asistenciatotales"] = 0;

                $row[$rs->personal_id]["asistencia"][date('d/m/Y', strtotime($rs->created_at))] = $rs->prefijo;
                if ($rs->RelationListAsistenciaJefeInmediato != null) {
                    $row[$rs->personal_id]["jefeinmeadito"] = $rs->RelationListAsistenciaJefeInmediato->primer_nombre . ' ' . $rs->RelationListAsistenciaJefeInmediato->primer_apellido;
                } else {
                    $row[$rs->personal_id]["jefeinmeadito"] = null;
                }
            } else {
                if ($rs->RelationListAsistenciaJefeInmediato != null) {
                    $row[$rs->personal_id]["jefeinmeadito"] = $rs->RelationListAsistenciaJefeInmediato->primer_nombre . ' ' . $rs->RelationListAsistenciaJefeInmediato->primer_apellido;
                } else {
                    $row[$rs->personal_id]["jefeinmeadito"] = null;
                }
                $row[$rs->personal_id]["asistencia"][date('d/m/Y', strtotime($rs->created_at))] = $rs->prefijo;
            }
            /// aqui para realizr el conteo de las Asistencias y las Faltas    
            if (in_array($rs->prefijo, ["A"])) {
                $row[$rs->personal_id]["asistente"] = $row[$rs->personal_id]["asistente"] + 1;
            }

            if (in_array($rs->prefijo, ["FI"])) {
                $row[$rs->personal_id]["faltainjustificada"] = $row[$rs->personal_id]["faltainjustificada"] + 1;
            }
        }
        
        return Excel::download(new AsistenciasExport($row,$period,$personal), 'Reporte De asistencia Operativa ' . $begin . ' al ' . $end . '.csv');
        
        } catch (\Exception $e){
          
            \Session::flash('error', 'Se encontro un problema al Exportar el reporte, '.$e->getMessage());
            return redirect()->back();
        }   
    }

    public function indicadoresIndex() {

         $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);       
        
        if($nivel){            
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel,$user);         
        } else {
           return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }      

        return view('asistencia.indicadores')->with(["areas" => $areas]);
    }

    public function indicadoresCreate(Request $request) {
        //dd($request->all());
        $namecampania = SkillModels::find($request->skill)->nombre_skill;

        $fecha = explode("-", $request->fecha);

        $begin = Carbon::createfromFormat('d/m/Y', trim($fecha[0]))->startofDay();
        $end = Carbon::createfromFormat('d/m/Y', trim($fecha[1]))->endofDay();

        $finichs = new DateTime($end);
        $finichs->modify('+ 1 day');

        $period = new DatePeriod(
                new DateTime($begin),
                new DateInterval('P1D'), $finichs);

        // se ubica todos los convados del dia por la fecha en cuestion
        $convocados = ConvocadosModels::select(db::raw("total_convocados, date_format(created_at,'%Y-%m-%d')as fecha"))
                        ->whereBetween('created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->where('skill_id', $request->skill)->get();

        ///Se calcula el maximo de convocados de todos los dias buscados ///
        $arrayconvocados = $convocados->pluck('total_convocados')->ToArray();

        $ingresos = PersonalModel::select(db::raw("count(id) as ingresos, date_format(created_at,'%Y-%m-%d')as fecha"))
                        ->whereBetween('created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('skill_id', $request->skill)->groupBy('fecha')->get();

        $nolaboral = AsistenciaOperativaModel::select(db::raw("count(dir_asistencia_operativa.id) as nolaboral, date_format(dir_asistencia_operativa.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_asistencia_operativa.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_asistencia_operativa.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_asistencia_operativa.skill_id', $request->skill)->whereIn('dir_asistencia_operativa.prefijo', ['NL'])->groupBy('fecha')->get();

        $asistentes = AsistenciaOperativaModel::select(db::raw("count(dir_asistencia_operativa.id) as asistentes, date_format(dir_asistencia_operativa.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_asistencia_operativa.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_asistencia_operativa.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
                                ->orwhere('nombre_cargo', 'like', 'Team%')->orwhere('nombre_cargo', 'like', 'Validador%')->pluck('id'))
                        ->where('dir_asistencia_operativa.skill_id', $request->skill)->whereIn('dir_asistencia_operativa.prefijo', ['A', 'FER', 'DOM'])->groupBy('fecha')->get();

        $faltas = AsistenciaOperativaModel::select(db::raw("count(dir_asistencia_operativa.id) as faltantes, date_format(dir_asistencia_operativa.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_asistencia_operativa.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_asistencia_operativa.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_asistencia_operativa.skill_id', $request->skill)->where('dir_asistencia_operativa.prefijo', 'FI')->groupBy('fecha')->get();
        //DD($request->skill,$faltas);
        $reposos = AsistenciaOperativaModel::select(db::raw("count(dir_asistencia_operativa.id) as reposos, date_format(dir_asistencia_operativa.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_asistencia_operativa.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_asistencia_operativa.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_asistencia_operativa.skill_id', $request->skill)->where('dir_asistencia_operativa.prefijo', 'SSO')->groupBy('fecha')->get();

        $cambios = ChangeCampaingModels::select(db::raw("count(dir_cambios_campaing.id) as cambio, date_format(dir_cambios_campaing.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_cambios_campaing.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_cambios_campaing.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_cambios_campaing.actual_skill_id', $request->skill)->groupBy('fecha')->get();

        $cambiosin = ChangeCampaingModels::select(db::raw("count(dir_cambios_campaing.id) as cambio, date_format(dir_cambios_campaing.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_cambios_campaing.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_cambios_campaing.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_cambios_campaing.nuevo_skill_id', $request->skill)->groupBy('fecha')->get();

        $vacaciones = AsistenciaOperativaModel::select(db::raw("count(dir_asistencia_operativa.id) as vacaciones, date_format(dir_asistencia_operativa.created_at,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_asistencia_operativa.personal_id', 'dir_personal.id')
                        ->whereBetween('dir_asistencia_operativa.created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_asistencia_operativa.skill_id', $request->skill)
                        ->where('dir_asistencia_operativa.prefijo', 'VAC')->groupBy('fecha')->get();

        $egresos = EgresadosModel::select(db::raw("count(dir_egresados.id) as egresos, date_format(dir_egresados.fecha,'%Y-%m-%d')as fecha"))
                        ->leftjoin('dir_personal', 'dir_egresados.dir_personal_id', 'dir_personal.id')
                        ->whereBetween('fecha', array("$begin", "$end"))
                        ->wherein('dir_personal.cargo_id', $this->ejecutivo)
                        ->where('dir_egresados.skill_id', $request->skill)->groupBy('fecha')->get();
        //dd($cambiosin);
        $row = array();

        $totalconvocados = 0;
        foreach ($convocados as $convocado) {
            $row[$convocado->fecha]["convocados"] = $convocado->total_convocados;
            $totalconvocados = $totalconvocados + $convocado->total_convocados;
        }  /// total activos         

        $totalingresos = 0;
        foreach ($ingresos as $ingreso) { ///ingresos en lp    
            $row[$ingreso->fecha]["ingresos"] = $ingreso->ingresos;
            $totalingresos = $totalingresos + $ingreso->ingresos;
        }



        foreach ($cambiosin as $cambiosing) { //// array_key_exists("ingresos",$row[$cambiosing->fecha])  personal que ha sido cambiado de campaña que ingresa a la nueva. Como egreso de una, ingresa en la otra           
            if (isset($row[$cambiosing->fecha]["ingresos"])) {
                $row[$cambiosing->fecha]["ingresos"] = $row[$cambiosing->fecha]["ingresos"] + $cambiosing->cambio;
                $totalingresos = $totalingresos + $cambiosing->cambio;
            } else {
                $row[$cambiosing->fecha]["ingresos"] = $cambiosing->cambio;
                $totalingresos = $totalingresos + $cambiosing->cambio;
            }
        }

        $totalasistente = 0;
        foreach ($asistentes as $asistente) {
            $row[$asistente->fecha]["asistente"] = $asistente->asistentes;
            $totalasistente = $totalasistente + $asistente->asistentes;
        }
        $nolabora = 0;
        foreach ($nolaboral as $nl) {
            $row[$nl->fecha]["nolaboral"] = $nl->nolaboral;
            $nolabora = $nolabora + $nl->nolaboral;
        }

        $totalfaltantes = 0;
        foreach ($faltas as $falta) {
            $row[$falta->fecha]["faltantes"] = $falta->faltantes;
            $totalfaltantes = $totalfaltantes + $falta->faltantes;
        }

        $totalegresos = 0;
        foreach ($egresos as $egreso) {
            $row[$egreso->fecha]["egresos"] = $egreso->egresos;
            $totalegresos = $totalegresos + $egreso->egresos;
        }

        foreach ($reposos as $reposo) {
            $row[$reposo->fecha]["reposos"] = $reposo->reposos;
        }

        foreach ($vacaciones as $vacacion) {
            $row[$vacacion->fecha]["vacaciones"] = $vacacion->vacaciones;
        }

        $totalcambios = 0;
        foreach ($cambios as $cambio) {
            $row[$cambio->fecha]["cambios"] = $cambio->cambio;
            $totalcambios = $totalcambios + $cambio->cambio;
        }

        try { /// calculamos el ausentismo FI / Asistente, en caso de no poder dividir, se coloca 0;
            $row["ausentismo"] = $totalfaltantes / $totalconvocados;
            $row["ausentismo"] = round($row["ausentismo"] * 100, 2);
        } catch (\Exception $ex) {
            $row["ausentismo"] = 0;
        }

        try {
            $recuentoperiodo = 0; //// hacemos recuento del periodo buscado
            foreach ($period as $key => $value) {
                $recuentoperiodo = $recuentoperiodo + 1;
            }
            $maxconvocados = max($arrayconvocados);    //// maximo de convocados
            $minconvocados = min($arrayconvocados);    //// min de convocados
            /// calculamos el promedio de los convocados segun el rango
            // $promedioconvocados = $totalconvocados / $recuentoperiodo;
            ///suma los ingresos, egresos y cambios se divive entre 2 para promediar
            $erpromedio = ($totalingresos + $totalegresos + $totalcambios) / 2;
            /// calcula el maximo de convocados +1 en el total de convocados, de existir se pasa el mayor convocados, caso contrario sera 0
            ///calculamos erpromedio suma(ingresos, cambios, egresos)
            /// y dividimos el promedio de la suma del minimo de convocados + el maximo de convocados 
            /// R = S / ( (MIN + MAX) /2 ) *100

            $rotacion = ($erpromedio / (($minconvocados + $maxconvocados) / 2)) * 100;
            $row["rotacion"] = round($rotacion, 2);
        } catch (\Exception $ex) {
            $row["rotacion"] = 0;
        }

        try {
            $indicerot = (($totalegresos + $totalcambios) / ($maxconvocados)) * 100;
            $row["indicerot"] = round($indicerot, 2);
        } catch (\Exception $ex) {
            $row["indicerot"] = 0;
        }
        //dd($row);
        return Excel::download(new IndicadoresExport($namecampania, $row, $period), 'Indicadores Operativos ' . $namecampania . '.xlsx');
    }
}
