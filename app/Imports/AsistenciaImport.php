<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\personal\PersonalModel;
use App\Models\asistencia\AsistenciaOperativaModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use DatePeriod;
use DateTime;
use DateInterval;
use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AsistenciaImport implements ToCollection, WithHeadingRow, WithValidation {

    use ActivityLogger;
    use Importable;

    public function collection(Collection $rows) {
       /// validacion de los valores de fecha desde y hasta, que hasta no sea menor que desde
        $error = array();        
        foreach($rows as $key => $row){
            $stardate   = Date::excelToDateTimeObject(trim($row['desde']))->format('Y-m-d');
            $enddate    = Date::excelToDateTimeObject(trim($row['hasta']))->format('Y-m-d');
            
            $fila = $key + 2;
            if($enddate < $stardate){
                $error[] = " fila ".$fila." fecha HASTA no puede ser menor que fecha DESDE";
            }            
        }      
        if($error != []){
            return back()->withErrors($error);
        }
        
        foreach ($rows as $row) {
            $mensaje_log = "Carga Masiva AsistenciaOp.";
            $personal = PersonalModel::where('cedula_empleado', $row["cedula"])->first();

            $stardate   = Date::excelToDateTimeObject(trim($row['desde']))->format('y-m-d');
            $enddate    = Date::excelToDateTimeObject(trim($row['hasta']))->format('y-m-d');
           
            
            ///solo se aplica cambio de estatus si se reciben sso y VACs
            switch ($row["tipo"]){
                case "SSO":
                    $personal->estatus_id = 7; ///estatus reposo
                    $tipo = "Reposos";
                    break;
                case "VAC":
                    $personal->estatus_id = 8; ///estatus Vacaciones
                    $personal->vacaciones_init = $stardate;
                    $personal->vacaciones_end  = $enddate;
                    $tipo = "Vacaciones";
                    break; 
                default:
                    $tipo = "Cargado RRHH";
                    break;
            }     
            $personal->save();

            $end = new DateTime($enddate);
            $end->modify('+ 1 day');

            $period = new DatePeriod(
                    new DateTime($stardate),
                    new DateInterval('P1D'), $end);

            foreach ($period as $key => $value) {

                $date = $value->format('Y-m-d');
                $dia = date('l', strtotime($date));

                if (AsistenciaOperativaModel::where('personal_id', $personal->id)->where('created_at', 'like', date('Y-m-d', strtotime($date)) . '%')->exists()) {

                    $asistencia = AsistenciaOperativaModel::where('personal_id', $personal->id)
                        ->where('created_at', 'like', date('Y-m-d', strtotime($date)) . '%')->first();
                    $asistencia->tipo_asistencia = $tipo;
                    $asistencia->prefijo        = $row["tipo"];
                    $asistencia->dia            = $dia;
                    $asistencia->observacion    = 'Corregido - RRHH ' . $row["observacion"];
                    $asistencia->save();
                } else {

                    $asistencia = new AsistenciaOperativaModel();
                    $asistencia->tipo_asistencia    = $tipo;                    
                    $asistencia->gerente_id         = $personal->gerente_area_id;
                    $asistencia->supervisor_id      = $personal->jefe_inmediato;
                    $asistencia->coordinador_id     = $personal->jefe_inmediato_segundo;
                    $asistencia->personal_id        = $personal->id;
                    $asistencia->area_trabajo_id    = $personal->area_trabajo_id;
                    $asistencia->cartera_id         = $personal->cartera_id;
                    $asistencia->skill_id           = $personal->skill_id;
                    $asistencia->prefijo            = $row["tipo"];
                    $asistencia->block              = 1;
                    $asistencia->dia                = $dia;
                    $asistencia->observacion        = $row["observacion"];
                    $asistencia->created_at         = date('Y-m-d H:i:s', strtotime($date));
                    $asistencia->updated_at         = date('Y-m-d H:i:s', strtotime($date));
                    $asistencia->save();
                }
            }
            $objeto_asistencia = json_encode($asistencia);
                       
            ActivityLogger::activity($mensaje_log . " " . $row["cedula"], $objeto_asistencia);
        }
        
        \Session::flash('success', 'Registros en la asistencia Procesado con exito. Recuerde cambiar el estatus del personal al finalizar el periodo');
        return back();
    }

    public function rules(): array {
        return [
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (!PersonalModel::where('cedula_empleado', trim($value))->exists()) {
                        $onFailure('La Cedula no Se encuentra Registrada');
                    }

                    if (PersonalModel::where('cedula_empleado', trim($value))->whereIn('estatus_id', [2, 3, 6, 11])->exists()) {
                        $onFailure('Esta persona se encuentra inactiva en el sistema, por favor verifique el listado de personal');
                    }
                }
            },
            'desde' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La fecha de Inicio es Obligatoria');
                }
            },
            'hasta' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La fecha de Final es Obligatoria');
                }
            },
            'tipo' => function ($attribute, $value, $onFailure) {                           
                if (trim($value) == "") {
                    $onFailure('el tipo de prefijo es obligatorio.');
                } else if (strlen($value) > 3 || (strlen($value) < 2)) {
                    $onFailure('Debe utilizar los prefijos VAC, SSO, PNR o PR en el campo tipo');
                }
            },
            'Observaciones' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('Las Observaciones son Obligatorias');
                }
            },
        ];
    }
}
