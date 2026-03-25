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

class InformeExport implements FromView, ShouldAutoSize, WithEvents, WithColumnWidths, WithTitle
{
    private $test_name;
    private $rango_evaluacion;
    private $nombre_completo;
    private $sede;
    private $position_name;
    private $cates;
    private $nota_final;
    private $headers_param;
    private $data_com;
    private $resultadoPromedio;

    public function __construct(string $test_name,$rango_evaluacion,string $nombre_completo,$sede,$position_name,$cates,$nota_final,$headers_param,$data_com,$resultadoPromedio) {
     
        $this->test_name = $test_name;
        $this->rango_evaluacion = $rango_evaluacion;
        $this->nombre_completo = $nombre_completo;
        $this->sede = $sede;
        $this->position_name = $position_name;
        $this->cates = $cates;
        $this->nota_final = $nota_final;
        $this->headers_param = $headers_param;
        $this->data_com = $data_com;
        $this->resultadoPromedio = $resultadoPromedio;

    }

    public function view(): View {
        return view('habilidades.reportes.informe', [
            'test_name' => $this->test_name,
            'rango_evaluacion' => $this->rango_evaluacion,
            'nombre_completo' => $this->nombre_completo,
            'sede' => $this->sede,
            'position_name' => $this->position_name,
            'cates' => $this->cates,
            'nota_final' => $this->nota_final,
            'headers_param' => $this->headers_param,
            'data_com' => $this->data_com,
            'resultadoPromedio' => $this->resultadoPromedio,

        ]);
    }

    public function title(): string
    {
        // Dividir el nombre completo en partes
        $partes = explode(" ", $this->nombre_completo);

        // Obtener la inicial del nombre
        $inicialNombre = strtoupper(substr($partes[0], 0, 1)) . ".";

        // Obtener el apellido (asumiendo que es la última parte)
        $apellido = $partes[count($partes) - 1];

                    // Dividir la cadena en palabras
            $palabras = explode(" ", $this->test_name);

            // Obtener la inicial de la primera palabra
            $inicial1 = strtoupper(substr($palabras[0], 0, 1));

            // Verificar si hay una segunda palabra
            if (isset($palabras[1])) {
                // Obtener la inicial de la segunda palabra
                $inicial2 = strtoupper(substr($palabras[1], 0, 1));
                $nombre_prueba = $inicial1 .".". $inicial2;
            } else {
                $nombre_prueba = $inicial1;
            }

        return $nombre_prueba.' '.$inicialNombre . " " . $apellido; // Nombre de la tercera hoja
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // All headers - set font size to 14           
                

                $event->sheet->getDelegate()->getStyle('A1:CA1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $event->sheet->getDelegate()->getStyle('A1:CA1')->getAlignment()->setWrapText(true);

                $event->sheet->getColumnDimension('A')->setWidth(2); // Ancho de 2 puntos para la columna A

                // Aquí se ajusta el texto para la columna 'C'
                $event->sheet->getStyle('C')->getAlignment()->setWrapText(true);


            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 24, // Ancho de la columna B a 24 puntos
            'C' => 28,
            'D' => 18,
            'E' => 33,
            // ... (ancho de otras columnas si es necesario)
        ];
    }
    
}
