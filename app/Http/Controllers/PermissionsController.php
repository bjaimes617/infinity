<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\Models\Permission;
use Illuminate\Support\Facades\Session;

class PermissionsController extends Controller
{
   public function __construct() {
        $this->middleware(['auth','verified','check.password']);
    }
    
    public function index()
    {
         $permissions = Permission::all();
        return view('permissions.index')->with('permission',$permissions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {    
        $request->validate([        
            'slug' => 'unique:permissions,slug,',     
        ]);
        
        $subaccion= $request->subaccion != null ? '.'.strtolower($request->subaccion) : '';        
        $create = strtolower($request->modulo).'.'.strtolower($request->accion).$subaccion;  
        
        $permission = new Permission();
        $permission->name = ucfirst($request->name);
        $permission->slug = $create; // optional
        $permission->description = ucfirst($request->descripcion); // optional
        $permission->model = ucfirst($request->modulo); // optional
        $permission->save();
        
        Session::flash('success', "La información fue registrada exitosamente.");
        
        return redirect(route('permissions.index'));
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
        $permission = Permission::find($id);
        $slug       = explode(".", $permission->slug);
        
        return view("permissions.edit")->with(["permission"=>$permission,"slug"=>$slug]);
        
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
        $request->validate([        
            'slug' => 'unique:permissions,slug,'. $id,     
        ]);
         
        $permission = Permission::find($id);        
        $subaccion  = $request->subaccion != null ? '.'.strtolower($request->subaccion) : '';        
        $create     = strtolower($request->modulo).'.'.strtolower($request->accion).$subaccion;       
        $permission->name = ucfirst($request->name);
        $permission->slug = $create; // optional
        $permission->description = ucfirst($request->descripcion); // optional
        $permission->model = ucfirst($request->modulo); // optional
        $permission->save();
        
        Session::flash('success', "El permiso fue actualizado exitosamente");
        
        return redirect(route('permissions.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {       
        $permission = Permission::find($id);
        $permission->forceDelete();  
                
        \Session::flash('success', "El permiso Seleccionado ha sido eliminado de la plataforma");        
        return redirect(route('permissions.index'));
        
    }
}
