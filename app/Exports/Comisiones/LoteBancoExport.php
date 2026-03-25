<?php

namespace App\Exports\Comisiones;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkOffset;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class LoteBancoExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

    private $bancos;

    public function __construct($bancos) {
        $this->bancos = $bancos;
    }

    public function chunkSize(): int {
        return 500;
    }

    public function startRow(): int {
        return 2;
    }

    public function headings(): array {
        $columns = [
            "Nombre Lote",
            "Periodo",
            "Bancos Emisores",
            "Fecha Pago",
            "Estatus"
        ];
        return $columns;
    }

    public function collection() {
        $this->bancos->each(function ($bancos) {
            $this->map($bancos);
        });
        return $this->bancos;
    }

    public function prepareRows($bancos): array {
        return array_map(function ($change) {

            $lista_bancos = json_decode($change->banco);
            
            $change->lista_banco = implode('|', $lista_bancos);
            
            return $change;
        }, $bancos);
    }

    public function map($bancos): array {
        $columns = [
            "Nombre Lote" => $bancos->lote_banco,
            "Periodo" => trans('meses.' . $bancos->mes) . " " . $bancos->anio,
            "Bancos Emisores" => $bancos->lista_banco,
            "Fecha Pago" => $bancos->fecha_pago,
            "Estatus" => $bancos->estatus
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet) {

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $sheet->getStyle('A:E')->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:E1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '252850',
                        ],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
