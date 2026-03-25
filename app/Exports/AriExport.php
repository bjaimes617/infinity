<?php

namespace App\Exports;

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

class AriExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {

    private $impuesto;

    public function __construct($impuesto) {
        $this->impuesto = $impuesto;
    }

    public function chunkSize(): int {
        return 3000;
    }

    public function startRow(): int {
        return 2;
    }

    public function headings(): array {
        $columns = [
            "Cedula",
            "Primer Nombre",
            "Segundo Nombre",
            "Primer Apellido",
            "Segundo Apellido",
            "Nomina",
            "Cargo",
            "Gerencia",
            "Unidad/Campaña",
            "Segmento/Skill",
            "Año Gravable",
            "Periodo",
            "Valor Unidad Tributaria",
            "a) Nombre Empresa",
            "b) Nombre Empresa",
            "c) Nombre Empresa",
            "d) Nombre Empresa",
            "a) Cantidad por Percibir de la Empresa",
            "b) Cantidad por Percibir de la Empresa",
            "c) Cantidad por Percibir de la Empresa",
            "d) Cantidad por Percibir de la Empresa",
            "Total Estimado",
            "Remuneraciones Determinadas",
            "Concepto1",
            "Concepto2",
            "Concepto3",
            "Concepto4",
            "Total Desgravamenes",
            "Monto Fijo Desgravamen",
            "Total U.T.",
            "Remuneraciones Determinadas - Total U.T.",
            "Impuesto Estimado",
            "Impuesto Año Gravable",
            "Rebaja Persona Natural",
            "Carga Familiar",
            "Total U.T. Familiar",
            "Total Rebajas",
            "Porcentaje Retención Inicial",
            "Determinación del Porcentaje",
            "Impuesto Retenido hasta la fecha",
            "Remuneraciones Percibidas hasta la fecha"
        ];
        return $columns;
    }

    public function collection() {
        $this->impuesto->each(function ($impuesto) {
            $this->map($impuesto);
        });
        return $this->impuesto;
    }

    public function prepareRows($impuesto): array {
        return array_map(function ($change) {

            $change->load('relationpersonal');

            if ($change->periodo == 1) {
                $change->trimestre = "Enero - Marzo";
            }elseif($change->periodo == 2){
                $change->trimestre = "Abril - Junio";
            }elseif($change->periodo == 3){
                 $change->trimestre = "Julio - Septiembre";
            }else{
                $change->trimestre = "Octubre - Diciembre";
            }

            if($change->carga_familiar == 0){

                $change->carga_familiar = "0";
            }
        

            $change->cartera = $change->relationpersonal->RelationCartera != null ? $change->relationpersonal->RelationCartera->nombre_cartera : "N/A";

             $change->skill = $change->relationpersonal->Relationskill != null ? $change->relationpersonal->Relationskill->nombre_skill : "N/A";

             $change->nomina = $change->relationpersonal->RelationNomina != null ? $change->relationpersonal->RelationNomina->nombre_nomina : null;

            
            $change->cargo = $change->relationpersonal->RelationCargos != null ? $change->relationpersonal->RelationCargos->nombre_cargo : null;
            
            $change->gerencia = $change->relationpersonal->RelationAreas->nombre_campana;           
            
            return $change;
        }, $impuesto);
    }

    public function map($impuesto): array {
        $columns = [
            "Cedula" => $impuesto->cedula_empleado,
            "Primer Nombre" => $impuesto->primer_nombre,
            "Segundo Nombre" => $impuesto->segundo_nombre,
            "Primer Apellido" => $impuesto->primer_apellido,
            "Segundo Apellido" => $impuesto->segundo_apellido,
            "Nomina" => $impuesto->nomina,           
            "Cargo" => $impuesto->cargo,
            "Gerencia" => $impuesto->gerencia,
            "Unidad/Campaña" => $impuesto->cartera,
            "Segmento/Skill" => $impuesto->skill,
            "Año Gravable" => $impuesto->anio,
            "Periodo" => $impuesto->trimestre,
            "Valor Unidad Tributaria" => $impuesto->unidad_tributaria." Bs.",
            "a) Nombre Empresa" => strtoupper($impuesto->nombre_empresa),
            "b) Nombre Empresa" => strtoupper($impuesto->nombre_empresa2),
            "c) Nombre Empresa" => strtoupper($impuesto->nombre_empresa3),
            "d) Nombre Empresa" =>strtoupper($impuesto->nombre_empresa4),
            "a) Cantidad por Percibir de la Empresa" => $impuesto->cantidad_organismo." Bs.",
            "b) Cantidad por Percibir de la Empresa" => $impuesto->cantidad_organismo2." Bs.",
            "c) Cantidad por Percibir de la Empresa" => $impuesto->cantidad_organismo3." Bs.",
            "d) Cantidad por Percibir de la Empresa" => $impuesto->cantidad_organismo4." Bs.",
            "Total Estimado" => $impuesto->total_estimado. "Bs.",
            "Remuneraciones Determinadas" => $impuesto->remun_deter." U.T.",
            "Concepto1" => $impuesto->valor_concepto1." Bs.",
            "Concepto2" => $impuesto->valor_concepto2." Bs.",
            "Concepto3" => $impuesto->valor_concepto3." Bs.",
            "Concepto4" => $impuesto->valor_concepto4." Bs.",
            "Total Desgravamenes" => $impuesto->total_desgravamenes." Bs.",
            "Monto Fijo Desgravamen" => $impuesto->monto_fijo_des." U.T.",
            "Total U.T." => $impuesto->total_ut." U.T.",
            "Remuneraciones Determinadas - Total U.T." => $impuesto->remun_menos_totalut." U.T.",
            "Impuesto Estimado" => $impuesto->impuesto_estimado." U.T.",
            "Impuesto Año Gravable" => $impuesto->impuesto_anio_gravable." U.T.",
            "Rebaja Persona Natural" => $impuesto->rebaja_persona_natural." U.T.",
            "Carga Familiar" => $impuesto->carga_familiar,
            "Total U.T. Familiar" => $impuesto->total_ut_familiar. " U.T.",
            "Total Rebajas" => $impuesto->total_rebajas." U.T.",
            "Porcentaje Retención Inicial" => $impuesto->porcentaje_reten_ini." %",
            "Determinación del Porcentaje" => $impuesto->deter_porcentaje." %",
            "Impuesto Retenido hasta la fecha" => $impuesto->impuesto_fecha,
            "Remuneraciones Percibidas hasta la fecha" => $impuesto->remun_fecha
            
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A1:AO1')->getFont()->setBold(true);

        $sheet->getStyle('A:AO')->getAlignment()->setHorizontal('center');


    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:AO1')->applyFromArray([
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
