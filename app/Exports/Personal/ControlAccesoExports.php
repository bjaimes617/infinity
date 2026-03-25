<?php

namespace App\Exports\Personal;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ControlAccesoExports implements WithHeadings,WithCustomCsvSettings, FromCollection ,WithMapping {

    public $data;

    public function __construct($data) {
        $this->data = $data;
        
    }

    public function startCell(): string {
        return 'A2';
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
             "Nomina",
            "ISP",
            "Sede",
            "Nombre",
            "Apellido",
            "Cedula",           
            "Gerencia",
            "Unidad/Campa6a",
            "Skill",
            "Temperatura"
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
            "Fecha"         =>  $dat->fecha,
            "Escaneos"      =>  $dat->escaneo,
            "Entrada"=>  $dat->entrada,
            "Salida"=>   $dat->salida,
            "Nomina"=>   $dat->nombre_nomina,
            "ISP"   =>   $dat->isp_ip,
            "Sede"  =>   $dat->nombre_sede,
            "Nombre"=>   $dat->primer_nombre,
            "Apellido"=> $dat->primer_apellido,
            "Cedula"  =>$dat->cedula_empleado,
            "Gerencia"=>$dat->nombre_campana,
            "Unidad/Campa6a"=>$dat->nombre_cartera,
            "Skill"=>   $dat->nombre_skill,
            "Temperatura"=>   $dat->temperatura,
        ];
         
        return $columns;    
    }

}