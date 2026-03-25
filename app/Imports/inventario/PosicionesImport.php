<?php

namespace App\Imports\inventario;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;


use App\Models\inventario\EquipoModel;
use App\Models\inventario\PosicionesModel;
use App\Models\inventario\MonitorModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;
use App\Models\personal\CarteraModel;

use App\Models\inventario\StockModel;

use App\Models\inventario\RelationEquiposStockModel;
use App\Models\inventario\RelationPosicionesPerifericosModel;
use App\Models\inventario\RelationPerifericosStockModel;
use App\Models\inventario\RelationPosicionesMonitorModel;
use App\Models\inventario\RelationPosicionCarterasModel;
use App\Models\inventario\RelationEquiposPosicionesModel;
use App\Models\inventario\RelationEquiposResponsableModel;
use App\Models\inventario\PerifericosModel;

class PosicionesImport implements ToCollection, WithHeadingRow, WithGroupedHeadingRow, WithValidation
{
    use Importable;
    private $data;     

    public function __construct() {
      
    }  

    public function collection(Collection $data)
    {
       try{
           $mensaje = '';
       foreach($data as $row){            
           ///se valida si el skill existe en la gerencia, si no, no procesa
            $gerencia = mb_convert_case($row["gerencia"],MB_CASE_TITLE, "UTF-8");
            foreach($row["skill"] as $skill){               
                if(SkillModels::where('nombre_skill','like','%'.ucwords(strtolower($skill)))->first()->RelationCarteras->RelationGerencias->nombre_campana !== $gerencia){
                    \Session::flash('error', 'El Skill '.ucwords(strtolower($skill)).' No corresponde a la Gerencia '.$gerencia);                    
                    $mensaje = 2;
                    break 2;
                }
            }
            
///si todo esta correcto se procede a registrar la posicion.
            $arraypos = explode("|", $row["posicion"]);            
            switch ($arraypos[0]) {
                case "PLA" : $ab = 2;
                    break;
                case "SIE" : $ab = 3;
                    break;
                case "AUT" : $ab = 4;
                    break;
                case "MAC" : $ab = 5;
                    break;
            }   
            ///actualizamos posicion
            $posicion = PosicionesModel::where('number',$arraypos[1])->where('sede',$ab)->first();
            $posicion->assigned     = "X";
            $posicion->tipo         = array_search($row["tipo_posicion"], config('app.tipoposicion'));
            $posicion->gerente_id   = PersonalModel::where('cedula_empleado',$row["gerente_cargo"])->first()->id;           
            $posicion->cargo_id     = CargoModel::where('nombre_cargo',$row["cargo"])->first()->id;     
            $posicion->save();
            
            ///asignamos los skill a la posicion
            foreach($row["skill"] as $skill){
                if($skill != null){
                    $dbskill = SkillModels::where('nombre_skill','like','%'.ucwords(strtolower($skill)))->first();                     
                    $relacarteras = RelationPosicionCarterasModel::create([
                        "inv_posiciones_id" => $posicion->id,
                        "skill_id"          => $dbskill->id, 
                        "cartera_id"        => $dbskill->cartera_id,
                        "area_trabajo_id"   => CarteraModel::find($dbskill->cartera_id)->area_trabajo_id,
                        "ceco"              => $dbskill->cod_ceco                        
                    ]);
                   
                }
            }                        
             ///asignamos perifericos a la posicion            
            foreach($row["periferico"] as $periferi){                            
                $perifericos = explode(" ", $periferi);                
                 switch ($perifericos[0]) {
                        case "ALM" : $ab = 1;
                            break;
                        case "PLA" : $ab = 2;
                            break;
                        case "SIE" : $ab = 3;
                            break;
                        case "AUT" : $ab = 4;
                            break;
                        case "MAC" : $ab = 5;
                            break ;                                           
                    }                 
                    ///swich de tipo de periferico
                switch ($perifericos[1]){
                            case "TECLADO": $var = 1;
                                break;
                            case "MOUSE":   $var = 2;
                                break;
                            case "HEATSET": $var = 3;
                                break;
                            case "T. VIDEO": $var = 4;
                                break;
                            case "T. INALAMBRICA": $var = 5;
                                break;
                            case "CORNETAS": $var = 6;
                                break;
                            case "ADAPTADORES": $var = 7;
                                break;
                        }  
                        ////swich de conexiones
                switch ($perifericos[2]){
                            case "USB": $con = 1;              
                                break;
                            case "PS/2":  $con = 2;           
                                break;
                            case  "JACK3.5mm":  $con = 3;           
                                break;
                            case "PCIE":  $con = 4;           
                                break;
                            case "AGP":  $con = 5;           
                                break; 
                            case "E/S":  $con = 6;           
                                break; 
                        }  
                        
                $stock = StockModel::find($ab);               
                                   
               $primerperiferico = $stock->Perifericos()->where('tipo', $var)->where('conexion', $con)->where('available', 'X')->first();
               $primerperiferico->assigned  = "X";
               $primerperiferico->available = null;
               $primerperiferico->save();
               
                ///actualizamos el stock del periferico y asignamos a la sede de la posicion
               RelationPerifericosStockModel::where('inv_perifericos_id', $primerperiferico->id)
                    ->where('inv_stocks_id', $stock->id)
                    ->update(["inv_stocks_id" => $posicion->sede]);                                      
                
                ///asignamos el periferico a la posicion
                RelationPosicionesPerifericosModel::create([
                    "inv_perifericos_id"=>  $primerperiferico->id,
                    "inv_posiciones_id" =>  $posicion->id
                ]);                       
            }
            
            ///buscamos el equipo y actualizamos colocando asignado el equipo
            $arrayequipo = explode("|", $row["equipo"]);    
            
            EquipoModel::where('id', 'like', '%' . sprintf("%01d", $arrayequipo[1]) . '%')->update([
                "available" => null,
                "assigned"  => "X"
            ]);
            
            $equipo = EquipoModel::where('id', 'like', '%' . sprintf("%01d", $arrayequipo[1]) . '%')->first();
         
            ///asignamos el equipo a la posocion
            RelationEquiposPosicionesModel::create([
                "inv_posiciones_id" => $posicion->id,
                "inv_equipos_id"    => $equipo->id
             ]);
            
            ///actualizamos el stock del equipo al la sede destino
           RelationEquiposStockModel::where('inv_equipos_id', $equipo->id)->update([
                "inv_stocks_id" => $posicion->sede
            ]); 
            
            if(RelationEquiposResponsableModel::where('inv_equipos_id', $equipo->id)->exists()){                    
                    RelationEquiposResponsableModel::where('inv_equipos_id', $equipo->id)->update([
                      "personal_id" =>  PersonalModel::where('cedula_empleado', $row["responsable"])->first()->id
                    ]);                                        
                } else {
                RelationEquiposResponsableModel::create([
                    "inv_equipos_id"    => $equipo->id,
                    "personal_id"       =>  PersonalModel::where('cedula_empleado', $row["responsable"])->first()->id
                ]);                    
            }
            
            /// sacamos el identificador del monitor
            $arraymonitor = explode("|", $row["monitor"]);     
            ////ubicamos el registro y cambios la asignacion
            MonitorModel::where('id', 'like', '%' . sprintf("%01d", $arraymonitor[1]) . '%')->update([
                "available" => null,
                "assigned"  => "X"
            ]);
            
            $monitor = MonitorModel::where('id', 'like', '%' . sprintf("%01d", $arraymonitor[1]) . '%')->first();
            
            RelationPosicionesMonitorModel::create([
                "inv_posiciones_id" => $posicion->id,
                "inv_monitor_id"    => $monitor->id
            ]);
              
        }   
            //// esto permite validar que sesion flash se mostrara
            if($mensaje != 2){
              \Session::flash('success', __('insert.position.masive.success'));  
            }            
            return true;
            
            } catch (\Throwable $e){               
                   \Session::flash('error', __('ErrorCode') . ' ' . $e->getMessage());
            }
    }
    
    public function rules(): array {
        return [
            "posicion" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Identificador de Posicion es Obligatorio.');
                }
                else {
                    $arraypos = explode("|", $value);                    
                    switch ($arraypos[0]) {
                        case "PLA" : $ab = 2;
                            break;
                        case "SIE" : $ab = 3;
                            break;
                        case "AUT" : $ab = 4;
                            break;
                        case "MAC" : $ab = 5;
                            break ;
                        default:
                           $onFailure('Identificador de la Sede no es Correcto.');
                        break;                    
                    }                   
                    if(!PosicionesModel::where('sede',trim($ab))->where('number', trim($arraypos[1]))->exists()) {
                        $onFailure('La Identificacion de posicion no existe.');
                    } else if (PosicionesModel::where('sede',trim($ab))->where('number', trim($arraypos[1]))->Wherenotnull('assigned')->exists()) {
                        $onFailure('La posicion suministrada esta ocupada.');
                    }
                }
            },
            "gerente_cargo" => function ($attribute, $value, $onFailure) {                
                if (trim($value) == "") {
                    $onFailure('la Cedula del Gerente a Cargo de la posicion es Obligatorio.');
                } else if(!PersonalModel::where('cedula_empleado',$value)->WhereIn('cargo_id',CargoModel::where('nombre_cargo','like','Gerente%')
                ->Orwhere('nombre_cargo','like','Coordinador%')->Orwhere('nombre_cargo','like','Customer Technical%')->pluck('id'))
                ->wherein('estatus_id', [1,7,8,9,10])->exists()) {
                  $onFailure('la cedula suministrada no corresponde a un cargo Gerencial.');
                }
            },
            "responsable" => function ($attribute, $value, $onFailure) {                
                if (trim($value) == "") {
                    $onFailure('la cedula del Responsable del (los) equipo(s) es Obligatoria.');
                } else if(!PersonalModel::where('cedula_empleado',$value)->exists()) {
                  $onFailure('la Cedula del Responsable no se encuentra registrada.');
                }
            },
            "gerencia" => function ($attribute, $value, $onFailure) { 
                if ($value == '') {
                    $onFailure('La Gerencia Es Obligatoria');
                }                 
                if(!AreatrabajoModel::where('nombre_campana','like','%'.$value)->exists()){
                    $onFailure('La Gerencia '.$value.' no existe.');
                }
            },
            "skill" => function ($attribute, $value, $onFailure) { 
                if (count($value) == 0) {
                    $onFailure('Al menos un Skill es Obligatorio');
                }                 
                foreach($value as $skill){                  
                    if($skill != null && !SkillModels::where('nombre_skill','like','%'.ucwords(strtolower($skill)))->exists()){
                         $onFailure('El Skill '.ucwords(strtolower($skill)).' no existe');
                    }
                }
            },
            "periferico" => function ($attribute, $value, $onFailure) { 
                if (count($value) == 0) {
                    $onFailure('Al menos un periferico es Obligatorio');
                }                 
                foreach($value as $key => $periferico){                  
                    $perifericos = explode(' ', $periferico);
                    if(count($perifericos) != 3 ){
                        $valor = $key + 1;                        
                        $onFailure('EL Periferico Nro.'.$valor .' No cumple con el formato requerido');
                    } else {     
                        switch ($perifericos[1]){
                            case "TECLADO": $var = 1;
                                break;
                            case "MOUSE": $var = 2;
                                break;
                            case "HEATSET": $var = 3;
                                break;
                            case "T. VIDEO": $var = 4;
                                break;
                            case "T. INALAMBRICA": $var = 5;
                                break;
                            case "CORNETAS": $var = 6;
                                break;
                            case "ADAPTADORES": $var = 7;
                                break;
                        }                        
                        switch ($perifericos[2]){
                            case "USB": $con = 1;              
                                break;
                            case "PS/2":  $con = 2;           
                                break;
                            case  "JACK3.5mm":  $con = 3;           
                                break;
                            case "PCIE":  $con = 4;           
                                break;
                            case "AGP":  $con = 5;           
                                break; 
                            case "E/S":  $con = 6;           
                                break; 
                        }     
                        switch ($perifericos[0]) {
                            case "ALM": $ab = 1;
                                break;
                            case "PLA" : $ab = 2;
                                break;
                            case "SIE" : $ab = 3;
                                break;
                            case "AUT" : $ab = 4;
                                break;
                            case "MAC" : $ab = 5;
                                break ;                           
                            default:
                               $onFailure('Identificador de Stock usando en el periferico Nro.'.$valor .'no es correcto');
                            break;                    
                        } 
                        $stock = StockModel::find($ab);                      
                        if(!$stock->Perifericos()->where('tipo', $var)->where('conexion', $con)->where('available','X')->exists()){
                            $valor = $key +1;
                            $onFailure('No hay disponibilidad para el Periferico Nro.'.$valor);
                        }
                    }
                }
            },          
            "equipo" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Identificador del Equipo es Obligatorio.');
                }
                $equipo = explode("|", $value);                
                if (count($equipo) == 2) {                  
                    if (!in_array($equipo[0], config('app.TipoEquipo'))) {
                        $onFailure('El Tipo de equipo ' . $equipo[0] . ' no existe.');
                    }                    
                    else if (!EquipoModel::where('id', 'like','%'.sprintf("%01d", $equipo[1]).'%')->whereNull('assigned')->exists()) {
                        $onFailure('El Equipo que intenta asignar no esta disponible');
                    }
                } else {
                    $onFailure('El formato del equipo es incorrecto.');
                }
            },
            "monitor" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Identificador del Monitor es Obligatorio.');
                }
                $monitor = explode("|", $value);                
                if (count($monitor) == 2) {                    
                    if (!MonitorModel::where('id', 'like', '%'.sprintf("%01d", $monitor[1]).'%')->whereNull('assigned')->exists()) {
                        $onFailure('El Monitor que intenta asignar no esta disponible');
                    }
                }
                else {
                    $onFailure('El formato del Monitor es incorrecto.');
                }
            },
        ];
      }
}
