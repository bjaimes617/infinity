<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\FeriadosAnualesModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CalendarioFeriadosImport implements ToCollection, WithHeadingRow, WithValidation {

    use Importable;

    public function collection(Collection $rows) {
 
        foreach ($rows as $row) {          
            
           $fecha = new FeriadosAnualesModel();
           $fecha->feriado = Date::excelToDateTimeObject(trim($row["feriado"]))->format('Y-m-d');
           $fecha->save();           
        }
        \Session::flash('success', 'Feriados cargados correctamente, estos impactan en la Asistencia Operativa.');
        return back();        
    }

    public function rules(): array {
        return [
            'feriado' => function ($attribute, $value, $onFailure) {
                if (FeriadosAnualesModel::where('feriado', Date::excelToDateTimeObject(trim($value))->format('Y-m-d'))->exists()) {
                    $onFailure('La Fecha que intentas Cargar ya Existe');
                }
            },
        ];
    }

}