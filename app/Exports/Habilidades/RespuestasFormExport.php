<?php

namespace App\Exports\Habilidades;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class RespuestasFormExport implements FromView, ShouldAutoSize, WithEvents, WithColumnWidths {

    private $preguntas_eval;
    private $resultado;

    public function __construct($preguntas_eval, $resultado) {
        $this->preguntas_eval = $preguntas_eval;
        $this->resultado = $resultado;
    }

    public function view(): View {
        return view('habilidades.reportes.respuestas_form', [
            'preguntas_eval' => $this->preguntas_eval,
            'resultado' => $this->resultado,
        ]);
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // All headers - set font size to 14           
                

                $event->sheet->getDelegate()->getStyle('A1:CA1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(90);

                $event->sheet->getDelegate()->getStyle('A1:CA1')->getAlignment()->setWrapText(true);

            },
        ];
    }

    public function columnWidths(): array
    {
        $result = [];
        $letters = range('A', 'Z');
    
        // Primera parte: de 'A' a 'Z'
        foreach ($letters as $letter) {
            $result[$letter] = 20;
        }
    
        // Segunda parte: de 'AA' a 'AZ'
        foreach ($letters as $firstLetter) {
            foreach ($letters as $secondLetter) {
                $result[$firstLetter . $secondLetter] = 20;
            }
        }
    
        // Tercera parte: de 'BA' a 'BZ'
        foreach ($letters as $firstLetter) {
            if ($firstLetter === 'B') {
                foreach ($letters as $secondLetter) {
                    $result[$firstLetter . $secondLetter] = 20;
                }
            }
        }
    
        // Cuarta parte: de 'CA' (ya que solo pediste hasta 'CA')
        $result['CA'] = 20;

        return $result;
    }

}


