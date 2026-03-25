<?php

namespace App\Exports\reclutamiento;

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
use Carbon\Carbon;

class CandidatosExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithChunkReading, WithCustomCsvSettings {

    private $result;

    public function __construct($result) {
        $this->result = $result;
    }

    public function chunkSize(): int {
        return 50;
    }

    public function getCsvSettings(): array {
        return [
            'input_encoding' => 'utf8mb4_general_ci',
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
           "Registrados desde",
            "Estatus Actual",
            "Nacionalidad",
            "Cedula"     ,
            "Nombres"     ,
            "Apellidos"    ,
            "Estado Civil"  ,
            "Lugar de Nacimiento"  ,
            "fecha de Nacimiento"   ,
            "Edad"                  ,
            "Sexo"                  ,
            "Cnts pers viven con usted",
            "Personas a cargo"      ,
            "Edad Personas a cargo" ,
            "Nombre del Conyuge"    ,
            
            "Hijos"                ,
            "Edad de los hijos"    ,
            "Sexo de los hijos"    ,
            
            "Telefono Celular"      ,
            "Telefono Habitacion"   ,
            "email"                 ,
                        
            "Estado"                ,
            "Ciudad"                ,
            "Parroquia"             ,
            "Direccion de Habitacion",
            
            "Talla Camisa"          ,
            "Talla Pantalon"        ,
            "Talla Calzado"         ,
            
            "Por donde Contacto"          ,
            "Trabajo Antes en la empresa" ,
            "Fecha en la que Trabajo"     ,
            "Parientes en la empresa"     ,
            "Mencione los Parientes"      ,
            
            "Inicio Bachiller"           ,
            "Fin Bachiller"              ,
            "Años Curasados Bachiller"   ,
            "instituto Bachiller"        ,
            "Lugar Bachiller"            ,
                        
            "Inicio Tsu"              ,
            "Fin Tsu"                 ,
            "Años Curasados Tsu"      ,
            "instituto Tsu"           ,
            "Lugar Tsu"               ,
            
            "Inicio Especializacion"              ,
            "Fin Especializacion"                 ,
            "Años Curasados Especializacion"      ,
            "instituto Especializacion"           ,
            "Lugar Especializacion"               ,
            
            "Inicio Postgrado"              ,
            "Fin Postgrado"                 ,
            "Años Curasados Postgrado"      ,
            "instituto Postgrado"           ,
            "Lugar Postgrado"               ,
            
            "Estudios Actuales?"            ,
            "Otros Estudios?"               ,
            
            "Nombre Apellido Referencia 1"  ,
            "Telefono Referencia 1"         ,
            "Ocupacion Referencia 1"        ,
            "Años de Conocidos Referencia 1" ,
            
            "Nombre Apellido Referencia 2"  ,
            "Telefono Referencia 2"         ,
            "Ocupacion Referencia 2"        ,
            "Años de Conocidos Referencia 2" ,
                        
            "Razon Social Empresa 1"    ,
            "Direccion Empresa 1"       ,
            "Ciudad Empresa 1"          ,
            "Telefono Empresa 1"        ,
            "Jefe inmediato Empresa 1"  ,
            "Cargo ejercido Empresa 1"  ,
            "Personas a cargo Empresa 1" ,
            "Fecha Ingreso Empresa 1"   ,
            "Fecha Egreso Empresa 1"    ,
            "Tiempo en la Empresa 1"    ,
            "Sueldo inicial Empresa 1"  ,
            "Sueldo final Empresa 1"    ,
            "Motivo del egreso Empresa 1" ,
            "Observaciones para Empresa 1" ,
            
            "Razon Social Empresa 2"    ,
            "Direccion Empresa 2"       ,
            "Ciudad Empresa 2"          ,
            "Telefono Empresa 2"        ,
            "Jefe inmediato Empresa 2"  ,
            "Cargo ejercido Empresa 2"  ,
            "Personas a cargo Empresa 2" ,
            "Fecha Ingreso Empresa 2"   ,
            "Fecha Egreso Empresa 2"    ,
            "Tiempo en la Empresa 2"    ,
            "Sueldo inicial Empresa 2"  ,
            "Sueldo final Empresa 2"    ,
            "Motivo del egreso Empresa 2" ,
            "Observaciones para Empresa 2" ,
      
        ];
        return $columns;
    }

    public function collection() {
        $this->result->each(function ($result) {
            $this->map($result);
        });
        return $this->result;
    }

    public function prepareRows($result): array {
        return array_map(function ($change) {
         
                    
            $change->created = carbon::create($change->created_at)->format('d/m/Y');   
            
            $change->status       = $change->RelationCapEstatus->estatus;
           
            $change->nacimiento  = $change->fecha_nacimiento;
            $change->edad        = isset($change->edad) ? $change->edad : null;
            $change->lugarnacimiento = $change->lugarnacimiento;
            $change->sexo            = $change->sexo;
            
            $change->personasviven   =$change->personasviven;
            $change->personasacargo = $change->personasacargo;
            $change->edadesacargo   = $change->edadesacargo;
            $change->edocivil       = $change->edocivil;     
            $change->conyuge        = $change->conyuge;
            $change->hijos          = $change->hijos;
            $change->edadhijos      = $change->edadhijos;
            $change->sexohijos      = $change->sexohijos;
    
            $change->habitacion      = $change->telefono_casa;
            $change->celular         = $change->telefono_celular;
            $change->email           = $change->email;      

            $change->estado      = $change->estado;         
            $change->ciudad      = $change->ciudad;
            $change->parroquia   = $change->parroquia;
            $change->direccion   = $change->direccion;
           
            $change->trabajoantes = $change->trabajoantes == "S" ? "SI"  : "NO";
          
            $change->parientes = $change->parientes == "S" ? "SI"  : "NO";
            
            $change->fingreso1  = $change->fingreso1 !== '000-00-00' ? $change->fegreso1 : null;
            $change->fegreso1   = $change->fegreso1 !== '000-00-00' ? $change->fegreso1 : null;
            
            $change->fingreso2  = $change->fingreso2 !== '000-00-00' ? $change->fingreso2 : null;
            $change->fegreso2   = $change->fegreso2 !== '000-00-00' ? $change->fegreso2 : null;
           return $change;
        }, $result);
    }

    public function map($result): array {
        $columns = [
            "Registrados desde"     => $result->created,
            "Estatus Actual"        =>$result->status,
            "Nacionalidad"          => $result->nacionalidad,
            "Cedula"                => $result->cedula,
            "Nombres"               => $result->nombres,
            "Apellidos"             => $result->apellidos,
            "Estado Civil"          => $result->edocivil,
            "Lugar de Nacimiento"   => $result->lugarnacimiento,
            "fecha de Nacimiento"   => $result->nacimiento,
            "Edad"                  => $result->edad,
            "Sexo"                  => $result->sexo,
            "Cnts pers viven con usted"   => $result->personasviven,
            "Personas a cargo"      => $result->personasacargo,
            "Edad Personas a cargo" => $result->edadesacargo,            
            "Nombre del Conyuge"    => $result->conyuge,
            
            "Hijos"                => $result->hijos,
            "Edad de los hijos"    => $result->edadhijos,
            "Sexo de los hijos"    => $result->sexohijos,
            
            "Telefono Celular"      => $result->celular,
            "Telefono Habitacion"   => $result->habitacion,
            "email"                 => $result->email,
                        
            "Estado"                => $result->estado,
            "Ciudad"                => $result->ciudad,
            "Parroquia"             => $result->parroquia,
            "Direccion de Habitacion"=> $result->direccion,
            
            "Talla Camisa"          =>  $result->camisa,
            "Talla Pantalon"        =>  $result->pantalon,
            "Talla Calzado"         =>  $result->calzado,
            
            "Por donde Contacto"          =>  $result->redes,
            "Trabajo Antes en la empresa" =>  $result->trabajoantes,
            "Fecha en la que Trabajo"     =>  $result->fechantes,            
            "Parientes en la empresa"     =>  $result->parientes,
            "Mencione los Parientes"      =>  $result->mencionelos ,
            
            "Inicio Bachiller"              => $result->initbachiller,
            "Fin Bachiller"                 => $result->endbachiller,
            "Años Curasados Bachiller"      => $result->cursadobachiller,
            "instituto Bachiller"           => $result->instbachiller,
            "Lugar Bachiller"               => $result->lugarbachiller,
                        
            "Inicio Tsu"              => $result->inittsu,
            "Fin Tsu"                 => $result->endtsu,
            "Años Curasados Tsu"      => $result->cursadotsu,
            "instituto Tsu"           => $result->insttsu,
            "Lugar Tsu"               => $result->lugartsu,
            
            "Inicio Especializacion"              => $result->initespecializacion,
            "Fin Especializacion"                 => $result->endespecializacion,
            "Años Curasados Especializacion"      => $result->cursadoespecializacion,
            "instituto Especializacion"           => $result->instespecializacion,
            "Lugar Especializacion"               => $result->lugarespecializacion,
            
            "Inicio Postgrado"              => $result->initpostgrado,
            "Fin Postgrado"                 => $result->endpostgrado,
            "Años Curasados Postgrado"      => $result->cursadopostgrado,
            "instituto Postgrado"           => $result->instpostgrado,
            "Lugar Postgrado"               => $result->lugarpostgrado,
            
            "Estudios Actuales?"            => $result->estudiosactuales,
            "Otros Estudios?"               => $result->otrosestudios,
            
            "Nombre Apellido Referencia 1"  => $result->referencia1,
            "Telefono Referencia 1"         => $result->telefono1,
            "Ocupacion Referencia 1"        => $result->ocuacion1,
            "Años de Conocidos Referencia 1" => $result->telefono1,
            
            "Nombre Apellido Referencia 2"  => $result->referencia2,
            "Telefono Referencia 2"         => $result->telefono2,
            "Ocupacion Referencia 2"        => $result->ocuacion2,
            "Años de Conocidos Referencia 2" => $result->telefono2,
                        
            "Razon Social Empresa 1"    => $result->nombreempresa1,
            "Direccion Empresa 1"       => $result->direccionempresa1,
            "Ciudad Empresa 1"          => $result->ciudadempresa1,
            "Telefono Empresa 1"        => $result->telefonoempresa1,
            "Jefe inmediato Empresa 1"  => $result->jefeinmediato1,
            "Cargo ejercido Empresa 1"  => $result->cargo1,
            "Personas a cargo Empresa 1" => $result->cargo1,
            "Fecha Ingreso Empresa 1"   => $result->fingreso1,
            "Fecha Egreso Empresa 1"    => $result->fegreso1,
            "Tiempo en la Empresa 1"    => $result->tiempo1,
            "Sueldo inicial Empresa 1"  => $result->sueldo11,
            "Sueldo final Empresa 1"    => $result->sueldo12,
            "Motivo del egreso Empresa 1" => $result->motivo1,
            "Observaciones para Empresa 1" => $result->observaciones1,
            
            "Razon Social Empresa 2"    => $result->nombreempresa2,
            "Direccion Empresa 2"       => $result->direccionempresa2,
            "Ciudad Empresa 2"          => $result->ciudadempresa2,
            "Telefono Empresa 2"        => $result->telefonoempresa2,
            "Jefe inmediato Empresa 2"  => $result->jefeinmediato2,
            "Cargo ejercido Empresa 2"  => $result->cargo2,
            "Personas a cargo Empresa 2" => $result->cargo2,
            "Fecha Ingreso Empresa 2"   => $result->fingreso2,
            "Fecha Egreso Empresa 2"    => $result->fegreso2,
            "Tiempo en la Empresa 2"    => $result->tiempo2,
            "Sueldo inicial Empresa 2"  => $result->sueldo21,
            "Sueldo final Empresa 2"    => $result->sueldo22,
            "Motivo del egreso Empresa 2"   => $result->motivo2,
            "Observaciones para Empresa 2"  => $result->observaciones2,
      
        ];
        return $columns;
    }
}
