<?php

namespace App\Exports\cursos;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteExport implements FromView, ShouldAutoSize  {
    private $data;

    public function __construct($data) {
        $this->data = $data;        
    }
    
        public function view(): View {
            return view('cursos.reportes.xls.reporte', [
                'data' => $this->data,
            ]);
        }
    

    public function chunkSize(): int {
        return 3000;
    }
}
