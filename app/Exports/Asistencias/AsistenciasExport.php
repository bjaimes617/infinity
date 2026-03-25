<?php

namespace App\Exports\Asistencias;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\FromView;

class AsistenciasExport implements FromView, WithCustomCsvSettings {

    private $asistencia;
    private $rango;
    private $personal; 

    public function __construct($asistencia,$rango,$personal) {
        $this->asistencia = $asistencia;
        $this->rango = $rango;        
        $this->personal = $personal;
    }
    
     public function view(): View {        
        return view('asistencia.xls.index')->with([
            'asistencia' => $this->asistencia,
            'rango' => $this->rango,
            'personal'=>$this->personal
        ]);
    }

    public function chunkSize(): int {
        return 3000;
    }

    public function getCsvSettings(): array {
        return [
            "Content-Encoding"        => "utf-8",
            "Content-type"        => "text/csv; charset=utf",
            'delimiter' => ';',
            "Content-type"=> "text/csv; charset=utf8mb4",
            'use_bom' => true,
        ];
    }

   
}
