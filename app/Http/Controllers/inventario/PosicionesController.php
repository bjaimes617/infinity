<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\inventario\MonitorModel;
use App\Models\inventario\StockModel;
use App\Models\inventario\PerifericosModel;
use App\Models\inventario\PosicionesModel;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\RelationPosicionesMonitorModel;
use App\Models\inventario\RelationEquiposPosicionesModel;
use App\Models\inventario\RelationEquiposStockModel;
use App\Models\inventario\RelationPosicionesPerifericosModel;
use App\Models\inventario\RelationEquiposResponsableModel;
use App\Models\inventario\RelationPerifericosStockModel;
use App\Models\inventario\RelationPosicionCarterasModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;
use App\Models\personal\CarteraModel;

class PosicionesController extends Controller {

    public function __construct() {
        $this->periferico = config('app.Perifericos');
    }

    private function encryptNumbers($data) {
        // Encriptar el dato
        $encryptedData = encrypt($data);
        // Filtrar solo los números
        //$filteredData = preg_replace('/[^0-9]/', '', $encryptedData);
        return $encryptedData;
    }

    private function decryptNumbers($encryptedData) {
        // Desencriptar el dato
        try {
            $decryptedData = decrypt($encryptedData);
            return $decryptedData;
        } catch (\exception $e) {
            return abort(403, "Lo sentimos, no podemos acceder al recurso que solicitas.");
        }
    }

    public function index() {
        $stock = StockModel::wherenotin('id', [1, 6])->get();
        return view('inventario.posiciones.index')->with(["stock" => $stock])->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $cargos = CargoModel::where('estatus_id',1)->orderby('nombre_cargo','ASC')->get();
        $monitor = MonitorModel::select(DB::raw('marca, count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('marca')->get();
        $disponibles = MonitorModel::where('available', "X")->get();
        $perifericos = PerifericosModel::select('tipo','conexion')->groupby('tipo','conexion')->get();        
        $sede = StockModel::WhereNotIn('id', [6])->get();
        $areas = AreatrabajoModel::where('estatus_id', 1)->get();
        return view('inventario.posiciones.create')->with(["monitor" => $monitor,
                    "perifericos" => $perifericos,
                    "disponibles" => $disponibles,
                    "sede" => $sede,
                    "areas" => $areas,
                    "cargo"=>$cargos
                ])->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        try{
        foreach ($request->posicionsel as $posicion) {
            $pos = PosicionesModel::find($posicion);
            $pos->assigned = "X";
            $pos->tipo = $request->tipo;
            $pos->gerente_id = $request->gerentecargo;           
            $pos->cargo_id = $request->cargo;
            $pos->etiqueta = 1; 
            $pos->save();
            
            //skill
            foreach($request->skill as $skill){
                $dbskill = SkillModels::find($skill);
                
                $relacarteras = new RelationPosicionCarterasModel();
                $relacarteras->inv_posiciones_id = $posicion;
                $relacarteras->skill_id         = $skill;
                $relacarteras->cartera_id       = $dbskill->cartera_id;
                $relacarteras->area_trabajo_id  = CarteraModel::find($dbskill->cartera_id)->area_trabajo_id;
                $relacarteras->ceco             = $dbskill->cod_ceco;
                $relacarteras->save();
            }
            //periferico
            foreach ($request->nombreinputsperifericos as $inputs) {
                    //extraemos el nombre y la conexion 0 para nombre 1 para conexion
                    $nombre = explode('_', $inputs);
                    ///validamos si el checkbox esta ON
                    if ($request->input('new' . strtolower($nombre[0]) . '_' . $nombre[1]) == "on") {
                        $stock = StockModel::find($request->stockperiferico);
                        ///ubicamos el ID del periferico
                        $tipoperi = array_search(strtoupper($nombre[0]), config('app.Perifericos'));
                        /// sacamos el registro y cambios a asignacion
                        $primerperiferico = $stock->Perifericos()->where('tipo', $tipoperi)->where('conexion', $nombre[1])->where('available', 'X')->first();
                        $primerperiferico->available = null;
                        $primerperiferico->assigned = 'X';
                        $primerperiferico->save();

                        ///cambiamos de stock el periferico
                        $relation = RelationPerifericosStockModel::where('inv_perifericos_id', $primerperiferico->id)
                                        ->where('inv_stocks_id', $request->stockperiferico)->first();
                        $relation->inv_stocks_id = $request->mapsede;
                        $relation->save();

                        //actualizamos y almacenamos a la posicion el primer resultado encontrado
                        $relac = new RelationPosicionesPerifericosModel();
                        $relac->inv_perifericos_id = $primerperiferico->id;
                        $relac->inv_posiciones_id = $posicion;
                        $relac->save();
                    }
                }
            //monitor
            foreach ($request->monitor as $monitores) {
                $monitor = MonitorModel::find($monitores);
                $monitor->assigned = 'X';
                $monitor->available = null;
                $monitor->save();

                $relacion = new RelationPosicionesMonitorModel();
                $relacion->inv_monitor_id = $monitor->id;
                $relacion->inv_posiciones_id = $posicion;
                $relacion->save();
            }
            //equipo
            foreach ($request->equipos as $equipo) {

                $equip = EquipoModel::find($equipo);
                $equip->assigned = 'X';
                $equip->available = null;
                $equip->save();

                $relaequipos = new RelationEquiposPosicionesModel();
                $relaequipos->inv_posiciones_id = $posicion;
                $relaequipos->inv_equipos_id = $equipo;
                $relaequipos->save();

                $relaequistok = RelationEquiposStockModel::where('inv_equipos_id', $equipo)->first();
                $relaequistok->inv_stocks_id = $request->mapsede;
                $relaequistok->save();

                $responsable = new RelationEquiposResponsableModel();
                $responsable->inv_equipos_id = $equipo;
                $responsable->personal_id = PersonalModel::where('cedula_empleado', $request->responsable)->first()->id;
                $responsable->save();
            }
        }
        \Session::flash('success', "Registro e Identificacion de la Posición realizada correctamente ");
        return redirect()->back();
         } catch (\Exception $e) {
          \Session::flash('error', __('ErrorCode'). $e->getMessage());
          return redirect()->back();
          }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request) {
        $row = array();
        $json = array();
        $monitor = array();
        $perifericos = array();
        $identificacionmap = array();

        if (!empty($request->stock)) {
            $posiciones = PosicionesModel::where('sede', $request->stock)->get();
            //$posiciones = PosicionesModel::where('id', 3)->get();
           // $posiciones->load('ToCartera', 'ToSkill', 'PosicionEquipo', 'PosicionMonitores');
            foreach ($posiciones as $pos) {                 
                switch ($pos->sede) {
                    case 2 : $ab = "PLA";
                        break;
                    case 3 : $ab = "SIE";
                        break;
                    case 4 : $ab = "AUT";
                        break;
                    case 5 : $ab = "MAC";
                        break;
                }                
                $row["posicion"] = $ab . '|' . $pos->number;
                
                if (!$pos->ToSkill->isEmpty()) {
                    foreach ($pos->ToSkill as $skills) {
                        $skill[] = $skills->nombre_skill;
                        $cartera[] = $skills->RelationCarteras->nombre_cartera;
                        $gerencia = $skills->RelationCarteras->RelationGerencias->nombre_campana;
                    }
                    $row["skill"] = implode(" & ", $skill);
                    $row["cartera"] = implode(" & ", $cartera);
                    $row["gerencia"] = $gerencia;
                    $skill = null;
                    $cartera = null;
                    $gerencia = null;
                }
                else {
                    $row["skill"] = "S/A";
                    $row["cartera"] = "S/A";
                    $row["gerencia"] = "S/A";
                }

                $row["area"] = $pos->area;                
                $row["responsable"] = $pos->PosicionEquipo != null ? 
                        ($pos->PosicionEquipo->equipo->EquipoAResponsable != null ?  
                         $pos->PosicionEquipo->equipo->EquipoAResponsable->responsable->primer_nombre . ' ' . $pos->PosicionEquipo->equipo->EquipoAResponsable->responsable->primer_apellido : "S/A") 
                    : 'S/A';
            

                if (!$pos->PosicionEquipos->isEmpty()) {
                    ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionEquipos as $equipo) {
                        $equip[] = $equipo->tipo . "|" . sprintf("%05d", $equipo->id);
                        $ip[] = $equipo->ip;
                    }
                   $row["equipo"]   = implode(" & ", $equip);
                   $row["ip"]       = implode(" & ", $ip);
                    $equip = null;
                    $ip = null;
                } else {
                    $row["equipo"] = "S/A";
                    $row["ip"] = "S/A";
                }
                
                if (!$pos->PosicionMonitores->isEmpty()) {
                    ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionMonitores as $monitores) {
                        $monitor[] = "MON|" . sprintf("%05d", $monitores->id) . "|" . $monitores->marca . "|" . $monitores->pulgadas . '"';
                    }
                    $row["monitor"] = implode(" & ", $monitor);
                    $monitor = null;
                } else {
                    $row["monitor"] = "S/A";
                }

                if (!$pos->PosicionPerifericos->isEmpty()) {
                    foreach ($pos->PosicionPerifericos as $periferic) {
                        switch ($periferic->getrawOriginal('tipo')) {
                            case 1: $var = '<i class="fas fa-keyboard"></i>';
                                break;
                            case 2: $var = '<i class="fas fa-mouse"></i>';
                                break;
                            case 3: $var = '<i class="fas fa-headset"></i>';
                                break;
                            case 4: $var = '<i class="fas fa-digital-tachograph"></i>';
                                break;
                            case 5: $var = '<i class="fas fa-wifi"></i>';
                                break;
                            case 6: $var = '<i class="fas fa-volume-off"></i>';
                                break;
                        }
                        $perifericos[] = $var;
                    }
                    $row["periferico"] = implode(" ", $perifericos);
                    $perifericos = null;
                } else {
                    $row["periferico"] = "S/A";
                }                
                //$disabled   = $pos->assigned == null ? 'style=" cursor: not-allowed;pointer-events: none;" ' : '';      
                $id = $this->encryptNumbers($pos->id);
                if($pos->migration == 1){
                    $url= "#";
                    $eventcolor = 'btn-danger';
                    $texto      ="Bloqueada por Migracion";
                    $icon       = '<i class="fas fa-ban"></i>';
                    $disabled   = 'style=" cursor: not-allowed;pointer-events: none;" ';
                  
                } else {
                    $url = route('inventario.edit.posiciones', $id);
                    $eventcolor = $pos->assigned == null ? 'btn-secondary':'btn-primary';;
                    $texto="Modificar";
                    $icon= '<i class="fas fa-edit"></i>';
                    $disabled   = $pos->assigned == null ? 'style=" cursor: not-allowed;pointer-events: none;" ' : '';
                    
                } 
                
                $row["acciones"] = '<div class="btn-group float-center">
                    <a href="#" class="btn btn-sm btn-primary" onmouseenter="$(\'polyline[id=' . $pos->id . ']\').tooltip(\'show\');" onmouseleave="$(\'polyline[id=' . $pos->id . ']\').tooltip(\'hide\');" data-toggle="tooltip" data-placement="top" title="Ubicar"><i class="fas fa-map-marked"></i></button>                                                                                                           
                    <a href="'.$url.'" class="btn btn-sm ' . $eventcolor . '"  ' . $disabled . ' data-toggle="tooltip" data-placement="top" title="'.$texto.'">'.$icon.'</button>                                                                                                           
                </div>';

                ///Para mostrar en el mapa
                $identificacionmap[$pos->id] = array();
                $identificacionmap[$pos->id][1] = $row["posicion"];     // Nro posicion
                $identificacionmap[$pos->id][2] = 'Gerencia: ' . $row["gerencia"]; // Gerencia
                $identificacionmap[$pos->id][3] = 'Cartera: ' . $row["cartera"];  // 
                $identificacionmap[$pos->id][4] = 'Skill: ' . $row["skill"];
                $identificacionmap[$pos->id][5] = 'Area: ' . $pos->area;
                $identificacionmap[$pos->id][9] = 'IP-LAN: ' . $row["ip"];
                $identificacionmap[$pos->id][7] = $ide[$pos->id] = implode("\n", $identificacionmap[$pos->id]);
                $json[] = $row;                
               // $row = null;
            }
            //dd($ide);
            $active = true;
        } else {
            ///para busqueda por un solo criterio
            $arrayequip = explode("|", $request->posicion);
            if (count($arrayequip) == 1) {
                $posiciones = PosicionesModel::where('number',$arrayequip)->get();
                foreach ($posiciones as $pos) {
                    
                    switch ($pos->sede) {
                        case 2 : $ab = "PLA";
                            break;
                        case 3 : $ab = "SIE";
                            break;
                        case 4 : $ab = "AUT";
                            break;
                        case 5 : $ab = "MAC";
                            break;
                    }
                    
                    if (!$pos->PosicionPerifericos->isEmpty()) {
                        foreach ($pos->PosicionPerifericos as $periferic) {
                            switch ($periferic->getrawOriginal('tipo')) {
                                case 1: $var = '<i class="fas fa-keyboard"></i>';
                                    break;
                                case 2: $var = '<i class="fas fa-mouse"></i>';
                                    break;
                                case 3: $var = '<i class="fas fa-headset"></i>';
                                    break;
                                case 4: $var = '<i class="fas fa-digital-tachograph"></i>';
                                    break;
                                case 5: $var = '<i class="fas fa-wifi"></i>';
                                    break;
                                case 6: $var = '<i class="fas fa-volume-off"></i>';
                                    break;
                            }
                            $perifericos[] = $var;
                        }
                        $row["periferico"] = implode(" ", $perifericos);
                        $perifericos = null;
                    } else {
                        $row["periferico"] = "S/A";
                    }

                    if (!$pos->PosicionEquipos->isEmpty()) {
                        ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                        foreach ($pos->PosicionEquipos as $equipo) {
                            $equip[] = $equipo->tipo . "|" . sprintf("%05d", $equipo->id);
                            $ip[] = $equipo->ip;
                        }
                        $row["equipo"] = implode(" & ", $equip);
                        $row["ip"] = implode(" & ", $ip);
                        $equip = null;
                        $ip = null;
                    } else {
                        $row["equipo"] = "S/A";
                        $row["ip"] = "S/A";
                    }

                    if (!$pos->PosicionMonitores->isEmpty()) {
                        ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                        foreach ($pos->PosicionMonitores as $monitores) {
                            $monitor[] = "MON|" . sprintf("%05d", $monitores->id) . "|" . $monitores->marca . "|" . $monitores->pulgadas . '"';
                        }
                        $row["monitor"] = implode(" & ", $monitor);
                        $monitor = null;
                    } else {
                        $row["monitor"] = "S/A";
                    }

                    $row["posicion"] = $ab . '|' . $pos->number;
                    $row["responsable"] = $pos->PosicionEquipo != null ? $pos->PosicionEquipo->equipo->EquipoAResponsable->responsable->primer_nombre . ' ' . $pos->PosicionEquipo->equipo->EquipoAResponsable->responsable->primer_apellido : 'S/A';
                                        
                    $id = $this->encryptNumbers($pos->id);
                    if ($pos->migration == 1) {
                        $url = "#";
                        $eventcolor = 'btn-danger';
                        $texto = "Bloqueada por Migracion";
                        $icon = '<i class="fas fa-ban"></i>';
                        $disabled = 'style=" cursor: not-allowed;pointer-events: none;" ';
                    } else {
                        $url = route('inventario.edit.posiciones', $id);
                        $eventcolor = $pos->assigned == null ? 'btn-secondary':'btn-primary';
                        $texto = "Modificar";
                        $icon = '<i class="fas fa-edit"></i>';
                        $disabled = $pos->assigned == null ? 'style=" cursor: not-allowed;pointer-events: none;" ' : '';
                    }
                    
                    $row["acciones"] = '<div class="btn-group float-center">                    
                    <a href="' . $url . '" class="btn btn-sm ' . $eventcolor . '"  ' . $disabled . ' data-toggle="tooltip" data-placement="top" title="' . $texto . '">' . $icon . '</button>                                                                                                           
                </div>';
                    $json[] = $row;
                }
            } else {
                switch ($arrayequip[0]) {
                    case "PLA" : $ab = 2;
                        break;
                    case "SIE" : $ab = 3;
                        break;
                    case "AUT" : $ab = 4;
                        break;
                    case "MAC" : $ab = 5;
                        break;
                    default:
                        return json_encode(abort(500,"El Identificador de la posición ingresada es incorrecto."));
                        break;
                }
                
                $pos = PosicionesModel::where('sede',$ab)->where('number',$arrayequip[1])->first();
                
                switch ($pos->sede) {
                        case 2 : $ab = "PLA";
                            break;
                        case 3 : $ab = "SIE";
                            break;
                        case 4 : $ab = "AUT";
                            break;
                        case 5 : $ab = "MAC";
                            break;
                    }
                    
                    if (!$pos->PosicionPerifericos->isEmpty()) {
                    foreach ($pos->PosicionPerifericos as $periferic) {
                        switch ($periferic->getrawOriginal('tipo')) {
                            case 1: $var = '<i class="fas fa-keyboard"></i>';
                                break;
                            case 2: $var = '<i class="fas fa-mouse"></i>';
                                break;
                            case 3: $var = '<i class="fas fa-headset"></i>';
                                break;
                            case 4: $var = '<i class="fas fa-digital-tachograph"></i>';
                                break;
                            case 5: $var = '<i class="fas fa-wifi"></i>';
                                break;
                            case 6: $var = '<i class="fas fa-volume-off"></i>';
                                break;
                        }
                        $perifericos[] = $var;
                    }
                    $row["periferico"] = implode(" ", $perifericos);
                    $perifericos = null;
                } else {
                    $row["periferico"] = "S/A";
                }

                if (!$pos->PosicionEquipos->isEmpty()) {
                    ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionEquipos as $equipo) {
                        $equip[] = $equipo->tipo . "|" . sprintf("%05d", $equipo->id);
                        $ip[] = $equipo->ip;
                    }
                    $row["equipo"] = implode(" & ", $equip);
                    $row["ip"] = implode(" & ", $ip);
                    $equip = null;
                } else {
                    $row["equipo"] = "S/A";
                    $row["ip"] = "S/A";
                }

                if (!$pos->PosicionMonitores->isEmpty()) {
                    ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionMonitores as $monitores) {
                        $monitor[] = "MON|" . sprintf("%05d", $monitores->id) . "|" . $monitores->marca . "|" . $monitores->pulgadas . '"';
                    }
                    $row["monitor"] = implode(" & ", $monitor);
                    $monitor = null;
                } else {
                    $row["monitor"] = "S/A";
                }

                $row["posicion"] = $ab . '|' . $pos->number;
                $row["responsable"] = $pos->PosicionEquipo != null ? $pos->PosicionEquipo->equipo->EquipoAResponsable->responsable->primer_nombre . ' ' . $pos->PosicionEquipo->equipo->EquipoAResponsable->responsable->primer_apellido : 'S/A';

                $id = $this->encryptNumbers($pos->id);
                if ($pos->migration == 1) {
                    $url = "#";
                    $eventcolor = 'btn-danger';
                    $texto = "Bloqueada por Migracion";
                    $icon = '<i class="fas fa-ban"></i>';
                    $disabled = 'style=" cursor: not-allowed;pointer-events: none;" ';
                } else {
                    $url = route('inventario.edit.posiciones', $id);
                    $eventcolor = $pos->assigned == null ? 'btn-secondary' : 'btn-primary';
                    $texto = "Modificar";
                    $icon = '<i class="fas fa-edit"></i>';
                    $disabled = $pos->assigned == null ? 'style=" cursor: not-allowed;pointer-events: none;" ' : '';
                }

                $row["acciones"] = '<div class="btn-group float-center">                    
                    <a href="' . $url . '" class="btn btn-sm ' . $eventcolor . '"  ' . $disabled . ' data-toggle="tooltip" data-placement="top" title="' . $texto . '">' . $icon . '</button>                                                                                                           
                </div>';
            }   
            $json[] = $row;
            $active = false;
        }

        if ($active) {
            switch ($request->stock) {
                case 2:
                    $mapa = view('inventario.mapas.plaza.plaza')->with(["type" => "all", "posicion" => $posiciones, "ident" => $ide])->render();
                    break;
                case 3:
                    $mapa = view('inventario.mapas.siemens.siemens')->with(["type" => "all", "posicion" => $posiciones, "ident" => $ide])->render();
                    break;
                case 4:
                    $mapa = view('inventario.mapas.autana.autana')->with(["type" => "all", "posicion" => $posiciones, "ident" => $ide])->render();
                    break;
                case 5:
                    $mapa = view('inventario.mapas.macaracuay.macaracuay')->with(["type" => "all", "posicion" => $posiciones, "ident" => $ide])->render();
                    break;
            }
        } else {
            $mapa = null;
        }
        
        return json_encode([$json, $mapa, $active]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($isd) {
              
        $sede = StockModel::WhereNotIn('id', [6])->get();        
        $id = $this->decryptNumbers($isd);
        
        $posicion = PosicionesModel::find($id)->load('PosicionEquipo', 'PosicionMonitores');
        $cargos= CargoModel::where('estatus_id',1)->orderby('nombre_cargo','ASC')->get();

        ///validamos si hay monitores asignados si no enviamos valores para la tabla para añadir.
        $tablemonitor = MonitorModel::select(DB::raw('marca, count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('marca')->get();

        $monitor = MonitorModel::where('available', "X")->get(); 
               
        ///array de todas las areas
        $areas = AreatrabajoModel::where('estatus_id', 1)->get();
        // unico skil asignado a la posicion
        $gerente = PersonalModel::find($posicion->gerente_id);
        //dd($id,$posicion->RelationSkillsAndCartera);
        if(!$posicion->RelationSkillsAndCartera->isEmpty()){
            $onearea = $posicion->RelationSkillsAndCartera[0]->area_trabajo_id;
        } else {
            $onearea = null;
        }
        $cambios = ["1"=>"SI","2"=>"NO"];
        //dd($onearea);
        ///para añadir nuevos perifericos a la posicion
        $perifericos = PerifericosModel::select('tipo','conexion')->groupby('tipo','conexion')->get();     
        
        return view('inventario.posiciones.edit')->with([
            "posicion" => $posicion,
            "sede" => $sede,                    
            "tablemonitor" => $tablemonitor,
            "disponibles" => $monitor,
            "areas" => $areas,
            "newperifericos"=>$perifericos,
            "gerente"=>$gerente,
            "onearea"=>$onearea,
            "cargo"=>$cargos,
            "cambios"=>$cambios
            ])->render();
    }

    public function UpdateSyncStatus($type, $id, Request $request) {
      
        try {
            switch ($type) {
                case "monitor":
                    $monitor = MonitorModel::find($id);
                    if ($request->estatus == "true") {
                        $monitor->assigned = "X";
                        $monitor->bad = null;
                    } else {
                        $monitor->assigned = null;
                        $monitor->bad = "X";
                    }
                    $monitor->save();
                    return json_encode('Estatus del monitor actualizado Correctamente');
                    break;
                case "equipo":
                    $equipo = EquipoModel::find($id);
                    if ($request->estatus == "true") {
                        $equipo->assigned = "X";
                        $equipo->bad = null;
                    } else {
                        $equipo->assigned = null;
                        $equipo->bad = "X";
                    }
                    $equipo->save();
                    return json_encode('Estatus del equipo actualizado Correctamente');
                    break;
                default:
                    $periferico = PerifericosModel::find($id);
                    if ($request->estatus == "true") {
                        $periferico->assigned = "X";
                        $periferico->bad = null;
                    } else {
                        $periferico->assigned = null;
                        $periferico->bad = "X";
                    }
                    $periferico->save();
                    return json_encode('Estatus del Periferico actualizado Correctamente');
                    break;
            }
        } catch (\Exception $e) {
            return json_encode(abort(500, __('ErrorCode'). $e->getMessage()));
        }
    }
    ///funcion de remplazo en la posicion
    public function UpdateContentPosition($position, $type, Request $request) { 
          
        $llave = $request->keyinput;        
        switch ($type) {
            case"equipo":
                
               $arrayequip = explode("|", $request->input('equipo'.$llave));
                
                $equip = EquipoModel::where('id', 'like', '%' . sprintf("%01d", $arrayequip[1]) . '%')->first();

                if ($request->input('new'.$llave) != null) {
                    ///relacion de posicion a equipo
                    
                    if(RelationEquiposPosicionesModel::where('inv_posiciones_id', $position)->where('inv_equipos_id',sprintf("%01d", $arrayequip[1]))->exists()){
                    
                        $detach = RelationEquiposPosicionesModel::where('inv_posiciones_id', $position)
                            ->where('inv_equipos_id',sprintf("%01d", $arrayequip[1]))->first();   
                        $detach->inv_equipos_id = $request->input('new'.$llave);
                        $detach->save();
                    } else {
                        $atach = new RelationEquiposPosicionesModel();
                        $atach->inv_equipos_id = $request->input('new'.$llave);
                        $atach->inv_posiciones_id = $position;
                        $atach->save();
                    }
                   
                    
                    if($equip->EquipoAResponsable != null){
                        $equip->EquipoAResponsable->delete();
                    }       
                    
                    /// coleccion equipo
                    $newmon = EquipoModel::find($request->input('new'.$llave));
                    $newmon->assigned = "X";
                    $newmon->available = null;
                    $newmon->save();
                    
                    $responsable = new RelationEquiposResponsableModel();
                    $responsable->personal_id       = PersonalModel::where('cedula_empleado', $request->input('responsable'.$llave))->first()->id;
                    $responsable->inv_equipos_id    = $request->input('new'.$llave);
                    $responsable->save();
                    
                    //actualizacion del stock del equipo
                    $updaterelac = RelationEquiposStockModel::where('inv_equipos_id',$newmon->id)->first();
                    $updaterelac->inv_stocks_id = $request->input('stock'.$llave);
                    $updaterelac->save();
                                        
                    $mensaje = "Nuevo equipo Asignado Correctamente";
                } else {
                    $equip->etiqueta = $request->input('etiqueta'.$llave);
                    $equip->save();
                    $mensaje = "Parametro del Equipo actualizado Correctamente";
                }              

                break;
            case"monitor":  
                
                $arrayequip = explode("|", $request->input('monitor'.$llave));
                
                ///este es para actualizar la etiqueda del monitor actual asignado a la posicion
                $mon = MonitorModel::where('id', 'like', '%' . sprintf("%01d", $arrayequip[1]) . '%')->first();
                
                if ($request->input('new'.$llave) != null) {
                    
                    $detach = RelationPosicionesMonitorModel::where('inv_posiciones_id',$position)
                    ->where('inv_monitor_id', $arrayequip[1])->first();
                    $detach->inv_monitor_id = $request->input('new'.$llave);
                    $detach->save();
                    
                    //este para actualizar el monitor asignado.
                    $newmon = MonitorModel::find($request->input('new'.$llave));
                    $newmon->assigned = "X";
                    $newmon->available = null;
                    $newmon->save();

                    $mensaje = "Nuevo monitor Asignado Correctamente";
                } else {
                    $mon->etiqueta = $request->input('etiqueta'.$llave);
                    $mon->save();
                    $mensaje = "Parametro del monitor actualizado Correctamente";
                }               
                break;
            default:
                $periferic = PerifericosModel::find($request->input('Periferico'));
                //dd($periferic);
                if (StockModel::find($request->input('stock'.$llave))->Perifericos()
                        ->where('tipo', $periferic->getrawOriginal('tipo'))
                        ->where('conexion', $periferic->getrawOriginal('conexion'))
                        ->where('available', "X")->exists()) {
                    ///ubicamos el nuevo periferico a asignar
                    $newperi = StockModel::find($request->input('stock'.$llave))->Perifericos() 
                            ->where('conexion', $periferic->getrawOriginal('conexion'))
                            ->where('tipo', $periferic->getrawOriginal('tipo'))
                            ->where('available', "X")->first();
                    
                    $newperi->assigned = "X";
                    $newperi->available = null;
                    $newperi->save();
                    
                    ///ubicamos la posicion
                    $posicion = PosicionesModel::find($position);
                    //ubicamos la relacion del periferico con la relacion y actualizamos el conteo de BAD se hace primero en el camibo de estatus
                    $relac = $posicion->PosicionRelacionPerifericos('inv_perifericos_id', $request->input('Periferico'))->first();
                    $relac->inv_perifericos_id = $newperi->id;
                    $relac->save();
                    
                    ///cambiamos el periferico de stock
                    $changestock = RelationPerifericosStockModel::where('inv_perifericos_id',$request->input('Periferico'))->first();
                    $changestock->inv_stocks_id = $posicion->sede;
                    $changestock->save();       
                            
                    return json_encode('Nuevo Periferico Asignado Correctamente');
                } else {
                    return json_encode(abort(500, "No hay Disponibilidad para Remplazar el periferico seleccionado."));
                }
                break;
        }

        return json_encode($mensaje);
    }
    
    public function AddNewPerifericoModalPosicion($type, Request $request) {
        
        try {
            switch ($type) {
                case "periferico":
                    $posicion = PosicionesModel::find($request->modalposition_addperiferic);                    
                    foreach ($request->nombreinputsperifericos as $inputs) {
                    //extraemos el nombre y la conexion 0 para nombre 1 para conexion
                    $nombre = explode('_', $inputs);
                    ///validamos si el checkbox esta ON
                    if ($request->input('new' . strtolower($nombre[0]) . '_' . $nombre[1]) == "on") {
                        $stock = StockModel::find($request->stockperiferico);
                        ///ubicamos el ID del periferico
                        $tipoperi = array_search(strtoupper($nombre[0]), config('app.Perifericos'));
                        //dd($tipoperi);
                        /// sacamos el registro y cambios a asignacion
                        $primerperiferico = $stock->Perifericos()->where('tipo', $tipoperi)->where('conexion', $nombre[1])->where('available', 'X')->first();
                        $primerperiferico->available = null;
                        $primerperiferico->assigned = 'X';
                        $primerperiferico->save();
                        
                        ///cambiamos de stock el periferico
                        $relation = RelationPerifericosStockModel::where('inv_perifericos_id', $primerperiferico->id)
                        ->where('inv_stocks_id', $request->stockperiferico)->first();                        
                        $relation->inv_stocks_id = $posicion->sede;
                        $relation->save();

                        //actualizamos y almacenamos a la posicion el primer resultado encontrado
                        $relac = new RelationPosicionesPerifericosModel();
                        $relac->inv_perifericos_id = $primerperiferico->id;
                        $relac->inv_posiciones_id = $posicion->id;
                        $relac->save();
                    }
                }
                        return json_encode("Nuevo periferico asignado Correctamente");
                    break;
                case "monitor":                   
                        foreach ($request->monitor as $monitores) {
                            $monitor = MonitorModel::find($monitores);
                            $monitor->assigned = 'X';
                            $monitor->available = null;
                            $monitor->save();

                            $relacion = new RelationPosicionesMonitorModel();
                            $relacion->inv_monitor_id   = $monitor->id;
                            $relacion->inv_posiciones_id = $request->modalposition_addperiferic;
                            $relacion->save();
                        }
                        return json_encode("Nuevo Monitor asignado Correctamente");
                    
                    break;
                case "equipo":
                
                $equip = EquipoModel::find($request->equipos);
                $equip->assigned = 'X';
                $equip->available = null;
               
                
                $posi = PosicionesModel::find($request->modalposition_addperiferic);
              //dd($request->all());
                
                if(RelationEquiposStockModel::where('inv_equipos_id', $equip->id)->where('inv_posiciones_id',$posi->id)->exists()){
                    $relaequipos = RelationEquiposPosicionesModel::where('inv_equipos_id', $equip->id)->first();
                    $relaequipos->inv_posiciones_id = $posi->id;
                    $relaequipos->inv_equipos_id = $equip->id;
                } else {
                    $relaequipos = new RelationEquiposPosicionesModel();
                    $relaequipos->inv_posiciones_id = $posi->id;
                    $relaequipos->inv_equipos_id = $equip->id;
                }
                              
                $relaequistok = RelationEquiposStockModel::where('inv_equipos_id', $equip->id)->first();
                $relaequistok->inv_stocks_id = $posi->sede;
               
                if(RelationEquiposResponsableModel::where('inv_equipos_id', $equip->id)->exists()){                    
                    $responsable= RelationEquiposResponsableModel::where('inv_equipos_id', $equip->id)->first();                    
                    $responsable->personal_id   = PersonalModel::where('cedula_empleado', $request->responsable)->first()->id;
                   
                } else {
                    $responsable = new RelationEquiposResponsableModel();
                    $responsable->inv_equipos_id = $equip->id;
                    $responsable->personal_id = PersonalModel::where('cedula_empleado', $request->responsable)->first()->id;
                  
                }
                 
                $relaequipos->save();
                $relaequistok->save();                
                $equip->save();                
                $responsable->save();
                
                return json_encode("Nuevo Equipo asignado Correctamente");
                    break;
            }
        } catch (\Exception $e) {
            return json_encode(abort(500, "Ocurrio un problema para el ".$type." ".$e->getMessage()));
        }
    }

    public function update(Request $request, $id) {
        try {
            ///ubicamos la posicion
            $posicion = PosicionesModel::find($id);
            $posicion->etiqueta = $request->etiquetapos;

            ///contabilizamos cuantos items tiene la posicion asignada.
            $itemsposicion = count($posicion->PosicionEquipos) + count($posicion->PosicionMonitores) + count($posicion->PosicionPerifericos);
            //dd($itemsposicion,$request->all());
            ///recorrido de equipos asignados para conocer quien debe salir
            foreach ($posicion->PosicionEquipos as $equiposvinculados) {
                $validateon[] = 'unlinkequipo' . $equiposvinculados->id;
                //dd($request->input('unlinkequipo'.$equiposvinculados->id)  == null);
                if ($request->input('unlinkequipo' . $equiposvinculados->id) == null) {
                    //dd($posicion->PosicionEquipo);
                    ///si el equipo esta malo no cambia su estatus.
                    if ($equiposvinculados->bad == null) {
                        $equiposvinculados->available = "X";
                        $equiposvinculados->assigned = null;
                        $equiposvinculados->save();
                    }
                    ///desvinculamos de la posicion
                    $posicion->PosicionEquipo->delete();
                    ///desvinculamos el responsable.
                    $equiposvinculados->EquipoAResponsable->delete();
                }
            }

            ///recorrido de MONITORES asignados para conocer quien debe salir
            foreach ($posicion->PosicionMonitores as $monitoresvinculados) {
                $validateon[] = 'unlinkmon' . $monitoresvinculados->id;
                //dd($request->input('unlinkequipo'.$equiposvinculados->id)  == null);
                if ($request->input('unlinkmon' . $monitoresvinculados->id) == null) {
                    //dd($posicion->PosicionEquipo);
                    ///si el equipo esta malo no cambia su estatus.
                    if ($monitoresvinculados->bad == null) {
                        $monitoresvinculados->available = "X";
                        $monitoresvinculados->assigned = null;
                        $monitoresvinculados->save();
                    }
                    ///desvinculamos de la posicion monit
                    $detachmon = RelationPosicionesMonitorModel::where('inv_posiciones_id', $posicion->id)
                                    ->where('inv_monitor_id', $monitoresvinculados->id)->first()->delete();
                }
            }

            ///recorrido de PERIFERICOS asignados para conocer quien debe salir
            foreach ($posicion->PosicionPerifericos as $perifericos) {
                $validateon[] = 'unlink' . strtolower($perifericos->tipo) . $perifericos->id;
                //dd($request->input('unlinkequipo'.$equiposvinculados->id)  == null);
                if ($request->input('unlink' . strtolower($perifericos->tipo) . $perifericos->id) == null) {
                    //dd($posicion->PosicionEquipo);
                    ///si el equipo esta malo no cambia su estatus.
                    if ($perifericos->bad == null) {
                        $perifericos->available = "X";
                        $perifericos->assigned = null;
                        $perifericos->save();
                    }
                    ///desvinculamos de la posicion
                    $detachmon = RelationPosicionesPerifericosModel::where('inv_posiciones_id', $posicion->id)
                                    ->where('inv_perifericos_id', $perifericos->id)->first()->delete();
                }
            }

            $contador = 0;
            ///recorremos el array con los nobmres de los inputs
            foreach ($validateon as $inputName) {
                if ($request->input($inputName) == null) {
                    $contador++;
                }
            }

            ///validamos si el contador es igual a la cantidad de items asignados, de ser cierto podran disponible la posicion desvinculando todo
            if ($contador === $itemsposicion) {
                $posicion->assigned = null;
                $posicion->tipo = null;
                $posicion->save();

                $posicion->RelationSkillsAndCartera()->delete();

                \Session::flash('success', "Desvinculación total de la posición realizada correctamente.");
                return redirect()->route('inventario.index.posiciones');
            } else {
                /// actualiza la posicion y continua y se mantiene los items que el swich no este en off                
                $posicion->tipo = $request->tipo;
                $posicion->save();

                if(RelationPosicionCarterasModel::where('inv_posiciones_id',$posicion->id)->exists()){
                    RelationPosicionCarterasModel::where('inv_posiciones_id',$posicion->id)->delete();
                }
                    
                foreach ($request->skill as $skill) {
                    $dbskill = SkillModels::find($skill);                    
                    $relacarteras = new RelationPosicionCarterasModel();
                    $relacarteras->inv_posiciones_id = $posicion->id;
                    $relacarteras->skill_id = $skill;
                    $relacarteras->cartera_id = $dbskill->cartera_id;
                    $relacarteras->area_trabajo_id = CarteraModel::find($dbskill->cartera_id)->area_trabajo_id;
                    $relacarteras->ceco = $dbskill->cod_ceco;
                    $relacarteras->save();
                }

                \Session::flash('success', "Actualización de la posicion realizada correctamente");
                return redirect()->route('inventario.index.posiciones');
            }
        } catch (\Throwable $e) {
            \Session::flash('error', $e->getMessage());
            Return back();
        }
    }
}
