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
use App\Models\personal\PermisosCarteraModel;

class ComisionesPortaSemanalExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

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
            "Observaciones",
            "Descripcion Incidencia",
            "Tasa",
            "Monto"
        ];
        return $columns;
    }

    public function collection() {

        foreach ($this->comisiones as $key => $value) {

            if ($value->relationSkill->cod_ceco == '00000') {
                // SOLO APLICABLE PARA PERSONAL CON SKILL DISTRIBIUDO
                if (PermisosCarteraModel::where('user_id', \Auth::user()->id)->exists()) {
                    //EXTRAER CARTERAS PERMISO DEL USUARIO
                    $skills = PermisosCarteraModel::select('skill_id')
                                    ->join('dir_area_trabajo', 'dir_permissions_user.area_trabajo_id', 'dir_area_trabajo.id')
                                    ->where('dir_permissions_user.user_id', \Auth::user()->id)
                                    ->groupBy('skill_id')->pluck('skill_id')->toArray();
                    //EXTRAER CECOS DE LA PERSONA CARGADA
                    $cecos = $value->relationPersonal->RelationCentroCosto()->select('skill_id')->get();
                    foreach ($cecos as $ceco) {
                        //  dd($skill->skill_id, $cecos);
                        //COMPARAR SI ES CECO DE LA PERSONA SE ENCUENTRA DENTRO DE LA CARTERA PERMISADA
                        if (!in_array($ceco->skill_id, $skills)) {
                            //DE NO ENCONTRARSE EL CECO DENTRO DE LAS CARTERAS PERMISADAS SE RETIRA EL REGISTRO
                            unset($this->comisiones[$key]);
                        }
                    }
                }
            }
            // code...
        }
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
            "Altas" => $comisiones->atlas_ar != null ? $comisiones->atlas_ar : null,
            "Activaciones" => $comisiones->activaciones_ar != null ? $comisiones->activaciones_ar : null,
            "CB" => $comisiones->cb_ar != null ? $comisiones->cb_ar : null,
            "Total Altas" => $comisiones->total_atlas_ar != null ? $comisiones->total_atlas_ar : null,
            "Comision USD" => $comisiones->comision_usd_ar != null ? $comisiones->comision_usd_ar : null,
            "Variable USD" => $comisiones->variable_usd_ar != null ? $comisiones->variable_usd_ar : null,
            "Bonificacion USD" => $comisiones->bonificacion_usd_ar != null ? $comisiones->bonificacion_usd_ar : null,
            "Descuentos USD" => $comisiones->descuentos_usd_ar != null ? $comisiones->descuentos_usd_ar : null,
            "Descuento Marketing" => $comisiones->pago_marketing_ar != null ? $comisiones->pago_marketing_ar : null,
            "Total USD" => $comisiones->total_usd_ar != null ? $comisiones->total_usd_ar : null,
            "Periodo" => $comisiones->periodo_name,
            "Mes" => trans('meses.' . $comisiones->mes),
            "Empresa" => $comisiones->nomina,
            "CECO" => $comisiones->ceco,
            "Estatus" => $comisiones->estatus_pago,
            "Observaciones" => $comisiones->observaciones,
            "Descripcion Incidencia" => $comisiones->descripcion_incidencia,
            "Tasa" => $comisiones->bs_tasa_ar != null ? $comisiones->bs_tasa_ar : null,
            "Monto" => $comisiones->total_pagar_ar != null ? $comisiones->total_pagar_ar : null,
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
