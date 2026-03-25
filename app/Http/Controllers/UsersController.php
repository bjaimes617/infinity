<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use jeremykenedy\LaravelRoles\Models\Role;

use App\Notifications\NewUser;
use App\Models\personal\PersonalModel;
use App\Models\personal\PermisosCarteraModel;
use App\Models\PasswordUpdate;
use App\Models\User;

use jeremykenedy\LaravelRoles\Models\Permission;
use Carbon\Carbon;
use App\libs\ApiCheckautoservicios;

class UsersController extends Controller
{
    public function __construct() {        
        $this->middleware(['auth', 'verified']);
        $this->middleware('check.password')->except(['changePassword', 'checkCurrentPassword', 'checkNewPassword', 'updatePassword']);    
    }
    
    public function index()
    {       
        $users = User::all();
        $permissions = Permission::where('model','Autorizacion')->get();
        return view('users.index')->with(["users"=>$users,"permissions"=>$permissions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->hasRole(['admin'])) {
            $roles = Role::orderBy('slug', 'asc')->get();
        } else {
            $roles = Role::where('slug', '<>', 'admin')->orderBy('slug', 'asc')->get();
        }        
        $campanas = CampanaModel::where('active',1)->get();        
        $permissions = Permission::all();
        $modelos = Permission::select('model')->where('model','<>',null)->groupby('model')->pluck('model')->toarray(); 
        return view('users.create')->with(["roles"=>$roles,"permission"=>$permissions,"campania"=> $campanas,"modelos"=>$modelos])->render();
    }

    public function AddPermissionsAditionals(Request $request) {
          
        $permissions = Role::find($request->id)->permissions()->select('permission_id')->get();
        foreach($permissions as $id){
            $array[] = $id->permission_id;
        }
            
        return json_encode($array);
        
    }
    
    public function SearchPermissionsUsers(Request $request) {
          
        $user = User::find($request->user);       
        $rol = $user->roles()->first();
        if($rol == null) {
            return json_encode(abort(500,"Usuario no posee un rol Asignado"));
        }
        $row = array();
        $data = array();
        
        $permissionrols = Role::find($rol->id)->permissions()->select('permission_id')->pluck('permission_id')->toarray();
        
///permisos adicionales del usuario
        $permisosAdicionales = $user->getpermissions()->whereNotIn('id',$permissionrols);
       
        foreach($permisosAdicionales as $perms){
           
            $row["permiso"] = $perms->name;
            $row["slug"] = $perms->slug;
            $row["accion"] = "";
            $data[] = $row;
        }
      //  dd($data);
        
        return json_encode(["0"=>$data,"1"=>$rol]);
        
    }
    
    public function store(Request $request)
    {
       //dd($request->all());
        $request->validate([            
            'username'  => 'required|unique:users',
            'email'     => 'email|required|unique:users',            
        ]);
        
        $password = 'directagroup';        
        $user = new User();
        $user->name         = ucfirst(strtolower($request->name));
        $user->cedula       = $request->cedula;
        $user->username     = $request->username;
        $user->email        = $request->email;
        $user->password     = $password;
        $user->estatus_id   = 3;
        $user->save();
        
        if(PersonalModel::where('cedula_empleado',$request->cedula)->exists()){
            $prs            = PersonalModel::where('cedula_empleado',$request->cedula)->first();
            $prs->user_id   = $user->id;
            $prs->save();
        }        
            $user->attachRole($request->roles);
                
        if(isset($request->permisos) && count($request->permisos) > 0){
            $user->attachPermission($request->permisos);
        }
        
        Session::flash('success', 'Las Credenciales de usuario han sido Generadas correctamente');
        if(isset($request->autoizaskills)){
            return redirect()->route('personal.ajustes.permisos.create')->with(["Newuser"=>$user->id]);
        } else {
            return redirect()->route('users.index');
        }
       /* try {
        * para enviar email al usuario, no operativo al no haber servidor de email
            $data = ['name' => $user->name, 'username' => $user->username, 'password' => $password];
            $mail = $user->notify(new NewUser($data));
            
            Session::flash('success', 'Fue enviando el email al usuario.');
        } catch (\Exception $e) { // Using a generic exception
            Session::flash('warning', 'No se pudo enviar el email al usuario, la contraseña es: directagroup');
        }       
        */
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
        
        $user = User::find($id);
        $roles = Role::orderBy('name', 'asc')->get();  
        ///todos los permisos
        $roleuser = array();
        $permissionrols = array();
        $permisosAdicionales = array();
        ///categorias de cada permiso
        $modelos = Permission::select('model')->where('model','<>',null)->groupby('model')->pluck('model')->toarray(); 
        /// rol de usuario asignado
        $rol = $user->roles()->first();
        if($rol != null){
            
            $roleuser[] = $rol->id; 
            /// permisos del rol para segmentar los permisos adicionales
            $permissionrols = Role::find($rol->id)->permissions()->select('permission_id')->pluck('permission_id')->toarray();
            ///permisos adicionales del usuario
            $permisosAdicionales = $user->getpermissions()->whereNotIn('id',$permissionrols)->pluck('id')->toarray();        
        }      
      
        $permissions = Permission::all();      
        
        $carterasautorizadas = $user->RelationPermisos()->exists();
        
        return view("users.edit")->with(["roles" => $roles, 
            'user'                  =>$user, 
            'roleuser'              =>$roleuser,
            "modelos"               =>$modelos,
            "permission"            =>$permissions,
            "permisosadicionales"   =>$permisosAdicionales,
            "permisosrol"           =>$permissionrols,
            "carterasautorizadas"   =>$carterasautorizadas
            ])->render();
    }
    
    public function update(Request $request, $id) {   
        
      //  dd(isset($request->permisos) && count($request->permisos) > 0,$request->all(),isset($request->resetpassword));
        $request->validate([             
            'email' => 'email|required|unique:users,id,' . $id,                 
        ]);
        $user = User::find($id);
        $user->name         = ucfirst(strtolower($request->name));
        $user->cedula       = $request->cedula;
        $user->email        = $request->email;
        $user->estatus_id   = $request->estatus;
        
        if(isset($request->resetpassword)){
            $user->password = "directagroup";
            $user->password_updated_at = null;       
        }         
        $user->save();   
        
        if(PersonalModel::where('cedula_empleado',$request->cedula)->exists()){
            $prs = PersonalModel::where('cedula_empleado',$request->cedula)->first();
            $prs->user_id = $user->id;
            $prs->save();
        }
        
        if ($request->filled('roles')){
            $user->roles()->sync($request->roles);
        }else{
            $user->detachAllRoles();
        }   
       // dd($request->all());
        if(isset($request->permisos) && count($request->permisos) > 0){
                $user->syncPermissions($request->permisos);
        } else {
                $user->detachAllPermissions();
        }
        
        \Session::flash('success', "El Usuario ha sido Actualizado Exitosamente.");
        if(isset($request->autorizaskill)){            
            if($user->RelationPermisos()->exists()){
                return redirect()->route('estructura.autorizacion.edit',$user->id);  
            } else {
                return redirect()->route('estructura.autorizacion.create')->with(["Newuser"=>$user->id]); 
            }            
        } else {
            return redirect()->route('users.index');
        }
    }    
       
    public function remove($id){
        $user = User::findOrFail($id);        
        $user->delete();
        \Session::flash('success', "El Usuario ha sido movido a la papelera.");
        return redirect()->route('users.index');
    }
    
    public function removeIndex() {
        
        $users = User::onlyTrashed()->get();
        return view('users.remove')->with("users", $users);
    }
        
    public function destroy($id) {
      
        $user = User::withTrashed()->find($id);
        
        if(PersonalModel::where('cedula_empleado',$user->cedula)->exists()){
            
            $prs = PersonalModel::where('cedula_empleado',$user->cedula)->first();
            $prs->user_id = null;
            $prs->save();
        }        
          $user->forceDelete(); 
        
        \Session::flash('success', "El Usuario ha sido eliminado completamente de la plataforma.");
        return redirect()->route('users.remove.index');       
    }
    
    public function restore($id) {
      
        User::withTrashed()->find($id)->restore();        
        
        \Session::flash('success', "El Usuario ha sido restaurado correctamente");
        return redirect()->route('users.remove.index');       
    }
    
    public function changePassword() {
        if (Session::has('password'))
            return response()->view('users/changepassword');
        else
            return redirect()->route('home');
    }
    
    public function checkNewPassword(Request $request) {
        if ($request->ajax()) {
            $lastPassword = PasswordUpdate::where('user_id', '=', \Auth::user()->id)->orderBy('id', 'desc')->take(config('app.lastpassword'))->get();
            if (count($lastPassword) > 0 && \Auth::user()->estatus_id != 3) {
                foreach ($lastPassword as $pass) {
                    if (\Hash::check($request->password, $pass->password)) {
                        return 'false';
                    }
                }
                return 'true';
            } else {
                return 'true';
            }
        }
    }

    public function updatePassword(Request $request) {
      
        $user = \Auth::user();        
        //if ($request->currentpassword && !\Hash::check($request->currentpassword, Auth::user()->password)) {
        //    Session::flash('error', 'La contraseña actual no coincide con la que ingreso, intente de nuevo');
        //    return redirect()->back();
        //}
        $lastPassword = PasswordUpdate::where('user_id', '=', \Auth::user()->id)->orderBy('id', 'desc')->take(config('app.lastpassword'))->get();
        if (count($lastPassword) > 0 && \Auth::user()->estatus_id != 3) {
            foreach ($lastPassword as $pass) {
                if (\Hash::check($request->password, $pass->password)) {
                    \Session::flash('error', 'La contraseña nueva no puede ser igual a las ultimas tres (03) que utilizaste, intente de nuevo');
                    return redirect()->back();
                }
            }
        } else {
            $user->estatus_id = 1;
        }
        $user->password = $request->password;
        $user->password_updated_at = Carbon::now();
        $pwu = new PasswordUpdate();
        $pwu->password = $user->password;
        $pwu->user_id = \Auth::user()->id;
        $pwu->save();

        if ($user->save()) {
            \Session::forget('password');
            \Session::flash('success', 'Su contraseña fue actualizada exitosamente.');
            return redirect()->route('home');
        }
    }
    
    public function profile() {       
      $tokens = null;
      $token = array();
       $validationUser = "0";        
        if(ENV('ACTIVATE_CHECK_USERS_AUTOSERVICIOS',false)){
            
            $bashurl = "URL_CHECK_USER_AUTOSERVICIOS";
            $api = new ApiCheckautoservicios($bashurl, \Auth::user()->cedula);
            $validationUser = $api->CheckValidateUserAutoservicio();
        }        
               
        foreach(\Auth::user()->tokens()->get() as $tokens){          
            $token[]  = $tokens->name.": ".$tokens->encrypt;            
        }
        
        $tokens = implode(" <br /> ", $token);   
      
        return view ('users.profile')->with(['validationUser'=>$validationUser,"tokens"=>$tokens]);
    }     
            
    public function StoreTokens(Request $request) {
               
        $habilidades = array();
        
        $habilidades = $request->permisos;
        
        $users = User::find($request->usuario);        
        $token = $users->createToken($request->nombre, $habilidades)->plainTextToken;        
        $iden = explode("|",$token);  
        
       DB::table('personal_access_tokens')->where('id',$iden[0])->update(["encrypt"=>$token]);       
        return json_encode(["Token Creado Exitosamente",$token]);
        
    }
    
    public function ShowTokens(Request $request){
        
        $user = User::find($request->user);
        $row  = array();
        $data = array();
        
        foreach($user->tokens()->get() as $tokens){
            $row["id"]      = $tokens->id;
            $row["name"]    = $tokens->name;
            $row["encrypt"] = $tokens->encrypt;
            
            $deleteHtml = '<button type="button" class="btn btn-sm btn-danger" id="deleted'.$tokens->id.'" onclick="DeleteTokens('.$tokens->id.');" data-toggle="tooltip"'
            . ' data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>';
                    
            $row["acciones"] = '<form action="'.route('users.tokens.delete',$tokens->id).'" method="POST" id="deleted'.$tokens->id.'">'
                    . '<input type="hidden" name="_method" value="DELETE">'. csrf_field().''
                    . '<input type="hidden" name="userid" value="'.$request->user.'">'
                    . '<div class="btn-group">'.$deleteHtml.'</div></form>';  
            
            $data[] =$row;
        }
        
        return json_encode($data);
    }
    
    public function DeleteTokens(Request $request, $id){
        
        $user = User::find($request->userid);
        $user->tokens()->where('id', $id)->delete();
      
        return json_encode("Token Eliminado Exitosamente.");
    }
}