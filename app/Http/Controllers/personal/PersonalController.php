<?php

namespace App\Http\Controllers\personal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\ZonaTransporte;
use App\Models\personal\CargoModel;
use App\Models\personal\NominaModel;
use App\Models\personal\EstatusModel;
use App\Models\personal\LibranzasModel;
use App\Models\personal\CentroCostoAsignadoModel;
use App\Models\personal\EgresadosModel;
use App\Models\personal\BancosModels;
use App\Models\personal\ChangeCampaingModels;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Models\personal\HistoricoPersonalModel;
use App\Models\personal\SedeModel;
use App\Models\personal\MotivosEgresosModel; 
use App\Models\RegionModel;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;
use App\libs\ApiCheckautoservicios;
use App\Models\asistencia\AsistenciaOperativaModel;
use jeremykenedy\LaravelRoles\Models\Role;
use App\Imports\personal\PersonalImport;
use App\Imports\personal\UpdatePersonalImport;
use Maatwebsite\Excel\Facades\Excel;
use \jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use Carbon\Carbon;
use DatePeriod;
use DateTime;
use DateInterval;
use App\Http\Controllers\PoliceEstructure;

class PersonalController extends Controller {

    use ActivityLogger;

    public function __construct() {
        $this->middleware(['auth', 'verified', 'check.password', 'BlockRegistedPersonal']);

        $this->nacademicos = config('app.especialidad');
        $this->horariolab = config('app.horariolaboral');
        $this->sedes = SedeModel::where('active', 1)->get();
        $this->contractual = config('app.contractual') != null ? config('app.contractual') : [];
        $this->tipoContrato = Config('app.tipoContrato') != null ? config('app.tipoContrato') : [];
        $this->nacionalidades = Config('app.nacionalidad') != null ? config('app.nacionalidad') : [];
        $this->especialidades = config('app.especialidad') != null ? config('app.especialidad') : [];
        $this->hijo = config('app.hijos') != null ? config('app.hijos') : [];
        $this->horarioTransportes = config('app.horarioTransporte') != null ? config('app.horarioTransporte') : [];
        $this->jornadas = config('app.jornada') != null ? config('app.jornada') : [];
        $this->tiemposContrato = config('app.tiemposContrato') != null ? config('app.tiemposContrato') : [];
        $this->motivosEgresos = MotivosEgresosModel::select('id','descripcion')->where('active', 1)->orderBy('descripcion','ASC')->pluck('descripcion','id')->toArray();
        $this->CargoGerentes = CargoModel::where('nombre_cargo', 'like', 'Director%')
                        ->Orwhere('nombre_cargo', 'like', 'Gerente%')
                        ->Orwhere('nombre_cargo', 'like', 'Customer Technical Support Manager%')
                        ->Orwhere('nombre_cargo', 'like', '%Presidente%')->pluck('id');                   
    }

    public function Imagesave($file) {
        $name_file = time() . '_' . $file->getClientOriginalName();
        if (\Storage::disk('personal')->put($name_file, file_get_contents($file->getRealPath()))) {
            return $name_file;
        } else {
            return false;
        }
    }

    public function deleteImg($nombre_imagen) {
        if (\Storage::disk('personal')->delete($nombre_imagen)) {
            return true;
        } else {
            return false;
        }
    }

    public function index() {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        //\session()->regenerate();        
        $estatus = EstatusModel::where('group_module', 'R')->get();
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {

            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('personal.personal.index')->with(["areas" => $areas, "status" => $estatus]);
    }

    public function CreatePersonal() {

        $region = RegionModel::where('active', 1)->get();
        $gerente = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")
                        ->whereIn('cargo_id', $this->CargoGerentes)
                        ->whereIn('dir_personal.estatus_id', [1, 7, 8, 9, 10])
                        ->orderby('primer_nombre', 'ASC')->get();

        $cargo = CargoModel::where('estatus_id', 1)->orderby('nombre_cargo', 'ASC')->get();
        $nomina = NominaModel::all();
        $estatus = EstatusModel::where('group_module', 'R')->get();
        $libranza = LibranzasModel::where('estatus_id', 1)->orderby('libranzas', 'ASC')->get();
        $zonatransporte = ZonaTransporte::where('estatus_id', 1)->orderBy('zonatransporte', 'asc')->get();
        $areatrabajo = AreatrabajoModel::where('estatus_id', 1)->orderBy('nombre_campana', 'asc')->get();
        $asignarcar = CarteraModel::where('estatus_id', 1)->get();
        $banco = BancosModels::where('estatus_id', 1)->orderby('bancos', 'ASC')->get();
        ///rolo para activar usuarios de asistencia
        $rol = Role::where('slug', 'like', '%operativa%')->orWhere('slug', 'like', 'cursos%')->get();

        return view('personal.personal.create')->with([
                    "gerente" => $gerente,
                    "horariolab" => $this->horariolab,
                    "sedes" => $this->sedes,
                    "contractual" => $this->contractual,
                    "nomina" => $nomina,
                    "cargo" => $cargo,
                    "status" => $estatus,
                    "libranza" => $libranza,
                    "area" => $areatrabajo,
                    "zonatransporte" => $zonatransporte,
                    "asignarcar" => $asignarcar,
                    "tipoContrato" => $this->tipoContrato,
                    "nacionalidades" => $this->nacionalidades,
                    "especialidades" => $this->especialidades,
                    "horariosTransporte" => $this->horarioTransportes,
                    "hijo" => $this->hijo,
                    "jornadas" => $this->jornadas,
                    "banco" => $banco,
                    "roles" => $rol,
                    "tiemposContrato" => $this->tiemposContrato,
                    "region" => $region,
                    "motivoegreso"=> $this->motivosEgresos,
                ])->render();
    }

    public function create(request $request) {

        $data = array();
        $row = array();

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        //\session()->regenerate();    

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        $dato = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id", "cedula_empleado",
                "fecha_ingreso", "situacion_contractual", "area_trabajo_id", "cartera_id", "estatus_id", "skill_id", "cargo_id");
        /*  DESCRIPCION DE NIVELES
         *   4: personal asignado y por skill autorizado
         *   3: Personal Por skill Autorizado
         *   2: Personal solo por asignados
         *   1: todos los registros
         */
        switch ($nivel) {
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->whereIn('cartera_id', $carteraPermisada)
                            ->whereIn('skill_id', $skillPermisado)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('area_trabajo_id', $request->area)
                            ->whereIn('cartera_id', $carteraPermisada)
                            ->whereIn('skill_id', $skillPermisado)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('cartera_id', $request->cartera)
                            ->whereIn('skill_id', $skillPermisado)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('skill_id', $request->skill)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->wherein('id', $idPersonals)
                            ->where('skill_id', $request->skill)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus != null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->whereIn('cartera_id', $carteraPermisada)
                            ->whereIn('skill_id', $skillPermisado)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->wherein('id', $idPersonals)
                            ->where('area_trabajo_id', $request->area)
                            ->whereIn('skill_id', $skillPermisado)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus != null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('cartera_id', $request->cartera)
                            ->whereIn('skill_id', $skillPermisado)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //  $result = $dato->get();
                break;
            case 3:
                $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                ///SELECTORES EN TODOS
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->WhereIn('skill_id', $skillsautorize)->orderby('primer_nombre', 'ASC');
                }
                /// DATOS EN GERENCIA Y SELECTORES EN TODOS
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)->WhereIn('cartera_id', $carterasautorize)->orderby('primer_nombre', 'ASC');
                }
                /// DATOS EN GERENCIA Y UNIDAD EL RESTO EN TODOS
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('cartera_id', $request->cartera)->WhereIn('skill_id', $skillsautorize)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS ENGERENCIA, UNIDAD Y SKIL Y ESTATUS EN TODOS
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus == null) {
                    $result = $dato->where('cartera_id', $request->cartera)->Where('skill_id', $request->skill)->orderby('primer_nombre', 'ASC');
                }
                //DATOS EN TODOS LOS SELECTORES
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->where('cartera_id', $request->cartera)->Where('skill_id', $request->skill)->whereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS SOLO EN ESTATUS
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->wherein('skill_id', $skillsautorize)->whereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS EN GERENCIA Y ESTATUS
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('area_trabajo_id', $request->area)->WhereIn('cartera_id', $carterasautorize)->whereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS EN GERENCIA Y UNIDAD Y EN ESTATUS
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('cartera_id', $request->cartera)->WhereIn('skill_id', $skillsautorize)->whereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                //  $result = $dato->get();
                break;
            case 2:

                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills = $carterasPersonal[2];
                $personal = $carterasPersonal[3];

                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->wherein('skill_id', $skills)
                            ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area)
                            ->wherein('skill_id', $skills)
                            ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area)
                            ->where('cartera_id', $request->cartera)
                            ->wherein('skill_id', $skills)
                            ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area)
                            ->where('cartera_id', $request->cartera)
                            ->where('skill_id', $request->skill)
                            ->orderby('primer_nombre', 'ASC');
                }

                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area)
                            ->where('cartera_id', $request->cartera)
                            ->where('skill_id', $request->skill)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->wherein('skill_id', $skills)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area)
                            ->where('cartera_id', $request->cartera)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }

                //   $result = $dato->get();
                break;
            case 1:
                ////4 SELECTORES EN TODOS
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('area_trabajo_id', $request->area)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('cartera_id', $request->cartera)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus == null) {
                    $result = $dato->where('skill_id', $request->skill)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area != null && $request->cartera != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->where('skill_id', $request->skill)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area == null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area != null && $request->cartera == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('area_trabajo_id', $request->area)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area != null && $request->cartera != null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('cartera_id', $request->cartera)
                            ->whereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                // $result = $dato->get();
                break;
            default:
                ///retorna el array vacio, no posee permisos.
                return json_encode($row);
                break;
        }

        $permisosPersonal["edit"] = $user->hasPermission('personal.edit');
        $permisosPersonal["historico"] = $user->hasPermission('personal.historial.actualizacion');
        $permisosPersonal["remover"] = $user->hasPermission('personal.remove');

        $dato->chunk(1000, function ($result) use (&$data, &$permisosPersonal) {
            $deleteHtml = '';
            $editHtml = '';
            $viewHtml = '';
            $historialHtml = '';

            foreach ($result as $datos) {
                $row["id"] = $datos->id;
                $row["cedula"] = $datos->cedula_empleado;
                $row["nombres"] = ucwords(strtolower($datos->nombre));
                $row["dateingreso"] = date('d/m/Y', strtotime($datos->fecha_ingreso));
                $row["area_trabajo"] = $datos->RelationAreas ? $datos->RelationAreas->nombre_campana : "-";
                $row["skill"] = $datos->Relationskill ? $datos->Relationskill->nombre_skill : "-";
                $row["cartera"] = $datos->RelationCartera ? $datos->RelationCartera->nombre_cartera : "-";
                $row["cargo"] = $datos->RelationCargos ? $datos->RelationCargos->nombre_cargo : "-";
                $row["contrato"] = $datos->situacion_contractual;
                $row["estatus"] = $datos->RelationEstatus->estatus;

                $viewHtml = '   
                <a href="' . route('personal.view', $datos->id) . '" target="_blank" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Ver">
                      <i class="fa fa-eye"></i>  
                </a>';

                if ($permisosPersonal["edit"]) {
                    $editHtml = '            
                    <a href="' . route('personal.edit', $datos->id) . '" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Editar">
                        <i class="fa fa-edit"></i>
                    </a>';
                }

                if ($permisosPersonal["historico"]) {
                    $historialHtml = '            
                    <a href="' . route('historial.personal', $datos->id) . '" target="_blank" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-placement="top" title="Ver Historial">
                        <i class="fa fa-list"></i>
                    </a>';
                }

                if ($permisosPersonal["remover"]) {
                    $deleteHtml = '   
                    <a href="' . route('personal.remove', $datos->id) . '" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Remover">
                        <i class="fa fa-trash"</i>
                    </a>';
                }

                $row["acciones"] = '<div class="btn-group">' . $viewHtml . ' ' . $editHtml . ' ' . $historialHtml . ' ' . $deleteHtml . '</div>';
                $data[] = $row;
            }
        });

        return json_encode($data);
    }

    public function store(Request $request) {

        try {
            $objPersonal = new PersonalModel();
            $valid = true;
            if (!empty($request->image)) {
                $name_file = $this->Imagesave($request->image);
                if ($name_file) {
                    $objPersonal->image = $name_file;
                } else {
                    $valid = false;
                }
            }
            $objPersonal->cod_profit = $request->profit;
            $objPersonal->nacionalidad = $request->nacionalidad;
            $objPersonal->cedula_empleado = $request->cedula_empleado;
            $objPersonal->primer_nombre = ucfirst(strtolower($request->pnombre));
            $objPersonal->segundo_nombre = ucfirst(strtolower($request->snombre));
            $objPersonal->primer_apellido = ucfirst(strtolower($request->papellido));
            $objPersonal->segundo_apellido = ucfirst(strtolower($request->sapellido));
            $objPersonal->email = $request->email;
            $objPersonal->rif = $request->rif;
            $objPersonal->nivel_academico = $request->nacademico;
            $objPersonal->especialidad = $request->especialidad;

            $objPersonal->fecha_nacimiento = Carbon::createFromFormat('d/m/Y', $request->nacimiento)->format('Y-m-d');
            $objPersonal->sexo = $request->sexo;
            $objPersonal->telefono_casa = $request->tlf1;
            $objPersonal->telefono_movil = $request->tlf2;
            $objPersonal->hijos = $request->hijos;

            $objPersonal->estado_id = in_array($request->sede, ['STV', 'ATENAS']) ? $request->estado : 3;
            $objPersonal->ciudad_id = in_array($request->sede, ['STV', 'ATENAS']) ? $request->ciudad : 149;

            $objPersonal->sector = $request->sector;
            $objPersonal->direccion = $request->direccion;

            switch ($request->activetransporte) {
                case "on":
                    $objPersonal->activetransporte = 1;
                    $objPersonal->htransporte = $request->htransporte;
                    $objPersonal->zona_transporte_id = $request->zona_transporte_id;
                    break;
                default:
                    $objPersonal->activetransporte = 0;
                    $objPersonal->htransporte = null;
                    $objPersonal->zona_transporte_id = 56; // no aplica
                    break;
            }

            $objPersonal->fecha_ingreso = Carbon::createFromFormat('d/m/Y', $request->fingreso)->format('Y-m-d');
            $objPersonal->nomina_id = $request->nomina;
            $objPersonal->cargo_id = $request->cargo;
            $objPersonal->estatuscargo = $request->estatuscargo;
            
            $objPersonal->nacionalidad_cuenta = $request->nacionalidad_cuenta;
            $objPersonal->cedula_titular = $request->cedulatitular;
            $objPersonal->nombre_titular = ucfirst(strtolower($request->nombretitular));
            $objPersonal->bancos_id = $request->banco;
            $objPersonal->cuenta_bancaria = $request->ctabancaria;
            
            $objPersonal->todoticket_integral     = $request->integral;
            $objPersonal->todoticket_alimentacion = $request->alimentacion;
                
            $objPersonal->sede_laboral = $request->sede;
            $objPersonal->horario_operativo = $request->horario;
            $objPersonal->jornada = $request->jornada;

            if (in_array($request->horario, [1, 2, 3, 4, 5])) {
                $objPersonal->turno = "AM";
            } else {
                $objPersonal->turno = "PM";
            }
            // date("H:i:s", strtotime($request->hentrada));
            $objPersonal->libranza_id = $request->libranza;
            $objPersonal->horario_entrada = Carbon::createFromFormat('H:i a', $request->hentrada)->format('H:i:s');
            $objPersonal->horario_salida = Carbon::createFromFormat('H:i a', $request->hsalida)->format('H:i:s');

            $objPersonal->gerente_area_id = $request->gerente == "0" ? null : $request->gerente;
            $objPersonal->jefe_inmediato = $request->jinmediato == "0" ? null : $request->jinmediato;
            $objPersonal->jefe_inmediato_segundo = $request->jinmediato2 == "0" ? null : $request->jinmediato2;

            $objPersonal->area_trabajo_id = $request->areatrabajo;
            $objPersonal->cartera_id = $request->cartera;
            $objPersonal->skill_id = $request->skill;
            $objPersonal->estatus_id = $request->status;

            $objPersonal->contrato_1 = Carbon::createFromFormat('d/m/Y', $request->contrato1)->format('Y-m-d');
            $objPersonal->finalizacion_1 = Carbon::createFromFormat('d/m/Y', $request->finalcontrato1)->format('Y-m-d');

            if (!empty($request->contrato2)) {
                $objPersonal->contrato_2 = Carbon::createFromFormat('d/m/Y', $request->contrato2)->format('Y-m-d');
                $objPersonal->finalizacion_2 = Carbon::createFromFormat('d/m/Y', $request->finalcontrato2)->format('Y-m-d');
            } else {
                $objPersonal->contrato_2 = null;
            }
            $objPersonal->situacion_contractual = $request->scontrato;
            $objPersonal->status_carnet = 3; //// por defecto se esta colocando no impreso
            $objPersonal->save();

            $id = $objPersonal->id;

            switch ($request->fegreso) {
                case null:
                    $objPersonal->fecha_egreso = null;
                    $objPersonal->motivo_egreso = null;
                    break;
                default:
                    $egreso = Carbon::createFromFormat('d/m/Y', $request->fegreso)->format('Y-m-d');
                    $objPersonal->fecha_egreso = $egreso;
                    $objPersonal->motivo_egreso = ucfirst(strtolower($request->motivoegreso));

                    if (!EgresadosModel::where('fecha', $egreso)->where('dir_personal_id', $id)->exists()) {
                        $nuevo_egreso = new EgresadosModel();
                        $nuevo_egreso->fecha = $egreso;
                        $nuevo_egreso->motivo = ucwords(strtolower($request->motivoegreso));
                        $nuevo_egreso->dir_personal_id = $id;
                        $nuevo_egreso->jefe_inmediato = $objPersonal->jefe_inmediato;
                        $nuevo_egreso->area_trabajo_id = $objPersonal->area_trabajo_id;
                        $nuevo_egreso->cartera_id = $objPersonal->cartera_id;
                        $nuevo_egreso->skill_id = $objPersonal->skill_id;
                        $nuevo_egreso->save();
                    }
                    /// eliminamos las asistencias tomadas a partir de su fecha de egreso hasta el dia en curso
                    AsistenciaOperativaModel::where('personal_id', $id)
                            ->whereBetween('created_at', array("$egreso 00:00:01", date('Y-m-d H:m:s', strtotime(now()))))->delete();

                    if (env('ACTIVE_DISBLED_AUTOSERVICIOS')) {
                        $bashurl = "URL_DISBLED_AUTOSERVICIOS";
                        $api = new ApiCheckautoservicios($bashurl, $objPersonal->cedula_empleado);
                        $api->CheckInfinityPersonal();
                        ///va contra autoservicios y verifica elimina el usuario si existe 
                    }

                    switch ($request->recontratar) {
                        case null:
                            if (!BlacklistModel::where('cedula_empleado', $request->cedula_empleado)->exists()) {
                                $blacklist = new BlacklistModel();
                                $blacklist->cedula_empleado = $request->cedula_empleado;
                                $blacklist->primer_nombre = ucfirst(strtolower($request->pnombre));
                                $blacklist->segundo_nombre = ucfirst(strtolower($request->snombre));
                                $blacklist->primer_apellido = ucfirst(strtolower($request->papellido));
                                $blacklist->segundo_apellido = ucfirst(strtolower($request->sapellido));
                                $blacklist->motivos = $request->norecontratar;
                                $blacklist->save();
                            }
                            break;
                    }
                    break;
            }
            $objPersonal->save();
            ////SWITCH SPARA GUARDAR LOS PORCENTAJES DE LOS CECOS
            switch (\Auth::user()->haspermission('personal.cecos.autorice')) {
                case true:
                    /// almaceno los porcentajes suministrados en un array separadado por coma
                    $porcentajes = explode(",", $request->porcentaje);
                    $i = 0;
                    foreach ($request->ceco as $centro) {
                        $areas = new CentroCostoAsignadoModel();
                        $areas->personal_id = $objPersonal->id;
                        $areas->cartera_id = $objPersonal->cartera_id;
                        $areas->skill_id = $centro;
                        $areas->porcentaje = $porcentajes[$i];
                        $areas->save();
                        $i++;
                    }
                    break;
                default:
                    foreach ($request->ceco as $centro) {
                        $areas = new CentroCostoAsignadoModel();
                        $areas->personal_id = $objPersonal->id;
                        $areas->cartera_id = $objPersonal->cartera_id;
                        $areas->skill_id = $centro;
                        $areas->porcentaje = null;
                        $areas->save();
                    }
                    break;
            }

            switch ($request->useractive) {
                case "on":
                    if ($objPersonal->RelationUser == null) {
                        $password = "directagroup";
                        $user = new User();
                        $user->name = ucfirst(strtolower($request->name));
                        $user->cedula = $request->cedula_empleado;
                        $user->username = $request->username;
                        $user->email = $request->useremail;
                        $user->email_verified_at = date('Y-m-d h:m:i', strtotime(now()));
                        $user->password = $password;
                        $user->estatus_id = 1;
                        $user->save();

                        $user->attachRole($request->roles);

                        $objPersonal->user_id = $user->id;
                        $objPersonal->save();
                    }
                    break;
            }

            $objeto_personal = json_encode($objPersonal);
            ActivityLogger::activity("Personal registrado " . $objPersonal->cedula_empleado, $objeto_personal);

            \Session::flash('success', 'El Expediente personal fue almacenado exitosamente.');
            return redirect(route('personal.add'));
        } catch (\Exception $e) {
            \Session::flash('error', "Ocurrio un problema " . $e->getMessage());
            return back();
        }
    }

    public function edit($id) {

        $personal = PersonalModel::find($id);

        $ciudad = $personal->ciudad_id !== null ? CiudadesModel::find($personal->ciudad_id) : CiudadesModel::find(149);
        $estado = $personal->estado_id !== null ? EstadosModel::find($personal->estado_id) : EstadosModel::find(3);
        $region = RegionModel::where('active', 1)->get();

        $supervisor1 = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id")->where('id', $personal->jefe_inmediato)->first();
        $supervisor2 = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id")->where('id', $personal->jefe_inmediato_segundo)->first();
        $cargo = CargoModel::Where('estatus_id', 1)->OrderBy('nombre_cargo', 'asc')->get();
        $libranza = LibranzasModel::Where('estatus_id', 1)->OrderBy('id', 'asc')->get();

        $acargo = CargoModel::where('nombre_cargo', 'like', 'Director%')
                        ->Orwhere('nombre_cargo', 'like', 'Gerente%')
                        ->Orwhere('nombre_cargo', 'like', 'Customer Technical Support Manager%')
                        ->Orwhere('nombre_cargo', 'like', '%Presidente%')->pluck('id');

        $gerente = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")
                        ->whereIn('cargo_id', $acargo)
                        ->whereIn('dir_personal.estatus_id', [1, 7, 8, 9, 10])
                        ->orderby('primer_nombre', 'ASC')->get();

        $nomina = NominaModel::Where('estatus_id', 1)->get();
        $estatus = EstatusModel::where('group_module', 'R')->get();

        $area = AreatrabajoModel::Where('estatus_id', 1)->get();

        $cartera = CarteraModel::where('area_trabajo_id', $personal->area_trabajo_id)
                        ->where('estatus_id', 1)->get();

        $skill = SkillModels::where('id', $personal->skill_id)->get();
        //dd($skill,$personal->skill_id);
        $zonas = ZonaTransporte::where('estatus_id', 1)->orderBy('zonatransporte', 'asc')->get();
        $banco = BancosModels::where('estatus_id', 1)->orderby('bancos', 'ASC')->get();

        $rol = Role::where('slug', 'like', '%operativa%')->orWhere('slug', 'like', 'cursos%')->get();
        $asisop = Role::where('slug', 'like', '%operativa%')->orWhere('slug', 'like', 'cursos%')->pluck('id')->toArray();

        $originblacklist = BlacklistModel::where('cedula_empleado', $personal->cedula_empleado)->first();

        if (BlacklistModel::where('cedula_empleado', $personal->cedula_empleado)->exists()) {
            $listanegra = "si"; /// true si no hay resultados
        } else {
            $listanegra = "no"; /// false si hay registros
        }
        if ($personal->RelationUser == null) {
            $autorice = false; /// no tiene usuario relacionado
            $rolactive = 0;
        } else if ($personal->RelationUser != null && in_array($personal->RelationUser->roles()->first()->id, $asisop)) {
            $autorice = true; /// tiene usuario pero es de asistencia y curso
            $rolactive = 1;
        } else {
            $autorice = false; /// tiene usuario pero con otro roll
            $rolactive = 2;
        }
        //dd($styleVacaciones);
        return view('personal.personal.editar')
                        ->with([
                            "blacklist" => $listanegra,
                            "originblacklist" => $originblacklist,
                            "personal" => $personal,
                            "supervisor1" => $supervisor1,
                            "supervisor2" => $supervisor2,
                            "gerente" => $gerente,
                            "academicos" => $this->nacademicos,
                            "horariolab" => $this->horariolab,
                            "sedes" => $this->sedes,
                            "contractual" => $this->contractual,
                            "nomina" => $nomina,
                            "cargo" => $cargo,
                            "status" => $estatus,
                            "libranza" => $libranza,
                            "area" => $area,
                            "cartera" => $cartera,
                            "skill" => $skill,
                            "zonatransporte" => $zonas,
                            "tipoContrato" => $this->tipoContrato,
                            "nacionalidades" => $this->nacionalidades,
                            "especialidades" => $this->especialidades,
                            "horariosTransporte" => $this->horarioTransportes,
                            "hijo" => $this->hijo,
                            "jornadas" => $this->jornadas,
                            "banco" => $banco,
                            "roles" => $rol,
                            "autorice" => $autorice,
                            "rolactive" => $rolactive,
                            "ciudad" => $ciudad,
                            "estado" => $estado,
                            "region" => $region,
                            "tiemposContrato" => $this->tiemposContrato,
                            "motivoegreso"=> $this->motivosEgresos,
        ]);
    }

    public function ViewPersonal($id) {

        $personal = PersonalModel::find($id);
        $ciudad = $personal->ciudad_id !== null ? CiudadesModel::find($personal->ciudad_id) : CiudadesModel::find(149);
        $estado = $personal->estado_id !== null ? EstadosModel::find($personal->estado_id) : EstadosModel::find(3);
        $region = RegionModel::where('active', 1)->get();

        $supervisor1 = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id")->where('id', $personal->jefe_inmediato)->first();
        $supervisor2 = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id")->where('id', $personal->jefe_inmediato_segundo)->first();
        $cargo = CargoModel::Where('estatus_id', 1)->OrderBy('nombre_cargo', 'asc')->get();
        $libranza = LibranzasModel::Where('estatus_id', 1)->OrderBy('id', 'asc')->get();

        $acargo = CargoModel::where('nombre_cargo', 'like', 'Director%')
                        ->Orwhere('nombre_cargo', 'like', 'Gerente%')
                        ->Orwhere('nombre_cargo', 'like', 'Customer Technical Support Manager%')
                        ->Orwhere('nombre_cargo', 'like', '%Presidente%')->pluck('id');

        $gerente = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")
                        ->whereIn('cargo_id', $acargo)
                        ->whereIn('dir_personal.estatus_id', [1, 7, 8, 9, 10])
                        ->orderby('primer_nombre', 'ASC')->get();

        $nomina = NominaModel::Where('estatus_id', 1)->get();
        $estatus = EstatusModel::where('group_module', 'R')->get();

        $area = AreatrabajoModel::Where('estatus_id', 1)->get();

        $cartera = CarteraModel::where('area_trabajo_id', $personal->area_trabajo_id)
                        ->where('estatus_id', 1)->get();

        $skill = SkillModels::where('id', $personal->skill_id)->get();
        //dd($skill,$personal->skill_id);
        $zonas = ZonaTransporte::where('estatus_id', 1)->orderBy('zonatransporte', 'asc')->get();
        $banco = BancosModels::where('estatus_id', 1)->orderby('bancos', 'ASC')->get();

        $rol = Role::where('slug', 'like', '%operativa%')->orWhere('slug', 'like', 'cursos%')->get();
        $asisop = Role::where('slug', 'like', '%operativa%')->orWhere('slug', 'like', 'cursos%')->pluck('id')->toArray();

        $originblacklist = BlacklistModel::where('cedula_empleado', $personal->cedula_empleado)->first();

        if (BlacklistModel::where('cedula_empleado', $personal->cedula_empleado)->exists()) {
            $listanegra = "si"; /// true si no hay resultados
        } else {
            $listanegra = "no"; /// false si hay registros
        }
        if ($personal->RelationUser == null) {
            $autorice = false; /// no tiene usuario relacionado
            $rolactive = 0;
        } else if ($personal->RelationUser != null && in_array($personal->RelationUser->roles()->first()->id, $asisop)) {
            $autorice = true; /// tiene usuario pero es de asistencia y curso
            $rolactive = 1;
        } else {
            $autorice = false; /// tiene usuario pero con otro roll
            $rolactive = 2;
        }

        return view('personal.personal.view')
                        ->with([
                            "blacklist" => $listanegra,
                            "originblacklist" => $originblacklist,
                            "personal" => $personal,
                            "supervisor1" => $supervisor1,
                            "supervisor2" => $supervisor2,
                            "gerente" => $gerente,
                            "academicos" => $this->nacademicos,
                            "horariolab" => $this->horariolab,
                            "sedes" => $this->sedes,
                            "contractual" => $this->contractual,
                            "nomina" => $nomina,
                            "cargo" => $cargo,
                            "status" => $estatus,
                            "libranza" => $libranza,
                            "area" => $area,
                            "cartera" => $cartera,
                            "skill" => $skill,
                            "zonatransporte" => $zonas,
                            "tipoContrato" => $this->tipoContrato,
                            "nacionalidades" => $this->nacionalidades,
                            "especialidades" => $this->especialidades,
                            "horariosTransporte" => $this->horarioTransportes,
                            "hijo" => $this->hijo,
                            "jornadas" => $this->jornadas,
                            "banco" => $banco,
                            "roles" => $rol,
                            "autorice" => $autorice,
                            "rolactive" => $rolactive,
                            "ciudad" => $ciudad,
                            "estado" => $estado,
                            "region" => $region,
                            "tiemposContrato" => $this->tiemposContrato,
                            "motivoegreso"=> $this->motivosEgresos
                        ])->render();
    }

    private function HistoricosStore($objPersonal) {

        $historial = new HistoricoPersonalModel();
        $historial->cod_profit = $objPersonal->cod_profit;
        $historial->image = $objPersonal->image;
        $historial->nacionalidad = $objPersonal->nacionalidad;
        $historial->cedula_empleado = $objPersonal->cedula_empleado;
        $historial->primer_nombre = $objPersonal->primer_nombre;
        $historial->segundo_nombre = $objPersonal->segundo_nombre;
        $historial->primer_apellido = $objPersonal->primer_apellido;
        $historial->segundo_apellido = $objPersonal->segundo_apellido;
        $historial->email = $objPersonal->email;
        $historial->nivel_academico = $objPersonal->getrawOriginal('nivel_academico');
        $historial->especialidad = $objPersonal->especialidad;
        $historial->rif = $objPersonal->rif;
        $historial->estado_id = $objPersonal->estado_id;
        $historial->ciudad_id = $objPersonal->ciudad_id;
        $historial->fecha_nacimiento = $objPersonal->fecha_nacimiento;
        $historial->sexo = $objPersonal->sexo;
        $historial->telefono_casa = $objPersonal->telefono_casa;
        $historial->telefono_movil = $objPersonal->telefono_movil;
        $historial->hijos = $objPersonal->hijos;
        $historial->sector = $objPersonal->sector;
        $historial->activetransporte = $objPersonal->activetransporte;
        $historial->htransporte = $objPersonal->htransporte;
        $historial->zona_transporte_id = $objPersonal->zona_transporte_id;
        $historial->direccion = $objPersonal->direccion;
        $historial->fecha_ingreso = $objPersonal->fecha_ingreso;
        $historial->nomina_id = $objPersonal->nomina_id;
        $historial->cargo_id = $objPersonal->cargo_id;
        $historial->estatuscargo = $objPersonal->getrawOriginal('estatuscargo');
        
       
        $historial->nacionalidad_cuenta = $objPersonal->nacionalidad_cuenta;
        $historial->cedula_titular = $objPersonal->cedula_titular;
        $historial->nombre_titular = ucfirst(strtolower($objPersonal->nombre_titular));
        $historial->bancos_id = $objPersonal->bancos_id;
        $historial->cuenta_bancaria = $objPersonal->cuenta_bancaria;
        $historial->todoticket_integral     = $objPersonal->todoticket_integral;
        $historial->todoticket_alimentacion = $objPersonal->todoticket_alimentacion;
        
        
        $historial->sede_laboral = $objPersonal->sede_laboral;
        $historial->horario_operativo = $objPersonal->horario_operativo;
        $historial->jornada = $objPersonal->getrawOriginal('jornada');
        $historial->turno = $objPersonal->turno;

        $historial->libranza_id = $objPersonal->libranza_id;
        $historial->horario_entrada = $objPersonal->horario_entrada;
        $historial->horario_salida = $objPersonal->horario_salida;
        $historial->gerente_area_id = $objPersonal->gerente_area_id;
        $historial->jefe_inmediato = $objPersonal->jefe_inmediato;
        $historial->jefe_inmediato_segundo = $objPersonal->jefe_inmediato_segundo;
        $historial->area_trabajo_id = $objPersonal->area_trabajo_id;
        $historial->cartera_id = $objPersonal->cartera_id;
        $historial->skill_id = $objPersonal->skill_id;
        $historial->contrato_1 = $objPersonal->contrato_1;
        $historial->finalizacion_1 = $objPersonal->finalizacion_1;
        $historial->contrato_2 = $objPersonal->contrato_2;
        $historial->finalizacion_2 = $objPersonal->finalizacion_2;
        $historial->situacion_contractual = $objPersonal->getrawOriginal('situacion_contractual');
        $historial->status_carnet = $objPersonal->status_carnet;
        $historial->fecha_egreso = $objPersonal->fecha_egreso;
        $historial->motivo_egreso = $objPersonal->motivo_egreso;
        $historial->estatus_id = $objPersonal->estatus_id;
        $historial->vacaciones_init = $objPersonal->vacaciones_init;
        $historial->vacaciones_end  = $objPersonal->vacaciones_end;
        $historial->user_id = null;

        //GUARDAMOS QUIEN ACTUALIZO EL REGISTRO
        $historial->modificado_por = \Auth::user()->username;
        //GUARDAMOS EL PERSONAL_ID PARA IDENTIFICAR DE QUIEN ES EL HISTORIAL
        $historial->personal_id = $objPersonal->id;
        $historial->save();

        return $historial;
    }

    private function GetDirtyHistorico($objPersonal, $historial) {

        $nuevos_valores = [];
        $valores_alt = [];

        // Verificar cambios realizados
        $changedFields = $objPersonal->getDirty(); // Obtiene los campos que fueron modificados
        // Antes de guardar, puedes inspeccionar qué ha cambiado
        if (!empty($changedFields)) {
            foreach ($changedFields as $field => $newValue) {
                $oldValue = $oldValues[$field] ?? null;
                $nuevos_valores[$field] = $newValue;
                $valores_alt[] = $field;
            }
        }

        // Convertir el array a JSON
        $cambiosJson = json_encode($nuevos_valores);
        $valores_alterados = json_encode($valores_alt);

        $historial->campos_alt = $valores_alterados;
        $historial->valores_nuevos = $cambiosJson;
        $historial->save();
    }

    public function update(Request $request, $id) {
      
        $objPersonal = PersonalModel::find($id);
        //ANTES DE ACTUALIZAR EL REGISTRO GUARDAMOS UN HISTORIAL DEL MISMO
        $historico = $this->HistoricosStore($objPersonal, $request);
        try {
            if ($request->image != null) {
                $this->deleteImg($objPersonal->image);
                $name_file = $this->Imagesave($request->image);
                if ($name_file) {
                    $objPersonal->image = $name_file;
                }
            }

            $objPersonal->cod_profit = $request->profit;
            $objPersonal->nacionalidad = $request->nacionalidad;
            $objPersonal->cedula_empleado = $request->cedula_empleado;
            $objPersonal->primer_nombre = ucwords(strtolower($request->pnombre));
            $objPersonal->segundo_nombre = ucwords(strtolower($request->snombre));
            $objPersonal->primer_apellido = ucwords(strtolower($request->papellido));
            $objPersonal->segundo_apellido = ucwords(strtolower($request->sapellido));
            $objPersonal->email = $request->email;
            $objPersonal->nivel_academico = $request->nacademico;
            $objPersonal->especialidad = $request->especialidad;
            $objPersonal->rif = $request->rif;
            $objPersonal->fecha_nacimiento = Carbon::createFromFormat('d/m/Y', $request->nacimiento)->format('Y-m-d');
            $objPersonal->sexo = $request->sexo;
            $objPersonal->telefono_casa = $request->tlf1;
            $objPersonal->telefono_movil = $request->tlf2;
            $objPersonal->hijos = $request->hijos;
            $objPersonal->estado_id = in_array($request->sede, ['STV', 'ATENAS']) ? $request->estado : 3;
            $objPersonal->ciudad_id = in_array($request->sede, ['STV', 'ATENAS']) ? $request->ciudad : 149;
            $objPersonal->sector = $request->sector;

            switch ($request->activetransporte) {
                case "on":
                    $objPersonal->activetransporte = 1;
                    $objPersonal->htransporte = $request->htransporte;
                    $objPersonal->zona_transporte_id = $request->zona_transporte_id;
                    break;
                default:
                    $objPersonal->activetransporte = 0;
                    $objPersonal->htransporte = null;
                    $objPersonal->zona_transporte_id = 56;
                    break;
            }

            $objPersonal->direccion = $request->direccion;
            $objPersonal->fecha_ingreso = Carbon::createFromFormat('d/m/Y', $request->fingreso)->format('Y-m-d');
            $objPersonal->nomina_id = $request->nomina;
            $objPersonal->cargo_id = $request->cargo;
            $objPersonal->estatuscargo = $request->estatuscargo;            

            ///Si el Usuario posee Permisos para Modificar las cuentas Bancarias o el valor almacenado esta Vacio
            if (\Auth::user()->hasPermission('personal.update.ctabancarias') ||
                ($objPersonal->nombre_titular == null 
                || $objPersonal->nacionalidad_cuenta == null
                || $objPersonal->cedula_titular == null
                || $objPersonal->bancos_id == null 
                || $objPersonal->cuenta_bancaria == null                
                #|| $personal->todoticket_integral == '0' 
                #|| $personal->todoticket_alimentacion == '0'
                )) {
               
                $objPersonal->nacionalidad_cuenta   = isset($request->nacionalidad_cuenta) ? $request->nacionalidad_cuenta : $objPersonal->nacionalidad_cuenta;
                $objPersonal->cedula_titular        = isset($request->cedulatitular) ? $request->cedulatitular : $objPersonal->cedula_titular;
                $objPersonal->nombre_titular        = isset($request->nombretitular) ? ucfirst(strtolower($request->nombretitular)) : $objPersonal->nombre_titular;
                $objPersonal->bancos_id             = isset($request->banco) ? $request->banco : $objPersonal->bancos_id;
                $objPersonal->cuenta_bancaria       = isset($request->ctabancaria) ? $request->ctabancaria : $objPersonal->cuenta_bancaria;                
                $objPersonal->todoticket_integral     = isset($request->integral) ? $request->integral : $objPersonal->todoticket_integral;
                $objPersonal->todoticket_alimentacion = isset($request->alimentacion) ? $request->alimentacion : $objPersonal->todoticket_alimentacion;
            }

            $objPersonal->sede_laboral      = $request->sede;
            $objPersonal->horario_operativo = $request->horario;
            $objPersonal->jornada           = $request->jornada;

            if (in_array($request->horario, [1, 2, 3, 4, 5])) {
                $objPersonal->turno = "AM";
            } else {
                $objPersonal->turno = "PM";
            }

            $objPersonal->libranza_id = $request->libranza;
            $objPersonal->horario_entrada = Carbon::createFromFormat('H:i a', $request->hentrada)->format('H:i:s');
            $objPersonal->horario_salida = Carbon::createFromFormat('H:i a', $request->hsalida)->format('H:i:s');
            $objPersonal->gerente_area_id = $request->gerente == "0" ? null : $request->gerente;
            $objPersonal->jefe_inmediato = $request->jinmediato == "0" ? null : $request->jinmediato;
            $objPersonal->jefe_inmediato_segundo = $request->jinmediato2 == "0" ? null : $request->jinmediato2;

            ///siempre y cuando la gerencia sea operaciones y la cartera cambie almacenamos el dato para los camibos de campaña
            if ($request->areatrabajo == 2 && $objPersonal->cartera_id != $request->cartera) {
                $change = new ChangeCampaingModels();
                $change->personal_id = $id;
                $change->area_trabajo_id = $request->areatrabajo;
                $change->actual_cartera_id = $objPersonal->cartera_id;
                $change->actual_skill_id = $objPersonal->skill_id;

                $change->nueva_cartera_id = $request->cartera;
                $change->nuevo_skill_id = $request->skill;
                $change->save();
            }

            $objPersonal->area_trabajo_id = $request->areatrabajo;
            $objPersonal->cartera_id = $request->cartera;
            $objPersonal->skill_id = $request->skill;

            $objPersonal->contrato_1 = Carbon::createFromFormat('d/m/Y', $request->contrato1)->format('Y-m-d');
            $objPersonal->finalizacion_1 = Carbon::createFromFormat('d/m/Y', $request->finalcontrato1)->format('Y-m-d');

            if (!empty($request->contrato2)) {
                $objPersonal->contrato_2 = Carbon::createFromFormat('d/m/Y', $request->contrato2)->format('Y-m-d');
                $objPersonal->finalizacion_2 = Carbon::createFromFormat('d/m/Y', $request->finalcontrato2)->format('Y-m-d');
            } else {
                $objPersonal->contrato_2 = null;
            }

            $objPersonal->situacion_contractual = $request->scontrato;
            $objPersonal->status_carnet = 3;

            $rs = null;
            $nivel = 0;
            if ($request->status == 1 && $request->fegreso != null) {
                $nivel = 1; ///activo
            } elseif (($request->status == 2 || $request->status == 6) && $request->fegreso != null) {
                $nivel = 2; //engresado
            } elseif ($request->status == 1 && $request->fegreso == null) {
                $nivel = 1; ///activo
            }


            switch ($nivel) {
                case 1:
                    $objPersonal->fecha_egreso = null;
                    $objPersonal->motivo_egreso = null;
                    break;
                case 2:
                    $egreso = Carbon::createFromFormat('d/m/Y', $request->fegreso)->format('Y-m-d');
                    $objPersonal->fecha_egreso = $egreso;
                    $objPersonal->motivo_egreso = ucfirst(strtolower($request->motivoegreso));

                    if (!EgresadosModel::where('fecha', $egreso)->where('dir_personal_id', $id)->exists()) {
                        $nuevo_egreso = new EgresadosModel();
                        $nuevo_egreso->fecha = $egreso;
                        $nuevo_egreso->motivo = ucwords(strtolower($request->motivoegreso));
                        $nuevo_egreso->dir_personal_id = $id;
                        $nuevo_egreso->jefe_inmediato = $objPersonal->jefe_inmediato;
                        $nuevo_egreso->area_trabajo_id = $objPersonal->area_trabajo_id;
                        $nuevo_egreso->cartera_id = $objPersonal->cartera_id;
                        $nuevo_egreso->skill_id = $objPersonal->skill_id;
                        $nuevo_egreso->save();
                    }
                    /// eliminamos las asistencias tomadas a partir de su fecha de egreso hasta el dia en curso
                    AsistenciaOperativaModel::where('personal_id', $objPersonal->id)
                            ->whereBetween('created_at', array("$egreso 00:00:01", date('Y-m-d H:m:s', strtotime(now()))))->delete();

                    ///va contra autoservicios y verifica elimina el usuario si existe 
                    if (ENV('ACTIVE_API_AUTOSERVICIOS', false)) {
                        $bashurl = "URL_DISBLED_AUTOSERVICIOS";
                        $api = new ApiCheckautoservicios($bashurl, $objPersonal->cedula_empleado);
                        $rs = $api->CheckInfinityPersonal();
                    }

                    switch ($request->recontratar) {
                        case null:
                            $blacklist = new BlacklistModel();
                            $blacklist->cedula_empleado = $request->cedula_empleado;
                            $blacklist->primer_nombre = ucfirst(strtolower($request->pnombre));
                            $blacklist->segundo_nombre = ucfirst(strtolower($request->snombre));
                            $blacklist->primer_apellido = ucfirst(strtolower($request->papellido));
                            $blacklist->segundo_apellido = ucfirst(strtolower($request->sapellido));
                            $blacklist->motivos = $request->norecontratar;
                            $blacklist->save();
                            break;
                    }
                    break;
            }

            /// creo un array con los estatus de egreso, abandono, inactivo. y validamos si lso datos guardados
            /// tiene alguno de estos estatus si es correcto, actualizaremos el Created_at como si fuera un registro nuevo
            //para los casos de re ingreso.            
            $estatus = [2, 3, 6];

            if (in_array($objPersonal->getrawOriginal('estatus_id'), $estatus) && empty($request->fegreso)) {

                $objPersonal->estatus_id = $request->status;
                $objPersonal->created_at = date('Y-m-d H:i:s', strtotime(now()));
            } else {
                $objPersonal->estatus_id = $request->status;
            }

          
            switch (\Auth::user()->haspermission('personal.cecos.autorice')) {
                case true;
                    $objPersonal->RelationCentroCosto()->delete();
                    $i = 0;
                    $porcentajes = explode(",", $request->porcentaje);
                    foreach ($request->ceco as $centro) {
                        $areas = new CentroCostoAsignadoModel();
                        $areas->personal_id = $objPersonal->id;
                        $areas->cartera_id = $objPersonal->cartera_id;
                        $areas->skill_id = $centro;
                        if ($porcentajes[0] != null) {
                            $areas->porcentaje = $porcentajes[$i];
                        } else {
                            $areas->porcentaje = null;
                        }
                        $areas->save();
                        $i++;
                    }
                    break;
                case false:
                    
                    $objPersonal->RelationCentroCosto()->delete();

                    foreach ($request->ceco as $centro) {
                        $areas = new CentroCostoAsignadoModel();
                        $areas->personal_id = $objPersonal->id;
                        $areas->cartera_id = $objPersonal->cartera_id;
                        $areas->skill_id = $centro;
                        $areas->save();
                    }
                    break;
            }
            /// $request->rolopactive 0 no posee, 1 si tiene usuario, 2 tiene otro rol
            switch ($request->rolopactive) {
                case 0: /// no posee rol de tipo operativo o no posee usuario
                    if ($request->useractive == "on") {
                        ////si el usuario no existe y no esta eliminado
                        if (!User::where('cedula', $objPersonal->cedula_empleado)->withTrashed()->exists()) {
                            $password = "directagroup";
                            $user = new User();
                            $user->name = ucwords(strtolower($objPersonal->primer_nombre . " " . $objPersonal->primer_apellido));
                            $user->cedula = $request->cedula_empleado;
                            $user->username = $request->username;
                            $user->email = $request->useremail;
                            $user->email_verified_at = date('Y-m-d h:m:i', strtotime(now()));
                            $user->password = $password;
                            $user->estatus_id = 1;
                            $user->save();
                            $user->detachAllPermissions();

                            $user->attachRole($request->roles);
                            $objPersonal->user_id = $user->id;
                            $objPersonal->save();
                        } else {
                            ///en caso que el usuario exista y este eliminado lo restauramos
                            $user = User::where('cedula', $objPersonal->cedula_empleado)->withTrashed()->first();
                            $user->deleted_at = null;
                            $user->estatus_id = 1;
                            $user->save();
                            $user->detachAllPermissions();
                            $user->attachRole($request->roles);
                            $objPersonal->user_id = $user->id;
                            $objPersonal->save();
                        }
                    }
                    break;
                case 1: ///  posee rol de tipo operativo y tiene usuario                          
                    if ($request->useractive == "on") {
                        if ($nivel === 2) { ///2 si es egresado o bandono
                            if (User::where('cedula', $objPersonal->cedula_empleado)->exists()) {
                                ///eliminamos los historicos para poder borrar el id usuario asociado al expediente
                                HistoricoPersonalModel::where('personal_id', $id)->delete();
                                foreach (User::where('cedula', $objPersonal->cedula_empleado)->get() as $user) {
                                    $id = $user->id;
                                    $user->delete();
                                    User::withTrashed()->find($id)->forceDelete();
                                }
                            }
                        } else {
                            $user = User::where('cedula', $objPersonal->cedula_empleado)->withTrashed()->first();
                            $user->name = ucwords(strtolower($objPersonal->primer_nombre . " " . $objPersonal->primer_apellido));
                            $user->email = $request->useremail;
                            $user->deleted_at = null;
                            if (isset($request->resetPasswordAsisOp) && $request->resetPasswordAsisOp == "on") {
                                $user->password = "directagroup";
                                $user->password_updated_at = null;
                            }
                            $user->save();

                            $objPersonal->user_id = $user->id;
                            $objPersonal->save();

                            //$user->detachAllPermissions();
                            $user->roles()->sync($request->roles);
                        }
                    } else {
                        $iduser = $objPersonal->user_id;
                        $objPersonal->RelationUser()->delete();
                        $objPersonal->user_id = null;
                        $objPersonal->save();
                        HistoricoPersonalModel::where('personal_id', $id)->delete();

                        $user = User::where('cedula', $objPersonal->cedula_empleado)->withTrashed()->delete();

                        if (User::where('cedula', $objPersonal->cedula_empleado)->exists()) {
                            HistoricoPersonalModel::where('personal_id', $id)->delete();
                            foreach (User::where('cedula', $objPersonal->cedula_empleado)->get() as $user) {
                                $id = $user->id;
                                $user->delete();
                                User::withTrashed()->find($id)->forceDelete();
                            }
                        }
                    }
                    break;
            }
       
            //// Condicion para el Estatus de Vacaciones.
            if($request->status == 8){

                ///registro nuevo
                if($objPersonal->vacaciones_init == null && $objPersonal->vacaciones_end == null){
                    $casoVacaciones = 0;
                }

                #tengo valores de vacaciones ya almacenados
                if($objPersonal->vacaciones_init != null && $objPersonal->vacaciones_end != null){
                    $casoVacaciones = 1;
                }

                switch($casoVacaciones){
                    case 0: 
                        $startDate  = Carbon::createFromFormat('d/m/Y', $request->vacaciones_init)->format('Y-m-d');
                        $enddate    = Carbon::createFromFormat('d/m/Y', $request->vacaciones_end)->format('Y-m-d');

                        $objPersonal->vacaciones_init = $startDate;
                        $objPersonal->vacaciones_end = $enddate;

                        $end = new DateTime($enddate);
                        $end->modify('+ 1 day');

                        $period = new DatePeriod(
                                new DateTime($startDate),
                                new DateInterval('P1D'), $end);
                        ///recorremos los dias respectivos del rango de vacaciones

                        foreach ($period as $key => $value) {
                            $date = $value->format('Y-m-d');
                            $dia = date('l', strtotime($date));

                                $asistencia = new AsistenciaOperativaModel();
                                $asistencia->tipo_asistencia    = "Vacaciones";                    
                                $asistencia->gerente_id         = $objPersonal->gerente_area_id;
                                $asistencia->supervisor_id      = $objPersonal->jefe_inmediato;
                                $asistencia->coordinador_id     = $objPersonal->jefe_inmediato_segundo;
                                $asistencia->personal_id        = $objPersonal->id;
                                $asistencia->area_trabajo_id    = $objPersonal->area_trabajo_id;
                                $asistencia->cartera_id         = $objPersonal->cartera_id;
                                $asistencia->skill_id           = $objPersonal->skill_id;
                                $asistencia->prefijo            = "VAC";
                                $asistencia->block              = 1;
                                $asistencia->dia                = $dia;
                                $asistencia->observacion        = "Vacaciones Cargadas Desde La Ficha de Personal";
                                $asistencia->created_at         = date('Y-m-d H:i:s', strtotime($date));
                                $asistencia->updated_at         = date('Y-m-d H:i:s', strtotime($date));
                                $asistencia->save();
                        }                        
                        $objeto_asistencia = json_encode(["Fecha Inicio:"=> $startDate, "fecha Final: "=> $enddate]);
                        ActivityLogger::activity("Registro de Vacaciones Cedula: ".$objPersonal->cedula_empleado , $objeto_asistencia);
                    break;
                    case 1:                        
                        $startDate  = Carbon::createFromFormat('d/m/Y', $request->vacaciones_init)->format('Y-m-d');
                        $enddate    = Carbon::createFromFormat('d/m/Y', $request->vacaciones_end)->format('Y-m-d');
                        /// si las fechas recibidas son diferentes a las almacenadas
                        if($startDate != $objPersonal->vacaciones_init || $enddate != $objPersonal->vacaciones_end){
                        
                            if (AsistenciaOperativaModel::where('personal_id', $objPersonal->id)
                                ->where('prefijo',"VAC")
                                ->whereBetween('created_at', array($objPersonal->vacaciones_init.' 00:00:01', $objPersonal->vacaciones_end. ' 23:59:59'))->exists())  {
                                
                                AsistenciaOperativaModel::where('personal_id', $objPersonal->id)->where('prefijo',"VAC")
                                ->whereBetween('created_at', array($objPersonal->vacaciones_init.' 00:00:01', $objPersonal->vacaciones_end. ' 23:59:59'))->delete();

                                $end = new DateTime($enddate);
                                $end->modify('+ 1 day');

                                $period = new DatePeriod(
                                        new DateTime($startDate),
                                        new DateInterval('P1D'), $end);
                                ///recorremos los dias respectivos del rango de vacaciones
                                $objPersonal->vacaciones_init = $startDate;
                                $objPersonal->vacaciones_end = $enddate;

                                foreach ($period as $key => $value) {
                                    $date = $value->format('Y-m-d');
                                    $dia = date('l', strtotime($date));

                                        $asistencia = new AsistenciaOperativaModel();
                                        $asistencia->tipo_asistencia    = "Vacaciones";                    
                                        $asistencia->gerente_id         = $objPersonal->gerente_area_id;
                                        $asistencia->supervisor_id      = $objPersonal->jefe_inmediato;
                                        $asistencia->coordinador_id     = $objPersonal->jefe_inmediato_segundo;
                                        $asistencia->personal_id        = $objPersonal->id;
                                        $asistencia->area_trabajo_id    = $objPersonal->area_trabajo_id;
                                        $asistencia->cartera_id         = $objPersonal->cartera_id;
                                        $asistencia->skill_id           = $objPersonal->skill_id;
                                        $asistencia->prefijo            = "VAC";
                                        $asistencia->block              = 1;
                                        $asistencia->dia                = $dia;
                                        $asistencia->observacion        = "Vacaciones Cargadas Desde La Ficha de Personal";
                                        $asistencia->created_at         = date('Y-m-d H:i:s', strtotime($date));
                                        $asistencia->updated_at         = date('Y-m-d H:i:s', strtotime($date));
                                        $asistencia->save();
                                }                        
                                $objeto_asistencia = json_encode(["Fecha Inicio:"=> $startDate, "fecha Final: "=> $enddate]);
                                ActivityLogger::activity("Registro de Vacaciones Cedula: ".$objPersonal->cedula_empleado , $objeto_asistencia);
                            } 
                        } 
                    break;
                }            
            } else {
                $objPersonal->vacaciones_init = null;
                $objPersonal->vacaciones_end = null;
            }

            $this->GetDirtyHistorico($objPersonal, $historico);

            if ($objPersonal->save()) {
                switch ($rs) {
                    case true:
                        \Session::flash('success', 'El Expediente personal fue actualizado exitosamente y Eliminado de Autoservicios.');
                        break;
                    default:
                        \Session::flash('success', 'El Expediente Web personal fue actualizado exitosamente.');
                        break;
                }
                $objeto_personal = json_encode($objPersonal);
                ActivityLogger::activity("Expediente Web Actualizado " . $objPersonal->cedula_empleado, $objeto_personal);
                return Redirect()->route('personal.index');
            } else {
                \Session::flash('error', "Ocurrio un error al actualizar");
            }
        } catch (\Exception $e) {
            \Session::flash('error', $e->getMessage().' '.$e->getLine());
            return back();
        }
    }

    public function remove($id) {
        $objPersonal = PersonalModel::find($id);
        $objPersonal->Delete();
        \Session::flash('success', 'El Expediente ha sido movido a la papelera.');
        return back();
    }

    public function RemoveIndex() {

        $personal = PersonalModel::onlyTrashed()->get();

        return view('personal.personal.deleted')->with("personal", $personal);
    }

    public function restore($id) {
        PersonalModel::withTrashed()->find($id)->restore();
        \Session::flash('success', 'El Expediente Ha sido Restaurado exitosamente.');
        return back();
    }

    public function destroy($id) {
        $obpersonal = PersonalModel::withTrashed()->find($id);

        if ($obpersonal->image != null) {
            $this->deleteImg($obpersonal->image);
        }

        $obpersonal->forceDelete();
        \Session::flash('success', 'El Expediente ha sido Elimnado en la totalidad de la plataforma');
        return back();
    }

    public function IndexUploadMasive() {

        return view('personal.personal.upload.index');
    }

    public function StoreUploadMasive(Request $request) {


        try {
            Excel::import(new PersonalImport, request()->file('archivo'));

            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) { //retorno de las reglas de validacion
            return back()->withErrors($e->failures());
        } catch (\App\Exceptions\ValidateAditionals $e) { //catch de validaciones adicionales del archivo
            // Errores de tus validaciones personalizadas
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $t) { //errornes comunes
            $mensaje = str_replace("\n", 'and', $t->getMessage());
            \Session::flash('error', "Error: " . $mensaje !== null ? $mensaje : $t->getMessage());
            return redirect()->route('personal.upload.masive');
        }
    }

    public function IndexUpdateMasive() {

        return view('personal.personal.upload.update');
    }

    public function UpdateMasive(Request $request) {

        try {
            Excel::import(new UpdatePersonalImport, request()->file('archivo'));
           
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) { //retorno de las reglas de validacion
            return back()->withErrors($e->failures());
        } catch (\App\Exceptions\ValidateAditionals $e) { //catch de validaciones adicionales del archivo
            // Errores de tus validaciones personalizadas
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $t) { //errornes comunes
            $mensaje = str_replace("\n", 'and', $t->getMessage());
            \Session::flash('error', "Error: " . $mensaje !== null ? $mensaje : $t->getMessage());
            return redirect()->route('personal.update.masive');
        }
    }

    public function historial($id) {

        $historial = HistoricoPersonalModel::where('personal_id', $id)->orderby('id','DESC')->get();

        return view('personal.personal.historial', compact('historial'));
    }

    public function historialView($id) {

        $personal = HistoricoPersonalModel::find($id);

        $region = RegionModel::where('active', 1)->get();
        $estados = EstadosModel::find($personal->estado_id == null ? 3 : $personal->estado_id);
        $ciudad = CiudadesModel::find($personal->ciudad_id);

        $supervisor1 = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id")
                        ->where('id', $personal->jefe_inmediato)->first();

        $supervisor2 = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "id")
                        ->where('id', $personal->jefe_inmediato_segundo)->first();

        $cargo = CargoModel::Where('estatus_id', 1)->OrderBy('nombre_cargo', 'asc')->get();
        $libranza = LibranzasModel::Where('estatus_id', 1)->OrderBy('id', 'asc')->get();

        $acargo = CargoModel::where('nombre_cargo', 'like', 'Director%')
                        ->Orwhere('nombre_cargo', 'like', 'Gerente%')
                        ->Orwhere('nombre_cargo', 'like', 'Customer Technical Support Manager%')
                        ->Orwhere('nombre_cargo', 'like', '%Presidente%')->pluck('id');

        $gerente = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")
                        ->whereIn('cargo_id', $acargo)
                        ->whereIn('dir_personal.estatus_id', [1, 7, 8, 9, 10])
                        ->orderby('primer_nombre', 'ASC')->get();

        $nomina = NominaModel::Where('estatus_id', 1)->get();
        $estatus = EstatusModel::where('group_module', 'R')->get();
        $area = AreatrabajoModel::Where('estatus_id', 1)->get();
        $cartera = CarteraModel::where('area_trabajo_id', $personal->area_trabajo_id)->where('estatus_id', 1)->get();
        $skill = SkillModels::where('id', $personal->skill_id)->get();
        $zonas = ZonaTransporte::where('estatus_id', 1)->orderBy('zonatransporte', 'asc')->get();
        $banco = BancosModels::where('estatus_id', 1)->orderby('bancos', 'ASC')->get();

        $originblacklist = BlacklistModel::where('cedula_empleado', $personal->cedula_empleado)->first();

        if (BlacklistModel::where('cedula_empleado', $personal->cedula_empleado)->exists()) {
            $listanegra = "si";
        } else {
            $listanegra = "no";
        }

        $cambios = json_decode($personal->valores_nuevos);

        /// ubicacion del contenido de los valores almacenados con relacion a otras tablas.
        if (isset($cambios->bancos_id)) {
            $banco_new = BancosModels::select('bancos')->where('id', $cambios->bancos_id)->first();
            $banco_lp = $banco_new->bancos;
        } else {

            $banco_lp = null;
        }
        if (isset($cambios->nomina_id)) {
            $nomina_new = NominaModel::select('nombre_nomina')->where('id', $cambios->nomina_id)->first();
            $nomina_lp = $nomina_new->nombre_nomina;
        } else {

            $nomina_lp = null;
        }
        if (isset($cambios->libranza_id)) {
            $libranza_new = LibranzasModel::select('libranzas')->where('id', $cambios->libranza_id)->first();
            $libranza_lp = $libranza_new->libranzas;
        } else {

            $libranza_lp = null;
        }
        if (isset($cambios->cargo_id)) {
            $cargo_new = CargoModel::select('nombre_cargo')->where('id', $cambios->cargo_id)->first();
            $cargo_lp = $cargo_new->nombre_cargo;
        } else {
            $cargo_lp = null;
        }
        if (isset($cambios->gerente_area_id)) {
            $gerente_new = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")->where('id', $cambios->gerente_area_id)->first();
            $gerente_lp = $gerente_new->nombre;
        } else {
            $gerente_lp = null;
        }
        if (isset($cambios->jefe_inmediato)) {
            $jefe_new = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")->where('id', $cambios->jefe_inmediato)->first();
            $jefe_lp = $jefe_new->nombre;
        } else {
            $jefe_lp = null;
        }
        if (isset($cambios->jefe_inmediato_segundo)) {
            $jefe2_new = PersonalModel::select(DB::raw("concat(primer_nombre,' ',primer_apellido) as nombre"), "dir_personal.id")->where('id', $cambios->jefe_inmediato_segundo)->first();
            $jefe2_lp = $jefe2_new->nombre;
        } else {
            $jefe2_lp = null;
        }
        if (isset($cambios->area_trabajo_id)) {
            $area_new = AreatrabajoModel::select('nombre_campana')->where('id', $cambios->area_trabajo_id)->first();
            $area_lp = $area_new->nombre_campana;
        } else {

            $area_lp = null;
        }
        if (isset($cambios->cartera_id)) {

            $cartera_new = CarteraModel::select('nombre_cartera')->where('id', $cambios->cartera_id)->first();

            $cartera_lp = $cartera_new->nombre_cartera;
        } else {

            $cartera_lp = null;
        }
        if (isset($cambios->skill_id)) {

            $skill_new = SkillModels::select('nombre_skill')->where('id', $cambios->skill_id)->first();

            $skill_lp = $skill_new->nombre_skill;
        } else {
            $skill_lp = null;
        }
        if (isset($cambios->estatus_id)) {

            $estatus_new = EstatusModel::select('estatus')->where('id', $cambios->estatus_id)->first();

            $estatus_lp = $estatus_new->estatus;
        } else {

            $estatus_lp = null;
        }
        if (isset($cambios->zona_transporte_id)) {

            $zonas_new = ZonaTransporte::select('zonatransporte')->where('id', $cambios->zona_transporte_id)->first();

            $zona_lp = $zonas_new->zonatransporte;
        } else {

            $zona_lp = null;
        }
        if (isset($cambios->estado_id)) {
            $estado_new = EstadosModel::where('id', $cambios->estado_id)->first();
            $estado_lp = $estado_new->nombre_estado;
            $region_lp = RegionModel::find($estado_new->region_id)->nombre_region;
            $ciudad_lp = isset($cambios->ciudad_id) ? CiudadesModel::find($cambios->ciudad_id)->ciudad : [];
        } else {
            $estado_lp = null;
            $region_lp = null;
            $ciudad_lp = null;
        }        
        if (isset($cambios->todoticket_integral)){
            $integral_lp = $cambios->todoticket_integral  == "1" ? "SI": "NO";
        }else {
            $integral_lp = null;
        }        
        if (isset($cambios->todoticket_alimentacion)){
            $alimentacion_lp = $cambios->todoticket_alimentacion  == "1" ? "SI": "NO";
        }else{
            $alimentacion_lp = null;
        }  
        
        if (isset($cambios->vacaciones_init)){
            $integral_lp = $cambios->vacaciones_init  == "1" ? "SI": "NO";
        }else {
            $integral_lp = null;
        }  
        if (isset($cambios->todoticket_integral)){
            $integral_lp = $cambios->todoticket_integral  == "1" ? "SI": "NO";
        }else {
            $integral_lp = null;
        }  
        
        if (isset($cambios->sede_laboral)) {
            $sedenew = SedeModel::select('real_name')->where('nombre_sede', $cambios->sede_laboral)->first();
            $sedelp = $sedenew->real_name;
        } else {
            $sedelp = null;
        }

        return view('personal.personal.historialView')
                        ->with([
                            "blacklist" => $listanegra,
                            "originblacklist" => $originblacklist,
                            "personal" => $personal,
                            "supervisor1" => $supervisor1,
                            "supervisor2" => $supervisor2,
                            "gerente" => $gerente,
                            "academicos" => $this->nacademicos,
                            "horariolab" => $this->horariolab,
                            "sedes" => $this->sedes,
                            "nomina" => $nomina,
                            "cargo" => $cargo,
                            "status" => $estatus,
                            "libranza" => $libranza,
                            "area" => $area,
                            "cartera" => $cartera,
                            "skill" => $skill,
                            "zonatransporte" => $zonas,
                            "banco" => $banco,
                            "cambios" => $cambios,
                            "estado" => $estados,
                            "region" => $region,
                            "ciudad" => $ciudad,
                            "integral_lp"=>$integral_lp,
                            "alimentacion_lp"=>$alimentacion_lp,
                            "region_lp" => $region_lp,
                            "estado_lp" => $estado_lp,
                            "ciudad_lp" => $ciudad_lp,
                            "banco_lp" => $banco_lp,
                            "nomina_lp" => $nomina_lp,
                            "libranza_lp" => $libranza_lp,
                            "cargo_lp" => $cargo_lp,
                            "gerente_lp" => $gerente_lp,
                            "jefe_lp" => $jefe_lp,
                            "jefe2_lp" => $jefe2_lp,
                            "area_lp" => $area_lp,
                            "cartera_lp" => $cartera_lp,
                            "skill_lp" => $skill_lp,
                            "estatus_lp" => $estatus_lp,
                            "zona_lp" => $zona_lp,
                            "sede_lp" => $sedelp,
                            "tipoContrato" => $this->tipoContrato,
                            "nacionalidades" => $this->nacionalidades,
                            "especialidades" => $this->especialidades,
                            "horariosTransporte" => $this->horarioTransportes,
                            "hijo" => $this->hijo,
                            "jornadas" => $this->jornadas,
                            "tiemposContrato" => $this->tiemposContrato
                        ])->render();
    }

    public function IndexReplicasExpedientes()
    {        
        return view('personal.replicas.index');
    }

    public function ShowReplicasExpedientes(Request $request)
    {
        try{
            $fecha = Carbon::createFromFormat('m/Y',$request->fecha);
            $año = $fecha->copy()->format('Y');
            $mes = $fecha->copy()->format('m');

            $directories = Storage::disk('replicalp')->files($año.'/'.$mes.'/');
            if(!empty($directories)){                
                foreach($directories as $files){
                    ///sacamos la ruta del nombre del archivo
                    /// 0 año 1 dia 2 nombre archivo
                    $ident= explode('/',$files);      
                    // 0 año , 1 mes, 2 dia, 3 nombre
                    $explodediareplica = explode('_',$ident[2]);            
                    $row["dia"]          = $explodediareplica[2];   
                    $row["nombres"]      = $ident[2];
                    $row["acciones"] =  '<a href="'.Storage::disk('replicalp')->url($files).'" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Descargar Replica del Expediente">
                        <i class="fa fa-download"></i>
                    </a>';
                        
                    $data[] = $row;
                }
                return json_encode($data);
            } else {
                return json_encode([]);
            }
        } catch(\Exception $e){
            return json_encode($e->getMessage());
        }
    }
}
