<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\Models\Role;
use jeremykenedy\LaravelRoles\Models\Permission;

class RolesController extends Controller
{
    public function __construct() {
        
        $this->middleware(['auth','verified','check.password']);
    }
    
    public function index()
    {        
        $role = Role::all();
        return view('roles.index')->with("roles", $role);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        
        $permission = Permission::all();
        $modelos = Permission::select('model')->where('model','<>',null)->groupby('model')->pluck('model')->toarray();      
        
        return view('roles.create')->with(["permission" =>$permission,'modelos'=>$modelos]);
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
            'slug' => 'unique:roles,slug',     
        ]);
      
        $role = new Role();
        $role->name = $request->name;
        $role->slug = $request->slug;
        $role->description = $request->descripcion; // optional
        $role->level = 1; // optional
        $role->save();
        
        if (count($request->permisos) > 0) {
            foreach ($request->permisos as $val) {
                $role->attachPermission($val);
            }
        }
        \Session::flash('success', "La información fue registrada exitosamente.");
        return redirect(route('roles.index'));
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
        
        $role = Role::find($id);
        $permissions = Permission::orderBy('slug', 'asc')->get();        
        
        $modelos = Permission::select('model')->where('model','<>',null)->groupby('model')->pluck('model')->toarray();
         
        $permissionsid = array();        
        foreach($role->permissions()->get() as $ids){
          $permissionsid[] = $ids->id;   
        }        
     //  dd($permissionsid);
        return view("roles.edit")->with(["role" => $role, 'permission' => $permissions, "idpermission"=>$permissionsid,"modelos"=>$modelos]);
    
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
            'slug' => 'required|unique:roles,id,' . $id,            
        ]);
         
        try{
           
        $role = Role::find($id);
        $role->name         = $request->name;
        $role->slug         = $request->slug; // optional
        $role->description  = $request->descripcion; // optional       
        $role->save();  
        
        $role->permissions()->sync([]);       
        if (count($request->permisos) > 0) {
            foreach ($request->permisos as $val) {
                $role->attachPermission($val);
            }
        }
        \Session::flash('success', "Rol de usuarios Actualizado correctamente.");
        return redirect(route('roles.index'));  
            
        } catch (\Exception $e){
            \Session::flash('error', $e->getMessage());
           return back();   
        }
    }
    
    public function remove($id)
    {        
        $role = Role::findOrFail($id);
        $role->delete();
        
      \Session::flash('success', "El rol de usuario movido a la papelera.");
        return redirect(route('roles.index'));
    }
    
    public function indexRemove() {       
        
        $roles = Role::onlyTrashed()->get();
        return view('roles.deleted')->with("roles", $roles);       
       
    }
    
    public function destroy($id) {
      
        $user = Role::withTrashed()->find($id)->forceDelete();        
        
        \Session::flash('success', "El Rol de usuario ha sido eliminado completamente de la plataforma.");
        return redirect()->route('roles.remove.index');       
    }
    
    public function restore($id) {
      
        $user = Role::withTrashed()->find($id)->restore();        
        
        \Session::flash('success', "El rol de usuario ha sido restaurado correctamente");
        return redirect()->route('roles.remove.index');       
    }
}
