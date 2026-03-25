<?php

namespace App\Http\Controllers\asistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\personal\PersonalModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\asistencia\AsistenciaOperativaModel;
use App\Models\personal\PermisosCarteraModel;
use App\Models\FeriadosAnualesModel;
use App\Models\personal\CargoModel;
use App\Models\personal\SkillModels;
use App\Models\User;

use App\Http\Controllers\PoliceEstructure;

use App\Imports\CalendarioFeriadosImport;
use App\Imports\AsistenciaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller {

    public $administrativo;
    public $ejecutivo;
    public $coordinador;

    public function __construct($supervisores = null, $ejecutivo = null, $coordinador = null, $senior = null) {

        $supervisores = CargoModel::where('nombre_cargo', 'like', 'Supervisor%')
                        ->orwhere('nombre_cargo', 'like', 'Analista%')
                        ->orwhere('nombre_cargo', 'like', 'Formador%')->pluck('id');
        $this->supervisor = $supervisores;

        $ejecutivo = CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
                        ->orwhere('nombre_cargo', 'like', 'Team%')
                        ->orwhere('nombre_cargo', 'like', 'Validador%')
                        ->orwhere('nombre_cargo', 'like', 'Back%')
                        ->orwhere('nombre_cargo', 'like', 'Asesor%')
                ->orwhere('nombre_cargo', 'like', 'Agente%')->pluck('id');
        $this->ejecutivo = $ejecutivo;

        $coordinador = CargoModel::where('nombre_cargo', 'like', 'Coordinador%')->pluck('id');
        $this->coordinador = $coordinador;

        $senior = CargoModel::Where('nombre_cargo', 'like', 'Senior%')->pluck('id');
        $this->senior = $senior;
        
         $this->otros = [];
         
        $this->itemsAsistencia = [ "A"  => "Asistente",
            "NL" => "No laborable", 
            "CC" =>"Cambio de Campaña",
            "DOM" =>"Domingo",
            "FER" =>"Feriado",
            "FI" => "Falta Injustificada",    
            "FJ" => "Falta justificada", 
            "PER" =>"Permiso Remunerado",
            "PNR" =>"Permiso No Remunerado",
            "SSO" =>"Reposo",                                            
            "VAC" =>"Vacaciones"];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
                      
        $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
        
        $initPersonal = $user->RelationToPersonal;

        if ($user->hasPermission(['asistencia.operativa.supervisor'])) {

            $buttonDisplay = ["ejecutivo" => "Ejecutivos"];
        } else if ($user->hasPermission(['asistencia.operativa.senior'])) {

            $buttonDisplay = ["ejecutivo" => "Ejecutivos", "supervisor" => "Supervisor"];
        } else if ($user->hasPermission(['asistencia.operativa.coordinador'])) {

            $buttonDisplay = ["ejecutivo" => "Ejecutivos", "supervisor" => "Supervisor", "senior" => "Seniors"];
        } else if ($user->hasPermission(['asistencia.operativa.gerentes'])) {

            $buttonDisplay = ["ejecutivo" => "Ejecutivos", "supervisor" => "Supervisor", "senior" => "Seniors", "coordinador" => "Cordinadores"];
        } else if ($user->hasPermission(['asistencia.operativa.otros'])) {

            $buttonDisplay = ["otros" => "Otros"];
        } else {
            $buttonDisplay = [];
        }

        return view('asistencia.index')->with(["personal" => $initPersonal, "buttonDisplay" => $buttonDisplay])->render();
    }
    
    public function create(request $request) {
        
        $tipo  = $request->asistencia;
        
        unset($this->itemsAsistencia["SSO"],
                $this->itemsAsistencia["CC"],
                $this->itemsAsistencia["FJ"],
                $this->itemsAsistencia["DOM"],
                $this->itemsAsistencia["FER"],
                $this->itemsAsistencia["PER"],
                $this->itemsAsistencia["PNR"],
                $this->itemsAsistencia["VAC"]);
        
        $begin = carbon::createfromformat('d/m/Y',$request->fecha)->startofDay(); 
        
        $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            ////\session()->put('_token', csrf_token());
        }
        
        $block  = true;
            
        //ubicamos la ficha de personal de quien toma asistencia.        
        $fichausuario = PersonalModel::where('cedula_empleado',$user->cedula)->first();
        
        /// ubicamos los Id del personal asignado de quien toma la asistencia segun el policia de estructura.        
        $idpersonal = PoliceEstructure::PersonalAsignaded($user->cedula);   
        
        ////se valida si posee personal asociado segun el cargo seleccionado, 
        //en caso contrario aplica asistencia de tipo otros que no se toma en cuenta los cargos
        if($tipo != "otros"){
            if(!PersonalModel::whereIn('id', $idpersonal)->whereDate('fecha_ingreso', '<=', $begin->copy()->format('Y-m-d'))->whereIn('cargo_id', $this->$tipo)->where('estatus_id', 1)->exists()) {
                $block = true;
                return response()->json(abort(500, "Usted no posee personal asociado bajo este esquema para tomar esta asistencia"));
            }
        } else {
            if(!PersonalModel::whereIn('id', $idpersonal)->whereDate('fecha_ingreso', '<=', $begin->copy()->format('Y-m-d'))->where('estatus_id', 1)->exists()) {
                $block = true;
                return response()->json(abort(500, "Usted no posee personal asociado bajo este esquema para tomar esta asistencia"));
            }
        }

//extraemos los ID del personal que tiene los cargos seleccionados
        $personalregistrado = PersonalModel::wherein('id',$idpersonal)
            ->whereDate('fecha_ingreso', '<=', $begin->copy()->format('Y-m-d'))
//            ->whereIn('cargo_id', $this->$tipo)
            ->where('estatus_id', 1)->pluck('id');        
        
        /// validamos si ya existe una asistencia en el dia del personal asignado.
        $registrados = AsistenciaOperativaModel::whereIn('personal_id', $personalregistrado)
            ->where('created_at','like',$begin->copy()->format('Y-m-d').' %')->pluck('personal_id');
        
        $faltan = $personalregistrado->diff($registrados);       
       
         $sql = PersonalModel::select("id","cedula_empleado","primer_nombre","primer_apellido","cargo_id","jefe_inmediato","skill_id","jefe_inmediato_segundo","estatus_id")->whereDate('fecha_ingreso', '<=', $begin);
        
        if($tipo !== "otros") {
            $asignados = $sql->whereIn('cargo_id', $this->$tipo)->whereIn('id', $faltan)->where('estatus_id', 1);
        } else {
            $asignados = $sql->whereIn('id', $faltan)->where('estatus_id', 1);
        }
        ///Condicion para limitar los freelancers a ser desplegados en una sola sede.
        /* if ($fichausuario->sede_laboral == "Macaracuay") {
            $acargo = $asignados->whereNotIn('sede_laboral', ["HomeOffice"])->orderby('primer_nombre', 'ASC')->get();                              
        } else {
            $acargo = $asignados->wherenotin('situacion_contractual', [3])->orderby('primer_nombre', 'ASC')->get();          
        }*/     
        
        //solo se excluye los homneoffice de los registros a desplegar
        $acargo = $asignados->whereNotIn('sede_laboral', ["HomeOffice"])->orderby('primer_nombre', 'ASC')->get(); 
        
        if ($acargo->isEmpty()) {
            $block = true;
            return response()->json(abort(500,"La Asistencia del personal ya ha sido registrada ese día"));
            /// si alguna de las variables tiene datos procedera a cargar la lista.
        } else {
            $block = false;            
            $data = view('asistencia.select', ["tipo" => $tipo, "personalacargo" => $acargo,"items"=>$this->itemsAsistencia ,"idpersonal" => $fichausuario->id])->render();
            return response()->json(["block" => $block, "printhtml" => $data]);
        }
        
    }

    public function store(Request $request) {      
        try {

            $begin  = carbon::createfromformat('d/m/Y',$request->fechaasistencia);             
            $dia = $begin->copy()->format('l'); ///dia de la semana en letras///            
          
            if (FeriadosAnualesModel::where('feriado', $begin)->exists()) {
                $feriado = true;
                $prefijo = "FER";
            } else {
                $feriado = false;
            }

            foreach ($request->operador as $key => $operador) {
                $personal = PersonalModel::find($operador);

                $sistencia = new AsistenciaOperativaModel();
                $sistencia->tipo_asistencia = $request->tipo;
                $sistencia->gerente_id      = $personal->gerente_area_id;
                $sistencia->supervisor_id   = $personal->jefe_inmediato;
                $sistencia->coordinador_id  = $personal->jefe_inmediato_segundo;
                $sistencia->personal_id     = $operador;
                $sistencia->area_trabajo_id = $personal->area_trabajo_id;
                $sistencia->cartera_id      = $personal->cartera_id;
                $sistencia->skill_id        = $personal->skill_id;

                if ($feriado && $request->input($personal->id) == "A") {
                    $sistencia->prefijo = $prefijo;
                } else if ($dia == "Sunday" && $request->input($personal->id) == "A") {
                    $sistencia->prefijo = "DOM";
                } else {
                    $sistencia->prefijo = $request->input($personal->id);
                }
                $sistencia->block       = 1;
                $sistencia->dia         = $dia;
                $sistencia->observacion = $request->observaciones.$personal->id;
                $sistencia->created_at  = $begin->copy()->format('Y-m-d H:i:s');
                $sistencia->created_at  = $begin->copy()->format('Y-m-d H:i:s');
                $sistencia->save();                
            }
            \Session::flash('success', "La asistencia del personal ha sido procesada exitosamente.");
            return redirect()->back();
        } catch (\Exception $e) {         
            \Session::flash('error', "Ocurrio un problema " . $e->getMessage());
            return back();
        }
    }

    public function indexAdministracion() {
  
        $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
  
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);       
        
        if($nivel){            
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel,$user);         
        } else {
           return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }      
        return view('asistencia.administracion.index')->with(["areas" => $areas]);
    }
 
    public function showAdministracion(Request $request) {

        $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
        
        $fecha = explode("-",$request->fecha);
       
        $begin  = carbon::createfromFormat('d/m/Y',trim($fecha[0]))->startofDay();
        $end    = carbon::createfromFormat('d/m/Y',trim($fecha[1]))->endofDay();
         
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        
        $dato = AsistenciaOperativaModel::WhereBetween('created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')));
       
        switch ($nivel) { 
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals= PoliceEstructure::PersonalAsignaded($user->cedula);
                
                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada   = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');                
                $skillPermisado     = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');
                
                if ($request->area_trabajo== null && $request->carteras == null && $request->skill == null) {
                    
                    $result = $dato->wherein('personal_id',$idPersonals)
                            ->whereIn('cartera_id',$carteraPermisada)
                            ->whereIn('skill_id',$skillPermisado)->orderby('created_at',"ASC");
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area_trabajo!= null && $request->carteras == null && $request->skill == null) {
                    
                    $result = $dato->wherein('personal_id',$idPersonals)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->whereIn('cartera_id',$carteraPermisada)
                            ->whereIn('skill_id',$skillPermisado)->orderby('created_at',"ASC");
                    
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo!= null && $request->carteras != null && $request->skill == null) {
                   
                    $result = $dato->wherein('personal_id',$idPersonals)
                        ->where('cartera_id', $request->carteras)
                        ->whereIn('skill_id',$skillPermisado)->orderby('created_at',"ASC");
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo!= null && $request->carteras != null && $request->skill != null) {
                    
                    $result = $dato->wherein('personal_id',$idPersonals)
                        ->where('skill_id', $request->skill)->orderby('created_at',"ASC");                    
                }                
                
                break; 
            case 3:    
                $carterasautorize   = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');                
                $skillsautorize     = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');
                
                ///SELECTORES EN TODOS
                if ($request->area_trabajo == null && $request->carteras == null) {
                    $result = $dato->WhereIn('skill_id', $skillsautorize)->orderby('created_at',"ASC");
                }
                /// DATOS EN GERENCIA Y SELECTORES EN TODOS
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)
                            ->WhereIn('cartera_id', $carterasautorize)->orderby('created_at',"ASC");
                }
                /// DATOS EN GERENCIA Y UNIDAD EL RESTO EN TODOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->carteras )
                            ->WhereIn('skill_id', $skillsautorize)->orderby('created_at',"ASC");
                }
                ///DATOS ENGERENCIA, UNIDAD Y SKIL Y ESTATUS EN TODOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->where('cartera_id', $request->carteras )
                            ->Where('skill_id', $request->skill)->orderby('created_at',"ASC");
                }
                //DATOS EN TODOS LOS SELECTORES
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->where('cartera_id', $request->carteras )
                            ->Where('skill_id', $request->skill)->orderby('created_at',"ASC");
                }               
                break; 
            case 2:               
                
                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills     = $carterasPersonal[2];
                $personal   = $carterasPersonal[3];                
                
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->whereIn('personal_id',$personal)
                        ->wherein('skill_id',$skills)->orderby('created_at',"ASC");
                }
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->whereIn('personal_id',$personal)
                        ->where('area_trabajo_id', $request->area_trabajo)                      
                        ->wherein('skill_id',$skills)->orderby('created_at',"ASC");
                }
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                    $result = $dato->whereIn('personal_id',$personal)
                        ->where('area_trabajo_id', $request->area_trabajo) 
                        ->where('cartera_id', $request->carteras )
                        ->wherein('skill_id',$skills)->orderby('created_at',"ASC");
                }
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->whereIn('personal_id',$personal)
                        ->where('area_trabajo_id', $request->area_trabajo) 
                        ->where('cartera_id', $request->carteras)  
                        ->where('skill_id', $request->skill)->orderby('created_at',"ASC");
                }                
                
            break;           
            case 1:
                ////4 SELECTORES EN TODOS
                if ($request->area_trabajo== null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->orderby('created_at',"ASC");
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area_trabajo!= null && $request->carteras == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)->orderby('created_at',"ASC");
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo!= null && $request->carteras != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->carteras)->orderby('created_at',"ASC");
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo!= null && $request->carteras != null && $request->skill != null) {
                    $result = $dato->where('skill_id', $request->skill)->orderby('created_at',"ASC");
                }
                
                break;  
            default:
            ///retorna el array vacio, no posee permisos.
            return json_encode(abort(403,"No cuenta los permisos para acceder a estos recursos."));
            break;
        }
        $sql =  $result->get();
        $data = array();
        try {
            foreach ($sql as $datos) {
                
                $row["cedula"] = $datos->RelationPersonal->cedula_empleado;
                $row["nombres"] = $datos->RelationPersonal->primer_nombre . ' ' . $datos->RelationPersonal->primer_apellido;
                $row["area_trabajo"] = $datos->RelationAreas->nombre_campana;
                $row["cartera"] = $datos->RelationCartera->nombre_cartera;
                $row["skill"] = $datos->RelationSkill->nombre_skill;
                $row["cargo"] = $datos->RelationPersonal->RelationCargos != null ? $datos->RelationPersonal->RelationCargos->nombre_cargo : '-';
                $row["dia"] = trans('semanas.' . $datos->dia) . ' ' . date('d/m/y', strtotime($datos->created_at));
                $row["prefijo"] = $datos->prefijo;
                
                if ($user->haspermission('asistencia.operativa.editar')) {
                    $editHtml = '            
            <a href="' . route('asistencia.administracion.edit', $datos->id) . '" class="btn btn-primary" data-toggle="tooltip" target="_blank" data-placement="top" title="Editar">
                <i class="fa fa-edit"></i>
            </a>';
                } else {
                    $editHtml = '';
                }
                $row["acciones"] = $editHtml;
                $data[] = $row;
               
            }
            return json_encode($data);
        } catch (\Exception $e) {
            return json_encode(abort(500,$e->getMessage()));
        }        
    }
    
    public function edit($id) {

        $asistencia = AsistenciaOperativaModel::find($id);
        
        return view('asistencia.administracion.edit')->with([
            "asistencia" => $asistencia,
            "items"     => $this->itemsAsistencia])->render();
    }
    
    public function update(Request $request, $id) {

        $asistencia = AsistenciaOperativaModel::find($id);
        $asistencia->prefijo = $request->listado;
        $asistencia->observacion = $request->observaciones;
        $asistencia->save();

        \Session::flash('success', 'El Registro de asistencia fue actualizado correctamente');
        return redirect()->route('asistencia.administracion');
    }

    public function indexReposos() {

        return view('asistencia.reposos.index');
    }

    public function createReposos(Request $request) {
     
        switch ($request->type) {
            case('Feriados'):
                Excel::import(new CalendarioFeriadosImport(), request()->file('archivo'));                
                break;

            case('RecMas'):
                Excel::import(new AsistenciaImport(), request()->file('archivo'));                
                break;
        }
        return redirect()->back();
    }
    
    public function AdministracionDelete(){
        
         $user = ENV('DEV_USER_CHANGE',false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : 
            \Auth::user();
        
        if (ENV('DEV_USER_CHANGE', false)) {          
            // Opcional: Regenerar el token CSRF explícitamente
            //\session()->put('_token', csrf_token());
        }
  
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);       
        
        if($nivel){            
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel,$user);         
        } else {
           return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }  
        
          return view('asistencia.administracion.delete')->with(["areas" => $areas]);
    }
    
    public function ShowDeleted(Request $request){
        
        try {       
        $fecha = explode("-",$request->fecha);
        $data = array();
        $begin  = carbon::createfromFormat('d/m/Y',trim($fecha[0]))->startofDay();
        $end    = carbon::createfromFormat('d/m/Y',trim($fecha[1]))->endofDay();
        
         $sup_dato = AsistenciaOperativaModel::select('supervisor_id','dia','created_at','area_trabajo_id','cartera_id','skill_id')
                 ->WhereBetween('created_at', array($begin->copy()->format('Y-m-d H:i:s'), $end->copy()->format('Y-m-d H:i:s')));
        
        
         if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
            $result = $sup_dato->orderby('skill_id', 'ASC');
        }
        if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
            $result = $sup_dato->Where('area_trabajo_id', $request->area_trabajo)->orderby('skill_id', 'ASC');
        }
        if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
            $result = $sup_dato->Where('area_trabajo_id', $request->area_trabajo)
                    ->Where('cartera_id', $request->carteras)->orderby('cartera_id', 'ASC');
        }
        if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
            $result = $sup_dato->Where('area_trabajo_id', $request->area_trabajo)
                    ->Where('cartera_id', $request->carteras)->Where('skill_id', $request->skill);
        }

        $sql = $result->groupBy('supervisor_id','dia')->orderby('supervisor_id',"asc")->get();
         
        
            foreach ($sql as $key => $sup) {
                $row["cedula"] = $sup->RelationListAsistenciaJefeInmediato->cedula_empleado;
                $row["nombres"] = $sup->RelationListAsistenciaJefeInmediato->primer_nombre . ' ' . $sup->RelationListAsistenciaJefeInmediato->primer_apellido;
                $row["area_trabajo"] = $sup->RelationListAsistenciaJefeInmediato->RelationAreas->nombre_campana;
                $row["cartera"] = $sup->RelationListAsistenciaJefeInmediato->RelationCartera->nombre_cartera;
                $row["skill"] = $sup->RelationListAsistenciaJefeInmediato->RelationSkill->nombre_skill;
                $row["dia"] = trans('semanas.' . $sup->dia) . '<br>' . date('d/m/Y', strtotime($sup->created_at));
                

                //SACAMOS LA CANTIDAD DE ASISTENCIAS TOMADAS POR EL SUPERVISOR

                $cantidad = AsistenciaOperativaModel::where('dia', $sup->dia)
                ->where('supervisor_id',$sup->supervisor_id)->where('area_trabajo_id',$sup->area_trabajo_id)->where('cartera_id',$sup->cartera_id)
                ->where('skill_id',$sup->skill_id)->where('created_at',$sup->created_at)->count();

                $row["asistencias"] = $cantidad;
                 $deleteHtml = '<button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fas fa-minus-circle"></i></button>';
                $formulario = '<form method="POST" id="formulariodeleted'.$key.'" onclick="ValidateDestroyAsistencia('.$key.');" action="'.route('asistencia.administracion.destroy', [$sup->supervisor_id, $sup->area_trabajo_id,$sup->cartera_id,$sup->skill_id,$sup->dia,$sup->created_at]).'">'
                        . '<input type="hidden" name="_token" value="'.csrf_token().'" >'
                        . '<input type="hidden" name="_method" value="DELETE"> '.$deleteHtml.'</form>';
               
                $row["acciones"] = $formulario;
                $data[] = $row;
            }
            
              return json_encode($data);
        } catch (\Exception $e) {
            return json_encode(abort(500,"Ocurrio un problema ".$e->getMessage()));
        }      
    }
    
   public function AdministracionDestroy($sup,$area,$cartera,$skill,$dia,$created_at){    
    try {

            AsistenciaOperativaModel::where('supervisor_id',$sup)
           ->where('area_trabajo_id',$area)
           ->where('cartera_id',$cartera)
           ->where('skill_id',$skill)
           ->where('dia',$dia)
           ->where('created_at',$created_at)
           ->delete();

            \Session::flash('success', 'Asistencia Eliminada con exito.');
            return back();

        } catch (\Exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }            

    }
}
