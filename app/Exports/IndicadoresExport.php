<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithBackgroundColor;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;


class IndicadoresExport implements FromView ,ShouldAutoSize, WithBackgroundColor{

    private $data;
    private $rango;
    private $namecampania;

    public function __construct($namecampania,$data,$rango) {
        $this->data = $data;
        $this->rango = $rango;
        $this->name = $namecampania;        
    
    }
    
     public function view(): View {        
        return view('asistencia.xls.indicadores')->with([
            'data' => $this->data,
            'rango' => $this->rango,
            'name' =>   $this->name,
            
        ]);
    }

    public function chunkSize(): int {
        return 3000;
    }
    
    public function backgroundColor()
    {
        // Return RGB color code.
        return 'ffffff';
    
        // Return a Color instance. The fill type will automatically be set to "solid"
        //return new Color(Color::COLOR_BLUE);
    
        // Or return the styles array
        //return [
       //      'fillType'   => Fill::FILL_GRADIENT_LINEAR,
       //      'startColor' => ['argb' => Color::COLOR_WHITE],
        //];
    }
}
