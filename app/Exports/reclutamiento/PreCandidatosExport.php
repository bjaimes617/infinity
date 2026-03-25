<?php

namespace App\Exports\reclutamiento;

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
use Carbon\Carbon;

class PreCandidatosExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithCustomCsvSettings {

    private $result;

    public function __construct($result) {
        $this->result = $result;
    }

    public function chunkSize(): int {
        return 50;
    }

    public function getCsvSettings(): array {
        return [
            'input_encoding' => 'utf8mb4_general_ci',
            'delimiter' => ';',
            "Content-type" => "text/csv; charset=utf8mb4",
            'use_bom' => true,
        ];
    }

    public function startRow(): int {
        return 2;
    }

    public function headings(): array {
        $columns = [
            "Registrados desde",
            "Nacionalidad",
            "Cedula",
            "Nombres",
            "Apellidos",
            "fecha de Nacimiento",
            "Edad",
            "Sexo",
            "Telefono Celular",
            "Telefono Habitacion",
            "email",
            "CArgo Postulado",
            "Experiencia En Ventas",
            "Detalle de la Experiencia",
            "Trabajo En servicios Anteriores",
            "servicios Anteriores",
            "Direccion de Habitacion"
        ];
        return $columns;
    }

    public function collection() {
        $this->result->each(function ($result) {
            $this->map($result);
        });
        return $this->result;
    }

    public function prepareRows($result): array {
        return array_map(function ($change) {
            
           $opciones = json_decode($change->options);          
          
           $change->created = carbon::create($change->created_at)->format('d/m/Y');           
           $change->nacimiento  = $opciones->nacimiento;
           $change->edad        = isset($opciones->edad) ? $opciones->edad : null;
           $change->sexo        = $opciones->sexo;
           $change->habitacion  = $opciones->habitacion;
           $change->direccion   = $opciones->direccion;
           $change->celular      = $opciones->celular;
           $change->email        = $opciones->email;           
           $change->cargopostulacion        = isset($opciones->cargopostulacion) ? $opciones->cargopostulacion : null;
           $change->experienciaventas       = $opciones->experienciaventas == "S" ? "SI" : "NO";
           $change->especifique             = isset($opciones->especifique) ? $opciones->especifique : null;
           $change->serviciosanteriores     = isset($opciones->serviciosanteriores) ? $opciones->serviciosanteriores :null;
                      
           return $change;
        }, $result);
    }

    public function map($result): array {
        $columns = [
            "Registrados desde"     => $result->created,
            "Nacionalidad"          => $result->nacionalidad,
            "Cedula"                => $result->cedula,
            "Nombres"               => $result->nombres,
            "Apellidos"             => $result->apellidos,
            "fecha de Nacimiento"   => $result->nacimiento,
            "Edad"                  => $result->edad,
            "Sexo"                  => $result->sexo,
            "Telefono Celular"      => $result->celular,
            "Telefono Habitacion"   => $result->habitacion,
            "email"                 => $result->email,
            "Cargo Postulado"       => $result->cargopostulacion,
            "Experiencia En Ventas" => $result->experienciaventas,
            "Detalle de la Experiencia"=> $result->especifique,
            "Trabajo En servicios Anteriores"=> $result->serviciosanteriores,
            "servicios Anteriores",
            "Direccion de Habitacion"=> $result->direccion,
        ];
        return $columns;
    }
}