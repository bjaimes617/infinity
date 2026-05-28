<?php

namespace App\Exports\Personal;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkOffset;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PersonalCecosExport implements ShouldAutoSize, WithColumnFormatting, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithCustomCsvSettings {

    private $personal;

    public function __construct($personal) {
        $this->personal = $personal;
    }

    public function chunkSize(): int {
        return 3000;
    }

    public function getCsvSettings(): array {
        return [
            "Content-Encoding" => "utf-8",
            "Content-type" => "text/csv; charset=utf",
            'delimiter' => ';',
            "Content-type" => "text/csv; charset=utf8mb4",
            'use_bom' => true,
        ];
    }

    public function startRow(): int {
        return 2;
    }

    public function headings(): array {
        $columns = [
            "Registrado",
            "Nacionalidad",
            "Cedula",
            "RIF",
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
            "Region",
            "Estado",
            "Ciudad",
            "Sector",
            "Direccion",
            "Utiliza Transporte",
            "Horario Transporte",
            "Transporte",
            "Fecha Ingreso",
            "Nomina",
            "Cargo",
            "Estatus Cargo",
            "Nacionalidad Cuenta",
            "CI Titular",
            "Nombre y Apellido del Titular",
            "Banco",
            "Nro Cuenta Bancaria",
            "Todo Ticket Integral" ,
            "Todo Ticket Alimentacion",
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
            "Fotografia",
            "Cod. CECO",
            "Nombre CECOS",
            "Porcentaje CECO",
            "Usuario_crm_portabilidad"
        ];
        return $columns;
    }

    public function collection() {
        $this->personal->each(function ($personal) {
            $this->map($personal);
        });

        return $this->personal;
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

    public function prepareRows($personal): array {
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
            
            $change->estado = $change->RelationEstado != null ? $change->RelationEstado->nombre_estado : null;
            $change->region = $change->RelationEstado != null ? $change->RelationEstado->RelationRegion->nombre_region : null;
            $change->ciudad = $change->RelationCiudad != null ? $change->RelationCiudad->ciudad : null;
            
            $change->todoticket_integral       = $change->todoticket_integral == "1" ? "SI" : "NO";
            $change->todoticket_alimentacion   = $change->todoticket_alimentacion == "1" ? "SI" : "NO";
            
            if ($change->activetransporte == '1') {
                $change->activetransporte = "SI";
            } else {
                $change->activetransporte = "NO";
            }

            if ($change->htransporte !== null) {
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

            if ($change->RelationGerente == null) {
                $change->gerente = "N/A";
            } else {
                $change->gerente = $change->RelationGerente->primer_nombre . ' ' . $change->RelationGerente->primer_apellido;
            }

            if ($change->RelationprimerLider == null) {
                $change->primerlider = "N/A";
            } else {
                $change->primerlider = $change->RelationprimerLider->primer_nombre . ' ' . $change->RelationprimerLider->primer_apellido;
            }

            if ($change->RelationsegundoLider == null) {
                $change->segundolider = "N/A";
            } else {
                $change->segundolider = $change->RelationsegundoLider->primer_nombre . ' ' . $change->RelationsegundoLider->primer_apellido;
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
               // dd($change, $change->RelationCentroCosto(),$e->getMessage());
            }
            return $change;
        }, $personal);
    }

    public function map($personal): array {
        $columns = [
            "Registrado"=> date('d/m/Y h:i:s', strtotime($personal->created_at)),
            "Nacionalidad"=> $personal->nacionalidad,
            "Cedula" => $personal->cedula_empleado,
            "RIF" => $personal->rif,
            "Primer Nombre" => $personal->primer_nombre,
            "Segundo Nombre" => $personal->segundo_nombre,
            "Primer Apellido" => $personal->primer_apellido,
            "Segundo Apellido" => $personal->segundo_apellido,
            "Email" => $personal->email,
            "Nivel Academico" => $personal->nivel_academico,
            "Especialidad" => $personal->especialidad,
            "Fecha Nacimiento" => date('d/m/Y', strtotime($personal->fecha_nacimiento)),
            "Sexo" => $personal->sexo,
            "Tlf. Local" => $personal->telefono_casa,
            "Tlf. Movil" => $personal->telefono_movil,
            "Cant. hijos" => $personal->hijos,
            "Region" =>$personal->region,
            "Estado" =>$personal->estado,
            "Ciudad" =>$personal->ciudad,
            "Sector" => $personal->sector,
            "Dirección" => $personal->direccion,
            "Utiliza Transporte"=>$personal->activetransporte,
            "Horario Transporte"=>$personal->htransporte,
            "Transporte" => $personal->zona_transporte,
            "Fecha Ingreso" => date('d/m/Y', strtotime($personal->fecha_ingreso)),
            "Nomina" => $personal->nomina,
            "Cargo" => $personal->cargo,
            "Estatus Cargo" => $personal->estatuscargo,
            "Nacionalidad Cuenta" => $personal->nacionalidad_cuenta,
            "CI Titular" => $personal->cedula_titular,
            "Nombre y Apellido del Titular" => $personal->nombre_titular,
            "Banco" => $personal->banco,
            "Nro Cuenta Bancaria" => "'" . strval($personal->cuenta_bancaria),
            "Todo Ticket Integral"      => $personal->todoticket_integral,
            "Todo Ticket Alimentacion"  => $personal->todoticket_alimentacion,
            "Horario" => $personal->horario_operativo,
            "Jornada Laboral" => $personal->jornada,
            "Turno" => $personal->turno,
            "Esquema Libranzas" => $personal->libranza,
            "Hora de Entrada" => date('h:i:s', strtotime($personal->hora_entrada)),
            "Hora de Salida" => date('h:i:s', strtotime($personal->hora_salida)),
            "Sede Laboral" => $personal->sede_laboral,
            "Gerente" => $personal->gerente,
            "1er. Lider Inmediato" => $personal->primerlider,
            "2do. Lider Inmediato" => $personal->segundolider,
            "Gerencia" => $personal->gerencia,           
            "Unidad / Campaña" => $personal->cartera,
            "Segmento/skill"=>$personal->skill,
            "Estatus" => $personal->estatus,
            "Firma 1er. Contrato" => date('d/m/Y', strtotime($personal->contrato_1)),
            "Finalizacion 1er. Contrato" => date('d/m/Y', strtotime($personal->finalizacion_1)),
            "Firma 2do. Contrato" => $personal->contrato2,
            "Finalizacion 2do. Contrato" => $personal->finalcontrato2,
            "Tipo de Contrato" => $personal->situacion_contractual,
            "Fecha de Egreso" => $personal->fechaEgreso,
            "Motivo del Egreso" => $personal->motivo_egreso,
            "fotografia" => $personal->image,
            "Cod. CECO" => $personal->codcecos,
            "Nombre CECOS" => $personal->cecos,
            "Porcentaje CECO" => $personal->porcentaje,
            "Usuario_crm_portabilidad"=> $personal->usuario_crm_portabilidad,
        ];

        return $columns;
    }

}
