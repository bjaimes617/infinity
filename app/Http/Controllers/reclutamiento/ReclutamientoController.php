<?php

namespace App\Http\Controllers\reclutamiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\reclutamiento\capCandidatosModel;
use App\Models\reclutamiento\capPreseleccionModel;

use App\Models\cursos\CursosModel;
use App\Models\cursos\ParticipantesModel;
use App\Models\cursos\HistorialModel;

use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\EstatusModel;

class ReclutamientoController extends Controller {

    public function __construct() {
        $this->nacionalidades = ['' => '-', 'V' => 'V', 'E' => 'E', 'P' => 'P'];
        $this->edocivil = ["S" => "Soltero(a)", "C" => "Casado(a)", "D" => "Divorciado(a)", "V" => "Viudo(a)", "O" => "Otro"];
        $this->redes = array(
            'Facebook' => 'Facebook',
            'Twitter' => 'Twitter',
            'Instagram' => 'Instagram',
            'LinkedIn' => 'LinkedIn',
            "computrabajo" => "CompuTrabajo",
            'Snapchat' => 'Snapchat',
            'TikTok' => 'TikTok',
            'Pinterest' => 'Pinterest',
            'YouTube' => 'YouTube',
            //'Reddit' => 'Reddit',
            'WhatsApp' => 'WhatsApp',
            'Telegram' => 'Telegram',
            //'Tumblr' => 'Tumblr',
            //'Flickr' => 'Flickr',
            //'Discord' => 'Discord',
            'Quora' => 'Quora',
            'Kuentro'=>'Kuentro'
        );
        
    $this->serviciosanteriores = array(
        "internet_por_fibra_optica" => "Internet Por Fibra Optica",
        "tv_por_suscripcion"        => "Tv Por suscripcion",
        "internet_alambrico"        => "Internet Alambrico",
        "internet_satelital"        => "Internet Satelital",
        "No"=>"No"
        );
    }

    public function Imagesave($file) {

        $name_file = time() . '_' . $file->getClientOriginalName();
        if (\Storage::disk('reclutamiento')->put($name_file, file_get_contents($file->getRealPath()))) {
            return Storage::disk('reclutamiento')->url($name_file);
        } else {
            return false;
        }
    }

    public function deleteImg($nombre_imagen) {
        if (\Storage::disk('reclutamiento')->delete($nombre_imagen)) {
            return true;
        } else {
            return false;
        }
    }

    public function Preseleccionstore(Request $request) {
         
//return response()->json($request->all(), 200);
        
        $formulario = $request->formulario;
        
        if(capPreseleccionModel::where('cedula',$formulario["cedula_empleado"])->exists()){
            return response()->json(["message"=>"Los Sentimos, esta cedula ya fue registrada."],500);
        } else {
            $preselect = new capPreseleccionModel();
            $preselect->nombres             = strtoupper($formulario["pnombre"]);
            $preselect->apellidos           = strtoupper($formulario["papellido"]);
            $preselect->nacionalidad        = strtoupper($formulario["nacionalidad"]);
            $preselect->cedula = trim(strtoupper($formulario["cedula_empleado"]));         
            $preselect->experiencia         = trim(strtoupper($formulario["experienciaventas"]));
            $preselect->cap_status_id       = 1;
            $preselect->options             = json_encode($formulario);
            $preselect->save();
            
            return response()->json(['message' => 'Registrado Exitosamente, puede cerrar esta ventana.'],200);
        }
        
    }    
        
    public function PreseleccionCandidatos(){
        
        return view('reclutamiento.preseleccion.index');
    }
    
    public function ShowPreseleccionCandidatos(Request $request){
        
        $fecha = explode("-", $request->fecha);
        $init = carbon::createfromformat('d/m/Y',trim($fecha[0]))->startOfday();
        $beging = carbon::createfromformat('d/m/Y',trim($fecha[1]))->endOfday();
        
        $data = capPreseleccionModel::whereBetween('created_at',array($init,$beging))->get();
        
        $row = array();
        $result= array();
        $servicios = $this->serviciosanteriores;
        
        foreach($data as $rs){
            $opciones = json_decode($rs->options);            
            $row["nombre"]      = $rs->nombres." ".$rs->apellidos;
            $row["cedula"]      = $rs->nacionalidad."".$rs->cedula;
            $row["edad"]        = $opciones->edad;
            $row["experiencia"] = $rs->experiencia == "S" ? "SI": "NO";
            $row["detalle"]     = isset($opciones->especifique) ? $opciones->especifique : "Sin Información";
            $row["cargo"]       = "N/A";
            $row["celular"]     = $opciones->celular;
            $row["habitacion"]  = $opciones->habitacion;
            $row["email"]       = $opciones->email;
            $row["direccion"]   = $opciones->direccion;
            $row["acciones"]   = '<form method="post" action="'.route('reclutamiento.estacion.Deleteprospecto',$rs->id).'"> ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-danger"><i class="fas fa-minus-circle"></i></button></form>';
            $row["serviciosanteriores"] = $servicios[$opciones->serviciosanteriores];
            $result[] = $row;                         
        }
        return json_encode($result);
    }
        
    public function DeleteProspecto($id){
        capPreseleccionModel::find($id)->delete();
        
        \Session::flash('success', 'Candidato Eliminado exitosamente.');
        return back();
        
    }
    
    public function SearchRegisterCandidato(Request $request){
        
        //return response()->json($request->all(),200);          
        $formulario = $request->formulario;        
        if(capPreseleccionModel::where('cedula', $formulario["cedula_candidato"])->exists()){
            
            $candidato = capPreseleccionModel::where('cedula', $formulario["cedula_candidato"])->first();      
            
            if(capCandidatosModel::where('cedula',$formulario["cedula_candidato"])
                ->where('campania',strtoupper($formulario["campania"]))->exists()){
                return response()->json(['message' => 'Sus Datos ya se encuentran registrados.'],400);
            } else {
                return response()->json($candidato,200);                  
            }
          //  return response()->json($candidato,200);            
        } else {
            return response()->json(['message' => 'No es posible ubicar su Registro, contacte a su reclutador.'],400);
        }
        
    }
    
    public function store(Request $request) {
        ////esta funcion se activa solo mediante recibimiento API desde reclutasimple srv 15:4180
        try {
            $formulario = $request->formulario;
            $urlimage = $request->urlFoto;
            $nameImagelocal = null;

            if ($urlimage != null) {
                $client = new Client();
                $response = $client->get($urlimage);

                if ($response->getStatusCode() == 200) {
                    $imageContents = $response->getBody()->getContents();

                    // Definir nombre y ruta para guardar la imagen
                    $filename = basename($urlimage);
                    Storage::disk('reclutamiento')->put($filename, $imageContents);
                    $nameImagelocal = Storage::disk('reclutamiento')->url($filename);
                }
            }
         
            if (capCandidatosModel::where('cedula', trim(strtoupper($formulario['cedula_empleado'])))->exists()) {
                return response()->json(['message' => 'Esta Cedula de Identidad ya se encuentra registrada en nuestro sistema'], 500);
            }

            $participante = new capCandidatosModel();
            $participante->photo = $nameImagelocal;
            $participante->cap_status_id = 2;
            $participante->campania = strtoupper($formulario["campania"]);
            $participante->nombres = strtoupper($formulario["pnombre"]);
            $participante->apellidos = strtoupper($formulario["papellido"]);
            $participante->nacionalidad = strtoupper($formulario["nacionalidad"]);

            $participante->cedula = trim(strtoupper($formulario["cedula_empleado"]));
            $participante->fecha_nacimiento = Carbon::createfromformat('d/m/Y', $formulario["nacimiento"])->format('Y-m-d');
            $participante->edad = trim(strtoupper($formulario["edad"]));
            $participante->lugarnacimiento = strtoupper($formulario["lugarnacimiento"]);
            $participante->pais = strtoupper($formulario["pais"]);
            $participante->sexo = strtoupper($formulario["sexo"]);
            $participante->personasviven = strtoupper($formulario["personasviven"]);
            $participante->personasacargo = strtoupper($formulario["personasacargo"]);
            $participante->edadesacargo = strtoupper($formulario["edadesacargo"]);

            $participante->edocivil = strtoupper($formulario["edocivil"]);
            $participante->conyuge = isset($formulario["conyuge"]) ? strtoupper($formulario["conyuge"]) : null;
            $participante->hijos = strtoupper($formulario["nrodehijos"]);
            $participante->edadhijos = strtoupper($formulario["edadhijos"]);
            $participante->sexohijos = isset($formulario["sexohijos"]) ? strtoupper($formulario["sexohijos"]) : null;
            $participante->direccion = strtoupper($formulario["direccion"]);
            $participante->ciudad = strtoupper($formulario["ciudad"]);
            $participante->parroquia = strtoupper($formulario["parroquia"]);
            $participante->estado = strtoupper($formulario["estado"]);
            $participante->telefono_celular = strtoupper($formulario["celular"]);
            $participante->telefono_casa = strtoupper($formulario["habitacion"]);
            $participante->email = strtoupper($formulario["email"]);
            $participante->camisa = strtoupper($formulario["camisa"]);
            $participante->pantalon = strtoupper($formulario["pantalon"]);
            $participante->zapatos = strtoupper($formulario["zapato"]);
            $participante->redes = strtoupper($formulario["redes"]);
            $participante->trabajoantes = strtoupper($formulario["trabajoantes"]);
            $participante->fechantes = isset($formulario["fechantes"]) ? Carbon::createfromformat('d/m/Y', $formulario["fechantes"])->format('Y-m-d') : null;
            $participante->parientes = strtoupper($formulario["parientes"]);
            $participante->mencionelos = isset($formulario["mencionelos"]) ? strtoupper($formulario["mencionelos"]) : null;

            $participante->initbachiller = $formulario["initbachiller"] != null ? strtoupper($formulario["initbachiller"]) : null;
            $participante->endbachiller = $formulario["endbachiller"] != null ? strtoupper($formulario["endbachiller"]) : null;
            $participante->cursadobachiller = $formulario["cursadobachiller"] != null ? strtoupper($formulario["cursadobachiller"]) : null;
            $participante->titulobachiller = $formulario["titulobachiller"] != null ? strtoupper($formulario["titulobachiller"]) : null;
            $participante->instbachiller = $formulario["instbachiller"] != null ? strtoupper($formulario["instbachiller"]) : null;
            $participante->lugarbachiller = isset($formulario["lugarbachiller"]) ? strtoupper($formulario["lugarbachiller"]) : null;

            $participante->inittsu = isset($formulario["inittsu"]) ? strtoupper($formulario["inittsu"]) : null;
            $participante->endtsu = isset($formulario["endtsu"]) ? strtoupper($formulario["endtsu"]) : null;
            $participante->cursadotsu = isset($formulario["cursadotsu"]) ? strtoupper($formulario["cursadotsu"]) : null;
            $participante->titulotsu = isset($formulario["titulotsu"]) ? strtoupper($formulario["titulotsu"]) : null;
            $participante->insttsu = isset($formulario["insttsu"]) ? strtoupper($formulario["insttsu"]) : null;
            $participante->lugartsu = isset($formulario["lugartsu"]) ? strtoupper($formulario["lugartsu"]) : null;

            $participante->initespecializacion = isset($formulario["initespecializacion"]) ? strtoupper($formulario["initespecializacion"]) : null;
            $participante->endespecializacion = isset($formulario["endespecializacion"]) ? strtoupper($formulario["endespecializacion"]) : null;
            $participante->cursadoespecializacion = isset($formulario["cursadoespecializacion"]) ? strtoupper($formulario["cursadoespecializacion"]) : null;
            $participante->tituloespecializacion = isset($formulario["tituloespecializacion"]) ? strtoupper($formulario["tituloespecializacion"]) : null;
            $participante->instespecializacion = isset($formulario["instespecializacion"]) ? strtoupper($formulario["instespecializacion"]) : null;
            $participante->lugarespecializacion = isset($formulario["lugarespecializacion"]) ? strtoupper($formulario["lugarespecializacion"]) : null;

            $participante->initpostgrado = isset($formulario["initpostgrado"]) ? strtoupper($formulario["initpostgrado"]) : null;
            $participante->endpostgrado = isset($formulario["endpostgrado"]) ? strtoupper($formulario["endpostgrado"]) : null;
            $participante->cursadopostgrado = isset($formulario["cursadopostgrado"]) ? strtoupper($formulario["cursadopostgrado"]) : null;
            $participante->titulopostgrado = isset($formulario["titulopostgrado"]) ? strtoupper($formulario["titulopostgrado"]) : null;
            $participante->instpostgrado = isset($formulario["instpostgrado"]) ? strtoupper($formulario["instpostgrado"]) : null;
            $participante->lugarpostgrado = isset($formulario["lugarpostgrado"]) ? strtoupper($formulario["lugarpostgrado"]) : null;

            $participante->estudiosactuales = isset($formulario["estudiosactuales"]) ? strtoupper($formulario["estudiosactuales"]) : null;
            $participante->otrosestudios = isset($formulario["otrosestudios"]) ? strtoupper($formulario["otrosestudios"]) : null;

            $participante->referencia1 = isset($formulario["referencia1"]) ? strtoupper($formulario["referencia1"]) : null;
            $participante->telefono1 = isset($formulario["telefono1"]) ? strtoupper($formulario["telefono1"]) : null;
            $participante->ocuacion1 = isset($formulario["ocuacion1"]) ? strtoupper($formulario["ocuacion1"]) : null;
            $participante->refanios1 = isset($formulario["refanios1"]) ? strtoupper($formulario["refanios1"]) : null;

            $participante->referencia2 = isset($formulario["referencia2"]) ? strtoupper($formulario["referencia2"]) : null;
            $participante->telefono2 = isset($formulario["telefono2"]) ? strtoupper($formulario["telefono2"]) : null;
            $participante->ocuacion2 = isset($formulario["ocuacion2"]) ? strtoupper($formulario["ocuacion2"]) : null;
            $participante->refanios2 = isset($formulario["refanios2"]) ? strtoupper($formulario["refanios2"]) : null;

            $participante->nombreempresa1 = isset($formulario["nombreempresa1"]) ? strtoupper($formulario["nombreempresa1"]) : null;
            $participante->direccionempresa1 = isset($formulario["direccionempresa1"]) ? strtoupper($formulario["direccionempresa1"]) : null;
            $participante->ciudadempresa1 = isset($formulario["ciudadempresa1"]) ? strtoupper($formulario["ciudadempresa1"]) : null;
            $participante->telefonoempresa1 = isset($formulario["telefonoempresa1"]) ? strtoupper($formulario["telefonoempresa1"]) : null;
            $participante->jefeinmediato1 = isset($formulario["jefeinmediato1"]) ? strtoupper($formulario["jefeinmediato1"]) : null;
            $participante->cargo1 = isset($formulario["cargo1"]) ? strtoupper($formulario["cargo1"]) : null;
            $participante->acargo1 = isset($formulario["acargo1"]) ? strtoupper($formulario["acargo1"]) : null;
            $participante->fingreso1 = isset($formulario["fingreso1"]) ? strtoupper($formulario["fingreso1"]) : null;
            $participante->fegreso1 = isset($formulario["fegreso1"]) ? strtoupper($formulario["fegreso1"]) : null;
            $participante->tiempo1 = isset($formulario["tiempo1"]) ? strtoupper($formulario["tiempo1"]) : null;
            $participante->sueldo11 = isset($formulario["sueldo11"]) ? strtoupper($formulario["sueldo11"]) : null;
            $participante->sueldo12 = isset($formulario["sueldo12"]) ? strtoupper($formulario["sueldo12"]) : null;
            $participante->motivo1 = isset($formulario["motivo1"]) ? strtoupper($formulario["motivo1"]) : null;
            $participante->observaciones1 = isset($formulario["observaciones1"]) ? strtoupper($formulario["observaciones1"]) : null;

            $participante->nombreempresa2 = isset($formulario["nombreempresa2"]) ? strtoupper($formulario["nombreempresa2"]) : null;
            $participante->direccionempresa2 = isset($formulario["direccionempresa2"]) ? strtoupper($formulario["direccionempresa2"]) : null;
            $participante->ciudadempresa2 = isset($formulario["ciudadempresa2"]) ? strtoupper($formulario["ciudadempresa2"]) : null;
            $participante->telefonoempresa2 = isset($formulario["telefonoempresa2"]) ? strtoupper($formulario["telefonoempresa2"]) : null;
            $participante->jefeinmediato2 = isset($formulario["jefeinmediato2"]) ? strtoupper($formulario["jefeinmediato2"]) : null;
            $participante->cargo2 = isset($formulario["cargo2"]) ? strtoupper($formulario["cargo2"]) : null;
            $participante->acargo2 = isset($formulario["acargo2"]) ? strtoupper($formulario["acargo2"]) : null;
            $participante->fingreso2 = isset($formulario["fingreso2"]) ? strtoupper($formulario["fingreso2"]) : null;
            $participante->tiempo2 = isset($formulario["tiempo2"]) ? strtoupper($formulario["tiempo2"]) : null;
            $participante->fegreso2 = isset($formulario["fegreso2"]) ? strtoupper($formulario["fegreso2"]) : null;
            $participante->sueldo21 = isset($formulario["sueldo21"]) ? strtoupper($formulario["sueldo21"]) : null;
            $participante->sueldo22 = isset($formulario["sueldo22"]) ? strtoupper($formulario["sueldo22"]) : null;
            $participante->motivo2 = isset($formulario["motivo2"]) ? strtoupper($formulario["motivo2"]) : null;
            $participante->observaciones2 = isset($formulario["observaciones2"]) ? strtoupper($formulario["observaciones2"]) : null;
            $participante->save();

            return response()->json(['message' => 'Registrado Exitosamente, puede cerrar esta ventana.']);
        } catch (\Throwable $e) {
            
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function estacion1() {
        $prospectos = capCandidatosModel::where('cap_status_id', 2)->get();
         $cursos = CursosModel::whereRaw("DATEDIFF(CURDATE(), inicio) <= 1")
                        ->whereIn('active', [1, 2])->orderby('inicio', 'ASC')->get();
         
        return view('reclutamiento.estacion1.index')->with(["prospectos" => $prospectos, "cursos"=>$cursos])->render();
    }

    public function ShowProspecto($id) {

        $pros = capCandidatosModel::find($id);

        $partial = view('reclutamiento.partials.inputsModal')
                        ->with(["pros" => $pros,
                            "nacionalidades" => $this->nacionalidades,
                            "edocivil" => $this->edocivil,
                            "redes" => $this->redes])->render();

        return json_encode($partial);
    }

    public function updateProspecto($id, Request $request) {

        $participante = capCandidatosModel::find($id);
        ///si tiene imagen y viene imagen nueva
        if ($participante->photo != null && isset($request->image)) {

            $this->deleteImg($participante->photo);
            $participante->photo = $this->Imagesave($request->image);

            /// si no tiene imagen cargada y se adjunta una
        } else if ($participante->photo == null && isset($request->image)) {
            $participante->photo = $this->Imagesave($request->image);
        }

        $participante->nombres = strtoupper($request->pnombre);
        $participante->apellidos = strtoupper($request->papellido);
        $participante->nacionalidad = strtoupper($request->nacionalidad);

        $participante->cedula = trim(strtoupper($request->cedula_empleado));
        $participante->fecha_nacimiento = Carbon::createfromformat('d/m/Y', $request->nacimiento)->format('Y-m-d');
        $participante->edad = trim(strtoupper($request->edad));
        $participante->lugarnacimiento = strtoupper($request->lugarnacimiento);
        $participante->pais = strtoupper($request->pais);
        $participante->sexo = strtoupper($request->sexo);
        $participante->personasviven = strtoupper($request->personasviven);
        $participante->personasacargo = strtoupper($request->personasacargo);
        $participante->edadesacargo = strtoupper($request->edadesacargo);

        $participante->edocivil = strtoupper($request->edocivil);
        $participante->conyuge = isset($request->conyuge) ? strtoupper($request->conyuge) : null;
        $participante->hijos = strtoupper($request->nrodehijos);
        $participante->edadhijos = strtoupper($request->edadhijos);
        $participante->sexohijos = isset($request->sexohijos) ? strtoupper($request->sexohijos) : null;
        $participante->direccion = strtoupper($request->direccion);
        $participante->ciudad = strtoupper($request->ciudad);
        $participante->parroquia = strtoupper($request->parroquia);
        $participante->estado = strtoupper($request->estado);
        $participante->telefono_celular = strtoupper($request->celular);
        $participante->telefono_casa = strtoupper($request->habitacion);
        $participante->email = strtoupper($request->email);
        $participante->camisa = strtoupper($request->camisa);
        $participante->pantalon = strtoupper($request->pantalon);
        $participante->zapatos = strtoupper($request->zapato);
        $participante->redes = strtoupper($request->redes);
        $participante->trabajoantes = strtoupper($request->trabajoantes);
        $participante->fechantes = isset($request->fechantes) ? Carbon::createfromformat('d/m/Y', $request->fechantes)->format('Y-m-d') : null;
        $participante->parientes = strtoupper($request->parientes);
        $participante->mencionelos = isset($request->mencionelos) ? strtoupper($request->mencionelos) : null;

        $participante->initbachiller = $request->initbachiller != null ? strtoupper($request->initbachiller) : null;
        $participante->endbachiller = $request->endbachiller != null ? strtoupper($request->endbachiller) : null;
        $participante->cursadobachiller = $request->cursadobachiller != null ? strtoupper($request->cursadobachiller) : null;
        $participante->titulobachiller = $request->titulobachiller != null ? strtoupper($request->titulobachiller) : null;
        $participante->instbachiller = $request->instbachiller != null ? strtoupper($request->instbachiller) : null;
        $participante->lugarbachiller = isset($request->lugarbachiller) ? strtoupper($request->lugarbachiller) : null;

        $participante->inittsu = isset($request->inittsu) ? strtoupper($request->inittsu) : null;
        $participante->endtsu = isset($request->endtsu) ? strtoupper($request->endtsu) : null;
        $participante->cursadotsu = isset($request->cursadotsu) ? strtoupper($request->cursadotsu) : null;
        $participante->titulotsu = isset($request->titulotsu) ? strtoupper($request->titulotsu) : null;
        $participante->insttsu = isset($request->insttsu) ? strtoupper($request->insttsu) : null;
        $participante->lugartsu = isset($request->lugartsu) ? strtoupper($request->lugartsu) : null;

        $participante->initespecializacion = isset($request->initespecializacion) ? strtoupper($request->initespecializacion) : null;
        $participante->endespecializacion = isset($request->endespecializacion) ? strtoupper($request->endespecializacion) : null;
        $participante->cursadoespecializacion = isset($request->cursadoespecializacion) ? strtoupper($request->cursadoespecializacion) : null;
        $participante->tituloespecializacion = isset($request->tituloespecializacion) ? strtoupper($request->tituloespecializacion) : null;
        $participante->instespecializacion = isset($request->instespecializacion) ? strtoupper($request->instespecializacion) : null;
        $participante->lugarespecializacion = isset($request->lugarespecializacion) ? strtoupper($request->lugarespecializacion) : null;

        $participante->initpostgrado = isset($request->initpostgrado) ? strtoupper($request->initpostgrado) : null;
        $participante->endpostgrado = isset($request->endpostgrado) ? strtoupper($request->endpostgrado) : null;
        $participante->cursadopostgrado = isset($request->cursadopostgrado) ? strtoupper($request->cursadopostgrado) : null;
        $participante->titulopostgrado = isset($request->titulopostgrado) ? strtoupper($request->titulopostgrado) : null;
        $participante->instpostgrado = isset($request->instpostgrado) ? strtoupper($request->instpostgrado) : null;
        $participante->lugarpostgrado = isset($request->lugarpostgrado) ? strtoupper($request->lugarpostgrado) : null;

        $participante->estudiosactuales = isset($request->estudiosactuales) ? strtoupper($request->estudiosactuales) : null;
        $participante->otrosestudios = isset($request->otrosestudios) ? strtoupper($request->otrosestudios) : null;

        $participante->referencia1 = isset($request->referencia1) ? strtoupper($request->referencia1) : null;
        $participante->telefono1 = isset($request->telefono1) ? strtoupper($request->telefono1) : null;
        $participante->ocuacion1 = isset($request->ocuacion1) ? strtoupper($request->ocuacion1) : null;
        $participante->refanios1 = isset($request->refanios1) ? strtoupper($request->refanios1) : null;

        $participante->referencia2 = isset($request->referencia2) ? strtoupper($request->referencia2) : null;
        $participante->telefono2 = isset($request->telefono2) ? strtoupper($request->telefono2) : null;
        $participante->ocuacion2 = isset($request->ocuacion2) ? strtoupper($request->ocuacion2) : null;
        $participante->refanios2 = isset($request->refanios2) ? strtoupper($request->refanios2) : null;

        $participante->nombreempresa1 = isset($request->nombreempresa1) ? strtoupper($request->nombreempresa1) : null;
        $participante->direccionempresa1 = isset($request->direccionempresa1) ? strtoupper($request->direccionempresa1) : null;
        $participante->ciudadempresa1 = isset($request->ciudadempresa1) ? strtoupper($request->ciudadempresa1) : null;
        $participante->telefonoempresa1 = isset($request->telefonoempresa1) ? strtoupper($request->telefonoempresa1) : null;
        $participante->jefeinmediato1 = isset($request->jefeinmediato1) ? strtoupper($request->jefeinmediato1) : null;
        $participante->cargo1 = isset($request->cargo1) ? strtoupper($request->cargo1) : null;
        $participante->acargo1 = isset($request->acargo1) ? strtoupper($request->acargo1) : null;
        $participante->fingreso1 = isset($request->fingreso1) ? strtoupper($request->fingreso1) : null;
        $participante->fegreso1 = isset($request->fegreso1) ? strtoupper($request->fegreso1) : null;
        $participante->tiempo1 = isset($request->tiempo1) ? strtoupper($request->tiempo1) : null;
        $participante->sueldo11 = isset($request->sueldo11) ? strtoupper($request->sueldo11) : null;
        $participante->sueldo12 = isset($request->sueldo12) ? strtoupper($request->sueldo12) : null;
        $participante->motivo1 = isset($request->motivo1) ? strtoupper($request->motivo1) : null;
        $participante->observaciones1 = isset($request->observaciones1) ? strtoupper($request->observaciones1) : null;

        $participante->nombreempresa2 = isset($request->nombreempresa2) ? strtoupper($request->nombreempresa2) : null;
        $participante->direccionempresa2 = isset($request->direccionempresa2) ? strtoupper($request->direccionempresa2) : null;
        $participante->ciudadempresa2 = isset($request->ciudadempresa2) ? strtoupper($request->ciudadempresa2) : null;
        $participante->telefonoempresa2 = isset($request->telefonoempresa2) ? strtoupper($request->telefonoempresa2) : null;
        $participante->jefeinmediato2 = isset($request->jefeinmediato2) ? strtoupper($request->jefeinmediato2) : null;
        $participante->cargo2 = isset($request->cargo2) ? strtoupper($request->cargo2) : null;
        $participante->acargo2 = isset($request->acargo2) ? strtoupper($request->acargo2) : null;
        $participante->fingreso2 = isset($request->fingreso2) ? strtoupper($request->fingreso2) : null;
        $participante->tiempo2 = isset($request->tiempo2) ? strtoupper($request->tiempo2) : null;
        $participante->fegreso2 = isset($request->fegreso2) ? strtoupper($request->fegreso2) : null;
        $participante->sueldo21 = isset($request->sueldo21) ? strtoupper($request->sueldo21) : null;
        $participante->sueldo22 = isset($request->sueldo22) ? strtoupper($request->sueldo22) : null;
        $participante->motivo2 = isset($request->motivo2) ? strtoupper($request->motivo2) : null;
        $participante->observaciones2 = isset($request->observaciones2) ? strtoupper($request->observaciones2) : null;
        $participante->save();

        \Session::flash('success', 'Informacion del prospecto actualizada correctamente.');
        return back();
    }
        
    public function paseToCursoCandidato($id, Request $request){
        
        try {
        $candidato = capCandidatosModel::find($id);
        
        if(ParticipantesModel::where('cedula_empleado',$candidato->cedula)->exists()){
            $participante =  ParticipantesModel::where('cedula_empleado',$candidato->cedula)->first();
        } else {
            $participante = new ParticipantesModel();
        }
     
        $participante->cedula_empleado  = $candidato->cedula;
        $participante->primer_nombre    = $candidato->nombres;
        $participante->segundo_nombre   = null;
        $participante->segundo_apellido = null;
        $participante->primer_apellido  = $candidato->apellidos;
        $participante->telefono_movil   = $candidato->telefono_celular;
        $participante->telefono_casa    = $candidato->telefono_casa;
        $participante->especialidad     = 1;
        $participante->horario_operativo =$request->horario;
        $participante->rec_cursos_id    = $request->curso;
        $participante->estatus_id       = 12;
        $participante->rec_observacion_id = 6;
        $participante->cambiocampana    = "NO";
        $participante->referido         = $request->referido;
        $participante->save();
        
        $historial = new HistorialModel();
        $historial->cedula_participante = $participante->cedula_empleado;
        $historial->nombre      = $participante->primer_nombre . " " . $participante->primer_apellido;
        $historial->id_curso    = $participante->rec_cursos_id;

        $buscar_campana     = CursosModel::select('cartera_id', 'skill_id','inicio', 'finaliza')
        ->where('id', $request->curso)->first();  
        
        $historial->cartera = CarteraModel::select('nombre_cartera')->where('id', $buscar_campana->cartera_id)->first()->nombre_cartera;
        $historial->skill   = SkillModels::select('nombre_skill')->where('id', $buscar_campana->skill_id)->first()->nombre_skill;

        $historial->fecha_inicio = $buscar_campana->inicio;
        $historial->fecha_fin = $buscar_campana->finaliza;       

        $historial->estatus_final = EstatusModel::select('estatus')->where('id', $participante->estatus_id)->first()->estatus;
        $historial->save();
        
        $candidato->cap_status_id = 3;
        $candidato->save();
        
            return json_encode(["message" => "Participante asignado al curso exitosamente" ]);
        } catch (\exception $e){
            return json_encode(abort(500,$e->getMessage()));
        }
    }
    
    public function GeneratePdfProspecto($id){
        
        $prospecto = capCandidatosModel::find($id);
        $nombre = $prospecto->nombres ." ".$prospecto->apellidos;
        $edocivil = $this->edocivil;
        $nacionalidad = $this->nacionalidades;
        
        return \PDF::loadView('reclutamiento.partials.planillaPrempleo', compact('prospecto','edocivil','nacionalidad'))
                
        ->setPaper('letter')->stream($nombre . '.pdf', array('Attachment' => 0));
    } 
}
