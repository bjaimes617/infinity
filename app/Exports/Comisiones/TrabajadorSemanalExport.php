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

class TrabajadorSemanalExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

    private $comisiones;

    public function __construct($comisiones) {
        $this->comisiones = $comisiones;
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
            "Cargo",
            "Gerencia",
            "Skill",
            "Altas",
            "Activaciones",
            "CB",
            "Total Altas",
            "Comision USD",
            "Variable USD",
            "Bonificacion USD",
            "Descuentos USD",
            "Descuento Marketing",
            "Total USD",
            "Periodo",
            "Mes",
            "Empresa",
            "CECO",
            "Estatus",
            "Observaciones"
        ];
        return $columns;
    }

    public function collection() {
        $this->comisiones->each(function ($comisiones) {
            $this->map($comisiones);
        });
        return $this->comisiones;
    }

    public function prepareRows($comisiones): array {
        return array_map(function ($change) {

            $change->load('relationPersonal', 'relationCartera', 'relationSkill', 'relationLote', 'relationGerencia');

            $change->trabajador = $change->relationPersonal->primer_nombre . " " . $change->relationPersonal->segundo_nombre . " " . $change->relationPersonal->primer_apellido . " " . $change->relationPersonal->segundo_apellido;

            $change->cargo = $change->relationPersonal->RelationCargos != null ? $change->relationPersonal->RelationCargos->nombre_cargo : null;

            $change->nomina = $change->relationPersonal->RelationNomina != null ? $change->relationPersonal->RelationNomina->nombre_nomina : null;

            $change->skill = $change->relationSkill != null ? $change->relationSkill->nombre_skill : "N/A";

            switch ($change->periodo) {

                case 1:
                    $change->periodo_name = "Semana 1";
                    break;
                case 2:
                    $change->periodo_name = "Semana 2";
                    break;
                case 3:
                    $change->periodo_name = "Semana 3";
                    break;
                case 4:
                    $change->periodo_name = "Semana 4";
                    break;
                case 5:
                    $change->periodo_name = "Semana 5";
                    break;
            }

            $change->gerencia = $change->relationGerencia->nombre_campana;

            if ($change->incidencia == 1) {

                $change->estatus_pago = "INCIDENCIA";
            }
            elseif ($change->incidencia == 2) {

                $change->estatus_pago = "NO PROCESADO";
            }
            else {

                $change->estatus_pago = "PAGADO";
            }

            return $change;
        }, $comisiones);
    }

    public function map($comisiones): array {
        $columns = [
            "CI" => $comisiones->cedula,
            "Trabajador" => $comisiones->trabajador,
            "Cargo" => $comisiones->cargo,
            "Gerencia" => $comisiones->gerencia,
            "Skill" => $comisiones->skill,
            "Altas" => $comisiones->atlas,
            "Activaciones" => $comisiones->activaciones,
            "CB" => $comisiones->cb,
            "Total Altas" => $comisiones->total_atlas,
            "Comision USD" => $comisiones->comision_usd,
            "Variable USD" => $comisiones->variable_usd,
            "Bonificacion USD" => $comisiones->bonificacion_usd,
            "Descuentos USD" => $comisiones->descuentos_usd,
            "Descuento Marketing" => $comisiones->pago_marketing,
            "Total USD" => $comisiones->total_usd,
            "Periodo" => $comisiones->periodo_name,
            "Mes" => trans('meses.' . $comisiones->mes),
            "Empresa" => $comisiones->nomina,
            "CECO" => $comisiones->ceco,
            "Estatus" => $comisiones->estatus_pago,
            "Observaciones" => $comisiones->observaciones
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet) {

        $sheet->getStyle('A1:U1')->getFont()->setBold(true);

        $sheet->getStyle('A:U')->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:U1')->applyFromArray([
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
