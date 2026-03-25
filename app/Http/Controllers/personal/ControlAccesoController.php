<?php

namespace App\Http\Controllers\personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\personal\AsistenciaRecepcionModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\AsistenciaModel;
use App\Imports\VisitantesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\cursos\ParticipantesModel;
use App\Models\personal\VisitantesModel;
use App\Http\Controllers\PoliceEstructure;

class ControlAccesoController extends Controller {

    public function __construct() {
        $this->middleware(['auth','verified','check.password']);
    }
    
    public function index() {

        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();            
        $nivel  = PoliceEstructure::VerifiedNivelPermissions($user);      
      
        if($nivel){            
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel,$user);         
        } else {
           return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }   
        
        return view('personal.control.QR')->with(["area" => $areas]);
    }

    public function create(Request $request) {

        $data = array();
        $row = array();
        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();      
        
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);   
        
        $dato = PersonalModel::where('estatus_id',1);
        
          switch ($nivel) {               
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals= PoliceEstructure::PersonalAsignaded($user->cedula);
                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada   = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');                
                $skillPermisado     = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');
                
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    
                    $result = $dato->wherein('id',$idPersonals)
                            ->whereIn('cartera_id',$carteraPermisada)
                            ->whereIn('skill_id',$skillPermisado)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    
                    $result = $dato->wherein('id',$idPersonals)
                            ->where('area_trabajo_id', $request->area)
                            ->whereIn('cartera_id',$carteraPermisada)
                            ->whereIn('skill_id',$skillPermisado)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                    
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                   
                    $result = $dato->wherein('id',$idPersonals)
                        ->where('cartera_id', $request->cartera)
                        ->whereIn('skill_id',$skillPermisado)
                        ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    
                    $result = $dato->wherein('id',$idPersonals)
                        ->where('skill_id', $request->skill)
                        ->where('status_carnet', $request->estatus)
                        ->orderby('primer_nombre', 'ASC');
                    
                }
                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->wherein('id',$idPersonals)
                            ->where('skill_id', $request->skill)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                  
                    $result = $dato->wherein('id',$idPersonals)
                            ->whereIn('cartera_id',$carteraPermisada)
                            ->whereIn('skill_id',$skillPermisado)                           
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->wherein('id',$idPersonals)
                            ->where('area_trabajo_id', $request->area)
                            ->whereIn('skill_id',$skillPermisado)                            
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus != null) {
                    
                    $result = $dato->wherein('id',$idPersonals)                           
                            ->where('cartera_id', $request->cartera)
                            ->whereIn('skill_id',$skillPermisado)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                $result = $dato->get();
                break; 
            case 3:    
                $carterasautorize   = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');                
                $skillsautorize     = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');
                
                ///SELECTORES EN TODOS
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->WhereIn('skill_id', $skillsautorize)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// DATOS EN GERENCIA Y SELECTORES EN TODOS
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)->WhereIn('cartera_id', $carterasautorize)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// DATOS EN GERENCIA Y UNIDAD EL RESTO EN TODOS
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->cartera)->WhereIn('skill_id', $skillsautorize)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///DATOS ENGERENCIA, UNIDAD Y SKIL Y ESTATUS EN TODOS
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    $result = $dato->where('cartera_id', $request->cartera)->Where('skill_id', $request->skill)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //DATOS EN TODOS LOS SELECTORES
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    $result = $dato->where('cartera_id', $request->cartera)->Where('skill_id', $request->skill)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///DATOS SOLO EN ESTATUS
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->wherein('skill_id', $skillsautorize)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///DATOS EN GERENCIA Y ESTATUS
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)->WhereIn('cartera_id', $carterasautorize)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///DATOS EN GERENCIA Y UNIDAD Y EN ESTATUS
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->cartera)->WhereIn('skill_id', $skillsautorize)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                $result = $dato->get();
                break; 
            case 2:               
                
                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills     = $carterasPersonal[2];
                $personal   = $carterasPersonal[3];                
              
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->whereIn('id',$personal)
                        ->wherein('skill_id',$skills)
                        ->where('status_carnet', $request->estatus)
                        ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->whereIn('id',$personal)
                        ->where('area_trabajo_id', $request->area)                      
                        ->wherein('skill_id',$skills)
                        ->where('status_carnet', $request->estatus)
                        ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                    $result = $dato->whereIn('id',$personal)
                        ->where('area_trabajo_id', $request->area) 
                        ->where('cartera_id', $request->cartera)
                        ->wherein('skill_id',$skills)
                        ->where('status_carnet', $request->estatus)
                        ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    $result = $dato->whereIn('id',$personal)
                        ->where('area_trabajo_id', $request->area) 
                        ->where('cartera_id', $request->cartera)  
                        ->where('skill_id', $request->skill)
                        ->where('status_carnet', $request->estatus)
                        ->orderby('primer_nombre', 'ASC');
                }
                
                 ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    $result = $dato->whereIn('id',$personal)
                            ->where('area_trabajo_id', $request->area) 
                            ->where('cartera_id', $request->cartera)   
                            ->where('skill_id', $request->skill)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->whereIn('id',$personal)
                            ->wherein('skill_id',$skills)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->whereIn('id',$personal)
                            ->where('area_trabajo_id', $request->area)                            
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                    $result = $dato->whereIn('id',$personal)
                            ->where('area_trabajo_id', $request->area)        
                            ->where('cartera_id', $request->cartera)                            
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }    
                
                $result = $dato->get();
            break;           
            case 1:
                ////4 SELECTORES EN TODOS
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->where('status_carnet', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)
                            ->where('status_carnet', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->cartera)
                            ->where('status_carnet', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    $result = $dato->where('skill_id', $request->skill)
                            ->where('status_carnet', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area != null && $request->cartera != null && $request->skill != null) {
                    $result = $dato->where('skill_id', $request->skill)                            
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area == null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area != null && $request->cartera == null && $request->skill == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)
                            ->where('status_carnet', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area != null && $request->cartera != null && $request->skill == null) {
                    $result = $dato->where('cartera_id', $request->cartera)
                            ->where('status_carnet', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                $result = $dato->get();
                break;  
            default:
                ///retorna el array vacio, no posee permisos.
                return json_encode($row);
                break;
        }
           

        foreach ($result as $datos) {
            $row["id"] = ' <input type="checkbox" name="personalid[]" id="personalid" value="' . $datos->id . '" class="required" data-parsley-required-message="Este campo es obligatorio" />';
            $row["cedula"] = $datos->cedula_empleado;
            $row["nombres"] = $datos->primer_nombre . ' ' . $datos->primer_apellido;
            $row["area_trabajo"] = $datos->RelationAreas->nombre_campana;
            $row["cartera"] = $datos->RelationCartera->nombre_cartera;
            $row["skill"] = $datos->Relationskill->nombre_skill;
            $row["cargo"] = $datos->RelationCargos->nombre_cargo;
            $row["sede"] = $datos->sede_laboral;
            $row["estatus"] = $datos->status_carnet;
            $data[] = $row;
        }
        return json_encode($data);
    }
    
    ///el QR esta apuntando a la ruta manual de recepcion faltan dos parametros por configurar
    // QR no operativos en la plataforma.
    public function CreatePrint(Request $request) {
        try {
            foreach ($request->personalid as $idpersonal) {

                $objpersonal[] = PersonalModel::select('dir_personal.id', 'primer_nombre', 'primer_apellido', 'nombre_cargo', 'cedula_empleado')
                                ->join('dir_cargo', 'dir_personal.cargo_id', 'dir_cargo.id')
                                ->where('dir_personal.id', $idpersonal)->first();

                $updatepersonal = PersonalModel::find($idpersonal);
                $updatepersonal->status_carnet = "1";
                $updatepersonal->save();
            }
            $total = count($objpersonal);
            $pdf = \PDF::loadView('personal.control.etiquetas', compact('objpersonal', 'total'));
            return $pdf->stream();
        } catch (\Exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function IndexVisitantes() {
        
        return View('personal.control.UploadVisitantes');
        
    }
    
    public function storeVisitantes(Request $request) {
                       
        Excel::import(new VisitantesImport($request->descripcion), request()->file('archivo'));        
        return redirect()->route('personal.visitantes.masive');
    }
    
    public function scannerQR($sede = "ninguna") {        
       
        if ($sede == "siemens" || $sede == "plaza" || $sede == "autana" ||  $sede == "macaracuay")
            return view('personal.control.ScannerCam')->with(['sede' => $sede]);
        else
            return view('personal.control.scanner.error')->with(['sede' => $sede]);
    }

    public function scannermanualQR($sede = "ninguna") {
        
        if ($sede == "siemens" || $sede == "plaza" || $sede == "autana" || $sede == "macaracuay")
            return view('personal.control.ScannerManual')->with(['sede' => $sede]);
        else
            return view('personal.control.ScannerManual')->with(['sede' => $sede]);
    }
   
    public function Checkpersonal($cedula, $sede, $temp = null) {
        switch ($sede):
            case "plaza" : $ip = "192.168.0.1";
                $sedeact = "Plaza Venezuela";
                break;
            case "autana" : $ip = "172.16.20.1";
                $sedeact = "Autana";
                break;
            case "siemens" : $ip = "192.168.5.1";
                $sedeact = "Siemens";
                break;            
                break;
            case "macaracuay":$ip = "200.90.10.194";
                $sedeact = "Macaracuay";
                break;
        endswitch;
      
        if(PersonalModel::where('cedula_empleado',$cedula)->whereNotIn('estatus_id',[2,3,5,6])->exists()) {           
            $personal = PersonalModel::where('cedula_empleado', $cedula)->first();
            /// invocamos todas las relaciones de registro evitamos una sub consulta
            $personal->load('RelationLibranzas', 'RelationCargos', 'RelationEstatus');
            $objempleado = new AsistenciaModel();
            $objempleado->personal_id = $personal->id;
            $objempleado->isp_ip = $ip;
            if ($temp == null || $temp == 'undefined') {
                $objempleado->temperatura = null;
            } else {
                $objempleado->temperatura = $temp;
            }
            $objempleado->save();
            
            //// revisamos la libranza y determinamos si el dia actual corresponde al dia libre de ser asi
            /// 2 para restringir y 1 para continuar
            
            if (($personal->RelationLibranzas != null)) {

                if ($personal->RelationLibranzas->libranzas != "Rotativo") {

                    $libranza = $personal->RelationLibranzas->libranzas;
                    list($dialibre1, $dialibre2) = explode(' y ', $libranza);
                }
                else {
                    $dialibre1 = "Rotativo";
                    $dialibre2 = "Rotativo";
                }
            }
            else {
                $dialibre1 = "No cargado";
                $dialibre2 = "No cargado";
            }

            ////verifico el dia actual ////
            $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
            $diaactual = $dias[date("w")];

            if ($diaactual == $dialibre1 || $diaactual == $dialibre2) {
                $Mensaje = '2'; //ESta en dia libre
            } else {
                $Mensaje = '1'; // Puede ingresar a las instalaciones
            }

            //dd($Mensaje);
            if ($personal->image == "") {
                $image = "nopic.jpg";
            } else {
                $image = $personal->image;
            }
            
            ///determinamos la hora de entrada y le restamos 30 minutos 
            $date = $personal->horario_entrada;
            $horaen = strtotime('-0 hour', strtotime($date));
            $horaen = strtotime('-30 minute', $horaen);
            $horaen = strtotime('-00 second', $horaen);
            $horaen = date('H:i:s', $horaen);
            if ($personal->sede_laboral == $sedeact || $personal->sede_laboral == "Inter Sede") {
                $paseasede = "1";
            } else {
                $paseasede = "2";
            }        
            $visitante = false;
            
             if($personal->area_trabajo_id == null || $personal->cartera_id == null || $personal->sede_laboral == null || $personal->skill_id == null || $personal->cargo_id == null || $personal->horario_operativo == null || $personal->libranza_id == null || $personal->horario_entrada == null || $personal->horario_salida == null || $personal->area_trabajo_id == "" || $personal->cartera_id == "" || $personal->sede_laboral == "" || $personal->skill_id == "" || $personal->cargo_id == "" || $personal->horario_operativo == "" || $personal->libranza_id == "" || $personal->horario_entrada == "" || $personal->horario_salida == ""){

                $completed = 0;

            }else{

                $completed = 1;
            }
            
            return view('personal.control.Scanner.Check')->with(["visitante"=>$visitante,"empleado" => $personal, 
                "horaentrada" => $horaen, 
                "paseasede"=>$paseasede,
                "Mensaje"=>$Mensaje,    
                "image"=>$image,
                "completed" => $completed]);
            
        }
        else if(ParticipantesModel::where('cedula_empleado',$cedula)->exists()) {            
        /////QUITAR CONTROL DE VISITANTES ACTUAL
            
            $personal = ParticipantesModel::where('cedula_empleado',$cedula)->first();            
            $image = "nopic.jpg";
            $visitante = true;            
            $curso = true;
            
            $asis = new AsistenciaRecepcionModel();
            $asis->cedula = $personal->cedula_empleado;
            $asis->nombres = $personal->primer_nombre." ". $personal->primer_apellido;
            $asis->curso_id = $personal->rec_cursos_id;
            $asis->isp = $ip;
            $asis->sede = $sedeact;
            $asis->save(); 
            
            return view('personal.control.Scanner.Check')->with(["empleado" => $personal,"visitante"=>$visitante,"curso"=>$curso,"image"=>$image]);
        }  else if(VisitantesModel::where('cedula_empleado',$cedula)->exists()) {
            
            $personal = VisitantesModel::where('cedula_empleado',$cedula)->first();            
            $image = "nopic.jpg";
            $visitante = true;                 
            $curso = false;
                        
            return view('personal.control.Scanner.Check')->with(["empleado" => $personal,"visitante"=>$visitante,"curso"=>$curso,"image"=>$image]);
        } else {           
            $visitante = false;
            return view('personal.control.Scanner.Check')->with(["empleado" => null,"visitante"=>$visitante]);
        }
       
    }
}