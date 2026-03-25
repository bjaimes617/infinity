<?php

namespace App\Exports\Personal;

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
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use App\Models\personal\PersonalModel;
use Illuminate\Support\Facades\DB;

class HistoricoActualizacionesExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithEvents {
    
     private $historico;

    public function __construct($historico) {
        $this->historico = $historico;
    }

    public function chunkSize(): int {
        return 1000;
    }

    public function startRow(): int {
        return 2;
    }

     public function headings(): array {
        $columns = [
            "Actualizado",
            "Modificado por",
            "Nacionalidad",
            "Cedula",
            "Primer Nombre",
            "Segundo Nombre",
            "Primer Apellido",
            "Segundo Apellido",
            "Email",
            "Nivel Academico",
            "Especialidad",
            "Fecha Nacimiento",
            "Sexo",
            "Tlf. Local",
            "Tlf. Movil",
            "Cant. hijos",
            "Sector",
            "Direccion",
            "Utiliza Transporte",
            "Horario Transporte",
            "Transporte",
            "Fecha Ingreso",
            "Nomina",
            "Cargo",
            "Estatus Cargo",
            "CI Titular",
            "Nombre y Apellido del Titular",
            "Banco",
            "Nro Cuenta Bancaria",
            "Horario",
            "Jornada Laboral",
            "Turno",
            "Esquema Libranzas",
            "Hora de Entrada",
            "Hora de Salida",
            "Sede Laboral",
            "Gerente",
            "1er. Lider Inmediato",
            "2do. Lider Inmediato",
            "Gerencia",
            "Unidad/Campaña",
            "Segmento/skill",
            "Estatus",
            "Firma 1er. Contrato",
            "Finalizacion 1er. Contrato",
            "Firma 2do. Contrato",
            "Finalizacion 2do. Contrato",
            "Tipo de Contrato",
            "Fecha de Egreso",
            "Motivo del Egreso",
            "Fotografia"
        ];
        return $columns;
    }

    public function collection() {
        $this->historico->each(function ($historico) {
            $this->map($historico);
        });
        return $this->historico;
    }

    public function columnFormats(): array {
        return [
            'l' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'M' => NumberFormat::FORMAT_TEXT,
            'Y' => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT, ////columna de nuero de cuenta            
            'AP' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function prepareRows($historico): array {
        return array_map(function ($change) {

            $change->load('RelationCargos', 'RelationEstatus', 'Relationbanco', 'RelationAreas', 'RelationLibranzas', 'RelationCentroCosto', 'RelationCartera',
                    'RelationGerente', 'RelationprimerLider', 'RelationsegundoLider', 'RelationZonaTransporte', 'RelationNomina');

            if ($change->image != null) {
                $change->image = "SI";
            } else {
                $change->image = "NO";
            }

            if ($change->RelationZonaTransporte == null) {
                $change->zona_transporte = "N/A";
            } else {
                $change->zona_transporte = $change->RelationZonaTransporte->zonatransporte;
            }            

            if ($change->activetransporte == '1') {
                $change->activetransporte = "SI";
            } else {
                $change->activetransporte = "NO";
            }

            if ($change->htransporte != null) {
                switch ($change->htransporte):
                    case 1 : $change->htransporte = "AM";
                        break;
                    case 2 : $change->htransporte = "PM";
                        break;
                    case 3 : $change->htransporte = "Ambos";
                        break;
                endswitch;
            } else {
                $change->htransporte = "";
            }

            if($change->gerente_area_id != null){

             $gerente_new = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")->where('id',$change->gerente_area_id)->first();

               $gerente_lp = $gerente_new->nombre;

               $change->gerente = $gerente_lp;
           }else{

              $change->gerente = "";
           }

           if ($change->jefe_inmediato != null) {

        $jefe_new = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")->where('id',$change->jefe_inmediato)->first();

        $jefe_lp = $jefe_new->nombre;

        $change->primerlider = $jefe_lp;

    }else{
     
     $change->primerlider = "";

    }

     if ($change->jefe_inmediato_segundo != null) {

        $jefe2_new = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")->where('id',$change->jefe_inmediato_segundo)->first();

        $jefe2_lp = $jefe2_new->nombre;

        $change->segundolider = $jefe2_lp;

    }else{
    
    $change->segundolider = "";

    }
            

            if ($change->RelationCartera == null) {
                $change->cartera = "N/A";
            } else {
                $change->cartera = $change->RelationCartera->nombre_cartera;
            }

            if ($change->Relationbanco == null) {
                $change->banco = "";
            } else {
                $change->banco = $change->Relationbanco->bancos;
            }

            if ($change->contrato_2 == null) {
                $change->contrato2 = null;
                $change->finalcontrato2 = null;
            } else {
                $change->contrato2 = date('d/m/Y', strtotime($change->contrato_2));
                $change->finalcontrato2 = date('d/m/Y', strtotime($change->finalizacion_2));
            }

            if ($change->fecha_egreso == null) {
                $change->fechaEgreso = null;
            } else {
                $change->fechaEgreso = date('d/m/Y', strtotime($change->fecha_egreso));
            }
            if($change->RelationNomina == null){
                $change->nomina     = null;                
            } else {
               $change->nomina     = $change->RelationNomina->nombre_nomina; 
            }
            
            if($change->RelationCargos == null){
                $change->cargo     = null;                
            } else {
                $change->cargo      = $change->RelationCargos->nombre_cargo;
            }
            
            if($change->RelationLibranzas == null){
                $change->libranza     = null;                
            } else {
                $change->libranza   = $change->RelationLibranzas->libranzas;
            }
            
            $change->gerencia   = $change->RelationAreas->nombre_campana;
            $change->skill      = $change->Relationskill->nombre_skill;
            $change->estatus    = $change->RelationEstatus->estatus;
            try {
            ////centros de costos separados por coma
            if ($change->RelationCentroCosto == null) {
                $change->centroCosto = null;
            } else {
                $cod = array();
                $ceco = array();
                $porcentaje = array();

                foreach ($change->RelationCentroCosto()->get() as $centroasignados) {
                    
                    array_push($cod, $centroasignados->RelationCECO->cod_ceco);
                    array_push($ceco, $centroasignados->RelationCECO->nombre_ceco);
                    array_push($porcentaje, number_format($centroasignados->porcentaje));
                }
                $string = '|';
                $change->codcecos = implode($string, $cod);
                $change->cecos = implode($string, $ceco);
                $change->porcentaje = implode($string, $porcentaje);
            }
            } catch (\Exception $e){
                dd($change, $change->RelationCentroCosto(),$e->getMessage());
            }
            return $change;
        }, $historico);
    }

    public function map($historico): array {
        $columns = [
            "Actualizado"=> date('d/m/Y h:i:s', strtotime($historico->created_at)),
            "Modificado por" => $historico->modificado_por,
            "Nacionalidad"=> $historico->nacionalidad,
            "Cedula" => $historico->cedula_empleado,
            "Primer Nombre" => $historico->primer_nombre,
            "Segundo Nombre" => $historico->segundo_nombre,
            "Primer Apellido" => $historico->primer_apellido,
            "Segundo Apellido" => $historico->segundo_apellido,
            "Email" => $historico->email,
            "Nivel Academico" => $historico->nivel_academico,
            "Especialidad" => $historico->especialidad,
            "Fecha Nacimiento" => date('d/m/Y', strtotime($historico->fecha_nacimiento)),
            "Sexo" => $historico->sexo,
            "Tlf. Local" => $historico->telefono_casa,
            "Tlf. Movil" => $historico->telefono_movil,
            "Cant. hijos" => $historico->hijos,
            "Sector" => $historico->sector,
            "Dirección" => $historico->direccion,
            "Utiliza Transporte"=>$historico->activetransporte,
            "Horario Transporte"=>$historico->htransporte,
            "Transporte" => $historico->zona_transporte,
            "Fecha Ingreso" => date('d/m/Y', strtotime($historico->fecha_ingreso)),
            "Nomina" => $historico->nomina,
            "Cargo" => $historico->cargo,
            "Estatus Cargo" => $historico->estatuscargo,
            "CI Titular" => $historico->cedula_titular,
            "Nombre y Apellido del Titular" => $historico->nombre_titular,
            "Banco" => $historico->banco,
            "Nro Cuenta Bancaria" => "'" . strval($historico->cuenta_bancaria),
            "Horario" => $historico->horario_operativo,
            "Jornada Laboral" => $historico->jornada,
            "Turno" => $historico->jornada,
            "Esquema Libranzas" => $historico->libranza,
            "Hora de Entrada" => date('h:i:s', strtotime($historico->horario_entrada)),
            "Hora de Salida" => date('h:i:s', strtotime($historico->horario_salida)),
            "Sede Laboral" => $historico->sede_laboral,
            "Gerente" => $historico->gerente,
            "1er. Lider Inmediato" => $historico->primerlider,
            "2do. Lider Inmediato" => $historico->segundolider,
            "Gerencia" => $historico->gerencia,           
            "Unidad / Campaña" => $historico->cartera,
            "Segmento/skill"=>$historico->skill,
            "Estatus" => $historico->estatus,
            "Firma 1er. Contrato" => date('d/m/Y', strtotime($historico->contrato_1)),
            "Finalizacion 1er. Contrato" => date('d/m/Y', strtotime($historico->finalizacion_1)),
            "Firma 2do. Contrato" => $historico->contrato2,
            "Finalizacion 2do. Contrato" => $historico->finalcontrato2,
            "Tipo de Contrato" => $historico->situacion_contractual,
            "Fecha de Egreso" => $historico->fechaEgreso,
            "Motivo del Egreso" => $historico->motivo_egreso,
            "fotografia" => $historico->image,
        ];

        return $columns;
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A1:AY1')->getFont()->setBold(true);
        $sheet->getStyle('A:AY')->getAlignment()->setHorizontal('center');


    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:AY1')->applyFromArray([
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
