<?php

namespace App\Exports\Evaluaciones;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;


class EvaluacionExport implements FromView, ShouldAutoSize {

    private $ab;
    private $indicadores;
    private $indicadores_count;

    public function __construct($ab,$indicadores_count,$indicadores) {
      
        $this->datos = $ab;
        $this->indicadores_count = $indicadores_count;
        $this->indicadores = $indicadores;
    }

    public function view(): View {

        return view('evaluacion.xls.indicadores', [
            'evaluado' => $this->datos,           
            'indicadores'=> $this->indicadores,
            'indicadores_count' => $this->indicadores_count,
         
         ]);

    } 

}


