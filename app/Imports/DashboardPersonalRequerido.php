<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\personal\CarteraModel;
use App\Models\personal\ChartsPersonalModel;
use Carbon\Carbon;

class DashboardPersonalRequerido implements ToCollection, WithHeadingRow, WithValidation
{
    private $mes;

    public function __construct($mes)
    {
        $this->mes = $mes;
    }

    public function collection(Collection $collection)
    {

        $fecharecibida = Carbon::createFromFormat('m/Y', $this->mes);
        $mes = $fecharecibida->format('m');
        $anio = $fecharecibida->format('Y');

        $carteraAll = CarteraModel::where('area_trabajo_id', 2)
            ->selectRaw('LOWER(nombre_cartera) as nombre_cartera, id')
            ->pluck('id', 'nombre_cartera')
            ->toArray();

        foreach ($collection as $row) {
            $nombreCarteraLower = strtolower($row['cartera']);

            if (isset($carteraAll[$nombreCarteraLower])) {
                $carteraId = $carteraAll[$nombreCarteraLower];

                ChartsPersonalModel::where('cartera_id', $carteraId)
                    ->where('mes', $mes)
                    ->where('anio', $anio)
                    ->update([
                        'requerido'    => $row['requeridos'],

                    ]);
            }
        }
        return true;
    }

    public function rules(): array
    {
        return [
            "cartera" => function ($attribute, $value, $fail) {
                if (empty($value)) {
                    $fail("La cartera es obligatoria");
                }
                if (!CarteraModel::selectRaw('LOWER("nombre_cartera")')->where("nombre_cartera", strtolower($value))->exists()) {
                    $fail("La cartera " . $value . " no existe");
                }
            },
            "requeridos" => function ($attribute, $value, $fail) {
                if (empty($value) && $value === "") {
                    $fail("Valor en requeridos es obligatorio ");
                }
                if (!is_numeric($value)) {
                    $fail("El valor de la columna requeridos debe ser numérico ");
                }
            },

        ];
    }
}
