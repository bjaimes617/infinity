<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\inventario\StockModel;
use App\Models\inventario\ComponentesModel;
use App\Models\inventario\RelationComponentesStockModel;

class ComponentesController extends Controller {

    public function __construct() {
        $this->componentes = ["RAM" => "RAM", "DISCO" => "DISCO", "CPU" => "CPU"];
        $this->generacion = ["1" => "1ra.", "2" => "2da.", "3" => "3ra.", "4" => "4ta.", "5" => "5ta.", "6" => "6ta.", "7" => "7ma.", "8" => "8va.", "9" => "9na.", "10" => "10ma.", "11" => "11va."];
        $this->expresion = ['MB' => 'MB - Megabyte', 'GB' => 'GB - Gigabyte', 'TB' => 'TB - Terabyte', 'MHz' => 'MHz - Megahertz'];
    }

    public function StockCreate() {
        $stock = StockModel::where('active', 1)->pluck('tipo', 'id');
        return view('inventario.componentes.create')->with(["sede" => $stock, "componente" => $this->componentes, "expresion" => $this->expresion, "generacion" => $this->generacion]);
    }

    public function StockStore(Request $request) {
        //dd($request->all());
        $i = 1;
        while ($i <= $request->cantidad) {
            $componente = new ComponentesModel();
            $componente->componente = strtoupper($request->componente);
            $componente->tipo = strtoupper($request->tipo);
            $componente->capacidad = $request->capacidad;
            $componente->expresion = $request->expresion;
            $componente->generacion = $request->generacion != null ? $request->generacion : null;
            $componente->segmento = $request->componente == "DISCO" ? "STO" : "PRO";
            $componente->estatus_disk = $request->componente == "DISCO" ? 1 : null;
            $componente->available = "X";
            $componente->save();

            $relation = new RelationComponentesStockModel();
            $relation->inv_componentes_id = $componente->id;
            $relation->inv_stocks_id = $request->stock;
            $relation->save();

            $i++;
        }
        \Session::flash('success', $request->cantidad . ' Componente(s) Almacenado(s) Correctamente.');
        return redirect()->back();
        //
    }

    public function StockIndex() {
        $stock = StockModel::where('active', 1)->get();

        return view('inventario.componentes.index')->with(["sede" => $stock]);
    }

    public function show(Request $request) {
        $row = array();
        $data = array();
        $stock = StockModel::find($request->almacen);
        $stock->load('Componentes');

        $conteos = $stock->Componentes()
                        ->select(DB::raw('componente, tipo,capacidad,expresion,'
                                        . ' count(assigned) as assigned,'
                                        . 'count(available) as available,count(bad) as bad '))
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
            $row["total"] = $comps->available + $comps->assigned + $comps->bad;
            $row["capacidad"] = $comps->capacidad . ' ' . $comps->expresion;
            $data[] = $row;
        }
        return json_encode($data);
    }

    public function update(Request $request, $id) {
        try {
            $compo = ComponentesModel::find($id);
            if ($request->estatus == "true") {
                $compo->assigned = "X";
                $compo->bad = null;
            } else {
                $compo->assigned = null;
                $compo->bad = "X";
            }
            $compo->save();

            return json_encode("Estatus del Componente actualizado Correctamente");
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

}
