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

class ObservacionesExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

    private $bancos;

    public function __construct($bancos) {
        $this->bancos = $bancos;
    }

    public function chunkSize(): int {
        return 3000;
    }

    public function startRow(): int {
        return 2;
    }

    public function headings(): array {
        $columns = [
            "CI",
            "Trabajador",
            "Nacionalidad",
            "CI Titular",
            "Cuenta",
            "Banco",
            "Observaciones",
            "Estatus",
            "USD",
            "Bs",
            "Total a Pagar"
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

            $change->load('relationPersonal');

            $change->trabajador = $change->relationPersonal->primer_nombre . " " . $change->relationPersonal->segundo_nombre . " " . $change->relationPersonal->primer_apellido . " " . $change->relationPersonal->segundo_apellido;

            $change->ci_titular = $change->relationPersonal != null ? $change->relationPersonal->cedula_titular : null;

            $change->cuenta = $change->relationPersonal != null ? $change->relationPersonal->cuenta_bancaria : null;

            $change->banco = $change->relationPersonal->Relationbanco != null ? $change->relationPersonal->Relationbanco->bancos : null;

            $change->nac = $change->relationPersonal != null ? $change->relationPersonal->nacionalidad_cuenta : null;

            $change->usd_ar = str_replace(",", ".", $change->usd);
            $change->bs_tasa_ar = str_replace(",", ".", $change->bs_tasa);
            $change->total_pagar_ar = str_replace(",", ".", $change->total_pagar);

            $change->usd_ar = (float) $change->usd_ar;
            $change->bs_tasa_ar = (float) $change->bs_tasa_ar;
            $change->total_pagar_ar = (float) $change->total_pagar_ar;

            return $change;
        }, $bancos);
    }

    public function map($bancos): array {
        $columns = [
            "CI" => $bancos->cedula,
            "Trabajador" => $bancos->trabajador,
            "Nacionalidad" => $bancos->nac,
            "CI Titular" => $bancos->ci_titular,
            "Cuenta" => $bancos->cuenta,
            "Banco" => $bancos->banco,
            "Observaciones" => $bancos->observaciones,
            "Estatus" => $bancos->estatus,
            "USD" => number_format($bancos->usd_ar, 2, '.', ','),
            "Bs" => number_format($bancos->bs_tasa_ar, 2, '.', ','),
            "Total a Pagar" => number_format($bancos->total_pagar_ar, 2, '.', ',')
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet) {

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $sheet->getStyle('A:K')->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:K1')->applyFromArray([
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
