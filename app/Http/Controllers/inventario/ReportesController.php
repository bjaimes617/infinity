<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\inventario\StockModel;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\MonitorModel;
use App\Models\inventario\PerifericosModel;
use App\Models\inventario\ComponentesModel;
use App\Models\inventario\RelationComponentesStockModel;
use App\Models\inventario\PosicionesModel;
use App\Models\personal\PersonalModel;
use App\Exports\Inventario\ConteoComponentesPerifericosExport;
use App\Exports\Inventario\DetallesPosicionesExport;
use App\Exports\Inventario\InventarioGlobalExport;

class ReportesController extends Controller {

    public function __construct() {
        $this->tipo = config('app.TipoReportesInventarios');
    }

    public function index() {
        $stock = StockModel::wherenotin('id', [6])->get();
        return view('inventario.reportes.index')->with(["sede" => $stock, "tipo" => $this->tipo])->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {
           
            $stock = StockModel::find($request->stock);
            switch ($request->type) {
                case 1:
                    //// contabilizacion de componentes
                    $componente = $stock->Componentes()->select(DB::raw('componente, tipo,capacidad,expresion,'
                                                    . ' count(assigned) as assigned,'
                                                    . 'count(available) as available,count(bad) as bad '))
                                    ->groupby('tipo', 'capacidad', 'expresion')->get();

                    $perifericos = $stock->Perifericos()->select(DB::raw('tipo,conexion, count(assigned) as assigned,count(available) as available,'
                                            . 'count(bad) as bad '))->groupby('tipo', 'conexion')->get();

                    $equipos = $stock->Equipos()->select('tipo', DB::raw('count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('tipo')->get();

                    $monitor = MonitorModel::select(DB::raw('marca, count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('marca')->get();

                    $datas = ["1" => $componente, "2" => $perifericos, "3" => $equipos, "4" => $monitor];
                    //dd($perifericos);
                    return Excel::download(new ConteoComponentesPerifericosExport($datas, $stock->tipo), 'Conteo de Componentes_Equipos_monitores_perifericos ' . date('d-m-Y', strtotime(now())) . '.xlsx');
                    break;
                case 2:
                    $row = array();
                    $data = array();
                    $maxequipos = 0;
                    $maxmonitores = 0;
                    $maxperifericos = 0;
                    if ($request->stock != 1) {
                        $posicion = PosicionesModel::where('sede', $request->stock)->get();
                        foreach ($posicion as $pos) {
                            $row["responsable"] = "-";
                            //dd($pos->PosicionEquipos);
                            if (!$pos->PosicionEquipos->isEmpty()) {
                                if ($maxequipos < count($pos->PosicionEquipos)) {
                                    $maxequipos = count($pos->PosicionEquipos);
                                }
                                $eq = $pos->PosicionEquipos()->select('inv_equipos.id', 'tipo', 'marca_pc')->get();
                                $row["equipos"] = $eq;

                                foreach ($eq as $asigequip) {
                                    $row["responsable"] = $asigequip->EquipoAResponsable != null ? $asigequip->EquipoAResponsable->Responsable->primer_nombre . " " . $asigequip->EquipoAResponsable->Responsable->primer_apellido : "-";
                                }
                            }
                            else {
                                $row["equipos"] = 'S/A';
                            }

                            if (!$pos->PosicionMonitores->isEmpty()) {
                                if ($maxmonitores < count($pos->PosicionMonitores)) {
                                    $maxmonitores = count($pos->PosicionMonitores);
                                }
                                $row["monitor"] = $pos->PosicionMonitores()->select('inv_monitor.id', 'marca', 'pulgadas')->get();
                            }
                            else {
                                $row["monitor"] = 'S/A';
                            }

                            if (!$pos->PosicionPerifericos->isEmpty()) {
                                if ($maxperifericos < count($pos->PosicionPerifericos)) {
                                    $maxperifericos = count($pos->PosicionPerifericos);
                                }
                                $row["perifericos"] = $pos->PosicionPerifericos()->select('tipo', DB::raw('count(tipo) as conteo'))->groupby('tipo')->Orderby('tipo', 'ASC')->get();
                            }
                            else {
                                $row["perifericos"] = 'S/A';
                            }


                            if (!$pos->ToSkill->isEmpty()) {
                                foreach ($pos->ToSkill as $skills) {
                                    $skill[] = $skills->nombre_skill;
                                    $cartera[] = $skills->RelationCarteras->nombre_cartera;
                                    $gerencia = $skills->RelationCarteras->RelationGerencias->nombre_campana;
                                    $ceco[] = $skills->cod_ceco;
                                }
                                $row["skill"] = implode(" & ", $skill);
                                $row["cartera"] = implode(" & ", $cartera);
                                $row["gerencia"] = $gerencia;
                                $row["ceco"] = implode(" | ", $ceco);
                                $skill = null;
                                $cartera = null;
                                $gerencia = null;
                                $ceco = null;
                            }
                            else {
                                $row["skill"] = "S/A";
                                $row["cartera"] = "S/A";
                                $row["gerencia"] = "S/A";
                                $row["ceco"] = "S/A";
                            }
                            if ($pos->gerente_id != null) {
                                $per = PersonalModel::find($pos->gerente_id);
                                $row["gerente"] = $per->primer_nombre . ' ' . $per->primer_apellido;
                            }
                            else {
                                $row["gerente"] = "S/A";
                            }

                            $data[$pos->id] = $row;
                        }

                        $groupcount = ["equipos" => $maxequipos, "monitores" => $maxmonitores, "perifericos" => $maxperifericos];

                        $arrays = ["1" => $data, "2" => $groupcount, "3" => $posicion];

                        return Excel::download(new DetallesPosicionesExport($arrays, $stock->tipo), 'Detalles Posiciones.xlsx');
                    }
                    else {
                        \Session::flash('info', "El stock seleccionado no tiene posiciones asociadas.");
                        return back();
                    }
                    break;
                case 3:
                    ///array para los datos del equipo
                    $equiparray = array();
                    $equipos = array();

                    foreach (EquipoModel::all() as $equip) {
                        //dd($equip);
                        if ($equip->RelationtoPosicion()->exists()) {
                            $posicion = $equip->RelationtoPosicion()->get();
                            foreach ($posicion as $pos) {
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
                                $concatpos[] = $ab . '|' . $pos->number;
                            }
                            $equiparray["posicion"] = implode(" && ", $concatpos);
                            $concatpos = null;
                        }
                        else {
                            $equiparray["posicion"] = "S/A";
                        }
                        $equiparray["nombre"] = $equip->tipo . "|" . sprintf("%05d", $equip->id);
                        $equiparray["tipo"] = $equip->tipo;
                        $equiparray["marca"] = $equip->marca_pc;
                        $equiparray["serial"] = $equip->serial_equipo;
                        $equiparray["fuente"] = $equip->estado_fuente != null ? config('app.EstatusDisks')[$equip->estado_fuente] : '';
                        $equiparray["memorias"] = $equip->slot;
                        $equiparray["asignadas"] = $equip->ram_assigned;
                        $equiparray["totaldiscos"] = $equip->disks;
                        $equiparray["lan_mac_address"] = $equip->lan_mac_address;
                        $equiparray["ip"] = $equip->ip;
                        $equiparray["wlan_mac_address"] = $equip->wlan_mac_address;
                        $equiparray["wip"] = $equip->wip;
                        $equiparray["fechaman"] = $equip->fecha_mantenimiento != null ? date('d/m/Y', strtotime($pos->fecha_mantenimiento)) : '-';
                        $equiparray["etiqueta"] = $equip->etiqueta;
                        $equiparray["estado"] = $equip->assigned != null ? "Asignada" : ($equip->available != null ? "Disponible" : "Dañada");

                        if ($equip->EquiposAComponentes()->where('componente', "CPU")->exists()) {
                            $cpu = $equip->EquiposAComponentes()->where('componente', "CPU")->first();
                            $equiparray["cpu"] = $cpu->tipo . " " . $cpu->capacidad . "" . $cpu->expresion;
                            $equiparray["generacion"] = $cpu->generacion;
                        }
                        else {
                            $equiparray["cpu"] = "S/A";
                            $equiparray["generacion"] = "S/A";
                        }

                        if ($equip->EquiposAComponentes()->where('componente', "RAM")->exists()) {
                            $ram = $equip->EquiposAComponentes()->where('componente', "RAM")->get();
                            // dd($equip);
                            $sumaram = 0;
                            foreach ($ram as $rams) {
                                $concatram[] = $rams->tipo . " " . $rams->capacidad . $rams->expresion;
                                $sumaram = $sumaram + $rams->capacidad;
                            }
                            $equiparray["ram"] = implode(" || ", $concatram);
                            $equiparray["totalram"] = $sumaram;
                            $concatram = null;
                            $sumaram = 0;
                        }
                        else {
                            $equiparray["ram"] = "S/A";
                            $equiparray["totalram"] = 0;
                        }

                        if ($equip->EquiposAComponentes()->where('componente', "DISCO")->exists()) {
                            $disco = $equip->EquiposAComponentes()->where('componente', "DISCO")->get();
                            //dd($disco);
                            foreach ($disco as $discos) {
                                $concatdisk[] = $discos->tipo . " " . $discos->capacidad . $discos->expresion;
                            }
                            $equiparray["discos"] = implode(" || ", $concatdisk);
                            $concatdisk = null;
                        }
                        else {
                            $equiparray["discos"] = "S/A";
                        }
                        $equipos[] = $equiparray;
                    }

                    $monitor = MonitorModel::all();
                    $monitors = array();
                    $monarray = array();
                    foreach ($monitor as $mon) {
                        //dd($mon,);
                        if ($mon->RelationMonitorPosicion()->exists()) {
                            $posicion = $mon->RelationMonitorPosicion()->get();
                            foreach ($posicion as $pos) {
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
                                $concatpos[] = $ab . '|' . $pos->number;
                            }
                            $monarray["posicion"] = implode(" && ", $concatpos);
                            $concatpos = null;
                        }
                        else {
                            $monarray["posicion"] = "S/A";
                        }
                        $monarray["identificador"] = "MON|" . sprintf("%05d", $mon->id);
                        $monarray["marca"] = $mon->marca;
                        $monarray["pulgadas"] = $mon->pulgadas . "''";
                        $monarray["serial"] = $mon->serial;
                        $monarray["tipofuente"] = config('app.TipoCable')[$mon->tipo_alimentador];
                        $monarray["etiqueta"] = $mon->etiqueta;
                        $monarray["estado"] = $mon->assigned != null ? "Asignada" : ($mon->available != null ? "Disponible" : "Dañada");
                        $monitors[] = $monarray;
                    }

                    ///perifericos por posicion
                    $pers = array();
                    $periarray = array();
                    $dataperifericos = array();

                    $posiciones = PosicionesModel::all();
                    foreach ($posiciones as $posis) {
                        if ($posis->PosicionRelacionPerifericos->isNotempty()) {
                            // dd($posis->PosicionRelacionPerifericos);
                            switch ($posis->sede) {
                                case 2 : $ab = "PLA";
                                    break;
                                case 3 : $ab = "SIE";
                                    break;
                                case 4 : $ab = "AUT";
                                    break;
                                case 5 : $ab = "MAC";
                                    break;
                            }
                            $periarray["posicion"] = $ab . "|" . $posis->number;

                            foreach (config('app.Perifericos') as $key => $orden) {
                                $array = $posis->PosicionPerifericos()->where('tipo', $key)->get();
                                $conteo = $posis->PosicionPerifericos()->where('tipo', $key)->count();

                                $pers = array();
                                foreach ($array as $values) {
                                    $pers[] = $values->conexion;
                                }
                                $periarray[trim($orden)] = $pers != null ? implode("|", $pers) : "-";
                                $periarray[trim($orden) . "cantidad"] = $conteo !== 0 ? $conteo : "0";
                            }
                            $dataperifericos[] = $periarray;
                            $periarray = null;
                        }
                    }

                    $data = ["Equipos" => $equipos, "Monitores" => $monitors, "Perifericos" => $dataperifericos];
                    return Excel::download(new InventarioGlobalExport($data, $stock->tipo), 'Inventario Global.xlsx');
                    break;
            }
        } catch (\exception $e) {
            \Session::flash('info', $e->getMessage());
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
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
