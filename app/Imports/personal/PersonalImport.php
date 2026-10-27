<?php

namespace App\Imports\personal;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\personal\PersonalModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\CargoModel;
use App\Models\personal\NominaModel;
use App\Models\personal\LibranzasModel;
use App\Models\personal\EstatusModel;
use App\Models\personal\ZonaTransporte;
use App\Models\personal\SedeModel;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Models\personal\CentroCostoAsignadoModel;
use App\Models\personal\BancosModels;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;
use App\Exceptions\ValidateAditionals;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;

class PersonalImport implements ToCollection, WithHeadingRow, WithValidation
{

    use Importable;
    use ActivityLogger;

    public function __construct()
    {
        $this->nacademicos = config('app.especialidad');
        $this->horariolab = config('app.horariolaboral');
        $this->contractual = config('app.contractual');
    }

    public function collection(Collection $rows)
    {

        try {
            foreach ($rows as $row) {
                /*if (PersonalModel::where('cedula_empleado', '=', trim($row['cedula_empleado']))->exists()) {
                   $objPersonal = PersonalModel::where('cedula_empleado', '=', trim($row['cedula_empleado']))->first();
                    CentroCostoAsignadoModel::where('personal_id', $objPersonal->id)->forcedelete();
                    $objPersonal->fecha_egreso = null;
                    $objPersonal->motivo_egreso = null;

                    $mensaje_log = "Personal Actualizado Masivo" . $row['cedula_empleado'];
                } else {
                    $objPersonal = new PersonalModel();
                    $mensaje_log = "Personal registrado Masivo" . $row['cedula_empleado'];
                }*/
                $objPersonal = new PersonalModel();
                $mensaje_log = "Personal registrado Masivo" . $row['cedula_empleado'];
                $objPersonal->cod_profit = trim($row['cod_profit']);
                $objPersonal->nacionalidad = $row['nacionalidad'];
                $objPersonal->cedula_empleado = ucfirst(strtolower(trim($row['cedula_empleado'])));
                $objPersonal->primer_nombre = ucfirst(strtolower(trim($row['primer_nombre'])));
                $objPersonal->segundo_nombre = ucfirst(strtolower(trim($row['segundo_nombre'])));
                $objPersonal->primer_apellido = ucfirst(strtolower(trim($row['primer_apellido'])));
                $objPersonal->especialidad = ucfirst(strtolower(trim($row['especialidad'])));
                $objPersonal->segundo_apellido = ucfirst(strtolower(trim($row['segundo_apellido'])));
                $objPersonal->rif = ucfirst(trim($row['rif']));
                $objPersonal->email = trim($row['email']);
                $nivelAcademico = "";
                switch ($row['nivel_academico']) {
                    case 'BACHILLER':
                        $nivelAcademico = 1;
                        break;
                    case 'T.S.U':
                        $nivelAcademico = 2;
                        break;
                    case 'LIC.':
                        $nivelAcademico = 3;
                        break;
                    case 'ING.':
                        $nivelAcademico = 4;
                        break;
                    case 'TECNICO MEDIO':
                        $nivelAcademico = 6;
                        break;
                    case 'MAGISTER':
                        $nivelAcademico = 7;
                        break;
                    case 'ESTUDIANTE':
                        $nivelAcademico = 8;
                        break;
                    case 'OTRO':
                        $nivelAcademico = 5;
                        break;
                }
                $objPersonal->nivel_academico = $nivelAcademico;
                $objPersonal->fecha_nacimiento = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['fecha_nacimiento']))->format('Y-m-d');

                $objPersonal->sexo = trim($row['sexo']);
                $objPersonal->telefono_casa = trim($row['telefono_casa']);
                $objPersonal->telefono_movil = trim($row['telefono_movil']);
                $objPersonal->hijos = trim($row['hijos']);
                $objPersonal->sector = trim($row['sector']);
                $objPersonal->direccion = trim($row['direccion']);

                if ($row['utiliza_transporte'] == "SI") {
                    $objPersonal->activetransporte = 1;

                    switch ($row['horario_transporte']) {
                        case 'AM':
                            $horariotrans = 1;
                            break;
                        case 'PM':
                            $horariotrans = 2;
                            break;
                        case 'AMBOS':
                            $horariotrans = 3;
                            break;
                    }
                    $objPersonal->htransporte = $horariotrans;
                    $objPersonal->zona_transporte_id = ZonaTransporte::where('zonatransporte', '=', trim($row['zona_transporte']))->where('estatus_id', '=', 1)->first()->id;
                } else {
                    $objPersonal->activetransporte = 0;
                    $objPersonal->htransporte = null;
                    $objPersonal->zona_transporte_id = ZonaTransporte::where('zonatransporte', '=', trim($row['zona_transporte']))->where('estatus_id', '=', 1)->first()->id;
                }
                $objPersonal->fecha_ingreso = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['fecha_ingreso']))->format('Y-m-d');
                $objPersonal->nomina_id = NominaModel::where('nombre_nomina', '=', trim($row['nomina']))->where('estatus_id', '=', 1)->first()->id;

                switch (strtolower($row["estatus_cargo"])):
                    case "periodo de prueba":
                        $estatuscargo = 1;
                        break;
                    case "encargado":
                        $estatuscargo = 2;
                        break;
                    case "titular":
                        $estatuscargo = 3;
                        break;
                endswitch;
                $objPersonal->estatuscargo = $estatuscargo;
                $objPersonal->cargo_id = CargoModel::where('nombre_cargo', '=', trim($row['cargo']))->where('estatus_id', '=', 1)->first()->id;

                $horarioOperativo = array_search($row['horario_operativo'], config('app.horariolaboral'));

                $objPersonal->horario_operativo = $horarioOperativo;

                switch ($row['jornada']) {
                    case '8 Horas':
                        $jornada = 1;
                        break;
                    case '6 Horas':
                        $jornada = 2;
                        break;
                }

                $objPersonal->jornada = $jornada;
                $objPersonal->turno = trim($row['turno']);
                $objPersonal->nacionalidad = $row['nacionalidad_cuenta'];
                $objPersonal->cedula_titular = $row['cedula_titular'];
                $objPersonal->nombre_titular = $row['nombre_titular'];
                $objPersonal->bancos_id = BancosModels::where('bancos', '=', trim($row['banco']))->first()->id;
                $objPersonal->cuenta_bancaria = $row['cuenta_bancaria'];

                $objPersonal->libranza_id = LibranzasModel::where('libranzas', '=', trim($row['libranza']))->first()->id;
                $objPersonal->horario_entrada = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['horario_entrada']))->format('H:i:s');
                $objPersonal->horario_salida = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['horario_salida']))->format('H:i:s');
                $objPersonal->gerente_area_id = PersonalModel::where('cedula_empleado', '=', trim($row['gerente_area']))->first()->id;
                $objPersonal->jefe_inmediato = PersonalModel::where('cedula_empleado', '=', trim($row['jefe_inmediato']))->first()->id;
                $objPersonal->jefe_inmediato_segundo = PersonalModel::where('cedula_empleado', '=', trim($row['jefe_inmediato_segundo']))->first()->id;

                $area = AreatrabajoModel::where('nombre_campana', '=', trim($row['area_trabajo']))->first()->id;
                $cartera = CarteraModel::where('nombre_cartera', '=', trim($row['cartera']))->where('area_trabajo_id', $area)->first()->id;
                $skill = SkillModels::where('nombre_skill', '=', trim($row['skill']))->where('cartera_id', $cartera)->first()->id;

                $objPersonal->area_trabajo_id = $area;
                $objPersonal->cartera_id    = $cartera;
                $objPersonal->skill_id      = $skill;
                $objPersonal->estatus_id    = 1;

                $objPersonal->contrato_1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['contrato_1']))->format('Y-m-d');
                $finalcontrato1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['contrato_1']))->format('Y-m-d');

                $daysadds = config('app.contrato1');

                $finalizacion = carbon::createfromformat('Y-m-d', $finalcontrato1)->addDays($daysadds - 1);
                $objPersonal->finalizacion_1 = $finalizacion->copy()->format('Y-m-d');

                switch ($row['situacion_contractual']) {
                    case 'Contratado':
                        $situacionContractual = 1;
                        break;
                    case 'Fijo':
                        $situacionContractual = 2;
                        break;
                    case 'Freelance':
                        $situacionContractual = 3;
                        break;
                    case 'Honorarios':
                        $situacionContractual = 4;
                        break;
                    case 'Finalizado':
                        $situacionContractual = 5;
                        break;
                    case 'Abandono':
                        $situacionContractual = 6;
                        break;
                    case 'Pendiente Por Contrato':
                        $situacionContractual = 7;
                        break;
                    case "Pendiente Por Documentacion":
                        $situacionContractual = 9;
                        break;
                }

                $objPersonal->estado_id = in_array($row['sede_laboral'], ['STV', 'ATENAS']) ?
                    EstadosModel::where('nombre_estado', 'like', '%' . $row['estado'] . '%')->first()->id :
                    3;
                $objPersonal->ciudad_id = in_array($row['sede_laboral'], ['STV', 'ATENAS']) ?
                    CiudadesModel::where('ciudad', 'like', '%' . $row['ciudad'] . '%')->first()->id :
                    149;

                $objPersonal->situacion_contractual = $situacionContractual;
                $objPersonal->status_carnet = 3;
                $objPersonal->sede_laboral = trim($row['sede_laboral']);
                $objPersonal->save();

                $objeto_personal = json_encode($objPersonal);

                $this->activity($mensaje_log, $objeto_personal);

                $areas = new CentroCostoAsignadoModel();
                $areas->personal_id = $objPersonal->id;
                $areas->cartera_id = $cartera;
                $areas->skill_id = $skill;
                $areas->porcentaje = null;
                $areas->save();
            }
            \Session::flash('success', 'El archivo fue cargado exitosamente por favor valide los Expedientes respectivos.');
            return true;
        } catch (\exception $e) {
            throw new ValidateAditionals([$e->getMessage()]);
        }
    }

    public function rules(): array
    {
        return [
            'nacionalidad' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La nacionalidad es obligatoria.');
                } else {
                    if (!in_array($value, ["V", "E", "P"])) {
                        $onFailure('la nacionalidad ingresada no existe');
                    }
                }
            },
            'cedula_empleado' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (BlacklistModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('Esta Persona Se encuentra en Lista Negra. No se puede registrar.');
                    }

                    if (!ctype_digit($value)) {
                        $onFailure('La Cedula de Identidad debe ser solo númerico');
                    }

                    if (PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula Ingresada Ya se encuentra Registrada.');
                    }
                }
            },
            'primer_nombre' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El primer nombre del empleado es obligatorio.');
                }
            },
            'primer_apellido' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El primer apellido del empleado es obligatorio.');
                }
            },
            'email' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Email del empleado es obligatorio.');
                }
            },
            'nivel_academico' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El nivel academico del empleado es obligatorio.');
                } else {
                    //config('app.especialidad'
                    //$nivelAcademico = array('BACHILLER', 'T.S.U', 'LIC.', 'ING.', 'TECNICO MEDIO', 'MAGISTER', 'OTRO');                  
                    if (!in_array(trim($value), config('app.especialidad'))) {
                        $onFailure('El nivel academico del empleado es invalido.');
                    }
                }
            },
            'fecha_nacimiento' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La fecha de nacimiento del empleado es obligatoria.');
                }
            },
            'sexo' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El sexo del empleado es obligatorio.');
                } else {
                    $sexo = array('M', 'F');
                    if (!in_array(trim($value), $sexo)) {
                        $onFailure('El sexo del empleado es invalido.');
                    }
                }
            },
            'telefono_casa' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El telefono de la casa del empleado es obligatorio.');
                }
            },
            'telefono_movil' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El telefono movil del empleado es obligatorio.');
                }
            },
            'sector' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El sector del empleado es obligatorio.');
                }
            },
            'direccion' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('la direccion del empleado es obligatoria.');
                }
            },
            'utiliza_transporte' => function ($attribute, $value, $onFailure) {
                if ($value == null || $value == '') {
                    $onFailure('Debe indicar si el personal usa o no El Transporte');
                }
            },
            'zona_transporte' => function ($attribute, $value, $onFailure) {
                if (!ZonaTransporte::where('zonatransporte', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                    $onFailure('La zona de transporte ingresada no existe.');
                }
            },
            'rif' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El registro Fiscal es obligatorio');
                }
            },
            'fecha_ingreso' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La fecha de ingreso del empleado es obligatoria.');
                }
            },
            'nomina' => function ($attribute, $value, $onFailure) {
                //dd(trim($value),!NominaModel::where('nombre_nomina', '=', trim($value))->where('estatus_id', '=', 1)->exists());
                if (!NominaModel::where('nombre_nomina', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                    $onFailure('La nomina ingresada no existe.');
                }
            },
            'cargo' => function ($attribute, $value, $onFailure) {
                if (!CargoModel::where('nombre_cargo', 'like', '%' . trim($value) . '%')->where('estatus_id', '=', 1)->exists()) {
                    $onFailure('El Cargo ingresado no existe.');
                }
            },
            'banco' => function ($attribute, $value, $onFailure) {
                //dd($value,strlen($value));
                if (trim($value) == "") {
                    $onFailure('El Banco no puede estar Vacio');
                } else if (!BancosModels::where('bancos', $value)->exists()) {
                    $onFailure('El nombre del banco no existe.');
                }
            },
            'cuenta_bancaria' => function ($attribute, $value, $onFailure) {
                //dd($value,strlen($value));
                if (strlen($value) < 20 && strlen($value) != 0) {
                    $onFailure('La cuenta Bancaria debe tener una longitud de 20 Digitos');
                }
            },
            'horario_operativo' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El horario operativo es obligatorio.');
                } else {
                    if (!in_array(trim($value), config('app.horariolaboral'))) {
                        $onFailure('El horario operativo del empleado es invalido.');
                    }
                }
            },
            'jornada' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La jornada es obligatoria.');
                } else {
                    $jornada = array('8 Horas', '6 Horas');
                    if (!in_array(trim($value), $jornada)) {
                        $onFailure('La jornada del empleado es invalida.');
                    }
                }
            },
            'turno' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El turno es obligatorio.');
                } else {
                    $turno = array('AM', 'PM');
                    if (!in_array(trim($value), $turno)) {
                        $onFailure('El turno del empleado es invalido.');
                    }
                }
            },
            'libranza' => function ($attribute, $value, $onFailure) {
                if (!LibranzasModel::where('libranzas', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                    $onFailure('La libranza ingresada no existe.');
                }
            },
            'horario_entrada' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El horario de entrada del empleado es obligatorio.');
                }
            },
            'horario_salida' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El horario de salida del empleado es obligatorio.');
                }
            },
            'sede_laboral' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La sede laboral es obligatoria.');
                } else {
                    if (!SedeModel::where('active', 1)->where('nombre_sede', trim($value))->pluck('nombre_sede')) {
                        $onFailure('La sede laboral es invalida.');
                    }
                }
            },
            'gerente_area' => function ($attribute, $value, $onFailure) {
                //dd($value,!in_array(PersonalModel::where('cedula_empleado', '=', trim($value))->first()->cargo_id, [1, 2, 3,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76,77,78,90,91]));
                if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                    $onFailure('La cedula del gerente del area ingresado no existe.');
                } else {
                    if (!in_array(PersonalModel::where('cedula_empleado', '=', trim($value))->first()->cargo_id, [1, 2, 3, 59, 60, 61, 62, 63, 64, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 90, 91])) {
                        $onFailure('El numero de cedula ingresado no corresponde al de un gerente de area.');
                    }
                }
            },
            'jefe_inmediato' => function ($attribute, $value, $onFailure) {
                if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                    $onFailure('La cedula del jefe inmediato ingresado no existe.');
                }
            },
            'jefe_inmediato_segundo' => function ($attribute, $value, $onFailure) {
                if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                    $onFailure('La cedula del jefe inmediato segundo ingresado no existe.');
                }
            },
            'area_trabajo' => function ($attribute, $value, $onFailure) {
                if (!AreatrabajoModel::where('nombre_campana', '=', trim($value))->exists()) {
                    $onFailure('La gerencia ingresada no existe.');
                }
            },
            'cartera' => function ($attribute, $value, $onFailure) {
                if ($value != null) {
                    if (!CarteraModel::where('nombre_cartera', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                        $onFailure('La Unidad o Campaña ingresada no existe.');
                    }
                }
            },
            'skill' => function ($attribute, $value, $onFailure) {
                if ($value != null) {
                    if (!SkillModels::where('nombre_skill', '=', trim($value))->where('active', '=', 1)->exists()) {
                        $onFailure('El Segmento o Skill no Existe');
                    }
                }
            },
            'contrato_1' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El horario de salida del empleado es obligatorio.');
                }
            },
            'estado' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Estado es Obligatorio.');
                } else {
                    if (!EstadosModel::where('nombre_estado', '=', strtoupper(ucfirst($value)))->where('active', '=', 1)->exists()) {
                        $onFailure('El estado no Existe');
                    }
                }
            },
            'ciudad' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La ciudad es Obligatoria.');
                } else {
                    if (!CiudadesModel::where('ciudad', '=', strtoupper(ucfirst($value)))->exists()) {
                        $onFailure('El ciudad Ingresada no Existe');
                    }
                }
            },
            'situacion_contractual' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La situacion contractual es obligatoria.');
                } else {

                    if (!in_array(ucwords(strtolower($value)), $this->contractual)) {
                        $onFailure('La situacion contractual ingresada es invalida.');
                    }
                }
            },
            'estatus_carnet' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El estatus del carnet es obligatorio.');
                } else {
                    $estatus = array('Impreso', 'Extraviado', 'No Impreso');
                    if (!in_array(trim($value), $estatus)) {
                        $onFailure('El estatus del carnet es invalido.');
                    }
                }
            }
        ];
    }
}
