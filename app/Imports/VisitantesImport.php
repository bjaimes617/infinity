<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\personal\PersonalModel;
use App\Models\personal\VisitantesModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\cursos\ParticipantesModel;

class VisitantesImport implements ToCollection, WithHeadingRow, WithValidation {

    use Importable;
        
    private $descripcion;

    public function __construct($descripcion) {
        
      
        $this->descripcion = $descripcion;       
    }
    public function collection(Collection $rows) {

        try{        
        foreach ($rows as $row) {              
            //dd($row);
            $objPersonal = new VisitantesModel();  
            $objPersonal->cedula_empleado = $row['cedula'];
            $objPersonal->primer_nombre = $row['primer_nombre'];
            $objPersonal->primer_apellido = $row['primer_apellido'];
            $objPersonal->gerencia = $this->descripcion;            
            $objPersonal->save();
        }
        \Session::flash('success', 'El registro de visitantes, fue procesado correctamente.');
        
        } catch (\Exception $e){
            \Session::flash('error', 'No Se puede Procesar el archivo, revise nuevamente el formato de carga.'.$e->getMessage());
        }
    }

    public function rules(): array {
        return [
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (ParticipantesModel::join('rec_cursos','rec_cparticipantes.rec_cursos_id','rec_cursos.id')->where('cedula_empleado', $value)->where('active',1)->exists()) {
                        $onFailure('Esta Cedula ya esta registrada en los listados de Cursos activos');
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
            }          
            
        ];
    }

}
