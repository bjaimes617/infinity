<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkOffset;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping; 
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CecosExports implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithCustomCsvSettings {

    private $ceco;

    public function __construct($ceco) {
        $this->ceco = $ceco;        
    }

    public function chunkSize(): int {
        return 3000;
    }

    public function getCsvSettings(): array {
        return [
            'input_encoding' => 'utf8mb4_general_ci',
            'delimiter' => ';',
            "Content-type"=> "text/csv; charset=utf8mb4",
            'use_bom' => true,
        ];
    }

    public function startRow(): int {
        return 2;
    }

    public function headings(): array {
        $columns = [           
            "Cod. CECO",
            "Nombre CECOS",
            "Estatus"     
        ];
        return $columns;
    }
    
     public function collection() {
        $this->ceco->each(function ($ceco) {
            $this->map($ceco);
        });
        return $this->ceco;
    }
    
    
    public function prepareRows($ceco): array {
        return array_map(function ($change) {            
            $change->codcecos = $change->cod_ceco;
            $change->cecos = $change->nombre_ceco;
            
            return $change;
        }, $ceco);
    }

    public function map($ceco): array {
        $columns = [
        "Cod. CECO"=>$ceco->codcecos,
        "Nombre CECOS"=>$ceco->cecos,     
        "Estatus" => $ceco->active  
        ];

        return $columns;
    }
}