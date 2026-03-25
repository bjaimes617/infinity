<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConstanciasExport implements FromView, ShouldAutoSize, WithEvents {

    private $constancias;

    public function __construct($constancias) {
        $this->constancias = $constancias;
    }

    public function view(): View {
        return view('constancias.generate', [
            'constancias' => $this->constancias,
        ]);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // All headers - set font size to 14           
                

                if(count($this->constancias)){
                    $cantidad = count($this->constancias) + 7;
                }else
                    $cantidad = 7;


            },
        ];
    }

}

