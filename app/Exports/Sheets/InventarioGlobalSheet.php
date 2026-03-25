<?php
namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;


class InventarioGlobalSheet implements FromCollection, WithTitle, ShouldAutoSize, WithHeadings,WithEvents
{
    private $data;
    private $title;
    private $cabeceras;

    public function __construct($data, $title, $cabeceras)
    {
        $this->data = $data;
        $this->title = $title;
        $this->headings = $cabeceras;
    }

    public function collection(): Collection
    {
        return collect($this->data);
    }

    public function title(): string
    {
        return $this->title;
    }
    
    public function headings(): array
    {
        return $this->headings;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // Reemplaza 'W1' con la última columna de tus hojas
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '1B3155', // Reemplaza '1B3155' con el color que desees
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}