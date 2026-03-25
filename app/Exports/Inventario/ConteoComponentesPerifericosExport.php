<?php

namespace App\Exports\Inventario;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class ConteoComponentesPerifericosExport implements FromView,ShouldAutoSize,WithEvents {

    private $data;
    private $sede;

    public function __construct($data,$sede) {
        $this->data = $data;
        $this->sede = $sede;
    }

    public function view(): View {
        return view('inventario.reportes.xls.ComponentesPerifericos', [
            'data' => $this->data,
            'sede' =>$this->sede
        ]);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
              
            },
        ];
    }
}
