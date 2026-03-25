<?php

namespace App\Http\Controllers\personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PoliceEstructure;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\personal\EgresadosModel;
use App\Models\personal\HistoricoPersonalModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\EstatusModel;
use App\Models\personal\PersonalModel;

use App\Models\User;

use App\Exports\Personal\PersonalCecosExport;
use App\Exports\Personal\PersonalEgresoExport;
use App\Exports\Personal\PersonalNuevoIngresoExport;
use App\Exports\Personal\ParticipanteAccesoExports;
use App\Exports\Personal\PersonalExport;
use App\Exports\Personal\ControlAccesoExports;
use App\Exports\Personal\HistoricoActualizacionesExport;
use App\Exports\Personal\PersonalHistoricoExport;

use Carbon\Carbon;

class ReporteController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   
        $estatus = EstatusModel::where('group_module', 'R')->get();
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        //dd($nivel);
        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }
        return view('personal.reportes.index')->with(["areas" => $areas, "status" => $estatus]);
    }

    public function create(Request $request) {
   
        $date = carbon::now();
        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        $dato = PersonalModel::select();
       
        switch ($nivel) {
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->whereIn('cartera_id', $carteraPermisada)
                            ->whereIn('skill_id', $skillPermisado)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->whereIn('cartera_id', $carteraPermisada)
                            ->whereIn('skill_id', $skillPermisado)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('cartera_id', $request->carteras)
                            ->whereIn('skill_id', $skillPermisado)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus == null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('skill_id', $request->skill)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->wherein('id', $idPersonals)
                            ->where('skill_id', $request->skill)
                            ->wherein('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus != null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->whereIn('cartera_id', $carteraPermisada)
                            ->whereIn('skill_id', $skillPermisado)
                            ->wherein('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->wherein('id', $idPersonals)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->whereIn('skill_id', $skillPermisado)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus != null) {

                    $result = $dato->wherein('id', $idPersonals)
                            ->where('cartera_id', $request->carteras)
                            ->whereIn('skill_id', $skillPermisado)
                            ->wherein('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                $result = $dato->get();
                break;
            case 3:
                $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                ///SELECTORES EN TODOS
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->WhereIn('skill_id', $skillsautorize)->orderby('primer_nombre', 'ASC');
                }
                /// DATOS EN GERENCIA Y SELECTORES EN TODOS
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)
                            ->WhereIn('cartera_id', $carterasautorize)->orderby('primer_nombre', 'ASC');
                }
                /// DATOS EN GERENCIA Y UNIDAD EL RESTO EN TODOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                            ->WhereIn('skill_id', $skillsautorize)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS ENGERENCIA, UNIDAD Y SKIL Y ESTATUS EN TODOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus == null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                            ->Where('skill_id', $request->skill)->orderby('primer_nombre', 'ASC');
                }
                //DATOS EN TODOS LOS SELECTORES
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                            ->Where('skill_id', $request->skill)->WhereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS SOLO EN ESTATUS
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->WhereIn('skill_id', $skillsautorize)
                            ->WhereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS EN GERENCIA Y ESTATUS
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)
                            ->WhereIn('cartera_id', $carterasautorize)
                            ->WhereIn('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                ///DATOS EN GERENCIA Y UNIDAD Y EN ESTATUS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                            ->WhereIn('skill_id', $skillsautorize)
                            ->wherein('estatus_id', $request->estatus)->orderby('primer_nombre', 'ASC');
                }
                $result = $dato->get();
                break;
            case 2:

                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills = $carterasPersonal[2];
                $personal = $carterasPersonal[3];

                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->wherein('skill_id', $skills)
                            ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->wherein('skill_id', $skills)
                            ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)
                            ->wherein('skill_id', $skills)
                            ->orderby('primer_nombre', 'ASC');
                }
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus == null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)
                            ->where('skill_id', $request->skill)
                            ->orderby('primer_nombre', 'ASC');
                }

                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)
                            ->where('skill_id', $request->skill)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->wherein('skill_id', $skills)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->whereIn('id', $personal)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }

                $result = $dato->get();
                break;
            case 1:
                ////4 SELECTORES EN TODOS
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA TIENE DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus == null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                            ->orderby('primer_nombre', 'ASC');
                }
                ////GERENCIA CARTERA Y SKILL TIENEN DATOS PERO ESTATUS NO
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus == null) {
                    $result = $dato->where('skill_id', $request->skill)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///TODOS LOS SELECTORES POSEEN DATOS
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null && $request->estatus != null) {
                    $result = $dato->where('skill_id', $request->skill)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                ///ESTATUS POSEE DATOS                
                if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                //// GERENCIA Y ESTATUS POSEE DATOS 
                if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('area_trabajo_id', $request->area_trabajo)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                /// GERENCIA, UNIDAD Y ESTATUS POSEEN DATOS.
                if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null && $request->estatus != null) {
                    $result = $dato->where('cartera_id', $request->carteras)
                            ->WhereIn('estatus_id', $request->estatus)
                            ->orderby('primer_nombre', 'ASC');
                }
                $result = $dato->get();
                break;
            default:
                ///retorna el array vacio, no posee permisos.
                \Session::flash('error', abort(500, "No posee permisos para acceder a esta información"));
                return back();
                break;
        }
        switch (\Auth::user()->haspermission('personal.cecos.autorice')) {
            case true;
                return Excel::download(new PersonalCecosExport($result), 'Reporte de personal con CECOS ' . $date->format('d-m-Y') . '.csv');
                break;
            default:
                return Excel::download(new PersonalExport($result), 'Reporte de personal DG y DIGA ' . $date->format('d-m-Y') . '.csv');
                break;
        }
    }

    public function indexEgresados() {

        $areas = AreatrabajoModel::select('dir_area_trabajo.id', 'nombre_campana')->get();

        return view('personal.reportes.egresados')->with(["areas" => $areas]);
    }

    public function createEgresados(request $request) {

        $arrayDate = explode("-", $request->fecha);
        //dd($arrayDate);
        $begin = carbon::createFromFormat('d/m/Y', trim($arrayDate[0]));
        $end = carbon::createFromFormat('d/m/Y', trim($arrayDate[1]));
        switch ($request->tipo) {
            case 1:
                $ordenador = "created_at";
                $personal = PersonalModel::whereBetween($ordenador, array($begin->copy()->startofDay()->format('Y-m-d h:i:s'), $end->copy()->endofDay()->format('Y-m-d h:i:s')));
                break;
            case 2:
                $ordenador = "fecha";
                $personal = EgresadosModel::whereBetween($ordenador, array($begin->copy()->format('Y-m-d'), $end->copy()->format('Y-m-d')));
                break;
        }

        if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
            $sql = $personal->Orderby($ordenador, 'ASC');
        }

        if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
            $sql = $personal->where('area_trabajo_id', $request->area_trabajo)->Orderby($ordenador, 'ASC');
        }

        if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
            $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)->Orderby($ordenador, 'ASC');
        }

        if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
            $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)
                            ->where('skill_id', $request->skill)->Orderby($ordenador, 'ASC');
        }

        $result = $sql->get();

        switch ($request->tipo) {
            case 1:
                return Excel::download(new PersonalNuevoIngresoExport($result), 'Historico de Nuevos Ingresos Directa' . $begin . ' al ' . $end . ' Group y DIGA.csv');
                break;
            case 2:
                return Excel::download(new PersonalEgresoExport($result), 'Historico de Egresados Directa' . $begin . ' al ' . $end . ' Group y DIGA.csv');
                break;
        }
    }

    #reporte de movimientos
    public function ReporteActualizacion() {

        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   

        $estatus = EstatusModel::where('group_module', 'R')->get();
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('personal.reportes.historial_actualizacion')->with(["areas" => $areas, "status" => $estatus]);
    }

    public function ReporteActualizaciones(request $request) {

        try {
            $arrayDate = explode("-", $request->fecha);
            $begin = carbon::createFromFormat('d/m/Y', trim($arrayDate[0]));
            $end = carbon::createFromFormat('d/m/Y', trim($arrayDate[1]));

            $ordenador = "created_at";
            $personal = HistoricoPersonalModel::whereBetween($ordenador, array($begin->copy()->startofDay()->format('Y-m-d H:i:s'), $end->copy()->endofDay()->format('Y-m-d H:i:s')));

            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                $sql = $personal->Orderby($ordenador, 'DESC');
            }

            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                        ->Orderby($ordenador, 'DESC');
            }

            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                                ->where('cartera_id', $request->carteras)->Orderby($ordenador, 'DESC');
            }

            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                                ->where('cartera_id', $request->carteras)
                                ->where('skill_id', $request->skill)->Orderby($ordenador, 'DESC');
            }

            $historico = $sql->get();
            return Excel::download(new HistoricoActualizacionesExport($historico), 'Historico Actualizaciones de Personal ' . $begin . ' al ' . $end . '.xlsx');
        } catch (\exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }
    
    #desgloce por created_at y update_at
     public function ReporteHistorico() {

         $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   

        $estatus = EstatusModel::where('group_module', 'R')->get();
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('personal.reportes.historial')->with(["areas" => $areas, "status" => $estatus]);
    }

     public function ReporteHistoricos(request $request) {

        try {
            
            $arrayDate = explode("-", $request->fecha);
            $begin = carbon::createFromFormat('d/m/Y', trim($arrayDate[0]));
            $end = carbon::createFromFormat('d/m/Y', trim($arrayDate[1]));

            $ordenador = "updated_at";
            
            $personal = PersonalModel::where(function ($query) use ($begin, $end) {
                $query->whereBetween('created_at', [
                            $begin->copy()->startOfDay()->format('Y-m-d H:i:s'),
                            $end->copy()->endOfDay()->format('Y-m-d H:i:s')
                        ])
                        ->orWhereBetween('updated_at', [
                            $begin->copy()->startOfDay()->format('Y-m-d H:i:s'),
                            $end->copy()->endOfDay()->format('Y-m-d H:i:s')
                ]);
            });

            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                $sql = $personal->Orderby($ordenador, 'DESC');
            }

            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                        ->Orderby($ordenador, 'DESC');
            }

            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
               
                $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                    ->where('cartera_id', $request->carteras)->Orderby($ordenador, 'DESC');
            }

            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                $sql = $personal->where('area_trabajo_id', $request->area_trabajo)
                    ->where('cartera_id', $request->carteras)
                    ->where('skill_id', $request->skill)->Orderby($ordenador, 'DESC');
            }

            $historico = $sql->get();
          
            return Excel::download(new PersonalHistoricoExport($historico), 'Historico Expedientes ' . $begin->copy()->format('Y-m-d') . ' al ' . $end->copy()->format('Y-m-d') . '.xlsx');
        } catch (\exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }
    
    public function indexAccessControl() {

        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        ///Sentencia solo para traer la
        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
            switch ($nivel) {
                case 1:
                    $atributo = [0 => "[TODOS]", 1 => ""];
                    break;
                case 2:
                case 3:
                case 4:
                    $atributo = [0 => "[SELECCIONE]", 1 => "REQUIRED"];
                    break;
            }
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        if ($user->haspermission('control.reporte.participantescursos')) {
            $type = [1 => "Empleados", 2 => "Participantes"];
        } else {
            $type = [2 => "Participantes"];
        }

        return view('personal.reportes.control')->with(["areas" => $areas, "atributos" => $atributo, "type" => $type])->render();
    }
    
    public function exportAccessControl(request $request) {

        try {
            $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   

            $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
            $arrayDate = explode("-", $request->fecha);

            $begin = carbon::create(trim($arrayDate[0]))->startOfDay()->format('Y-m-d H:i:s');
            $end = carbon::create(trim($arrayDate[1]))->EndOfDay()->format('Y-m-d H:i:s');
            $data = array();

            switch ($request->type) {
                case 1: /// Reporte de tipo Empleados
                    switch ($nivel) {
                        case 4:
                            ///Ubicamos los Id de todo el personal que tiene asignado
                            $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                            /// extraemos los skills que estan activos en la plataforma
                            $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                            $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('dir_personal.skill_id', $skillPermisado)
                                        ->whereIn('dir_personal.id', $idPersonals)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.area_trabajo_id', $request->area_trabajo)
                                        ->whereIn('dir_personal.skill_id', $skillPermisado)
                                        ->whereIn('dir_personal.id', $idPersonals)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('dir_personal.skill_id', $skillPermisado)
                                        ->where('dir_personal.cartera_id', $request->cartera)
                                        ->whereIn('dir_personal.id', $idPersonals)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.skill_id', $request->skill)
                                        ->whereIn('dir_personal.id', $idPersonals)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        case 3:
                            $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                            $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('dir_personal.cartera_id', $carterasautorize)
                                        ->whereIn('dir_personal.skill_id', $skillsautorize)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.area_trabajo_id', $request->area_trabajo)
                                        ->whereIn('dir_personal.cartera_id', $carterasautorize)
                                        ->whereIn('dir_personal.skill_id', $skillsautorize)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.cartera_id', $request->cartera)
                                        ->whereIn('dir_personal.skill_id', $skillsautorize)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.skill_id', $request->skill)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        case 2:
                            $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                            ///$carterasPersonal ===> area 0 | cartera 1 | skill 2 | personal asignado 3
                            $skills = $carterasPersonal[2];
                            //$skillsString = implode(',', $skills);
                            $personal = $carterasPersonal[3];
                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('dir_personal.skill_id', $skills)
                                        ->whereIn('dir_personal.id', $personal)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.area_trabajo_id', $request->area_trabajo)
                                        ->whereIn('dir_personal.skill_id', $skills)
                                        ->whereIn('dir_personal.id', $personal)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.cartera_id', $request->cartera)
                                        ->whereIn('dir_personal.skill_id', $skills)
                                        ->whereIn('dir_personal.id', $personal)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.skill_id', $request->skill)
                                        ->whereIn('dir_personal.id', $personal)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        case 1:
                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {

                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.area_trabajo_id', $request->area_trabajo)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.cartera_id', $request->cartera)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.personal_id = c.personal_id) AS salida")
                                        ->addSelect([
                                            'c.isp_ip',
                                            'dir_sede.nombre_sede',
                                            'dir_personal.primer_nombre',
                                            'dir_personal.primer_apellido',
                                            'dir_personal.cedula_empleado',
                                            'dir_nomina.nombre_nomina',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.temperatura'
                                        ])
                                        ->join('dir_personal', 'c.personal_id', '=', 'dir_personal.id')
                                        ->leftJoin('dir_cartera', 'dir_personal.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'dir_personal.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'dir_personal.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->leftJoin('dir_sede', 'c.isp_ip', '=', 'dir_sede.ISP')
                                        ->join('dir_nomina', 'dir_personal.nomina_id', '=', 'dir_nomina.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('dir_personal.skill_id', $request->skill)
                                        ->groupBy('fecha', 'c.personal_id')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        default:
                            ///retorna el array vacio, no posee permisos.
                            \Session::flash('error', abort(500, "No posee permisos para acceder a esta información"));
                            return back();
                            break;
                    }
                    $data = $sql->get();

                    if (!$data->isEmpty()) {
                        return Excel::download(new ControlAccesoExports($data), 'Control de Acceso del personal Activo ' . carbon::create(trim($arrayDate[0]))->format('Y-m-d') . ' .csv');
                    } else {
                        \Session::flash('info', "No hay información actualmente disponible.");
                        return back();
                    }
                    Break;
                case 2: // reporte de participantes
                    // dd("caso 2",$nivel);
                    switch ($nivel) {
                        case 4:
                            ///Ubicamos los Id de todo el personal que tiene asignado
                            $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                            /// extraemos los skills que estan activos en la plataforma
                            $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                            $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('rec_cursos.skill_id', $skillPermisado)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.area_trabajo_id', $request->area_trabajo)
                                        ->whereIn('rec_cursos.skill_id', $skillPermisado)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.area_trabajo_id', $request->area_trabajo)
                                        ->where('rec_cursos.cartera_id', $request->carteras)
                                        ->whereIn('rec_cursos.skill_id', $skillPermisado)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.area_trabajo_id', $request->area_trabajo)
                                        ->where('rec_cursos.cartera_id', $request->carteras)
                                        ->where('rec_cursos.skill_id', $request->skill)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        case 3:
                        case 2:
                            $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                            $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                            'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('rec_cursos.skill_id', $skillPermisado)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.area_trabajo_id', $request->area_trabajo)
                                        ->whereIn('rec_cursos.skill_id', $skillPermisado)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.cartera_id', $request->carteras)
                                        ->whereIn('rec_cursos.skill_id', $skillPermisado)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                              'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->whereIn('rec_cursos.skill_id', $request->skill)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        case 1:
                            if ($request->area_trabajo == null && $request->carteras == null && $request->skill == null) {

                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                            'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras == null && $request->skill == null) {

                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                            'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.area_trabajo_id', $request->area_trabajo)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill == null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                            'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.area_trabajo_id', $request->area_trabajo)
                                        ->where('rec_cursos.cartera_id', $request->carteras)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            if ($request->area_trabajo != null && $request->carteras != null && $request->skill != null) {
                                $sql = DB::table('dir_asistencia_recepcion as c')
                                        ->selectRaw("DATE(c.created_at) AS fecha, 
                            COUNT(1) AS escaneo,
                            (SELECT MIN(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS entrada,
                            (SELECT MAX(b.created_at) 
                             FROM dir_asistencia_recepcion AS b 
                             WHERE DATE_FORMAT(b.created_at, '%Y-%m-%d') = DATE_FORMAT(c.created_at, '%Y-%m-%d') 
                               AND b.cedula = c.cedula) AS salida")
                                        ->addSelect([
                                            'c.nombres',
                                            'c.cedula',
                                            'dir_area_trabajo.nombre_campana',
                                            'dir_cartera.nombre_cartera',
                                            'dir_skill.nombre_skill',
                                            'c.sede',
                                            'rec_cursos.id'
                                        ])
                                        ->join('rec_cursos', 'c.curso_id', '=', 'rec_cursos.id')
                                        ->leftJoin('dir_cartera', 'rec_cursos.cartera_id', '=', 'dir_cartera.id')
                                        ->leftJoin('dir_skill', 'rec_cursos.skill_id', '=', 'dir_skill.id')
                                        ->join('dir_area_trabajo', 'rec_cursos.area_trabajo_id', '=', 'dir_area_trabajo.id')
                                        ->whereBetween('c.created_at', [$begin, $end])
                                        ->where('rec_cursos.skill_id', $request->skill)
                                        ->groupBy('fecha', 'c.cedula')
                                        ->orderBy('entrada', 'asc');
                            }
                            break;
                        default:
                            ///retorna el array vacio, no posee permisos.
                            \Session::flash('error', abort(500, "No posee permisos para acceder a esta información"));
                            return back();
                            break;
                    }

                    $data = $sql->get();

                    if (!$data->isEmpty()) {
                        return Excel::download(new ParticipanteAccesoExports($data), 'Control de Acceso de Participantes Cursos ' . carbon::create(trim($arrayDate[0]))->format('Y-m-d') . '.csv');
                    } else {
                        \Session::flash('info', "No hay información actualmente disponible.");
                        return back();
                    }
                    break;
            }
        } catch (\Throwable $e) {
            \Session::flash('error', "Ocurrio un problema durante la consulta " . $e->getMessage());
            return back();
        }
    }
}
