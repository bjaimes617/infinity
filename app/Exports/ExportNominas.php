<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ExportNominas implements ShouldAutoSize, FromCollection, WithHeadings, WithChunkReading, WithMapping, WithCustomCsvSettings
{
    private $items;
    private $data;

    public function __construct($items, $data)
    {
        $this->items    = $items;
        $this->data     = collect($data);
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'utf8mb4_general_ci',
            'delimiter' => ';',
            "Content-type" => "text/csv; charset=utf8mb4",
            'use_bom' => true,
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headings(): array
    {
        #genera elñ heading del archivo conjunto a los hedings de lso items de todos los recibos
        $trabajadores = [
            "CI",
            "Trabajador",
            "Nomina",
            "Cargo",
            "Gerencia",
            "Unidad/Campaña",
            "Periodo",
            "Corte",
            "CECO"
        ];
        foreach ($this->items as $item) {
            array_push($trabajadores, $item);
        }
        # añade las ultimas columnas del archivo
        $faltante = [
            "Asignacion",
            "Deduccion",
            "Total"
        ];
        foreach ($faltante as $faltante) {
            array_push($trabajadores, $faltante);
        }

        return $trabajadores;
    }

    public function collection()
    {
        $this->data->each(function ($data) {
            $this->map($data);
        });
        return $this->data;
    }

    public function prepareRows($data): array
    {
        return array_map(function ($data) {

            return $data;
        }, $data);
    }

    public function map($data): array
    {

        $trabajadores = [
            "CI" => $data["cedula"],
            "Trabajador" => $data["nombre"],
            "Nomina" => $data["nomina"],
            "Cargo" => $data["cargo"],
            "Gerencia" => $data["gerencia"],
            "Unidad/Campaña" => $data["campana"],
            "Periodo" => $data["periodo"],
            "Corte" => $data["corte"],
            "CECO" => $data["cecos"]
        ];

        foreach ($this->items as $key => $item) {
            if ($data[$key] !== 0.0 && !empty($data[$key])) {
                $trabajadores[$item] = number_format($data[$key], 2, ',', '');
            } else {
                $trabajadores[$item] =  number_format(0.0, 2, ',', '');
            }
            $var = $key;
        }

        $faltante = [
            "Asignacion" =>  number_format($data["asignaciones"], 2, ',', ''),
            "Deduccion" => number_format($data["deducciones"], 2, ',', ''),
            "Total" => number_format($data["total"], 2, ',', '')
        ];
        foreach ($faltante as $faltante) {
            array_push($trabajadores, $faltante);
        }

        return $trabajadores;
    }
}
