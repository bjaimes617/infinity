<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\inventario\StockModel;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\RelationEquipoComponentesModel;
use App\Models\inventario\ComponentesModel;
use App\Models\inventario\PosicionesModel;

use App\Models\inventario\RelationEquiposStockModel;
use App\Models\inventario\RelationComponentesStockModel;

class EquiposController extends Controller
{
    private function encryptNumbers($data) {
        // Encriptar el dato
        $encryptedData = encrypt($data);
        // Filtrar solo los números
        //$filteredData = preg_replace('/[^0-9]/', '', $encryptedData);
        return $encryptedData;
    }

    private function decryptNumbers($encryptedData) {
        // Desencriptar el dato
        $decryptedData = decrypt($encryptedData);            
        return $decryptedData;
    }
    
    public function index()
    {
        $stock = StockModel::wherenotin('id',[1,6])->get();
        return view('inventario.equipos.index')->with(["stock"=>$stock])->render();
    }
    
    public function create()
    {
        $stock = StockModel::wherenotin('id',[6])->get();
        return view('inventario.equipos.create')->with(["sede"=>$stock])->render();
    }
   
    public function store(Request $request) {       
       
        $equip = new EquipoModel();
        $equip->tipo            = $request->tipo;
        $equip->marca_pc        = strtoupper($request->marca);
        $equip->estado_fuente   = $request->fuente;
        $equip->serial_equipo   = $request->serial;
        $equip->slot            = $request->slot;
        $equip->ram_assigned    = $request->assignedram;
        $equip->disks           = $request->disks; 
        $equip->lan_mac_address = $request->macadress;        
        $equip->ip              = $request->ip;
        
        $equip->wlan_mac_address = $request->wlanmacadress;
        $equip->wip             = $request->wip;
        $equip->available   = "X";
        $equip->etiqueta    = 1;
        $equip->save();
       // dd($request->all());
        if($request->cpu != null){            
            $updatecomponente = ComponentesModel::find($request->cpu);
            $updatecomponente->available    = null;
            $updatecomponente->assigned     = "X";
            $updatecomponente->save();
            
             ///Eliminamos la relacion en la pivot de componente Stock
            $updatecomponente->RelationPivotComponenteStocks != null ?  $updatecomponente->RelationPivotComponenteStocks->delete() : '';
                
            ///se vuelve a unir el componente al nuevo stock
            $newrelation1 = new RelationComponentesStockModel();
            $newrelation1->inv_stocks_id      = $request->asigstock;
            $newrelation1->inv_componentes_id = $updatecomponente->id;
            $newrelation1->save();
            
            //unimos el componente al equipo
            $comp1 = new RelationEquipoComponentesModel();       
            $comp1->inv_equipos_id      = $equip->id;
            $comp1->inv_componentes_id  = $updatecomponente->id;
            $comp1->save();
        }
        
        /// validamos si efectivamente vienen discos en el form
        if ($request->disks != null) {
            ///recorremos todos los impos de los discos para poder actualizar el componente y relacionar con el equipo
            for ($e = 1; $e <= intval($request->disks); $e++) {                
            
                $updatecomponente1              = ComponentesModel::find($request->input('selectdisco'.$e));
                $updatecomponente1->available   = null;
                $updatecomponente1->assigned    = "X";                
                $updatecomponente1->save();
                
                ///Eliminamos la relacion en la pivot de componente Stock
                $updatecomponente1->RelationPivotComponenteStocks != null ?  $updatecomponente1->RelationPivotComponenteStocks->delete() : '';
                
                ///se vuelve a unir el componente al nuevo stock
                $newrelation2 = new RelationComponentesStockModel();
                $newrelation2->inv_stocks_id         = $request->asigstock;
                $newrelation2->inv_componentes_id    = $updatecomponente1->id;
                $newrelation2->save();
                
                //unimos el componente al equipo
                $comp1 = new RelationEquipoComponentesModel();
                $comp1->inv_componentes_id  = $updatecomponente1->id;
                $comp1->inv_equipos_id      = $equip->id;
                $comp1->save();
            }
        }
        
        if ($request->assignedram != null) {
            for ($a = 1; $a <= intval($request->assignedram); $a++) {               
                $updatecomponente2              = ComponentesModel::find($request->input('selectram'.$a));
                $updatecomponente2->available   = null;
                $updatecomponente2->assigned    = "X";
                $updatecomponente2->save();

                ///Eliminamos la relacion en la pivot de componente Stock
                $updatecomponente2->RelationPivotComponenteStocks != null ?  $updatecomponente2->RelationPivotComponenteStocks->delete() : '';
                
                 ///se vuelve a unir el componente al nuevo stock
                $newrelation2 = new RelationComponentesStockModel();
                $newrelation2->inv_stocks_id        = $request->asigstock;
                $newrelation2->inv_componentes_id   = $updatecomponente2->id;
                $newrelation2->save();
                
                //unimos el componente al equipo
                $comp1 = new RelationEquipoComponentesModel();
                $comp1->inv_componentes_id  = $updatecomponente2->id;
                $comp1->inv_equipos_id      = $equip->id;
                $comp1->save();
            }
        }
        /// por ultimo unimos el equipo al stock
        $stock = new RelationEquiposStockModel();
        $stock->inv_equipos_id  = $equip->id;
        $stock->inv_stocks_id   = $request->asigstock;
        $stock->save();
        
        $etiqueta = $equip->tipo."|".sprintf("%05d", $equip->id);
        
        \Session::flash('success', "Datos del Equipo Almacenado Correctamente, Etiqueta: ".$etiqueta);
        return redirect(route('inventario.create.equipo'));
        
    }

    
    public function show(Request $request){
        $row= array();
        $json = array();
        $location = array();
        $identificacionmap = array();
        //dd("aca");
        ///para ubicar por Sede
        if(!empty($request->stock) && $request->equipo == null){            
            $stock = StockModel::find($request->stock);
            /// ubiscamos el stock|sede seleccionado y traemos todos los equipos ya asociados            
            $equipos = $stock->Equipos;
            //dd($equipos[2]->RelationtoPosicion);
            foreach($equipos as $equips){
                $has = $this->encryptNumbers($equips->id); 
                $row["id"]          = $equips->tipo."|".sprintf("%05d", $equips->id); 
                $row["responsable"] = $equips->EquipoAResponsable != null ? $equips->EquipoAResponsable->Responsable->primer_nombre.' '.$equips->EquipoAResponsable->Responsable->primer_apellido : 'S/A';               
                $row["estatus"]     = $equips->assigned != null ? '<i class="fas fa-link" data-toggle="tooltip" data-placement="top" title="Asignada"></i>' : ($equips->bad != null ? '<i class="fas fa-times-circle" data-toggle="tooltip" data-placement="top" title="Dañada"></i>' : '<i class="fas fa-check-circle" data-toggle="tooltip" data-placement="top" title="Disponible"></i>');
                ///si el equipo esta asignado a una posicion
                if($equips->RelationtoPosicion->isnotEmpty()){                  
                    foreach($equips->RelationtoPosicion as $asignedposicion){                        
                        switch ($asignedposicion->sede) {
                        case 2 : $ab = "PLA";
                            break;
                        case 3 : $ab = "SIE";
                            break;
                        case 4 : $ab = "AUT";
                            break;
                        case 5 : $ab = "MAC";
                            break;
                        }
                        $location[] = $ab . '|' . $asignedposicion->number;
                        $row["posicion"] = $ab . '|' . $asignedposicion->number;
                        if (!$asignedposicion->ToSkill->isEmpty()) {
                            foreach ($asignedposicion->ToSkill as $skills) {
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
                        $row["area"]    = $asignedposicion->area; 
                        
                        $identificacionmap[$asignedposicion->id] = array();
                        $identificacionmap[$asignedposicion->id][1] = $row["posicion"];     // Nro posicion
                        $identificacionmap[$asignedposicion->id][2] = 'Equipo: '.$row["id"] ; // Identifricador del equipo
                        $identificacionmap[$asignedposicion->id][3] = 'Gerencia: ' . $row["gerencia"]; // Gerencia
                        $identificacionmap[$asignedposicion->id][4] = 'Cartera: ' . $row["cartera"];  // 
                        $identificacionmap[$asignedposicion->id][5] = 'Skill: ' . $row["skill"];
                        $identificacionmap[$asignedposicion->id][6] = 'Area: ' . $asignedposicion->area;
                        $identificacionmap[$asignedposicion->id][7] = 'IP: '.$equips->ip;
                        ///concatenamos todo el array con salto de lineas
                        $ide[$asignedposicion->id] = implode(" \n ",$identificacionmap[$asignedposicion->id]);                           
                        $row["posicion"]    = implode(" & ",$location);
                        $row["acciones"]    = '<div class="btn-group float-center">  
                            <button onmouseenter="$(\'polyline[id='.$asignedposicion->id.']\').tooltip(\'show\');" onmouseleave="$(\'polyline[id='.$asignedposicion->id.']\').tooltip(\'hide\');" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Ubicar"><i class="fas fa-map-pin"></i></button>                                                                                                                                                         
                                <a href='.route("inventario.edit.equipo", $has).' target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Modificar"><i class="fas fa-edit"></i></a>                                                                                                                             
                            </div>'; 
                        $json[] = $row;                
                        $location = []; 
                    }
                } else {   
                        
                        ///en caso de que el equipo no este asignado a una posicion                    
                        $row["posicion"]= "S/A";
                        $row["gerencia"]= "S/A";
                        $row["cartera"] = "S/A";
                        $row["skill"]   = "S/A";
                        $row["area"]    = "S/A"; 
                        $row["acciones"]    = '<div class="btn-group float-center">  
                            <button disabled class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Ubicar"><i class="fas fa-map-pin"></i></button>                                                                                                                         
                                <a href='.route("inventario.edit.equipo", $has).' target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Modificar"><i class="fas fa-edit"></i></a>                                                                                                                             
                            </div>'; 
                        $json[] = $row;   
                } 
            }                     
        $active = true;
        } else {           
///variable para activar el mapa traera todos los equipos que coincidan
            $active = false;
            //// para busqueda por identificador de PC
            $arrayidentificador = explode("|", $request->equipo);            
            /// si solo se usa un criterio de busqueda por ID   
            switch (count($arrayidentificador)) {
                case 1:                    
                    $equipo = EquipoModel::where('id', 'like', '%' . sprintf("%01d", $request->equipo) . '%')->get();
                    foreach ($equipo as $equips) {
                        $has = $this->encryptNumbers($equips->id);
                        $row["id"] = $equips->tipo . "|" . sprintf("%05d", $equips->id);
                        $row["responsable"] = $equips->EquipoAResponsable != null ? $equips->EquipoAResponsable->Responsable->primer_nombre . ' ' . $equips->EquipoAResponsable->Responsable->primer_apellido : 'S/A';
                        $row["estatus"] = $equips->assigned != null ? '<i class="fas fa-link" data-toggle="tooltip" data-placement="top" title="Asignada"></i>' : ($equips->bad != null ? '<i class="fas fa-times-circle" data-toggle="tooltip" data-placement="top" title="Dañada"></i>' : '<i class="fas fa-check-circle" data-toggle="tooltip" data-placement="top" title="Disponible"></i>');
                        if ($equips->RelationtoPosicion->isnotEmpty()) {
                            foreach ($equips->RelationtoPosicion as $asignedposicion) {
                                switch ($asignedposicion->sede) {
                                    case 2 : $ab = "PLA";
                                        break;
                                    case 3 : $ab = "SIE";
                                        break;
                                    case 4 : $ab = "AUT";
                                        break;
                                    case 5 : $ab = "MAC";
                                        break;
                                }
                                $location[] = $ab . '|' . $asignedposicion->number;
                                $row["posicion"] = $ab . '|' . $asignedposicion->number;
                                if (!$asignedposicion->ToSkill->isEmpty()) {
                                    foreach ($asignedposicion->ToSkill as $skills) {
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
                                $row["area"] = $asignedposicion->area;

                                $identificacionmap[$asignedposicion->id] = array();
                                $identificacionmap[$asignedposicion->id][1] = $row["posicion"];     // Nro posicion
                                $identificacionmap[$asignedposicion->id][2] = 'Equipo: ' . $row["id"]; // Identifricador del equipo
                                $identificacionmap[$asignedposicion->id][3] = 'Gerencia: ' . $row["gerencia"]; // Gerencia
                                $identificacionmap[$asignedposicion->id][4] = 'Cartera: ' . $row["cartera"];  // 
                                $identificacionmap[$asignedposicion->id][5] = 'Skill: ' . $row["skill"];
                                $identificacionmap[$asignedposicion->id][6] = 'Area: ' . $asignedposicion->area;
                                $identificacionmap[$asignedposicion->id][7] = 'IP: ' . $equips->ip;
                                ///concatenamos todo el array con salto de lineas
                                $ide[$asignedposicion->id] = implode(" \n ", $identificacionmap[$asignedposicion->id]);
                                $row["posicion"] = implode(" & ", $location);
                                $row["acciones"] = '<div class="btn-group float-center">                              
                                <a href=' . route("inventario.edit.equipo", $has) . ' target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Modificar"><i class="fas fa-edit"></i></a>                                                                                                                             
                            </div>';
                                $json[] = $row;
                                $location = [];
                            }
                        } else {
                            ///en caso de que el equipo no este asignado a una posicion                    
                            $row["posicion"] = "S/A";
                            $row["gerencia"] = "S/A";
                            $row["cartera"] = "S/A";
                            $row["skill"] = "S/A";
                            $row["area"] = "S/A";
                            $row["acciones"] = '<div class="btn-group float-center">                              
                                <a href=' . route("inventario.edit.equipo", $has) . ' target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Modificar"><i class="fas fa-edit"></i></a>                                                                                                                             
                            </div>';
                            $json[] = $row;
                        }
                    }
                    break;
                case 2:                    
                    switch ($arrayidentificador[0]) {
                        case "LAPTOP":$val = 1;
                            break;
                        case "PC": $val = 2;
                            break;
                        case "SRV": $val = 3;
                            break;
                        default:
                            $val = '';
                            break;
                    }                    
                    if ($val != '') {
                        $equipo = EquipoModel::where('id', 'like', '%' . sprintf("%01d", $arrayidentificador[1]) . '%')->where('tipo', $val)->get();
                        
                        foreach ($equipo as $equips) {
                            //dd($equips);
                            $has = $this->encryptNumbers($equips->id);
                            $row["id"] = $equips->tipo . "|" . sprintf("%05d", $equips->id);
                            $row["responsable"] = $equips->EquipoAResponsable != null ? $equips->EquipoAResponsable->Responsable->primer_nombre . ' ' . $equips->EquipoAResponsable->Responsable->primer_apellido : 'S/A';
                            $row["estatus"] = $equips->assigned != null ? '<i class="fas fa-link" data-toggle="tooltip" data-placement="top" title="Asignada"></i>' : ($equips->bad != null ? '<i class="fas fa-times-circle" data-toggle="tooltip" data-placement="top" title="Dañada"></i>' : '<i class="fas fa-check-circle" data-toggle="tooltip" data-placement="top" title="Disponible"></i>');

                            if ($equips->RelationtoPosicion->isnotEmpty()) {
                                foreach ($equips->RelationtoPosicion as $asignedposicion) {
                                    switch ($asignedposicion->sede) {
                                        case 2 : $ab = "PLA";
                                            break;
                                        case 3 : $ab = "SIE";
                                            break;
                                        case 4 : $ab = "AUT";
                                            break;
                                        case 5 : $ab = "MAC";
                                            break;
                                    }
                                    //dd("pasa");
                                    $location[] = $ab . '|' . $asignedposicion->number;
                                    $row["posicion"] = $ab . '|' . $asignedposicion->number;
                                    if (!$asignedposicion->ToSkill->isEmpty()) {
                                        foreach ($asignedposicion->ToSkill as $skills) {
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
                                    $row["area"] = $asignedposicion->area;

                                    $identificacionmap[$asignedposicion->id] = array();
                                    $identificacionmap[$asignedposicion->id][1] = $row["posicion"];     // Nro posicion
                                    $identificacionmap[$asignedposicion->id][2] = 'Equipo: ' . $row["id"]; // Identifricador del equipo
                                    $identificacionmap[$asignedposicion->id][3] = 'Gerencia: ' . $row["gerencia"]; // Gerencia
                                    $identificacionmap[$asignedposicion->id][4] = 'Cartera: ' . $row["cartera"];  // 
                                    $identificacionmap[$asignedposicion->id][5] = 'Skill: ' . $row["skill"];
                                    $identificacionmap[$asignedposicion->id][6] = 'Area: ' . $asignedposicion->area;
                                    $identificacionmap[$asignedposicion->id][7] = 'IP: ' . $equips->ip;
                                    ///concatenamos todo el array con salto de lineas
                                    $ide[$asignedposicion->id] = implode(" \n ", $identificacionmap[$asignedposicion->id]);
                                    $row["posicion"] = implode(" & ", $location);
                                    $row["acciones"] = '<div class="btn-group float-center">                              
                                <a href=' . route("inventario.edit.equipo", $has) . ' target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Modificar"><i class="fas fa-edit"></i></a>                                                                                                                             
                            </div>';
                                    $json[] = $row;
                                    $location = [];
                                }
                            } else {
                                ///en caso de que el equipo no este asignado a una posicion                    
                                $row["posicion"] = "S/A";
                                $row["gerencia"] = "S/A";
                                $row["cartera"] = "S/A";
                                $row["skill"] = "S/A";
                                $row["area"] = "S/A";
                                $row["acciones"] = '<div class="btn-group float-center">                              
                                <a href=' . route("inventario.edit.equipo", $has) . ' target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Modificar"><i class="fas fa-edit"></i></a>                                                                                                                             
                            </div>';
                                $json[] = $row;
                            }
                        }
                    } else {
                        return json_encode(abort(500, "El Valor " . $request->equipo . " es incorrecto"));
                    }
                    break;
            }
        }
        /// para desplegar el mapa de la sede        
        if ($active) {
            ////Esto se ejecutara para cargar los mapas con las posiciones no asignadas
            $posi = PosicionesModel::where('sede', $request->stock)->get();
            $posicionesmapa = array();
            foreach ($posi as $post) {
                //dd(isset($ide[$post->id]));
                if (isset($ide[$post->id]) == false) {
                    //dd($pos)
                    switch ($post->sede) {
                        case 2 : $ab = "PLA";
                            break;
                        case 3 : $ab = "SIE";
                            break;
                        case 4 : $ab = "AUT";
                            break;
                        case 5 : $ab = "MAC";
                            break;
                    }
                    $posicionesmapa[$post->id] = array();
                    $posicionesmapa[$post->id][1] = $ab . '|' . $post->number;    // Nro posicion
                    $posicionesmapa[$post->id][2] = 'Equipo: S/A'; // Equipo
                    $posicionesmapa[$post->id][3] = 'Gerencia: S/A'; // Gerencia
                    $posicionesmapa[$post->id][4] = 'Cartera:  S/A';  // cartera 
                    $posicionesmapa[$post->id][5] = 'Skill: S/A';
                    $posicionesmapa[$post->id][6] = 'Area: ' . $post->area; // area de la posicion
                    $posicionesmapa[$post->id][7] = 'IP: S/A'; /// ip del equipo asignado
                    $ide[$post->id] = implode(" \n ", $posicionesmapa[$post->id]);
                }
            }
            switch ($request->stock) {
                case 2:
                    $mapa = view('inventario.mapas.plaza.plaza')->with(["type" => "all", "posicion" => $posi, "ident" => $ide])->render();
                    break;
                case 3:
                    $mapa = view('inventario.mapas.siemens.siemens')->with(["type" => "all", "posicion" => $posi, "ident" => $ide])->render();
                    break;
                case 4:
                    $mapa = view('inventario.mapas.autana.autana')->with(["type" => "all", "posicion" => $posi, "ident" => $ide])->render();
                    break;
                case 5:
                    $mapa = view('inventario.mapas.macaracuay.macaracuay')->with(["type" => "all", "posicion" => $posi, "ident" => $ide])->render();
                    break;
            }
        } else {
            $mapa = null;
        }
        return json_encode([$json, $mapa, $active]);
    }

    public function edit($id)
    {            
        $equip = EquipoModel::find($this->decryptNumbers($id));
        $sede   = StockModel::WhereNotIn('id',[6])->get();
        $cambios = ["1"=>"SI","2"=>"NO"];
        //dd($equip->EquiposAComponentes()->get());
        return view('inventario.equipos.edit')->with(["sede"=>$sede,"equipo"=>$equip,"cambios"=>$cambios])->render();
    }
    
     public function AddNewComponente(Request $request) {       
        
         try {
            $equipo = EquipoModel::find($request->equipo);

            $componente = ComponentesModel::find($request->selectNewComponente);
            $componente->assigned   = "X";
            $componente->available  = null;
            $componente->save();
            
            $equipo->ram_assigned   = $componente->componente == "RAM" ? $equipo->ram_assigned + 1 : $equipo->ram_assigned;
            $equipo->disks          = $componente->componente == "DISCO" ? $equipo->disks + 1 : $equipo->disks;            
            $equipo->save();
            
            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $request->equipo;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            $changesto = $componente->RelationPivotComponenteStocks;
            $changesto->inv_stocks_id = $equipo->EquipoAstock->inv_stocks_id;
            $changesto->save();
            
            \Session::flash('success', "Nuevo Componente asignado Correctamente");
            return back();
        } catch (\exception $e) {
            return back(abort(500, $e->getMessage()));
        }
    }
     
    public function update(Request $request, $id) {
       
        try {
            $equip = EquipoModel::find($this->decryptNumbers($id));
            $equip->tipo = $request->tipo;
            $equip->marca_pc = strtoupper($request->marca);
            $equip->estado_fuente = $request->fuente;
            $equip->serial_equipo = $request->serial;
            $equip->slot = $request->slot;
            $equip->ram_assigned = $request->assignedram;
            $equip->disks = $request->disks;
            $equip->lan_mac_address = $request->macadress;
            $equip->ip = $request->ip;
            $equip->wlan_mac_address = $request->wlanmacadress;
            $equip->wip = $request->wip;
            $equip->fecha_mantenimiento = $request->mantenimiento != null ? date('Y-m-d', strotime($request->mantenimiento)) : null;

            foreach ($equip->EquiposAComponentes()->get() as $componente) {
                //dd($componente, $equip, $equip->EquiposAComponentes()->get(),$request->all());
                switch ($componente->componente) {
                    case "DISCO":
                        $componente->estatus_disk = $request->input('tipoestatus' . $componente->id);
                        $componente->save();
                        if ($request->input('unlikdisk' . $componente->id) == null  ) {
                            RelationEquipoComponentesModel::where('inv_equipos_id', $this->decryptNumbers($id))->where('inv_componentes_id', $componente->id)->first()->Delete();
                            $equip->disks = intval($equip->disks) != 0 ? intval($equip->disks) - 1 : intval($equip->disks);
                            if ($componente->bad == null) {
                                $componente->available = "X";
                                $componente->assigned = null;
                                $componente->save();
                            }
                        }

                        break;
                    case "RAM":                      
                        if ($request->input('unlikram' . $componente->id) == null   ) {
                            RelationEquipoComponentesModel::where('inv_equipos_id', $this->decryptNumbers($id))->where('inv_componentes_id', $componente->id)->first()->Delete();                           
                            $equip->ram_assigned = intval($equip->ram_assigned) != 0 ? intval($equip->ram_assigned) - 1 : 0;
                            if ($componente->bad == null) {
                                $componente->available = "X";
                                $componente->assigned = null;
                                $componente->save();
                            }
                        }
                        break;
                    CASE "CPU":
                        if ($request->input('unlikcpu') == null) {
                            RelationEquipoComponentesModel::where('inv_equipos_id', $this->decryptNumbers($id))->where('inv_componentes_id', $componente->id)->first()->Delete();
                            if ($componente->bad == null) {
                                $componente->available = "X";
                                $componente->assigned = null;
                                $componente->save();
                            }
                        }
                        break;
                }
            }
            $equip->save();
            
            return json_encode("El equipo ha sido actualizado correctamente");
        } catch (\Exception $e) {            
            return json_encode(abort(500,$e->getMessage()));
        }
    }

    /* esto remplaza los componentes uan vez sean seleccionados en modificar componentes view equip -> edit*/
    public function ReplaceComponenteEquip(Request $request) {
       
     try {
            $keycomp = $request->KeyComponente;         
            $oldid = explode("|", $request->input('componente'.$keycomp));
            
            //ubicamos el componente viejo y el nuevo
            $componente     = ComponentesModel::find(trim($oldid[0]));
            
            $componentenew  = ComponentesModel::find($request->input('new'.$keycomp));
                        
            // creamos la unicon para el componente nuevo
            $union = new RelationEquipoComponentesModel();
            $union->inv_equipos_id      = $request->idequipo;
            $union->inv_componentes_id  = $componentenew->id;
            $union->save();
            /// cambio el stock al componente nuevo
            $changesto                  = $componentenew->RelationPivotComponenteStocks;
            $changesto->inv_stocks_id   = $componente->RelationPivotComponenteStocks->inv_stocks_id;
            $changesto->save();

            //eliminamos la relacion del componente viejo.
            $componente->RelationPivotComponenteEquipo()->where('inv_equipos_id',$request->idequipo)->delete();

            ///actualizamos el estado del componente nuevo
            $componentenew->assigned = "X";
            $componentenew->available = null;
            $componentenew->save();

            return json_encode("Nuevo Componente asignado Correctamente");
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }   
}
