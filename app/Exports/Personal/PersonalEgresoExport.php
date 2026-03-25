<?php

namespace App\Exports\Personal;

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

class PersonalEgresoExport implements ShouldAutoSize, WithColumnFormatting, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithCustomCsvSettings {

    private $personal;

    public function __construct($personal) {
        $this->personal = $personal;
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
            "Cedula",
            "Primer Nombre",
            "Segundo Nombre",
            "Primer Apellido",
            "Segundo Apellido",
            "Email",
            "Nivel Academico",           
            "1er. Lider Inmediato",           
            "Gerencia",
            "Unidad/Campaña", 
            "Skill",
            "Fecha de Egreso",
            "Motivo del Egreso",            
        ];
        return $columns;
    }

    public function collection() {
        $this->personal->each(function ($personal) {
            $this->map($personal);
        });
        return $this->personal;
    }
    
        
    public function columnFormats(): array {
        return [           
        ];
    }

    public function prepareRows($personal): array {
        return array_map(function ($change) {

            $change->load('RelationAreas', 'RelationCartera','personal','RelationprimerLider');
           
            $change->cedula_empleado  = $change->personal->cedula_empleado;
            $change->primer_nombre    = $change->personal->primer_nombre;
            $change->segundo_nombre   = $change->personal->segundo_nombre;
            $change->primer_apellido  = $change->personal->primer_apellido;
            $change->segundo_apellido = $change->personal->segundo_apellido;
            $change->email            = $change->personal-> email;
            $change->nivel_academico  = $change->personal->nivel_academico;
        
            if ($change->RelationprimerLider == null) {
                $change->primerlider = "N/A";
            } else {
                $change->primerlider = $change->RelationprimerLider->primer_nombre . ' ' . $change->RelationprimerLider->primer_apellido;
            }
            $change->gerencia   = $change->RelationAreas->nombre_campana;
            $change->skill      = $change->Relationskill->nombre_skill;
            if ($change->RelationCartera == null) {
                $change->cartera = "N/A";
            } else {
                $change->cartera = $change->RelationCartera->nombre_cartera;
            }             

            $change->fechaEgreso = date('d/m/Y', strtotime($change->fecha)); 
            $change->motivo_egreso = $change->motivo;
            
            return $change;
        }, $personal);
    }

    public function map($personal): array {
        $columns = [
            "Cedula" => $personal->cedula_empleado,
            "Primer Nombre" => $personal->primer_nombre,
            "Segundo Nombre" => $personal->segundo_nombre,
            "Primer Apellido" => $personal->primer_apellido,
            "Segundo Apellido" => $personal->segundo_apellido,
            "Email" => $personal->email,
            "Nivel Academico" => $personal->nivel_academico,   
            "1er. Lider Inmediato" => $personal->primerlider,           
            "Gerencia" => $personal->gerencia,
            "Unidad/Campaña" => $personal->cartera,
            "Segmento/Skill" => $personal->skill,
            "Fecha de Egreso" => $personal->fechaEgreso,
            "Motivo del Egreso" => $personal->motivo_egreso,           
        ];

        return $columns;
    }

}
