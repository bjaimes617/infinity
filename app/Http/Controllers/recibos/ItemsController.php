<?php

namespace App\Http\Controllers\recibos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\recibos\ItemsModels;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = ItemsModels::all();
        return view ('recibos.items.index')->with(["items"=>$items]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('recibos.items.create');
    
    }
    
    public function CheckItems(Request $request)
    {
       if(ItemsModels::where('coditem',$request->cod)->exists()){
           return json_encode(true);
       } else {
           return json_encode(false);
       }
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $item = new ItemsModels();
            $item->coditem = $request->cod;
            $item->descripcion = $request->descripcion;
            $item->tipo = $request->tipo;
            $item->save();            
                  
            \Session::flash('success', 'El Item ha sido registado Exitosamente');
            return back();
        } catch (\Exception $e){
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $items = ItemsModels::find($id);
        return view ('recibos.items.edit')->with(["items"=>$items]);
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
         try{
            $item = ItemsModels::find($id);
            $item->coditem = $request->cod;
            $item->descripcion = $request->descripcion;
            $item->tipo = $request->tipo;
            $item->save();            
                  
            \Session::flash('success', 'El Item ha sido actualizado Exitosamente');
            return redirect()->route('recibos.items.index');
        } catch (\Exception $e){
            \Session::flash('error', $e->getMessage());
            return back();
        }
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
