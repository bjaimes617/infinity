<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\inventario\StockModel;
use App\Models\inventario\PerifericosModel;
use App\Models\inventario\RelationPerifericosStockModel;
use App\Models\inventario\MonitorModel;
use Illuminate\Support\Facades\DB;

class PerifericosController extends Controller
{
   
    public function __construct() {
        $this->periferico = config('app.Perifericos');
        $this->conexiones = config('app.ConexionPerifericos');
    }
    
    public function index(){
        $stock = StockModel::where('active',1)->get();
        return view('inventario.perifericos.index')->with(["sede"=>$stock])->render();
    }

    public function create(){   
        
        $stock = StockModel::where('active',1)->get();
        return view ('inventario.perifericos.create')
        ->with(["sede"=>$stock,"periferico"=>$this->periferico,"conexion"=>$this->conexiones])->render();
    }
    
    public function store(Request $request){
 
     $i = 1;
        while ($i <= $request->cantidad){          
            $componente = new PerifericosModel();            
            $componente->tipo       = $request->periferico;
            $componente->conexion   = $request->conexion;          
            $componente->available  = "X";
            $componente->save();            
            
            $relation = new RelationPerifericosStockModel();
            $relation->inv_perifericos_id   = $componente->id;
            $relation->inv_stocks_id        = $request->stock;
            $relation->save();            
            $i++;
        }
         \Session::flash('success', $request->cantidad." Perifericos(s) Almacenado(s) Correctamente.");
            return redirect()->back();
    }

    public function IndexMonitor() {
        
        $monitor = MonitorModel::select(DB::raw('marca, count(assigned) as assigned,count(available) as available,count(bad) as bad '))->groupby('marca')->get();        
        return view('inventario.perifericos.monitor.index')->with(["monitor"=>$monitor])->render();
        
    }
    
    public function CreateMonitor() {
        
        $stock = StockModel::where('active',1)->pluck('tipo','id');
        return view('inventario.perifericos.monitor.create')->with(["sede"=>$stock])->render()  ;
    }
    
    public function StoreMonitor(Request $request) {
        
        $monitor = new MonitorModel();
        $monitor->marca     = strtoupper($request->marcamonitor);
        $monitor->pulgadas  = $request->pulgadas;
        $monitor->serial    = $request->serial;
        $monitor->tipo_alimentador = $request->fuente;
        $monitor->etiqueta  = 1;
        $monitor->available = "X";
        $monitor->save();
        
        \Session::flash('success',' Monitor, MON|'.sprintf("%05d", $monitor->id).' Almacenado Correctamente.');
        return back();
    }
    
}
