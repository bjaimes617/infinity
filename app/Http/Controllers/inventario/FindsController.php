<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\ComponentesModel;
use App\Models\inventario\PerifericosModel;
use App\Models\inventario\MonitorModel;
use App\Models\inventario\StockModel;
use Illuminate\Support\Facades\DB;
use App\Models\inventario\PosicionesModel;

use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;

class FindsController extends Controller {

    public function autocompletes(Request $request) {

        //dd("aca",$request->all());
        switch ($request->campo) {
            case "tipo":
                ////registro de stocks
                $componente = ComponentesModel::select('tipo')->where('componente', $request->componente)->where('tipo', 'like', '%' . $request->termino . '%')->groupby('tipo')->orderby('tipo', 'ASC')->get();
                $data = array();
                foreach ($componente as $componentes) {
                    $data[] = strtoupper($componentes->tipo);
                }
                return json_encode($data);
                break;
            case "marcamonitor":
                //// autocomplete cuando sea creacion de monitor 
                $marcas = MonitorModel::select('marca')->where('marca', 'like', '%' . $request->termino . '%')->groupby('marca')->orderby('marca', 'ASC')->get();
                $data = array();
                foreach ($marcas as $marca) {
                    $data[] = strtoupper($marca->marca);
                }
                return json_encode($data);
                break;
            case "marca":
                //// autocomplete cuando sea creacion de equipos
                $marcas = EquipoModel::select('marca_pc')->where('marca_pc', 'like', '%' . $request->termino . '%')->groupby('marca_pc')->orderby('marca_pc', 'ASC')->get();
                $data = array();
                foreach ($marcas as $marca) {
                    $data[] = strtoupper($marca->marca_pc);
                }
                return json_encode($data);
                break;
            case "cpu":
                //// autocomplete cuando sea creacion de equipos
                $stock = StockModel::find($request->stock);
                $cpus = $stock->Componentes()->where('componente', "CPU")
                    ->where('available', "X")->orderby('id', 'ASC')->get();
                $data = array();
                $select = array();
                foreach ($cpus as $cpu) {
                    $select["id"] = $cpu->id;
                    $select["descripcion"] = $cpu->id . " | " . strtoupper($cpu->tipo) ." de ". $cpu->generacion." Generacion | ". $cpu->capacidad . " " . $cpu->expresion;
                    $data[] = $select;
                }
                return json_encode([null,$data]);
                break;
            case "ram":             
                $stock = StockModel::find($request->stock);
                $sql = $stock->Componentes()->where('componente', "RAM")->where('available', "X")->orderby('id', 'ASC')->get();
                $data   = array();
                $select = array();
                
                foreach ($sql as $cpu) {
                    $select["id"] = $cpu->id;
                    $select["descripcion"] = $cpu->id. " | Tipo " . strtoupper($cpu->tipo) . " | de " . $cpu->capacidad . " " . $cpu->expresion;;
                    $data[] = $select; 
                }
                  return json_encode([null,$data]);
                break;
            case "disco":               
                //// autocomplete cuando sea creacion de equipos
                $stock = StockModel::find($request->stock);
                $sql = $stock->Componentes()->where('componente', "DISCO")->where('available', "X")->orderby('id', 'ASC')->get();

                $data = array();
                $select=array();
                foreach ($sql as $cpu) {
                    $select["id"] = $cpu->id;
                    $select["descripcion"] = $cpu->id . " | Tipo " . strtoupper($cpu->tipo) . " | de " . $cpu->capacidad . " " . $cpu->expresion;
                    $data[] = $select;                    
                }
                  return json_encode([null,$data]);
                break;
        }
    }   
    
    ///tablas de componenes por stock para la CREAR en equipos
    public function AsignacionComponentesEquipos(Request $request) {
        $row = array();
        $data = array();
        $stock = StockModel::find($request->almacen);
        $stock->load('Componentes');

        $conteos = $stock->Componentes()
        ->select(DB::raw('componente, tipo,capacidad,expresion,'
                        . ' count(assigned) as assigned,'
                        . 'count(available) as available,count(bad) as bad '))
        ->groupby('tipo', 'capacidad', 'expresion')->OrderBy('componente','ASC')->get();

        foreach ($conteos as $comps) {
            switch ($comps->componente) {
                case "RAM":
                    $row["icon"] = $comps->componente;
                    break;
                case "CPU":
                    $row["icon"] = $comps->componente;
                    break;
                case "DISCO":
                    $row["icon"] = $comps->componente;
                    break;
            }
            $row["componente"] = $comps->componente;
            $row["tipo"] = $comps->tipo;
            $row["available"] = $comps->available;
            $row["asignado"] = $comps->assigned;
            $row["bad"] = $comps->bad;
            $row["total"] = $comps->available + $comps->assigned + $comps->bad;
            $row["capacidad"] = $comps->capacidad . ' ' . $comps->expresion;
            $data[] = $row;   
        }        
        
        $ram = $stock->Componentes()->where('componente', "RAM")->where('available', "X")->orderby('id', 'ASC')->get();
        $rams = array();
        $valor = array();
        foreach ($ram as $cpu) {
            $valor["id"] = $cpu->id ;
            $valor["texto"] = $cpu->id . " | Tipo " . strtoupper($cpu->tipo) . " | de " . $cpu->capacidad . " " . $cpu->expresion;
            $rams[] = $valor;
        }   
        
        $cpus = $stock->Componentes()->where('componente', "CPU")->where('available', "X")->orderby('id', 'ASC')->get();                
        $cpuses = array();      
        foreach ($cpus as $cpu) {
            $valor["id"] = $cpu->id  ;
            $valor["texto"] = $cpu->id . " | " . strtoupper($cpu->tipo) ." de ". $cpu->generacion." Generacion | ". $cpu->capacidad . " " . $cpu->expresion;
            $cpuses[] = $valor;                                    
        }  
        
        $disk = $stock->Componentes()->where('componente', "DISCO")->where('available', "X")->orderby('id', 'ASC')->get();                
        $discos = array();      
        foreach ($disk as $cpu) {
            $valor["id"] = $cpu->id  ;
            $valor["texto"] = $cpu->id . " | Tipo " . strtoupper($cpu->tipo) . " | de " . $cpu->capacidad . " " . $cpu->expresion;
            $discos[] = $valor;                                    
        }  
        
        return json_encode(["0"=>$data,"1"=>$rams,"2"=>$cpuses,"3"=>$discos]);
    }
    
    ///tabla de busqueda para ADICIONAR nuevos componentes al equipo
    public function SearchAddComponenteEquip(Request $request) {
        
        //dd($request->all());
        $row = array();
        $data = array();
        $select =array();
        $stock = StockModel::find($request->almacen);
        $stock->load('Componentes');

        $conteos = $stock->Componentes()
        ->select(DB::raw('componente, tipo,capacidad,expresion,'
                        . ' count(assigned) as assigned,'
                        . 'count(available) as available,count(bad) as bad '))
        ->where('componente',$request->type)
        ->groupby('tipo', 'capacidad', 'expresion')->get();

        foreach ($conteos as $comps) {
            switch ($comps->componente) {
                case "RAM":
                    $row["icon"] = $comps->componente;                    
                    break;
                case "CPU":
                    $row["icon"] = $comps->componente;                    
                    break;
                case "DISCO":
                    $row["icon"] = $comps->componente;                    
                    break;
            }
            $row["componente"] = $comps->componente;
            $row["tipo"] = $comps->tipo;
            $row["available"] = '<b style="color:green;">' . $comps->available . '</b>';
            $row["asignado"] = '<b style="color:blue;">' . $comps->assigned . '</b>';
            $row["bad"] = '<b style="color:red;">' . $comps->bad . '</b>';
            //$row["total"] = $comps->available + $comps->assigned + $comps->bad;
            $row["capacidad"] = $comps->capacidad . ' ' . $comps->expresion;
            $data[] = $row;   
        }
        
        switch ($request->type) {           
            case "cpu":
                //// autocomplete cuando sea creacion de equipos
                $stock = StockModel::find($request->almacen);                
                $cpus = $stock->Componentes()->where('componente', "CPU")->where('available', "X")->orderby('id', 'ASC')->get();                
                $select = array();
                $valor = array();                
                foreach ($cpus as $cpu) {
                    $valor["id"] = $cpu->id  ;
                    $valor["texto"] = $cpu->id . " | " . strtoupper($cpu->tipo) ." de ". $cpu->generacion." Generacion | ". $cpu->capacidad . " " . $cpu->expresion;
                    $select[] = $valor;                                    
                } 
                
                break;
            case "ram":               
                $stock = StockModel::find($request->almacen);                
                $sql = $stock->Componentes()->where('componente', "RAM")->where('available', "X")->orderby('id', 'ASC')->get();
                $select = array();
                $valor = array();
                foreach ($sql as $cpu) {
                    $valor["id"] = $cpu->id ;
                    $valor["texto"] = $cpu->id . " | Tipo " . strtoupper($cpu->tipo) . " | de " . $cpu->capacidad . " " . $cpu->expresion;
                    $select[] = $valor;
                }               
                break;
            case "disco":             
                $stock = StockModel::find($request->almacen);
                $sql = $stock->Componentes()->where('componente', "DISCO")->where('available', "X")->orderby('id', 'ASC')->get();
                $select = array();
                $valor = array();
                foreach ($sql as $cpu) {
                    $valor["id"] = $cpu->id;
                    $valor["texto"] = $cpu->id . " | Tipo " . strtoupper($cpu->tipo) . " | de " . $cpu->capacidad . " " . $cpu->expresion;
                    $select[] = $valor;
                }                
                break;
        }
        
        return json_encode(["0"=>$data,"1"=>$select]);
    }
    
    ///tabla dee periferico por stock para la asignacion de posiciones
    public function AsignacionPerifericosPosicion(Request $request) {
       
        $data = array();
        $checks = array();
        $activecheck= array();
        $arregloperiferic = array();
        
        $sock = isset($request->almacen) ? $request->almacen : $request->stock;
        $stock = StockModel::find($sock);    
        $conteos = $stock->Perifericos()->select(DB::raw('tipo,conexion, count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('tipo','conexion')->get();  
        
        $perifericostotales = PerifericosModel::select(DB::raw('tipo, conexion, count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('tipo','conexion')->get();
        
        foreach($perifericostotales as $arreglos){
           $arregloperiferic[$arreglos->tipo.'_'.$arreglos->getrawOriginal('conexion')] = 'disabled';
        }
        //dd($arregloperiferic);
        foreach ($conteos as $comps) {
            $row["tipo"]        = $comps->tipo;
            $row["conexion"]    = $comps->conexion;
            $row["available"]   = '<b style="color:green;">' . $comps->available . '</b>';
            $row["asignado"]    = '<b style="color:blue;">' . $comps->assigned . '</b>';
            $row["bad"]         = '<b style="color:red;">' . $comps->bad . '</b>';
            $row["total"]       = $comps->assigned + $comps->available + $comps->bad;
           
          // dd(array_key_exists($comps->tipo.'_'.$comps->getrawOriginal('conexion'),$arregloperiferic),$comps->tipo.'_'.$comps->getrawOriginal('conexion'));
           if (array_key_exists($comps->tipo . '_' . $comps->getrawOriginal('conexion'), $arregloperiferic)) {
                if ($comps->available == 0) {
                    $arregloperiferic[$comps->tipo . '_' . $comps->getrawOriginal('conexion')] = "disabled";
                }
                else {
                    $arregloperiferic[$comps->tipo . '_' . $comps->getrawOriginal('conexion')] = "active";
                }
            }

            $data[] = $row;
        }
       // dd($activecheck);
        return json_encode([$data,$arregloperiferic]);
    }

    public function AsignacionEquiposPosicion(Request $request){
        
        $row = array();
        $data = array();
        
        $dispos =array();
        $corrida = array();
        
        $sede = isset($request->sede) ? $request->sede : $request->stock;
        
        $stock = StockModel::find($sede); 
        
        $a = $stock->Equipos()->select('tipo',DB::raw('count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('tipo')->get();
        
        $equiposdisponibles = $stock->Equipos()->where('available','X')->get();
        
        foreach($a as $equipo){
            $row["tipo"] = $equipo->tipo;
            $row["available"]   = '<b style="color:green;">' . $equipo->available . '</b>';
            $row["asignado"]    = '<b style="color:blue;">' . $equipo->assigned . '</b>';
            $row["bad"]         = '<b style="color:red;">' . $equipo->bad . '</b>';
            $row["total"] = $equipo->assigned + $equipo->available + $equipo->bad; 
            //dd($row);
            $data[] = $row;
        }
        
        $disco = null;
        $cpu = null;
        
        foreach($equiposdisponibles as $equips){
           
            $corrida["id"] = $equips->id; 
            
            foreach($equips->EquiposAComponentes as $componente){                                
            switch ($componente->componente) {
            case "DISCO": $disco = $componente->tipo." ".$componente->capacidad." ".$componente->expresion;
                break;            
            case "CPU": $cpu = $componente->tipo." ".$componente->capacidad."".$componente->expresion." ".$componente->generacion." Generacion";
                break;
            case 3: $val = "Server";
                break;
            default:
                $val = '';
                break;
        } 
            }
            
            $corrida["descripcion"] = $equips->tipo.'|'.sprintf("%05d", $equips->id).'| '.$equips->marca_pc.' | '.$disco.' | '.$cpu;
            $dispos[] = $corrida;
            //dd($dispos);
        }
        
        return json_encode([$data,$dispos]);
    }
    
    public function GetMapSVGSede(Request $request) {
        ///funcion para ubicar el mapa par aasignar las posiciones
        $pos = PosicionesModel::where('sede',$request->sede)->get();
        $type = "one";
        if($pos->Isempty()){
            return json_encode(abort(500, "Lo sentimos, el Mapa no se encuentra disponible."));
        } else {
            switch ($request->sede){
                case 2:
                    $mapa = view('inventario.mapas.plaza.plaza',["posicion"=>$pos,"type"=>$type])->render();
                    break;
                case 3: 
                     $mapa = view('inventario.mapas.siemens.siemens',["posicion"=>$pos,"type"=>$type])->render();
                    break;
                case 4:
                    $mapa = view('inventario.mapas.autana.autana',["posicion"=>$pos,"type"=>$type])->render();
                    break;
                case 5:
                    $mapa = view('inventario.mapas.macaracuay.macaracuay',["posicion"=>$pos,"type"=>$type])->render();
                    break;
                
            }
            //dd($mapa);
            return json_encode($mapa);
        }
        
    }

    public function SelectAreasPos(Request $request) {
        try {
            $cartera = CarteraModel::select('id', 'nombre_cartera')
                            ->where('area_trabajo_id', $request->area)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();        
            
        $cargos = CargoModel::where('nombre_cargo','like','Gerente%')->pluck('id');  
        $gerentes = PersonalModel::where('area_trabajo_id',$request->area)
                ->WhereIn('cargo_id',$cargos)
                ->wherein('estatus_id', [1,7,8,9,10])->get();        
        $row = $cartera;
            return json_encode([$row,$gerentes]);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }    
}
