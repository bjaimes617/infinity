<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\inventario\StockModel;
use App\Models\inventario\PosicionesModel;
use App\Models\inventario\RelationEquiposPosicionesModel;
use App\Models\inventario\RelationEquiposStockModel;
use App\Models\inventario\RelationPosicionesPerifericosModel;
use App\Models\inventario\RelationPosicionesMonitorModel;
use App\Models\inventario\MigracionesModel;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\MonitorModel;
use App\Models\inventario\PerifericosModel;
use App\Models\inventario\RelationPerifericosStockModel;
use App\Models\inventario\RelationPosicionCarterasModel;

class MigracionController extends Controller {

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
        $data = array();
        $row = array();
        $migraciones = MigracionesModel::where('procesado', '0')->get();

        foreach ($migraciones as $migra) {
            $encodesolicitud = json_decode($migra->solicitud);
            $encodeinventario = json_decode($migra->inventario);
            $row["codigo"] = 'MDG|' . sprintf("%03d", $migra->id);
            $row["id"] = $this->encryptNumbers($migra->id);
            $row["salida"] = $migra->sede_salida;
            $row["destino"] = $migra->sede_destino;
            $row["cantidad"] = count($encodeinventario);
            $row["solicitante"] = $migra->RelationUsers->name;
            $row["fecha"] = date('d/m/Y', strtotime($migra->created_at));
            $data[] = $row;
        }

        return view('inventario.migraciones.index')->with(["migrar" => $data])->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $stock = StockModel::wherenotin('id', [1, 6])->get();
        return view('inventario.migraciones.create')->with(["stock" => $stock, "perifericos" => $this->periferico])->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
       
        try {
            $row = array();
            $data = array();

            foreach ($request->sedesalida as $key => $salida) {
                ///bloquemaos la posicion de salida y la de destino para que no puedan ser modificadas
                $pos = PosicionesModel::find($salida);
                $pos->migration = 1;
                

                $destino = PosicionesModel::find($request->sededestino[$key]);
                $destino->migration = 1;
              

                /// generamos un array con todos los ID asociados a la posicion en funcion a lo actual        
                if (!$pos->PosicionEquipos->isEmpty()) {
                    ///equipos ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionEquipos as $equipo) {
                        $equip[] = $equipo->id;
                    }
                    $row[$salida]["equipo"] = $equip;
                    $equip = null;
                } else {
                    $row[$salida]["equipo"] = null;
                }
                ///monitores asociados
                if (!$pos->PosicionMonitores->isEmpty()) {
                    ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionMonitores as $monitores) {
                        $monitor[] = $monitores->id;
                    }
                    $row[$salida]["monitor"] = $monitor;
                    $monitor = null;
                } else {
                    $row[$salida]["monitor"] = null;
                }
                ///perifericos asociados
                if (!$pos->PosicionPerifericos->isEmpty()) {
                    foreach ($pos->PosicionPerifericos as $periferic) {
                        $perifericos[] = $periferic->id;
                    }
                    $row[$salida]["periferico"] = $perifericos;
                    $perifericos = null;
                } else {
                    $row[$salida]["periferico"] = null;
                }
                
                if (!$pos->RelationSkillsAndCartera->isEmpty()) {                    
                    $row[$salida]["relacionskill"] = $pos->RelationSkillsAndCartera;                   
                } else {
                    $row[$salida]["relacionskill"] = null;
                }
                ///almacenamos un array con todos los identificadores de la posicion        
                $data[] = $row;
            }

            ///genermaos el registro de migracion.
            $migracion = new MigracionesModel();
            $migracion->sede_salida     = $request->stocksalida;
            $migracion->sede_destino    = $request->stockdestino;
            $migracion->solicitud       = json_encode($request->all());
            $migracion->inventario      = json_encode($data);
            $migracion->procesado       = 0;
            $migracion->user_id         = \Auth::user()->id;
            
            $migracion->save();
            $pos->save();
            $destino->save();
            
            \Session::flash('success', "Proceso de Migracion Iniciado y registrado Exitosamente.");
            return redirect()->route('inventario.create.migracion');
        } catch (\Exception $e) {
            \Session::flash('error',$e->getMessage());
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
        $data = array();
         $posicion = PosicionesModel::where('sede', $request->sede)->wherenotnull('assigned')->where('migration', '0')->get();
            foreach ($posicion as $pos) {
                if (!$pos->PosicionEquipos->isEmpty()) {
                    ///$monitores ,traemos todos los $monitores asociados a la posicion                                                           
                    foreach ($pos->PosicionEquipos as $equipo) {
                        $equip[] = $equipo->tipo . "|" . sprintf("%05d", $equipo->id);
                        $ip[] = $equipo->ip;
                    }
                    $row["equipo"] = implode(" & ", $equip);
                    $equip = null;
                } else {
                    $row["equipo"] = "S/A";
                }

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
                $row["id"]          = $pos->id;
                $row["posicion"]    = $ab . "|" . $pos->number;
                $row["area"]        = $pos->area;
                $row["assignad"]    = $pos->assigned == null ? 'Disponible' : 'Ocupada';

                $data[] = $row;
            }

        return json_encode($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ViewMigration($id) {
        $data = array();
        $row = array();
        ///desencriptamos el id de la migracion
        $id = $this->decryptNumbers($id);
        $migrar = MigracionesModel::find($id);
        ///estas variables son convertidas en objeto stdClass  para validar si existen las propiedades usar property_exists($array, valor),        
        $encodesolicitud = json_decode($migrar->solicitud);
        $encodeinventario = json_decode($migrar->inventario);
        //dd($encodesolicitud, $encodeinventario);
        //recorremos las posiciones de la solicitud a migrar
        foreach ($encodesolicitud->sedesalida as $key => $solicitud) {
            ///bucamos la posicion
            $pos = PosicionesModel::find($solicitud);
            ///identificamos la sede
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
            $row["migrar"] = $ab . '|' . $pos->number;
            ///buscamos la posicion destino
            $dis = PosicionesModel::find($encodesolicitud->sededestino[$key]);
            ///identificamos la sede
            switch ($dis->sede) {
                case 2 : $bb = "PLA";
                    break;
                case 3 : $bb = "SIE";
                    break;
                case 4 : $bb = "AUT";
                    break;
                case 5 : $bb = "MAC";
                    break;
            }
            $row["destino"] = $bb . '|' . $encodesolicitud->sededestino[$key];
            ///obtenemos el array con el inventario que se debe mover            
            //$arrayinventario = $encodeinventario[$key]->{$solicitud};
            /// genemramos el inventario.
            if (property_exists($encodesolicitud, 'equipo')) {
                foreach ($encodeinventario[$key]->{$solicitud}->{"equipo"} as $equipo) {
                    $equip = EquipoModel::find($equipo);
                    $namequipo[] = $equip->tipo . "|" . sprintf("%05d", $equip->id);
                }
                $row["equipo"] = implode(" & ", $namequipo);
                $namequipo = null;
            } else {
                $row["equipo"] = "";
            }
            ///validamos si el monitor debe ser movido
            if (property_exists($encodesolicitud, 'monitor')) {
                foreach ($encodeinventario[$key]->{$solicitud}->{"monitor"} as $monitor) {
                    $monitores = MonitorModel::find($monitor);
                    $namemonitor[] = "MON|" . sprintf("%05d", $monitores->id) . "|" . $monitores->marca . "|" . $monitores->pulgadas . '"';
                }
                $row["monitor"] = implode(" & ", $namemonitor);
                $namemonitor = null;
            } else {
                $row["monitor"] = "";
            }
            ///validamos si existe la propiedad de perifericos y cuentos seran movidos
            //dd(property_exists($encodeinventario[$key]->{$solicitud}, 'periferico'));
            if (property_exists($encodeinventario[$key]->{$solicitud}, 'periferico') ) {

                foreach ($encodeinventario[$key]->{$solicitud}->{"periferico"} as $periferico) {
                    $invperiferico = PerifericosModel::find($periferico);
                    ///validamos el el periferico esta permitido para migrar                     
                    if (property_exists($encodesolicitud, $invperiferico->getrawOriginal("tipo"))) {
                        switch ($invperiferico->getrawOriginal('tipo')) {
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
                        $arrayperifec[] = $var;
                    }
                }
                $row["periferico"] = implode(" ", $arrayperifec);
                $arrayperifec = null;
            } else {
                $row["periferico"] = "";
            }
            $data[] = $row;
        }
        return json_encode($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($ids) {      
        try {
            $id = $this->decryptNumbers($ids);
            $mig = MigracionesModel::find($id);

            $encodesolicitud = json_decode($mig->solicitud);
            $encodeinventario = json_decode($mig->inventario);

            foreach ($encodesolicitud->sedesalida as $key => $saliendo) {
                ///tomamos el valor de la sede destino para poder cambiar las relaciones
                $destino = PosicionesModel::find($encodesolicitud->sededestino[$key]);
                $salida = PosicionesModel::find($saliendo);

                if (property_exists($encodesolicitud, 'equipo')) {
                    foreach ($encodeinventario[$key]->{$saliendo}->{"equipo"} as $equipo) {

                        $equipos = RelationEquiposPosicionesModel::where('inv_equipos_id', $equipo)
                                        ->where('inv_posiciones_id', $saliendo)->first();
                        $equipos->inv_posiciones_id = $destino->id;
                        $equipos->save();

                        $stockequip = RelationEquiposStockModel::where('inv_equipos_id', $equipo)->first();
                        $stockequip->inv_stocks_id = $encodesolicitud->stockdestino;
                        $stockequip->save();
                    }
                }

                if (property_exists($encodesolicitud, 'monitor')) {
                    foreach ($encodeinventario[$key]->{$saliendo}->{"monitor"} as $monitor) {
                        $monitores = RelationPosicionesMonitorModel::where('inv_monitor_id', $monitor)
                                        ->where('inv_posiciones_id', $saliendo)->first();
                        $monitores->inv_posiciones_id = $destino->id;
                        $monitores->save();
                    }
                }

                if (property_exists($encodeinventario[$key]->{$saliendo},'periferico')) {
                    foreach ($encodeinventario[$key]->{$saliendo}->{"periferico"} as $periferico) {
                        $invperiferico = PerifericosModel::find($periferico);
                        ///validamos el el periferico esta permitido para migrar                     
                        if (property_exists($encodesolicitud, $invperiferico->getrawOriginal("tipo"))) {

                            $perifericos = RelationPosicionesPerifericosModel::where('inv_perifericos_id', $periferico)
                                            ->where('inv_posiciones_id', $saliendo)->first();
                            $perifericos->inv_posiciones_id = $destino->id;
                            $perifericos->save();

                            $stockperiferic = RelationPerifericosStockModel::where('inv_perifericos_id', $periferico)->first();
                            $stockperiferic->inv_stocks_id = $encodesolicitud->stockdestino;
                            $stockperiferic->save();
                        }
                    }
                }

                if($encodesolicitud->infopos === "S"){
                    $destino->migration     = 0;
                    $destino->assigned      = "X";
                    $destino->tipo          = $salida->tipo;
                    $destino->gerente_id    = $salida->gerente_id;                  
                    $destino->cargo_id      = $salida->cargo_id;

                    ///ubicamos el arreglo de relaciones de skill de la posicion
                    if (property_exists($encodeinventario[$key]->{$saliendo},'relacionskill')){
                        ///recorremos las que esta ne el json para sacar el ID
                        foreach($encodeinventario[$key]->{$saliendo}->{'relacionskill'} as $campanias){                        
                        /// ubicamos el registro actual de la posicion
                        $relacarteras = RelationPosicionCarterasModel::where('id',$campanias->id)->first();                        
                        ///actualizamos el ident de la posicion
                        $relacarteras->inv_posiciones_id    = $destino->id;                      
                        $relacarteras->save();                           
                        }                       
                    }    
                    ///actializamos y dejamos las posicion de salida disponible
                    $salida->migration     = 0;
                    $salida->assigned      = null;
                    $salida->tipo          = null;
                    $salida->gerente_id    = null;                 
                    $salida->cargo_id      = null;   

                } else {
                    ///si no se requiere mover la identificacion de la posicion, se deja la posicion de slaida libre y 
                    //ocupamos la de destino, cambiamos migracion a 0 para liberar.
                    $salida->migration     = 0;
                    $salida->assigned      = null;   

                    $destino->migration     = 0;
                    $destino->assigned      = "X";
                }
                $destino->save();
                $salida->save();
            }

            $mig->procesado = 1;
            $mig->save();
            return json_encode("Migracion de Posiciones Realizada Correctamente. Actualizando...");
        } catch (\Exception $e) {
            return abort(500, $e->getMessage()." en ".$e->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }
}
