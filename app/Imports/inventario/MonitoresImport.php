<?php

namespace App\Imports\inventario;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use App\Models\inventario\MonitorModel;

class MonitoresImport implements ToCollection, WithHeadingRow, WithValidation {

    use Importable;
    
    public function collection(Collection $row) {
        try {
            foreach ($row as $rows) {     
                //dd("holiz luis, estoy probando que llega");
                $monitor = new MonitorModel();
                $monitor->id        = $rows["identificador"];
                $monitor->marca     = $rows["marca"];
                $monitor->pulgadas  = $rows["pulgadas"];
                $monitor->serial    = $rows["serial"];
                $monitor->available = "X";
                $monitor->tipo_alimentador = array_search($rows["alimentador"], config('app.TipoCable'));
                $monitor->etiqueta  = array_search($rows["etiqueta"], config('app.EstatusEtiqueta'));
                $monitor->save();
            }
            \Session::flash('success', "Archivo de Monitores Cargado Existosamente."); 
            return true;
        } catch (\Throwable $e) {
            \Session::flash('error', $e->getMessage());   
            return back();
        }
    }

    public function rules(): array {
        return ["identificador" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Identificador es Obligatorio');
                } else {
                    if (MonitorModel::where('id', trim($value))->exists()) {
                        $onFailure('El Identificador ya Existe');
                    }
                }
            },
            "marca" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Marca es Obligatoria');
                }
            },
            "serial" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El valor del serial es Obligatorio');
                }
            },
            "pulgadas" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El valor Pulgadas es Obligatorio');
                }
            },
            "alimentador" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El valor del Alimentador es Obligatorio');
                } else if (!array_search($value, config('app.TipoCable'))) {
                    $onFailure('El valor del Alimentador no es correcto');
                }
            },
            "etiqueta" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El valor de la Etiqueta es Obligatorio');
                } else if (!array_search($value, config('app.EstatusEtiqueta'))) {
                    $onFailure('El valor de la Etiqueta no es correcto');
                }
            }
        ];
    }
}
