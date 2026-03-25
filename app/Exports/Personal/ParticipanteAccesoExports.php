<?php

namespace App\Exports\Personal;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class ParticipanteAccesoExports implements WithHeadings, FromCollection ,WithMapping {

    public $data;

    public function __construct($data) {
        
        $this->data = $data;
    }
   
    
     public function getCsvSettings(): array {
        return [
            'input_encoding' => 'utf8mb4_general_ci',
            'delimiter' => ';',
            "Content-type"=> "text/csv; charset=utf8mb4",
            'use_bom' => true,
        ];
    }

    public function headings(): array {
        $columns = [
            "Fecha",
            "Escaneos",
            "Entrada",
            "Salida",
            "Cedula",
            "Nombres",
            "Curso",    
            "Gerencia",
            "Unidad/Campa6a",
            "Skill",
            "Sede"
        ];
        return $columns;
    }

  public function collection() {
        $this->data->each(function ($data) {
            $this->map($data);
        });
        return $this->data;
    }
    
       public function map($dat): array {
      
         $columns = [
            "Fecha"     => $dat->fecha,
            "Escaneos"  => $dat->escaneo,
            "Entrada"   => $dat->entrada,
            "Salida"    => $dat->salida, 
            "Cedula"    => $dat->cedula,
            "Nombres"   => $dat->nombres,            
            "curso"     => "DGC".$dat->id,
            "Gerencia"  => $dat->nombre_campana,
            "Unidad/Campa6a"=>$dat->nombre_cartera,
            "Skill" => $dat->nombre_skill,
            "Sede"  => $dat->sede,
        ];
         
        return $columns;    
    }
}

