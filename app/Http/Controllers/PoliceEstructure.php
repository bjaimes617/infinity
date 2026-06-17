<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\SkillRangoQuincenal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GerenciasExport;
use App\Exports\CecosExports;

class PoliceEstructure extends Controller
{

    private $type_format;
    public function __construct($type_format = array())
    {
        $this->type_format  = ["A" => "Formato Quincenal", "B" => "Formato Semanal", "C" => "Formato Carga Variables "];
    }
    //*
    //  personal.all.granted - todas las gerencias
    //  personal.for.skills - todo lo de los skills
    //  personal.for.personal_asigned - Solo el personal por debajo y del skill
    //  
    //*//
    /// se verifica nivel de permiso que posee
    static function VerifiedNivelPermissions($user)
    {

        try {
            ///skill autorizado y personal asignado de su jerarquia del skill - gestion administrativa y asistencia
            //   dd($user,$user->hasAllPermissions(['personal.for.skill','personal.for.skillasigned']));
            if ($user->hasAllPermissions(['personal.for.skill', 'personal.for.skillasigned'])) {
                return 4;
            }
            /// todo el skill autorizado, sin limite por de personal
            if ($user->hasPermission('personal.for.skill') && !$user->hasPermission('personal.for.skillasigned')) {
                return 3;
            }
            ///autoriza solo personal asignado a su jerarquia 
            if (!$user->hasPermission('personal.for.skill') && $user->hasPermission('personal.for.skillasigned')) {
                return 2;
            }
            ///todas las gerencias - todo el resto        
            if ($user->hasPermission('personal.all.granted')) {
                return 1;
            }
            return false;
        } catch (\Exception $e) {
            return abort(500, "ocurrio un problema la verificar sus permisos de Accesos. " . $e->getMessage());
        }
    }

    ///se despliegan las gerencias en funcion al nivel de permiso que posee.
    static function VerifiedGerenciasPermissions($nivel, $user)
    {

        switch ($nivel) {
            case (4):
            case (3):
                if (PermisosCarteraModel::where('user_id', $user->id)->exists()) {
                    $areas = PermisosCarteraModel::select('dir_area_trabajo.id', 'nombre_campana')
                        ->join('dir_area_trabajo', 'dir_permissions_user.area_trabajo_id', 'dir_area_trabajo.id')
                        ->where('user_id', $user->id)
                        ->groupby('dir_area_trabajo.id')->orderby('nombre_campana', 'DESC')->get();
                } else {
                    return abort(403, "No cuenta con los permisos necesarios para acceder a estos recursos.");
                }
                break;
            case (2):
                $Principal = PersonalModel::where('cedula_empleado', $user->cedula)->first();
                ///Por Jefe inmediato 1
                $asigna1 = $Principal->RelationPersonalacargo()->select('id')->where('estatus_id', 1)->get();
                ///Por Jefe inmediato 2
                $asigna2 = $Principal->RelationPersonalacargo2()->select('id')->where('estatus_id', 1)->get();
                ///Por Gerente de Area
                $asigna3 = $Principal->RelationPersonalacargoGerente()->select('id')->where('estatus_id', 1)->get();

                $concat1 = $asigna3->concat($asigna2)->unique('id');
                $concat2 = $concat1->concat($asigna1)->unique('id');

                $areasid = PersonalModel::select('area_trabajo_id')->whereIn('id', $concat2)->groupby('area_trabajo_id')->pluck('area_trabajo_id');
                $areas = AreatrabajoModel::select('id', 'nombre_campana')->wherein('id', $areasid)->orderby('nombre_campana', 'DESC')->get();

                break;
            case (1):
                $areas = AreatrabajoModel::select('id', 'nombre_campana')->orderby('nombre_campana', 'DESC')->get();
                break;
        }

        return $areas;
    }

    ///se despliega las campañas en funcion a la gerencia enviada o permitida
    public function GerenciaChangeVerified(Request $request)
    {

        try {
            $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

            $nivel  = $this->VerifiedNivelPermissions($user);
            $row    = array();
            $data   = array();

            switch ($nivel) {
                case (4):
                case (3):
                    $carteraPermisada = $user->RelationPermisos()->where('area_trabajo_id', $request->area)->groupby('cartera_id')->pluck('cartera_id');

                    $carteras = CarteraModel::select('id', 'nombre_cartera')
                        ->wherein('id', $carteraPermisada)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();

                    break;
                case (2):
                    $Principal = PersonalModel::where('cedula_empleado', $user->cedula)->first();
                    ///Por Jefe inmediato 1
                    $asigna1 = $Principal->RelationPersonalacargo()->select('id')->where('estatus_id', 1)->get();
                    ///Por Jefe inmediato 2
                    $asigna2 = $Principal->RelationPersonalacargo2()->select('id')->where('estatus_id', 1)->get();
                    ///Por Gerente de Area
                    $asigna3 = $Principal->RelationPersonalacargoGerente()->select('id')->where('estatus_id', 1)->get();

                    $concat1 = $asigna3->concat($asigna2)->unique('id');
                    $concat2 = $concat1->concat($asigna1)->unique('id');

                    $areasid = PersonalModel::select('cartera_id')->whereIn('id', $concat2)->groupby('cartera_id')->pluck('cartera_id');
                    $carteras = CarteraModel::select('id', 'nombre_cartera')->where('area_trabajo_id', $request->area)->wherein('id', $areasid)->orderby('nombre_cartera', 'DESC')->get();
                    break;
                case (1):
                    $carteras = CarteraModel::select('id', 'nombre_cartera')
                        ->where('area_trabajo_id', $request->area)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();
                    break;
            }

            foreach ($carteras as $cartera) {
                $row["id"] = $cartera->id;
                $row["nombre_cartera"] = $cartera->nombre_cartera;
                $data[] = $row;
            }
            return json_encode($data);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

    /// se despliega los Skills en funcion a la campaña enviada o permitida
    public function CampaingChangeVerified(Request $request)
    {
        try {

            $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

            $nivel  = $this->VerifiedNivelPermissions($user);
            $row    = array();
            $data   = array();

            switch ($nivel) {
                case (4):
                case (3):
                    $skillPermisado = $user->RelationPermisos()->wherein('cartera_id', is_array($request->cartera) ? $request->cartera : array($request->cartera))
                        ->groupby('skill_id')->pluck('skill_id');

                    $skill = SkillModels::select('id', 'nombre_skill')
                        ->wherein('id', $skillPermisado)->where('active', 1)->orderby('nombre_skill', 'ASC')->get();
                    break;
                case (2):
                    $Principal = PersonalModel::where('cedula_empleado', $user->cedula)->first();
                    ///Por Jefe inmediato 1
                    $asigna1 = $Principal->RelationPersonalacargo()->select('id')->where('estatus_id', 1)->get();
                    ///Por Jefe inmediato 2
                    $asigna2 = $Principal->RelationPersonalacargo2()->select('id')->where('estatus_id', 1)->get();
                    ///Por Gerente de Area
                    $asigna3 = $Principal->RelationPersonalacargoGerente()->select('id')->where('estatus_id', 1)->get();

                    $concat1 = $asigna3->concat($asigna2)->unique('id');
                    $concat2 = $concat1->concat($asigna1)->unique('id');

                    $skill = SkillModels::select('id', 'nombre_skill')
                        ->wherein('id', PersonalModel::select('skill_id')
                            ->wherein('id', $concat2)
                            ->wherein('cartera_id', is_array($request->cartera) ? $request->cartera : array($request->cartera))
                            ->groupby('skill_id')->pluck('skill_id'))
                        ->wherenotnull('cartera_id')
                        ->where('active', 1)->orderby('nombre_skill', 'ASC')->get();

                    break;
                case (1):
                    $skill = SkillModels::select('id', 'nombre_skill')
                        ->wherein('cartera_id', is_array($request->cartera) ? $request->cartera : array($request->cartera))
                        ->wherenotnull('cartera_id')
                        ->where('active', 1)->orderby('nombre_skill', 'ASC')->get();
                    break;
            }

            foreach ($skill as $cartera) {
                $row["id"] = $cartera->id;
                $row["nombre_skill"] = $cartera->nombre_skill;
                $data[] = $row;
            }
            return json_encode($data);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

    static function PersonalAsignaded($cedula)
    {

        $Principal = PersonalModel::where('cedula_empleado', $cedula)->first();

        ///Por Jefe inmediato 1
        $asigna1 = $Principal->RelationPersonalacargo()->select('id')->get();
        ///Por Jefe inmediato 2
        $asigna2 = $Principal->RelationPersonalacargo2()->select('id')->get();
        ///Por Gerente de Area
        $asigna3 = $Principal->RelationPersonalacargoGerente()->select('id')->get();

        $concat1 = $asigna1->concat($asigna2)->unique();
        $concat2 = $concat1->concat($asigna3)->unique();
        return $concat2->pluck('id');
    }

    static function PersonalForEstructure($user)
    {

        ///buscamos la ficha de personal
        $Principal = PersonalModel::where('cedula_empleado', $user->cedula)->first();
        ///Por Jefe inmediato 1
        $asigna1 = $Principal->RelationPersonalacargo()->select('id')->get();
        ///Por Jefe inmediato 2
        $asigna2 = $Principal->RelationPersonalacargo2()->select('id')->get();
        ///Por Gerente de Area
        $asigna3 = $Principal->RelationPersonalacargoGerente()->select('id')->get();
        ///unimos todos los ID
        $concat1 = $asigna3->concat($asigna2)->unique('id');
        $concat2 = $concat1->concat($asigna1)->unique('id');
        //extraemos el area del personal que posee activo
        $area   = PersonalModel::select('area_trabajo_id')
            ->whereIn('id', $concat2)
            ->where('estatus_id', 1)
            ->groupby('area_trabajo_id')
            ->pluck('area_trabajo_id');
        //extraemos la cartera del personal que posee activo
        $cartera = PersonalModel::select('cartera_id')
            ->whereIn('id', $concat2)
            ->groupby('cartera_id')
            ->pluck('cartera_id');
        //extraemos el skill del personal que posee activo
        $skill  = PersonalModel::select('skill_id')
            ->whereIn('id', $concat2)
            ->where('estatus_id', 1)
            ->groupby('skill_id')
            ->pluck('skill_id');

        //extraemos los id de todo el personal asociado
        $personalAsignado = $concat2->pluck('id');

        return [$area, $cartera, $skill, $personalAsignado];
    }

    ///para traer todos los cargos o cargos basados en el personal asignado 
    static function CargosForEstructure($user, $nivel, $skill)
    {

        switch ($nivel) {
            case 1:
                $cargo_ids = PersonalModel::select('cargo_id')->where('skill_id', $skill)->groupby('cargo_id')->pluck('cargo_id');
                $cargos = CargoModel::whereIn('id', $cargo_ids)->get();
                break;
            default:
                ///buscamos la ficha de personal
                $Principal = PersonalModel::where('cedula_empleado', $user->cedula)->first();
                ///Por Jefe inmediato 1
                $asigna1 = $Principal->RelationPersonalacargo()->select('cargo_id')->where('estatus_id', 1)->get();
                ///Por Jefe inmediato 2
                $asigna2 = $Principal->RelationPersonalacargo2()->select('cargo_id')->where('estatus_id', 1)->get();
                ///Por Gerente de Area
                $asigna3 = $Principal->RelationPersonalacargoGerente()->select('cargo_id')->where('estatus_id', 1)->get();
                ///unimos todos los ID
                $concat1 = $asigna3->concat($asigna2)->unique('cargo_id');
                $concat2 = $concat1->concat($asigna1)->unique('cargo_id');

                $cargos = CargoModel::whereIn('id', $concat2)->get();

                break;
        }


        return $cargos;
    }
    ///funcion para el sub modulo de Estructura gerencial index no requiere permisos | autorize == true para apartado de autorizar usuario
    public function AreasEstructuras(Request $request)
    {
        try {
            if ($request->autorize == null) {
                $cartera = CarteraModel::where('area_trabajo_id', $request->area)->orderby('nombre_cartera', 'ASC')->get();
                return json_encode($cartera);
            } else {
                $sql = CarteraModel::whereIn('area_trabajo_id', $request->area)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();
                $cartera = array();
                foreach ($sql as $carterass) {
                    $row["id"] = $carterass->id;
                    $row["nombre_cartera"] = $carterass->nombre_cartera . " - " . $carterass->RelationGerencias->nombre_campana;
                    $row["area_trabajo_id"] = $carterass->area_trabajo_id;
                    $cartera[] = $row;
                }
                return json_encode($cartera);
            }
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

    ///funcion para el sub modulo de Estructura gerencial index no requiere permisos
    public function CarterasEstructuras(Request $request)
    {
        try {
            if ($request->autorize == null) {
                $cartera = SkillModels::where('cartera_id', $request->cartera)
                    ->wherenotnull('cartera_id')->orderby('nombre_skill', 'ASC')->get();

                return json_encode($cartera);
            } else {
                $cartera = array();
                $sql = SkillModels::whereIn('cartera_id', $request->cartera)
                    ->wherenotnull('cartera_id')->where('active', 1)->orderby('nombre_skill', 'ASC')->get();

                foreach ($sql as $skills) {
                    $row["id"] = $skills->id;
                    $row["nombre_skill"] = $skills->nombre_skill . " - " . $skills->RelationCarteras->RelationGerencias->abrev;
                    $row["cartera_id"] = $skills->cartera_id;
                    $cartera[] = $row;
                }
                return json_encode($cartera);
            }
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }
    ///rengos de periodos de pago
    public function RangoPeriodoSkills(Request $request)
    {
        $rango = array();

        if (!empty($request->skill_id) || !isset($request->skill_id)) {
            //BUSCAMOS QUE TIPO DE PERIODO TIENE ESE SKILL
            $skill = SkillModels::where('id', $request->skill_id)->first();

            switch ($skill->tipo_periodo) {
                case "Semanal":
                    $array = json_decode($skill->dias);
                    return json_encode([
                        "periodo" => $skill->tipo_periodo,
                        "dias" => $array,
                        "rango" => $rango,
                        "type_format" => json_decode($skill->type_format_uploads)
                    ], true);
                    break;
                case "Quincenal":
                    $rango = SkillRangoQuincenal::where('skill_id', $request->skill_id)->first();
                    return json_encode([
                        "periodo" => $skill->tipo_periodo,
                        "dias" => $skill->dias,
                        "rango" => $rango,
                        "type_format" => json_decode($skill->type_format_uploads)
                    ], true);
                    break;
                case "Mixto":
                    $array = json_decode($skill->dias);
                    $rango = SkillRangoQuincenal::where('skill_id', $request->skill_id)->first();
                    return json_encode([
                        "periodo" => $skill->tipo_periodo,
                        "dias" => $array,
                        "rango" => $rango,
                        "type_format" => json_decode($skill->type_format_uploads)
                    ], true);
                    break;
            }
        }
    }

    ///Vemos las gerencias campañas y skills para crear, editar o eliminar
    public function index()
    {

        $areas = AreatrabajoModel::all();
        $skills = SkillModels::wherenotnull('cartera_id')->Orderby('cartera_id', 'asc')->get();

        return view('estructura.areas.index')->with(["skill" => $skills, "areas" => $areas, "type_format" => $this->type_format]);
    }
    ///creamos nuevas estructuras
    public function create()
    {
        $areas = AreatrabajoModel::orderby('nombre_campana', 'asc')->get();
        return view('estructura.areas.create')->with(["areas" => $areas, "type_format" => $this->type_format]);
    }
    ///almacena las estructura gerencia, campaña o skill
    public function store(Request $request, $tipo)
    {

        switch ($tipo) {
            case "gerencias":
                $array = explode(" ", $request->area_trabajo);
                if (count($array) === 3 || count($array) > 3) {
                    $obs = substr($array[2], 0, 3);
                } else {
                    $obs = substr($array[1], 0, 3);
                }

                $dirarea_trabajo = new AreatrabajoModel();
                $dirarea_trabajo->nombre_campana = ucwords(strtolower($request->area_trabajo));
                $dirarea_trabajo->estatus_id = 1;
                $dirarea_trabajo->abrev = strtoupper($obs);
                $dirarea_trabajo->save();
                \Session::flash('success', 'La Gerencia fue registrada exitosamente.');
                return redirect()->route('estructura.areas.create');
                break;
            case "unidades":

                $cartera = new CarteraModel();
                $cartera->nombre_cartera =  ucwords(strtolower($request->cartera));
                $cartera->area_trabajo_id = $request->gerencia;
                $cartera->estatus_id = 1;
                $cartera->save();

                \Session::flash('success', 'La Unidad/Campaña fue registrada exitosamente.');
                return redirect()->route('estructura.areas.create');
                break;
            case "skill":
                $skill = new SkillModels();
                $skill->nombre_skill  = ucwords(strtolower($request->skills));
                $skill->cartera_id    = $request->carteras;
                $skill->cod_ceco      = $request->codceco;
                $skill->nombre_ceco   = strtoupper($request->nombceco);
                $skill->tipo_periodo  = $request->tipo_periodo;
                $skill->type_format_uploads = json_encode($request->tipo_formato);

                switch ($request->tipo_periodo) {
                    case "Mixto":

                        $skill->dias = json_encode($request->dias);
                        $skill->save();

                        $rango = new SkillRangoQuincenal();

                        $rango->gerencia_id    = $request->area_trabajo;
                        $rango->campana_id     = $request->carteras;
                        $rango->skill_id       = $skill->id;
                        $rango->desde1 = $request->desde1;
                        $rango->hasta1 = $request->hasta1;
                        $rango->desde2 = $request->desde2;
                        $rango->hasta2 = $request->hasta2;
                        $rango->save();

                        break;
                    case "Semanal":
                        $skill->dias = json_encode($request->dias);
                        $skill->save();
                        break;
                    case "Quincenal":
                        $skill->save();
                        $rango = new SkillRangoQuincenal();

                        $rango->gerencia_id = $request->area_trabajo;
                        $rango->campana_id = $request->carteras;
                        $rango->skill_id = $skill->id;
                        $rango->desde1 = $request->desde1;
                        $rango->hasta1 = $request->hasta1;
                        $rango->desde2 = $request->desde2;
                        $rango->hasta2 = $request->hasta2;
                        $rango->save();
                        break;
                }

                \Session::flash('success', 'El Segmento/Skill fue registrado exitosamente.');
                return redirect()->route('estructura.areas.create');
                break;
        }
    }

    ///acutalizamos las gerencias campañas y skills
    public function UpdateEstructura(Request $request, $tipo)
    {
        // dd($request->all());
        switch ($tipo) {
            case "gerencias":
                $array = explode(" ", $request->gerenciaedit);
                if (count($array) === 3 || count($array) > 3) {
                    $obs = substr($array[2], 0, 3);
                } else {
                    $obs = substr($array[1], 0, 3);
                }
                $obareas = AreatrabajoModel::find($request->area_trabajo);
                $obareas->nombre_campana = ucwords(strtolower($request->gerenciaedit));
                $obareas->abrev = strtoupper($obs);
                $obareas->save();

                \Session::flash('success', 'La Gerencia ha sido Actualizada Correctamente.');
                return redirect()->route('estructura.areas.index');
                break;
            case "unidades":
                $objcarteras = CarteraModel::find($request->carteras);
                $objcarteras->nombre_cartera = ucwords(strtolower($request->carteraedit));
                $objcarteras->estatus_id = $request->estatuscartera;
                $objcarteras->save();
                \Session::flash('success', 'La Unidad/Campaña Ha sido Actualizada Correctamente.');
                return redirect()->route('estructura.areas.index');
                break;
            case "skill":
                // dd($request->all());
                $skill = SkillModels::find($request->skill);
                $skill->nombre_skill    = ucwords(strtolower($request->editskills));
                $skill->cod_ceco        = $request->codceco;
                $skill->nombre_ceco     = $request->nombceco;
                $skill->active          = $request->estatusskill == "Activo" ? 1 : 2;
                $skill->tipo_periodo    = $request->tipo_periodo;
                $skill->dias = null;
                $skill->type_format_uploads = json_encode($request->tipo_formato);

                switch ($request->tipo_periodo) {
                    case "Mixto":

                        $skill->dias = json_encode($request->dias);
                        $skill->save();

                        SkillRangoQuincenal::where('skill_id', $request->skill)->delete();

                        $rango = new SkillRangoQuincenal();
                        $rango->gerencia_id    = $request->area_trabajo;
                        $rango->campana_id     = $request->carteras;
                        $rango->skill_id       = $skill->id;
                        $rango->desde1 = $request->desde1;
                        $rango->hasta1 = $request->hasta1;
                        $rango->desde2 = $request->desde2;
                        $rango->hasta2 = $request->hasta2;
                        $rango->save();

                        break;
                    case "Semanal":
                        $skill->dias = json_encode($request->dias);
                        $skill->save();

                        if (SkillRangoQuincenal::where('skill_id', $request->skill)->exists()) {
                            SkillRangoQuincenal::where('skill_id', $request->skill)->delete();
                        }

                        break;
                    case "Quincenal":
                        $skill->save();

                        SkillRangoQuincenal::where('skill_id', $request->skill)->delete();

                        $rango = new SkillRangoQuincenal();
                        $rango->gerencia_id = $request->area_trabajo;
                        $rango->campana_id = $request->carteras;
                        $rango->skill_id = $skill->id;
                        $rango->desde1 = $request->desde1;
                        $rango->hasta1 = $request->hasta1;
                        $rango->desde2 = $request->desde2;
                        $rango->hasta2 = $request->hasta2;
                        $rango->save();
                        break;
                }

                \Session::flash('success', 'El Segmento/Skill Ha sido Actualizado Correctamente.');
                return redirect()->route('estructura.areas.index');
                break;
        }
    }

    public function DeleteCarteras(Request $request)
    {

        switch ($request->sendformdelete) {
            case "gerencia":
                $areas = AreatrabajoModel::findOrFail($request->delgerencia);
                $carteras = $areas->RelationCarterass()->pluck('id');
                SkillModels::whereIn('cartera_id', $carteras)->delete();
                $areas->RelationCarterass()->delete();
                $areas->forceDelete();
                \Session::flash('success', 'La Estructura de la Gerencia ha sido eliminada en su totalidad de la plataforma.');
                return redirect()->route('estructura.areas.index');
                break;
            case "unidad":
                $objcarteras = CarteraModel::findOrFail($request->delunidad);
                $objcarteras->Relationskill()->delete();
                $objcarteras->forceDelete();

                \Session::flash('success', 'La Unidad/Campaña y sus Skills Han sido Eliminadas de la plataforma Correctamente.');
                return redirect()->route('estructura.areas.index');
                break;
            case "skill":
                $skill = SkillModels::findOrFail($request->delskill);
                $skill->forceDelete();
                \Session::flash('success', 'El Segmento/Skill Ha sido Eliminado de la plataforma.');
                return redirect()->route('estructura.areas.index');
                break;
        }
    }

    public function PermisosList()
    {

        $users = PermisosCarteraModel::select('user_id')->groupby('user_id')->get()->load('RelationUsers', 'Relationskill');

        return view('estructura.permisos.index')->with(["user" => $users])->render();
    }

    public function PermisosCreate()
    {
        $areasall = AreatrabajoModel::all();

        $user_id = PermisosCarteraModel::select('user_id')->groupby('user_id')->get();

        $relacionEloquent = 'roles'; //Nombre de tu relacion con roles en el modelo User

        $usuariosAdmins = User::whereHas($relacionEloquent, function ($query) {
            return $query->where('slug', 'admin');
        })->pluck('id');

        $users = User::whereNotIn('id', $user_id)
            ->whereNotIn('id', $usuariosAdmins)->where('estatus_id', 1)->orderby('name', 'ASC')->get();

        return view('estructura.permisos.create')
            ->with([
                "user" => $users,
                "areas" => $areasall
            ]);
    }

    public function PermisosStore(Request $request)
    {

        foreach ($request->skill as $idskill) {
            $store = SkillModels::where('id', $idskill)->first();
            $permissions = new PermisosCarteraModel();
            $permissions->area_trabajo_id = $store->RelationCarteras->RelationGerencias->id;
            $permissions->cartera_id = $store->cartera_id;
            $permissions->skill_id = $store->id;
            $permissions->user_id = $request->usuario;
            $permissions->save();
        }
        \Session::flash('success', 'Los permisos para el usuario han sido asignados exitosamente.');
        return redirect()->route('estructura.autorizacion.usuarios');
    }

    public function PermisosEdit($id)
    {

        $carteras = PermisosCarteraModel::where('user_id', $id)->get();

        foreach ($carteras as $ids) {
            $perareas[]     = $ids->area_trabajo_id;
            $percarteras[]  = $ids->cartera_id;
            $skills[]       = $ids->skill_id;
        }

        $areas          = AreatrabajoModel::where('estatus_id', 1)->orderby('nombre_campana', 'ASC')->get();
        $cartera        = CarteraModel::whereIn('area_trabajo_id', $perareas)->orderby('nombre_cartera', 'ASC')->get();
        $skill          = SkillModels::whereIn('cartera_id', $percarteras)->orderby('nombre_skill', 'ASC')->get();

        return view('estructura.permisos.edit')
            ->with([
                "skill" => $skill,
                "areas" => $areas,
                "cartera" => $cartera,
                "areas_asinadas" => $perareas,
                "carteras_asignadas" => $percarteras,
                "skills_asignadas" => $skills,
                "id" => $id
            ])->render();
    }

    public function PermisosUpdate(Request $request, $id)
    {

        try {
            PermisosCarteraModel::where('user_id', $id)->delete();
            foreach ($request->skill as $idskill) {

                $store = SkillModels::where('id', $idskill)->first();

                $permissions = new PermisosCarteraModel();
                $permissions->area_trabajo_id = $store->RelationCarteras->RelationGerencias->id;
                $permissions->cartera_id = $store->cartera_id;
                $permissions->skill_id = $store->id;
                $permissions->user_id = $id;
                $permissions->save();
            }

            \Session::flash('success', 'los Permisos han sido actualizados correctamente');
            return redirect()->route('estructura.autorizacion.usuarios');
        } catch (\Exception $e) {

            \Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function PermisosDestroy($id)
    {
        User::withTrashed()->find($id)->RelationPermisos()->delete();
        \Session::flash('success', 'Ha sido retirada la autorización al usuario');
        return redirect()->route('estructura.autorizacion.usuarios');
    }

    public function PermisosShow(Request $request)
    {

        $carteras = PermisosCarteraModel::where('user_id', $request->user)->get();
        $data = array();

        foreach ($carteras as $ids) {
            $row["campania"]    = $ids->RelationAreas->nombre_campana;
            $row["cartera"]     = $ids->RelationCarteras->nombre_cartera;
            $row["skill"]       = $ids->Relationskill->nombre_skill;
            $data[] = $row;
        }

        return json_encode($data);
    }

    /// reporte de estructura de gerencias
    public function GerencialGenerate()
    {

        $gerencias = SkillModels::select(
            DB::raw("dir_area_trabajo.estatus_id AS estatus_area_trabajo_id"),
            DB::raw("dir_cartera.estatus_id AS estatus_cartera_id"),
            'dir_area_trabajo.nombre_campana',
            'dir_cartera.nombre_cartera',
            'dir_skill.nombre_skill',
            'dir_skill.active as skill_active'
        )
            ->leftJoin('dir_cartera', 'dir_skill.cartera_id', '=', 'dir_cartera.id')
            ->leftJoin('dir_area_trabajo', 'dir_cartera.area_trabajo_id', '=', 'dir_area_trabajo.id')
            ->where('dir_skill.cartera_id', '!=', null)
            ->get();

        return Excel::download(new GerenciasExport($gerencias), 'Gerencias Registradas.xlsx');
    }

    public function CecosGenerate()
    {

        $carteras = SkillModels::WhereNotIn('cod_ceco', ['00000'])->groupby('nombre_ceco')->get();
        return Excel::download(new CecosExports($carteras), 'CECOS Registrados.csv');
    }
}
