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

class TrabajadorExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

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
            "Campaña",
            "Skill",
            "Comision USD",
            "Variable USD",
            "Bonificacion USD",
            "Bono Nocturno USD",
            "HE USD",
            "Jornada Fin de Semana USD",
            "Descuentos USD",
            "Total USD",
            "KPI Comisionable",
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

            $change->campana = $change->relationCartera != null ? $change->relationCartera->nombre_cartera : "N/A";

            $change->skill = $change->relationSkill != null ? $change->relationSkill->nombre_skill : "N/A";

            $change->gerencia = $change->relationGerencia->nombre_campana;

            $change->nomina = $change->relationPersonal->RelationNomina != null ? $change->relationPersonal->RelationNomina->nombre_nomina : null;

            if ($change->periodo == 1) {

                $change->periodo_name = "1era. Quincena";
            }
            else {

                $change->periodo_name = "2da. Quincena";
            }

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
            "Campaña" => $comisiones->campana,
            "Skill" => $comisiones->skill,
            "Comision USD" => $comisiones->comision_usd,
            "Variable USD" => $comisiones->variable_usd,
            "Bonificacion USD" => $comisiones->bonificacion_usd,
            "Bono Nocturno USD" => $comisiones->bono_noc_usd,
            "HE USD" => $comisiones->he_usd,
            "Jornada Fin de Semana USD" => $comisiones->jornada_fin_usd,
            "Descuentos USD" => $comisiones->descuentos_usd,
            "Total USD" => $comisiones->total_usd,
            "KPI Comisionable" => $comisiones->cantidad_ventas,
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
