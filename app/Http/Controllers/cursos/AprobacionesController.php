<?php

namespace App\Http\Controllers\cursos;

use App\Http\Controllers\Controller;
use App\Models\cursos\AsistenciaModel;
use App\Models\cursos\CursosModel;
use App\Models\cursos\ParticipantesModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\EstatusModel;
use Illuminate\Http\Request;
use App\Models\personal\CentroCostoAsignadoModel;
use Illuminate\Support\Facades\DB;
use App\Models\personal\ChangeCampaingModels;
use Carbon\Carbon;
use App\Models\cursos\HistorialModel;

use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;

class AprobacionesController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->documentacion = ["1" => "Doc. Completa", "2" => "Doc. Incompleta"];
        $this->accion = ["1" => "Migrar", "2" => "En Espera"];
    }

    public function index()
    {

        $initPersonal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();

        if ($initPersonal) {
            $cursos = CursosModel::where('active', 0)
                ->where('automaticClose', 1)
                ->where('personal_id', $initPersonal->id)
                ->whereRaw("DATEDIFF(CURDATE(), finaliza) <= 15")->where('migrado', "NO")
                ->orderBy('id', 'desc')->get();
        } else {
            $cursos = array();
        }

        return view('cursos.aprobaciones.index')->with(["cursos" => $cursos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $es = EstatusModel::find($request->estatus)->relationObservacionesCurso()->get();
        return json_encode($es);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        try {
            // dd("pruebas", $request->all());
            foreach ($request->participante as $participante) {

                $parts = ParticipantesModel::find($participante);
                $parts->estatus_id = $request->input('listado' . $participante);
                $parts->rec_observacion_id = $request->input('observacion' . $participante);
                $parts->save();

                $estatus = EstatusModel::where('id', $request->input('listado' . $participante))->first()->estatus;

                $this->activity("Participante - " . $parts->primer_nombre . " " . $parts->primer_apellido . " - " . $parts->cedula_empleado . " - " . $estatus);

                if (HistorialModel::where('cedula_participante', $parts->cedula_empleado)->where('id_curso', $id)->exists()) {

                    $historial_participante = HistorialModel::where('cedula_participante', $parts->cedula_empleado)
                        ->where('id_curso', $id)->first();

                    $buscar_estatus = EstatusModel::select('estatus')->where('id', $parts->estatus_id)->first()->estatus;
                    $historial_participante->estatus_final = $buscar_estatus;
                    $historial_participante->save();
                }
            }
            \Session::flash('success', 'Actualizacion de participantes realizado con exito.');
            return back();
        } catch (\Throwable $e) {
            \Session::flash('error', 'Ocurrio un problema ' . $e->getMessage());
            return back();
        }
    }

    public function show($id)
    {
        /* 12 activo 
         * 19 esperando certificado
         * 20 cambio campaña
         */
        $part = ParticipantesModel::where('rec_cursos_id', $id)->whereIn('estatus_id', [12, 19, 20])->get();

        $estatus = EstatusModel::where('group_module', 'C')->wherenotin('id', [12, 21])->get();
        $row = array();
        $info = array();

        foreach ($part as $participante) {
            $row["id"]          = $participante->id;
            $row["cedula_empleado"] = $participante->cedula_empleado;
            $row["nombre"]      = $participante->primer_nombre . ' ' . $participante->primer_apellido;
            $row["estatus"]     = $participante->RelationEstatus->estatus;
            $row["asistencia"]  = AsistenciaModel::where('rec_cparticipantes_id', $participante->id)->where('prefijo', "A")->count();
            $row["inasistencia"] = AsistenciaModel::where('rec_cparticipantes_id', $participante->id)->where('prefijo', "I")->count();
            $info[] = $row;
        }

        $data = view('cursos.aprobaciones.select', ["info" => $info, "estatus" => $estatus, "curso" => $id])->render();
        return response($data, 200);
    }


    public function aprobacionParticipantesIndex()
    {

        $cursos = CursosModel::where('active', 0)
            ->where('automaticClose', 1)
            ->where('migrado', 'NO')
            ->whereRaw("DATEDIFF(CURDATE(), finaliza) <= 120")->orderBy('id', 'desc')->get();

        return view('cursos.aprobaciones.aprobar')->with(["cursos" => $cursos]);
    }


    public function showAprobadoslp($id)
    {
        $part = ParticipantesModel::where('rec_cursos_id', $id)->where('estatus_id', 14)->get();
        $estatus = EstatusModel::wherein('id', [1, 11])->get();
        $row = array();
        $info = array();

        foreach ($part as $participante) {
            $row["id"] = $participante->id;
            $row["cedula_empleado"] = $participante->cedula_empleado;
            $row["nombre"] = $participante->primer_nombre . ' ' . $participante->primer_apellido;
            $row["estatus"]     = $participante->RelationEstatus->estatus;
            $row["asistencia"] = AsistenciaModel::Select(DB::raw('count(id) as conteo'))->where('rec_cparticipantes_id', $participante->id)->where('prefijo', "A")->first()->conteo;
            $row["inasistencia"] = AsistenciaModel::Select(DB::raw('count(id) as conteo'))->where('rec_cparticipantes_id', $participante->id)->where('prefijo', "I")->first()->conteo;
            $info[] = $row;
        }
        $data = view('cursos.aprobaciones.preselect', ["info" => $info, "estatus" => $estatus, "accion" => $this->accion, "documentacion" => $this->documentacion])->render();
        return response($data, 200);
    }

    public function aprobacionParticipantesStore(Request $request)
    {
        $conteototal = count($request->participante);
        $contador = 0;

        try {
            foreach ($request->participante as $participante) {
                if ($request->input('accion' . $participante) == 1) {
                    $contador++;
                    $parts      = ParticipantesModel::find($participante);
                    $id_curso   = $parts->rec_cursos_id;
                    $parts->estatus_id = 21;

                    if ($request->input('listado' . $participante) == 1) {
                        if (PersonalModel::where('cedula_empleado', $parts->cedula_empleado)->exists()) {
                            $objPersonal = PersonalModel::where('cedula_empleado', $parts->cedula_empleado)->first();
                            CentroCostoAsignadoModel::where('personal_id', $objPersonal->id)->forcedelete();
                        } else {
                            $objPersonal = new PersonalModel();
                        }
                        $objPersonal->cedula_empleado   = $parts->cedula_empleado;
                        $objPersonal->primer_nombre     = ucfirst(strtolower($parts->primer_nombre));
                        $objPersonal->segundo_nombre    = ucfirst(strtolower($parts->segundo_nombre));
                        $objPersonal->primer_apellido   = ucfirst(strtolower($parts->primer_apellido));
                        $objPersonal->segundo_apellido  = ucfirst(strtolower($parts->segundo_apellido));
                        $objPersonal->nivel_academico   = $parts->especialidad;
                        $objPersonal->telefono_casa     = $parts->telefono_casa;
                        $objPersonal->contrato_1        = null;
                        $objPersonal->finalizacion_1    = null;
                        $objPersonal->contrato_2        = null;
                        $objPersonal->finalizacion_2    = null;
                        $objPersonal->telefono_movil    = $parts->telefono_movil;
                        $objPersonal->horario_operativo = $parts->getrawOriginal('horario_operativo');

                        if ($parts->cambiocampana === "SI") {
                            ///si corresponde a cambio de campaña se debe registrar el cambio
                            if ($objPersonal->cartera_id !== $parts->RelationCurso->cartera_id) {
                                $change = new ChangeCampaingModels();
                                $change->personal_id = $objPersonal->id;
                                $change->area_trabajo_id = $objPersonal->area_trabajo_id;
                                $change->actual_cartera_id = $objPersonal->cartera_id;
                                $change->actual_skill_id = $objPersonal->skill_id;
                                $change->nueva_cartera_id = $parts->RelationCurso->cartera_id;
                                $change->nuevo_skill_id = $parts->RelationCurso->skill_id;
                                $change->save();
                            }
                        } else {
                            //// si no corresponde a un cambio de campañase establecen las situciones contractuales 
                            //7 para doc completa 9 para falta documentacion

                            $objPersonal->cargo_id = null;
                            if ($request->input('documentacion' . $participante) === 1) {
                                $objPersonal->situacion_contractual = 7;
                            } else {
                                $objPersonal->situacion_contractual = 9;
                            }
                        }

                        $objPersonal->gerente_area_id           = null;
                        $objPersonal->jefe_inmediato_segundo    = null;
                        $objPersonal->jefe_inmediato            = null;
                        $objPersonal->area_trabajo_id           = $parts->RelationCurso->area_trabajo_id;
                        $objPersonal->cartera_id                = $parts->RelationCurso->cartera_id;
                        $objPersonal->skill_id                  = $parts->RelationCurso->skill_id;
                        $objPersonal->estatus_id                = 1;

                        $areas = new CentroCostoAsignadoModel();
                        $areas->personal_id     = $objPersonal->id;
                        $areas->cartera_id      = $parts->RelationCurso->cartera_id;
                        $areas->skill_id        = $parts->RelationCurso->skill_id;
                        $areas->porcentaje      = null;

                        $areas->save();
                        $objPersonal->save();
                        $parts->save();

                        ///si el estatus de la migracion del curso es diferente a 1
                    } else if (PersonalModel::where('cedula_empleado', $parts->cedula_empleado)->wherenull('fecha_egreso')->exists()) {

                        $objPersonal = PersonalModel::where('cedula_empleado', $parts->cedula_empleado)->first();
                        $objPersonal->estatus_id = 1;
                        $objPersonal->save();
                        $parts->save();
                    }

                    if (HistorialModel::where('cedula_participante', $parts->cedula_empleado)->where('id_curso', $parts->rec_cursos_id)->exists()) {
                        $historial_participante = HistorialModel::where('cedula_participante', $parts->cedula_empleado)->where('id_curso', $parts->rec_cursos_id)->first();
                        $buscar_estatus = EstatusModel::select('estatus')->where('id', $parts->estatus_id)->first();
                        $historial_participante->estatus_final = $buscar_estatus->estatus;
                        $historial_participante->save();
                    }

                    $objeto_personal = json_encode($objPersonal);
                    $this->activity("Migracion Curso - LP " . $objPersonal->cedula_empleado, $objeto_personal);
                }

                if ($contador == $conteototal) {
                    $curso = CursosModel::where('id', $id_curso)->first();
                    $curso->migrado = "SI";
                    $curso->save();
                }
            }

            \Session::flash('success', 'Participantes migrados con exito, Actualice Expedientes');
            return back();
        } catch (\Throwable $e) {
            \Session::flash('error', 'Ocurrio un problema ' . $e->getMessage());
            return back();
        }
    }
}
