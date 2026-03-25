<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\MonitorModel;
use App\Models\inventario\PosicionesModel;

class EtiquetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $etiqueta = config('app.EstatusEtiqueta');
        $tipoetiqueta = ["1"=>"Equipos","2"=>"Monitores","3"=>"Posiciones"];
        unset($etiqueta[2]);
        return view ('inventario.etiquetas.index')->with(["etiqueta"=>$etiqueta,"tipo"=>$tipoetiqueta])->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
       
        switch ($request->tipo){
            case 1:
                $arrayequipo= array();
                $data = array();
                $equipo = EquipoModel::where('etiqueta',$request->etiquetas)->get();
                foreach($equipo as $equip){
                    $arrayequipo["seleccione"]      = '<div class="icheck-primary d-inline">                                                                
                    <input type="checkbox" class="check" value="'.$equip->id.'"  name="listado[]" id="'.$equip->id.'">'
                            . '<label for="'.$equip->id.'"></label></div>';
                    $arrayequipo["estatus"]         = $equip->etiqueta;                    
                    $arrayequipo["identificador"]   = $equip->tipo . "|" . sprintf("%05d", $equip->id);
                    $data[] =  $arrayequipo;
                   // dd($data);
                }
                break;
            case 2:                
                $data = array();
                $equipo = MonitorModel::where('etiqueta',$request->etiquetas)->get();
                foreach($equipo as $equip){
                        $arrayequipo["seleccione"]   = '<div class="icheck-primary d-inline">                                                                
                    <input type="checkbox" class="check" value="'.$equip->id.'" name="listado[]" id="'.$equip->id.'">'
                                . '<label for="'.$equip->id.'"></label></div>';
                    $arrayequipo["estatus"]         = $equip->etiqueta;                    
                    $arrayequipo["identificador"]   = "MON|" . sprintf("%05d", $equip->id);
                    $data[] =  $arrayequipo;
                    //dd($data);
                }
                break;
            case 3:
                $data = array();
                $equipo = PosicionesModel::where('etiqueta', $request->etiquetas)->get();
                foreach ($equipo as $equip) {
                    $arrayequipo["seleccione"] = '<div class="icheck-primary d-inline">                                                                
                    <input type="checkbox" class="check" value="' . $equip->id . '" name="listado[]" id="' . $equip->id . '">'
                            . '<label for="' . $equip->id . '"></label></div>';
                    $arrayequipo["estatus"] = config('app.EstatusEtiqueta')[$equip->etiqueta];
                    switch ($equip->sede) {
                        case 2 : $ab = "PLA";
                            break;
                        case 3 : $ab = "SIE";
                            break;
                        case 4 : $ab = "AUT";
                            break;
                        case 5 : $ab = "MAC";
                            break;
                    }

                    $arrayequipo["identificador"] = $ab . "|" . $equip->number;
                    $data[] = $arrayequipo;
                    //dd($data);
                }
                break;
        }
      
        return json_encode($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = array();
        $array= array();
        switch ($request->tipoqr){
            case 1:                
                $equipos = EquipoModel::whereIn('id',$request->listado)->get();   
                foreach($equipos as $equip){
                    //dd($equip);
                    $array["id"]                = $equip->id;
                    $array["identificador"]     = "Equipo: ".$equip->tipo . "|" . sprintf("%05d", $equip->id);
                    $array["marca"]             = "Marca: ".$equip->marca_pc;
                    $array["lan_mac_address"]   = "Mac Address: ".$equip->lan_mac_address;
                    $data[] = $array;
                    $equip->etiqueta = 2;
                    $equip->save();
                }
                $title="Equipos";
                //dd($data);
                break;
            case 2:                                                
                $equipos = MonitorModel::whereIn('id',$request->listado)->get(); 
                foreach($equipos as $equip){
                    //dd($equip);
                    $array["id"]                = $equip->id;
                    $array["identificador"]     ="MON|" . sprintf("%05d", $equip->id);
                    $array["marca"]             = "Marca: ".$equip->marca;                    
                    $data[] = $array;
                    $equip->etiqueta = 2;
                    $equip->save();
                }
                $title="Monitores";
                break;
            case 3:
                $equipos = PosicionesModel::whereIn('id', $request->listado)->get();
                foreach ($equipos as $equip) {
                    //dd($equip);
                    $array["id"] = $equip->id;
                    switch ($equip->sede) {
                        case 2 : $ab = "PLA";
                            break;
                        case 3 : $ab = "SIE";
                            break;
                        case 4 : $ab = "AUT";
                            break;
                        case 5 : $ab = "MAC";
                            break;
                    }
                    $array["identificador"] = "Posición: ".$ab . "|" . $equip->number;
                    $data[] = $array;
                    $equip->etiqueta = 2;
                    $equip->save();
                }
                $title="Posiciones";
                break;           
        }
        $total = count($data);
        $pdf = \PDF::loadView('inventario.etiquetas.pdf.pdf', compact('data', 'total'));
        return $pdf->stream('Etiquetas de'.$title.'.pdf');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
