<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GerenciasExport implements FromView, ShouldAutoSize, WithEvents {

    private $gerencias;

    public function __construct($gerencias) {
        $this->gerencias = $gerencias;
    }

    public function view(): View {
        return view('personal.reportes.gerencias', [
            'gerencias' => $this->gerencias,
        ]);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // All headers - set font size to 14           
                

                if(count($this->gerencias)){
                    $cantidad = count($this->gerencias) + 7;
                }else
                    $cantidad = 7;


            },
        ];
    }

}
