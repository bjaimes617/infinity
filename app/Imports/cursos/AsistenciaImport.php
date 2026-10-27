<?php

namespace App\Imports\cursos;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use App\Models\cursos\CursosModel;
use App\Models\cursos\AsistenciaModel;
use App\Models\cursos\ObservacionesModels;
use App\Models\cursos\ParticipantesModel;
use App\Models\cursos\PersonalModel;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AsistenciaImport implements ToCollection, WithHeadingRow
{
    use ActivityLogger;
    use Importable;

    private string $curso;
    private array $cabecera, $participantes;

    public function __construct($curso, $cabecera)
    {
        $this->curso = $curso;
        #recibimos las cabeceras del archivo para extraer la fecha
        $this->cabecera = $cabecera;

        $this->facilitador = CursosModel::find($this->curso);
        $this->participantes = $this->facilitador->RelationParticipantes()->pluck('id', 'cedula_empleado')->toArray();

        ///desincorporado - son egresos
        $this->egresos = ObservacionesModels::where('estatus_id', 15)->pluck('id', 'observaciones')->toArray();
        /// abanono  - abandono
        $this->abandonos = ObservacionesModels::where('estatus_id', 13)->pluck('id', 'observaciones')->toArray();
    }

    public function collection(Collection $collection)
    {


        $begin = $this->cabecera[3];
        if (Carbon::hasFormat($begin, 'Y_m_d')) {
            $fecha = Carbon::createFromFormat('Y_m_d', $begin)->format('Y-m-d');
        } else {
            // 2. Si no, que Carbon intente adivinar el formato que esté recibiendo
            $fecha = Carbon::parse($begin)->format('Y-m-d');
        }

        $dia = date('l', strtotime($fecha)); ///dia de la semana en letras///   
        $hora = date('H:i:s', strtotime(now()));
        $ids_part = array_keys($this->participantes);
        foreach ($collection as $row) {

            if (array_search($row["cedula"], $ids_part)) {
                $sistencia = new AsistenciaModel();
                $sistencia->facilitador_id  = $this->facilitador->personal_id;
                $sistencia->rec_cparticipantes_id = $this->participantes[$row["cedula"]];
                $sistencia->rec_cursos_id   = $this->curso;
                $sistencia->prefijo         = $row[$begin];
                $part = ParticipantesModel::find($this->participantes[$row["cedula"]]);

                switch ($row[$begin]) {
                    case "E":
                        $part->estatus_id = 15;
                        $part->rec_observacion_id = $this->egresos[$row["observaciones"]];
                        if ($part->cambiocampana == "SI") {
                            $objPersonal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                            $objPersonal->estatus_id = 1;
                            $objPersonal->save();
                        }
                        break;
                    case "NA":
                        $part->estatus_id = 17;
                        $part->rec_observacion_id = 14;
                        if ($part->cambiocampana == "SI") {
                            $objPersonal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                            $objPersonal->estatus_id = 1;
                            $objPersonal->save();
                        }
                        break;
                    case "AB":
                        $part->estatus_id = 13;
                        $part->rec_observacion_id = $this->abandonos[$row["observaciones"]];
                        if ($part->cambiocampana == "SI") {
                            $objPersonal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                            $objPersonal->estatus_id = 1;
                            $objPersonal->save();
                        }
                        break;
                }

                if ($part->cambiocampana == "SI") {
                    $personal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                    if (!AsistenciaOperativaModel::where('personal_id', $personal->id)
                        ->where('created_at', 'like', $begin . '%')->exists()) {
                        $sistenciaop = new AsistenciaOperativaModel();
                        $sistenciaop->tipo_asistencia = "CC";
                        $sistenciaop->gerente_id = $personal->gerente_area_id;
                        $sistenciaop->supervisor_id = $personal->jefe_inmediato;
                        $sistenciaop->coordinador_id = $personal->jefe_inmediato_segundo;
                        $sistenciaop->personal_id = $personal->id;
                        $sistenciaop->area_trabajo_id = $personal->area_trabajo_id;
                        $sistenciaop->cartera_id = $personal->cartera_id;
                        $sistenciaop->skill_id = $personal->skill_id;
                        switch ($this->participantes[$row["cedula"]]) {
                            case "A":
                                $val = "CCA";
                                break;
                            case "I":
                                $val = "CCI";
                                break;
                            default:
                                $val = "CC" . $this->participantes[$row["cedula"]];
                                break;
                        }
                        $sistenciaop->prefijo = $val;
                        $sistenciaop->block = 1;
                        $sistenciaop->dia = $dia;
                        $sistenciaop->observacion = "Registro de Asistencia automatico por cambio de campaña";
                        $sistenciaop->created_at = $begin . " " . $hora;
                        $sistenciaop->created_at = $begin . " " . $hora;
                        $sistenciaop->save();
                    }
                }

                $sistencia->block = 1;
                $sistencia->dia = $dia;
                $sistencia->observacion = "Registro de Asistencia del curso modalidad Masiva";
                $sistencia->created_at = $fecha . " " . $hora;
                $sistencia->created_at = $fecha . " " . $hora;

                $part->save();
                $sistencia->save();

                $this->activity("Curso - Registro de asistencia: " . $row["cedula"] . " Curso " . $this->curso . " " . json_encode($row));
            } else {
                continue;
            }
        }

        if ($this->facilitador->finaliza == $fecha) {
            $this->facilitador->automaticClose = 1;
            $this->facilitador->active = 0;
            $this->facilitador->save();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Cedula de Identidad es Obligatoria');
                } else if (!ctype_digit($value)) {
                    $onFailure('La Cedula de Identidad debe ser solo númerico');
                } else if (!array_key_exists($value, $this->participantes)) {
                    $onFailure('El participante No se encuentra Asignado al Curso seleccionado');
                }
            },
            $this->cabecera[3] => function ($attribute, $value, $onFailure) {
                $prefijosPermitidos = ['A', 'I', 'NA', 'E', 'AB'];
                if ($value == "") {
                    $onFailure('El Prefijo es obligatorio');
                } else if (!in_array(strtoupper($value), $prefijosPermitidos)) {
                    $onFailure('El Prefijo Ingresado ' . strtoupper($value) .  'es Invalido');
                }
            },
            'observaciones' => [
                Rule::requiredIf(function () {
                    $fecha = $this->cabecera[3];
                    // Para evitar enredos con el Request global, usamos la regla requerida condicional:
                    return in_array($fecha, ['E', 'AB']);
                }),
                // Formma limpia estándar de Laravel:
                'required_if:' . $this->cabecera[3] . ',E,AB'
            ],
        ];
    }
}
