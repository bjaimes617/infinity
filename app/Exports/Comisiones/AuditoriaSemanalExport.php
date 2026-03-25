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
use App\Models\Comisiones\OperacionesComisiones;
use App\Models\Comisiones\LoteModel;
use App\Models\Comisiones\BancoModel;

class AuditoriaSemanalExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

    private $auditoria;

    public function __construct($auditoria) {
        $this->auditoria = $auditoria;
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
            "Observaciones",
            "Descripcion Incidencia",
            "Tasa",
            "Monto"
        ];
        return $columns;
    }

    public function collection() {
        $this->auditoria->each(function ($auditoria) {
            $this->map($auditoria);
        });
        return $this->auditoria;
    }

    public function prepareRows($auditoria): array {
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
            elseif ($change->incidencia == 3) {

                $change->estatus_pago = "AUDITADO";
            }
            else {

                $change->estatus_pago = "PAGADO";
            }


            if (OperacionesComisiones::select('lote_id')->where('id', $change->id)->exists()) {

                $lote_ids = OperacionesComisiones::select('lote_id')->where('id', $change->id)->first();

                $lote_id = $lote_ids->lote_id;

                if (!LoteModel::select('lotebanco_id')->where('id', $lote_id)->exists()) {

                    $change->bs_tasa = "";
                    $change->total_pagar = "";
                }
                else {

                    $lotebanco_ids = LoteModel::select('lotebanco_id')->where('id', $lote_id)->first();

                    $lotebanco_id = $lotebanco_ids->lotebanco_id;

                    $montos = BancoModel::select('bs_tasa', 'total_pagar')->where('lotebanco_id', $lotebanco_id)->where('cedula', $change->cedula)->get();

                    foreach ($montos as $value) {

                        $change->bs_tasa = $value->bs_tasa . " Bs";

                        $change->total_pagar = $value->total_pagar . " Bs";
                    }
                }
            }
            else {

                $change->bs_tasa = "";
                $change->total_pagar = "";
            }

            $change->comision_usd_ar = str_replace(",", ".", $change->comision_usd);
            $change->variable_usd_ar = str_replace(",", ".", $change->variable_usd);
            $change->bonificacion_usd_ar = str_replace(",", ".", $change->bonificacion_usd);
            $change->descuentos_usd_ar = str_replace(",", ".", $change->descuentos_usd);
            $change->pago_marketing_ar = str_replace(",", ".", $change->pago_marketing);
            $change->atlas_ar = str_replace(",", ".", $change->atlas);
            $change->activaciones_ar = str_replace(",", ".", $change->activaciones);
            $change->cb_ar = str_replace(",", ".", $change->cb);

            $change->total_usd_ar = str_replace(",", ".", $change->total_usd);
            $change->total_atlas_ar = str_replace(",", ".", $change->total_atlas);

            $change->bs_tasa_ar = str_replace(",", ".", $change->bs_tasa);
            $change->total_pagar_ar = str_replace(",", ".", $change->total_pagar);

            $change->atlas_ar = (float) $change->atlas_ar;
            $change->comision_usd_ar = (float) $change->comision_usd_ar;
            $change->variable_usd_ar = (float) $change->variable_usd_ar;
            $change->descuentos_usd_ar = (float) $change->descuentos_usd_ar;
            $change->pago_marketing_ar = (float) $change->pago_marketing_ar;
            $change->activaciones_ar = (float) $change->activaciones_ar;
            $change->cb_ar = (float) $change->cb_ar;

            $change->bonificacion_usd_ar = (float) $change->bonificacion_usd_ar;

            $change->total_usd_ar = (float) $change->total_usd_ar;
            $change->total_atlas_ar = (float) $change->total_atlas_ar;

            $change->bs_tasa_ar = (float) $change->bs_tasa_ar;
            $change->total_pagar_ar = (float) $change->total_pagar_ar;

           //SI ES DISTRIBIUDO HACEMOS EL PROCESO NORMAL DE CECOS
            if ($change->relationSkill->cod_ceco == '00000') {
                if ($change->relationPersonal->RelationCentroCosto == null) {
                    $change->centroCosto = null;
                }
                else {
                    $cod = array();
                    foreach ($change->relationPersonal->RelationCentroCosto()->get() as $centroasignados) {
                        array_push($cod, $centroasignados->RelationCECO->cod_ceco);
                    }
                    $string = '|';
                    $change->codcecos = implode($string, $cod);
                }
            } else {
                $change->codcecos = $change->relationSkill->cod_ceco;
            }


            //dd($change,$change->atlas_ar);

            return $change;
        }, $auditoria);
    }

    public function map($auditoria): array {
        $columns = [
            "CI" => $auditoria->cedula,
            "Trabajador" => $auditoria->trabajador,
            "Cargo" => $auditoria->cargo,
            "Gerencia" => $auditoria->gerencia,
            "Skill" => $auditoria->skill,
            "Altas" => $auditoria->atlas_ar != null ? $auditoria->atlas_ar : null,
            "Activaciones" => $auditoria->activaciones_ar != null ? $auditoria->activaciones_ar : null,
            "CB" => $auditoria->cb_ar != null ? $auditoria->cb_ar : null,
            "Total Altas" => $auditoria->total_atlas_ar != null ? $auditoria->total_atlas_ar : null,
            "Comision USD" => $auditoria->comision_usd_ar != null ? $auditoria->comision_usd_ar : null,
            "Variable USD" => $auditoria->variable_usd_ar != null ? $auditoria->variable_usd_ar : null,
            "Bonificacion USD" => $auditoria->bonificacion_usd_ar != null ? $auditoria->bonificacion_usd_ar : null,
            "Descuentos USD" => $auditoria->descuentos_usd_ar != null ? $auditoria->descuentos_usd_ar : null,
            "Descuento Marketing" => $auditoria->pago_marketing_ar != null ? $auditoria->pago_marketing_ar : null,
            "Total USD" => $auditoria->total_usd_ar != null ? $auditoria->total_usd_ar : null,
            "Periodo" => $auditoria->periodo_name,
            "Mes" => trans('meses.' . $auditoria->mes),
            "Empresa" => $auditoria->nomina,
            "CECO" => $auditoria->codcecos,
            "Estatus" => $auditoria->estatus_pago,
            "Observaciones" => $auditoria->observaciones,
            "Descripcion Incidencia" => $auditoria->descripcion_incidencia,
            "Tasa" => $auditoria->bs_tasa_ar != null ? $auditoria->bs_tasa_ar : null,
            "Monto" => $auditoria->total_pagar_ar != null ? $auditoria->total_pagar_ar : null,
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet) {

        $sheet->getStyle('A1:X1')->getFont()->setBold(true);

        $sheet->getStyle('A:X')->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:X1')->applyFromArray([
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
