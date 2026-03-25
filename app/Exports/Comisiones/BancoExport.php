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

class BancoExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

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
            "Empresa",
            "CI",
            "Trabajador",
            "Cargo",
            "Gerencia",
            "Campaña",
            "Skill", 
            "Estatus Personal",
            "Nacionalidad",
            "CI Titular",
            "Cuenta",
            "Banco",
            "Todo Ticket Integral",
            "Todo Ticket Alimentacion",
            "Observaciones",
            "Estatus de Pago",
            "USD",
            "Bs",
            "Total a Pagar",
            "cecos"
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
            
            $change->cargo = $change->relationPersonal->RelationCargos != null ? $change->relationPersonal->RelationCargos->nombre_cargo : null;
            $change->campana = $change->relationCampana != null ? $change->relationCampana->nombre_cartera : "N/A";
            $change->skill = $change->relationSkill != null ? $change->relationSkill->nombre_skill : "N/A";
            $change->gerencia = $change->relationGerencia->nombre_campana;
            
            $change->estatuspersonal    = $change->relationPersonal != null ? $change->relationPersonal->RelationEstatus->estatus : null;
            
            $change->ci_titular = $change->relationPersonal != null ? $change->relationPersonal->cedula_titular : null;

            $change->cuenta = $change->relationPersonal != null ? $change->relationPersonal->cuenta_bancaria : null;

            $change->banco = $change->relationPersonal->Relationbanco != null ? $change->relationPersonal->Relationbanco->bancos : null;
            
            $change->integral       = $change->relationPersonal->todoticket_integral == 1 ? "SI" : "NO";
            $change->alimentacion   = $change->relationPersonal->todoticket_alimentacion == 1 ? "SI" : "NO";

            $change->nac = $change->relationPersonal != null ? $change->relationPersonal->nacionalidad_cuenta : null;
            $change->empresa = $change->relationPersonal != null ? $change->relationPersonal->RelationNomina->nombre_nomina : null;
            
            if ($change->relationPersonal->RelationCentroCosto == null) {
                $change->centroCosto = null;
            } else {
                $cod = array();
                $ceco = array();
                $porcentaje = array();

                foreach ($change->relationPersonal->RelationCentroCosto()->get() as $centroasignados) {
                    
                    array_push($cod, $centroasignados->RelationCECO->cod_ceco);
                    array_push($ceco, $centroasignados->RelationCECO->nombre_ceco);
                    array_push($porcentaje, number_format($centroasignados->porcentaje));
                }
                $string = '|';
                $change->codcecos = implode($string, $cod);
                $change->cecos = implode($string, $ceco);
                $change->porcentaje = implode($string, $porcentaje);
            }
            
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
            "Empresa"       => $bancos->empresa,
            "CI"            => $bancos->cedula,
            "Trabajador"    => $bancos->trabajador,
            "Cargo"         => $bancos->cargo,
            "Gerencia"      => $bancos->gerencia,
            "Campaña"       => $bancos->campana,
            "Skill"         => $bancos->skill,         
            "Estatus Personal"=>$bancos->estatuspersonal,
            "Nacionalidad"  => $bancos->nac,
            "CI Titular"    => $bancos->ci_titular,
            "Cuenta"        => $bancos->cuenta,
            "Banco"         => $bancos->banco,
            "Todo Ticket Integral"      => $bancos->integral,
            "Todo Ticket Alimentacion"  => $bancos->alimentacion,
            "Observaciones" => $bancos->observaciones,
            "Estatus de Pago"=> $bancos->estatus,
            "USD"           => number_format($bancos->usd_ar, 2, '.', ','),
            "Bs"            => number_format($bancos->bs_tasa_ar, 2, '.', ','),
            "Total a Pagar" => number_format($bancos->total_pagar_ar, 2, '.', ','),
            "cecos"         =>$bancos->codcecos
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet) {

     $sheet->getStyle('A1:T1')->getFont()->setBold(true);

        $sheet->getStyle('A:T')->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:T1')->applyFromArray([
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
