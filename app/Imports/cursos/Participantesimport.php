<?php

namespace App\Imports\cursos;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\cursos\ParticipantesModel;
use App\Models\personal\BlacklistModel;
use App\Models\personal\PersonalModel;
use App\Models\cursos\HistorialModel;
use App\Models\cursos\CursosModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\EstatusModel;
use Carbon\Carbon;

class Participantesimport implements ToCollection, WithHeadingRow, WithValidation {

    use Importable;

    public $curso;
    public $referido;

    public function __construct($curso,$referido) {
        $this->curso = $curso;
        $this->referido = $referido;
    }

    public function collection(Collection $collection) {
       try {
            foreach ($collection as $row) {
                switch ($row["cambiocampana"]) {
                    case "SI":
                        if (PersonalModel::where('cedula_empleado', $row["cedula"])->where('estatus_id', 1)->exists()) {

                            $objPersonal = PersonalModel::where('cedula_empleado', $row["cedula"])->where('estatus_id', 1)->first();

                            if (ParticipantesModel::where('cedula_empleado', $row["cedula"])->exists()) {
                                $participante = ParticipantesModel::where('cedula_empleado', $row["cedula"])->first();
                                $participante->created_at = date('Y-m-d H:i:s', strtotime(now()));
                            } else {
                                $participante = new ParticipantesModel();
                            }
                            
                            $participante->cedula_empleado = $row["cedula"];
                            $participante->primer_nombre = $row["primer_nombre"];
                            $participante->segundo_nombre = $row["segundo_nombre"];
                            $participante->segundo_apellido = $row["segundo_apellido"];
                            $participante->primer_apellido = $row["primer_apellido"];
                            $participante->telefono_movil = $row["movil"];
                            $participante->telefono_casa = $row["local"];
                            $participante->especialidad = array_keys(config('app.especialidad'), $row["nivel_academico"], true)[0];
                            $participante->horario_operativo = array_keys(config('app.horariolaboral'), $row["horario"], true)[0];
                            $participante->rec_cursos_id = $this->curso;
                            $participante->estatus_id = 12;
                            $participante->rec_observacion_id = 6;
                            $participante->cambiocampana = "SI";
                            $participante->referido = $this->referido;
                            $participante->save();

                            $objPersonal->estatus_id = 20; ///estatus cambio de campaña
                            $objPersonal->save();

                            $historial = new HistorialModel();
                            $historial->cedula_participante = $participante->cedula_empleado;
                            $historial->nombre = $participante->primer_nombre . " " . $participante->primer_apellido;
                            $historial->id_curso = $participante->rec_cursos_id;

                            $buscar_campana = $participante->RelationCurso;
                            $buscar_cartera = $buscar_campana->RelationCartera->nombre_cartera;
                            $buscar_skill = $buscar_campana->Relationskill->nombre_skill;
                            $historial->cartera = $buscar_cartera;
                            $historial->skill = $buscar_skill;

                            $historial->fecha_inicio = $buscar_campana->inicio;
                            $historial->fecha_fin = $buscar_campana->finaliza;
                            $historial->estatus_final = $participante->RelationEstatus->estatus;
                            $historial->save();
                        } else {
                            \Session::flash('error', 'No existen trabajadores activos para cambio de Campaña');
                            return back()->withErrors([]);
                        }
                        break;
                    default:
                        if (ParticipantesModel::where('cedula_empleado', $row["cedula"])->exists()) {
                            $participante = ParticipantesModel::where('cedula_empleado', $row["cedula"])->first();
                            $participante->created_at = date('Y-m-d h:i:s', strtotime(now()));
                        } else {
                            $participante = new ParticipantesModel();
                        }
                        $participante->cedula_empleado = $row["cedula"];
                        $participante->primer_nombre = $row["primer_nombre"];
                        $participante->segundo_nombre = $row["segundo_nombre"];
                        $participante->segundo_apellido = $row["segundo_apellido"];
                        $participante->primer_apellido = $row["primer_apellido"];
                        $participante->telefono_movil = $row["movil"];
                        $participante->telefono_casa = $row["local"];
                        $participante->especialidad = array_keys(config('app.especialidad'), $row["nivel_academico"], true)[0];
                        $participante->horario_operativo = array_keys(config('app.horariolaboral'), $row["horario"], true)[0];
                        $participante->rec_cursos_id = $this->curso;
                        $participante->cambiocampana = "NO";
                        $participante->estatus_id = 12;
                        $participante->rec_observacion_id = 6;
                        $participante->referido = $this->referido;
                        $participante->save();

                        $historial = new HistorialModel();
                        $historial->cedula_participante = $participante->cedula_empleado;
                        $historial->nombre = $participante->primer_nombre . " " . $participante->primer_apellido;
                        $historial->id_curso = $participante->rec_cursos_id;

                        $buscar_campana = $participante->RelationCurso;
                        $buscar_cartera = $buscar_campana->RelationCartera->nombre_cartera;
                        $buscar_skill = $buscar_campana->Relationskill->nombre_skill;
                        $historial->cartera = $buscar_cartera;
                        $historial->skill = $buscar_skill;
                        $historial->fecha_inicio = $buscar_campana->inicio;
                        $historial->fecha_fin = $buscar_campana->finaliza;
                        $historial->estatus_final = $participante->RelationEstatus->estatus;
                        $historial->save();
                        break;
                }
            }

            \Session::flash('success', 'Los participantes han sido asignados correctamente');
            return true;
        } catch (\Throwable $e) {
            \Session::flash('error', 'Ocurrio un problema, ' . $e->getMessage());
            return back()->withErrors([]);
        }
    }

    public function rules(): array {
        return [
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Cedula de Identidad es Obligatoria');
                } else if (!ctype_digit($value)) {
                    $onFailure('La Cedula de Identidad debe ser solo númerico');
                } else if ($value >= 6 && $value <= 8) {
                    $onFailure('La Cedula debe contener una logintud mayor a 6 o menor 8');
                } else if (PersonalModel::where('cedula_empleado', $value)->exists()) {
                    $objPersonal = PersonalModel::where('cedula_empleado', $value)->first();
                    $situacion = $objPersonal->getrawOriginal('situacion_contractual');
                    // SI LA SITUACION CONTRACTUAL NO ES FREELANCE HACEMOS LA COMPROBACION DE LOS 90 DIAS
                    if ($situacion != 3) {
                        if ($objPersonal->fecha_egreso != null) {
                            $fecha1 = Carbon::today();
                            $fecha2 = Carbon::parse($objPersonal->fecha_egreso);
                            ///validamos cuantos dias han pasado desde que fue egresado
                            $diferencia_en_dias = $fecha1->diffInDays($fecha2);
                            if ($diferencia_en_dias <= config('app.diaspararecontratar')) {
                                $onFailure('Esta persona tiene menos de 90 Días de Egresado.');
                            }
                        }
                    }
                } else if (ParticipantesModel::Where('cedula_empleado', $value)->exists()) {
                    if (!ParticipantesModel::Where('cedula_empleado', $value)->whereIn('estatus_id', [15, 17, 13, 16, 21])->exists()) {
                        $onFailure('Este participante aun no ha sido determinado como Egresado en otro curso.');
                    }
                } else if (BlacklistModel::where('cedula_empleado', $value)->exists()) {
                    $onFailure('Este participante no puede ser contratado Lista Negra, informar a Talento Humano');
                }
            }, 'primer_nombre' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El primer nombre es Obligatorio');
                }
            }, 'primer_apellido' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El primer apellido es Obligatorio');
                }
            }, 'nivel_academico' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Nivel Academico es Obligatorio');
                } elseif (!in_array($value, config('app.especialidad'))) {
                    $onFailure('El nivel Academino que ingresaste, no existe');
                }
            }, 'local' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El número de Telefono Local es Obligatorio');
                }
            }, 'movil' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El número de Telefono Movil es Obligatorio');
                }
            }, 'horario' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Horario Laboral es Obligatorio');
                } elseif (!in_array($value, config('app.horariolaboral'))) {
                    $onFailure('El Horario Laboral que ingresaste no existe.');
                }
            }, 'cambiocampana' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('Debe indicar si es un cambio de Campaña');
                }
            }
        ];
    }
}
