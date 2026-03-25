<?php

namespace App\Exports\Habilidades\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;


class RfExport implements FromView, ShouldAutoSize, WithEvents, WithColumnWidths, WithTitle
{
    private $preguntas_eval_rf;
    private $datas;
    private $result;
    private $cantidad;
    private $resultadoPromedio;
    private $ex;

    public function __construct($preguntas_eval_rf, $datas,$result,$cantidad,$resultadoPromedio,$ex) {
        $this->preguntas_eval_rf = $preguntas_eval_rf;
        $this->datas = $datas;
        $this->result = $result;
        $this->cantidad = $cantidad;
        $this->resultadoPromedio = $resultadoPromedio;
        $this->ex = $ex;
    }

    public function view(): View {
        return view('habilidades.reportes.rf', [
            'preguntas_eval_rf' => $this->preguntas_eval_rf,
            'datas' => $this->datas,
            'result' => $this->result,
            'cantidad' => $this->cantidad,
            'resultadoPromedio' => $this->resultadoPromedio,
            'ex' => $this->ex,
        ]);
    }

    public function title(): string
    {
        return 'RF'; // Nombre de la segunda hoja
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
