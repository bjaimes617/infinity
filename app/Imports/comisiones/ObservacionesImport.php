<?php

namespace App\Imports\comisiones;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Comisiones\BancoModel;
use App\Models\personal\BlacklistModel;
use App\Models\personal\PersonalModel;
use App\Models\Comisiones\LoteBancoModel;
use App\Models\Comisiones\LoteModel;
use App\Models\Comisiones\OperacionesComisiones;
use App\Exceptions\ValidateAditionals;
use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use App\Exceptions\ImportValidationException;
use Exception;

class ObservacionesImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function __construct($request) {
        $this->request      = $request;
     
    }

    public function collection(Collection $rows)
    {
       
        ///ubicamos el lote general por el id del consolidado de bancos
        $lotes_id = LoteModel::whereIn('lotebanco_id',$this->request["comisiones"])->get();
        
            $loteoperacion = array();
            $validLoteBanco = array();
            $msg_cedula     = array();
            $mes            = array();
            $anio           = array();
        
        foreach($rows as $cedulas){
            $cedula[] =  $cedulas['cedula'];
        }    
        
        foreach($lotes_id as $lots){
                ///se validan todas las cedulas del archivo si existen en el archivo de consolidado, en caso de no existir llenamos 
                ///la variable de mensajes y hechamos todos para atras
                foreach($rows as $row){
                if($lots->relationOperaciones()->where('cedula', $row['cedula'])->exists()){                  
                    array_push($validLoteBanco,$lots->lotebanco_id);
                    array_push($loteoperacion,$lots->id);
                } else {
                    $msg_cedula[] = "La cedula " . $cedula . "no esta incluida en el lote banco seleccionado";
                }
            }
            
            $mes[]  = $lots->mes;
            $anio[] = $lots->anio;
        }
        
        if (empty($validLoteBanco)) {
            throw new ValidateAditionals($msg_cedula);
        }
            
        $lotebancario   = array_unique($validLoteBanco);
        $anioUnique     = array_unique($anio);           
        $mesUnique      = array_unique($mes);
        $operaciones    =array_unique($loteoperacion,);
      
         foreach($rows as $row){
          
        ///registro consolidado del personal
            BancoModel::whereIn('lotebanco_id',$lotebancario)
            ->whereIn('anio',$anioUnique)->whereIn('mes',$mesUnique)
            ->where('cedula', trim($row['cedula']))->update(['estatus' => 'INCIDENCIA', 'observaciones' => $row['observaciones']]);
            
        ///registros del personal a pagar
            OperacionesComisiones::whereIn('lote_id', $operaciones)
            ->where('cedula',$row['cedula'])
            ->update( ['incidencia' => 1,'descripcion_incidencia' => $row['observaciones'] ]);
        }
        
        //cambiamos el estatus del lote bancario (consolidado) a incidencias
        LoteBancoModel::whereIn('id', $lotebancario)->update(['estatus' => 'PAGADO CON INCIDENCIAS']);
        ///cambiamos el estatus del com_lotes a incidencia tabla principal de lotes cargados
        LoteModel::whereIn('id',$operaciones)->update(['estatus' => 'PAGADO CON INCIDENCIAS']);
        \Session::flash('success', 'Observaciones cargadas exitosamente');
        return true;
                
        
    }

    public function rules(): array {
        return [
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (BlacklistModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('Esta Persona Se encuentra en Lista Negra.');
                    } else if (!ctype_digit($value)) {
                        $onFailure('La Cedula de Identidad debe ser solo númerico');
                    }
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula ' .$value. ' no existe en el listado de personal.');
                    }

                }
            },
            'observaciones' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('Las Observacion es obligatoria.');
                } 
            }
        ];
    }
}
