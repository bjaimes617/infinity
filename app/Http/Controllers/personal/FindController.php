<?php

namespace App\Http\Controllers\personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Models\EstadosModel;

class FindController extends Controller {
    
    ///personal -> create
    public function SearchCedula(Request $request) {
        
        if (BlacklistModel::where('cedula_empleado', $request->cedula_empleado)->exists()) {     
            
            return json_encode(["blacklist" => false,
                "message"=>"La cedula Ingresada, se encuentra bloqueada para registrar. Lista Negra."]
            );
        }
        
        if (PersonalModel::where('cedula_empleado', $request->cedula_empleado)->exists()) {
          
            $ob     = PersonalModel::where('cedula_empleado', $request->cedula_empleado)->first()->id;
            $url    = route('personal.edit', $ob);            
           
            return json_encode(["response" => false,
                "message" => 'La cedula Que acabas de ingresar ya esta registrada y puedes <b>Precargar sus datos Aqui </b><a href="' . $url . '"><i class="fas fa-external-link-alt"></i></a>'
            ]);
        } else {
            
            return json_encode(["blacklist" => false,
                "response" => true,
            ]);
        }
    }
    
    public function SearchCuentas(Request $request) {
        
        if (PersonalModel::where('cedula_empleado','<>',$request->cedula)->where('cuenta_bancaria', $request->ctabancaria)->exists()) {
            return "false";
        }
        else {
            return "true";
        }
    }    
    ///personal -> create -> lideres
    public function FindCargosLideres(request $request) {
        
            $Objjefe = CargoModel::find($request->idCargo);
            $rsl = $Objjefe->RelationPersonal()->whereIn('dir_personal.estatus_id', [1, 7, 8])->orderBy('primer_nombre', 'ASC')->get();
            return json_encode($rsl);
    }        
    ///personal -> create
    public function AreasCreateAddpersonal(Request $request) {
        
        if($request->area != null){
            $objcarteras = CarteraModel::where('area_trabajo_id', $request->area)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();
            return json_encode($objcarteras);
        } else {
             return json_encode(abort(500,"Error de consulta"));
        }
       
    }
    ///personal -> create
    public function carterasCreateAddpersonal(Request $request) {
                
        if ($request->cartera != null) {
            $objcarteras = SkillModels::where('cartera_id', $request->cartera)->where('active', 1)->orderby('nombre_skill', 'ASC')->get();
            return json_encode($objcarteras);
        }
        else {
            return json_encode(abort(500, "Error de consulta"));
        }
    }
       
    public function selectorAreasHp(Request $request) {

        try {

            $personal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();

            $user = User::find(\Auth::user()->id);
            //$user = User::where('cedula',24636828)->first();
            $row = array();
            $i = 0;

            if ($user->hasPermission(['personal.all.granted'])) {
                ///puedo ver todas las carteras necesarias
                $level = 2;
            }
            else if (PermisosCarteraModel::where('user_id', $user->id)->exists()) {
                ///puedo ver las carteras asignadas al usuario
                $level = 1;
            }
            else if ($personal->RelationUser->hasAnyPermission(['asistencia.operativa.supervisor', 'asistencia.operativa.coordinador', 'asistencia.operativa.senior', 'asistencia.operativa.otros'])) {
                //si soy de asistencia operativa
                $level = 4;
            }
            else {
                /// error de busqueda.
                $level = 0;
            }
            switch ($level) {
                case 2:
                    $cartera = CarteraModel::select('id', 'nombre_cartera')
                                    ->where('area_trabajo_id', $request->area)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();
                    $row = $cartera;
                    break;
                case 1:
                    foreach ($user->RelationPermisos()->where('area_trabajo_id', $request->area)->groupby('cartera_id')->get() as $carteras) {
                        $row[$i]["id"] = $carteras->RelationCarteras->id;
                        $row[$i]["nombre_cartera"] = $carteras->RelationCarteras->nombre_cartera;
                        $i++;
                    }
                    break;
                case 4:

                    $personal1 = $personal->RelationPersonalacargo()->pluck('area_trabajo_id');
                    $personal2 = $personal->RelationPersonalacargo2()->pluck('area_trabajo_id');

                    $asignados = $personal1->concat($personal2)->unique();

                    $cartera = CarteraModel::select('id', 'nombre_cartera')
                                    ->whereIn('area_trabajo_id', $asignados)->where('estatus_id', 1)->orderby('nombre_cartera', 'ASC')->get();
                    $row = $cartera;
                    break;
                case 0:
                    return json_encode(abort(403, "Lo sentimos, recursos no autorizados."));
                    break;
            }
            return json_encode($row);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

    public function selectorCarterasHp(Request $request) {
        // dd("aca");
        try {

            $personal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();

            $user = User::find(\Auth::user()->id);
            //$user = User::where('cedula',24636828)->first();
            $i = 0;

            if ($user->hasPermission(['personal.all.granted'])) {
                ///puedo ver todas las carteras necesarias
                $level = 2;
            }
            else if (PermisosCarteraModel::where('user_id', $user->id)->exists()) {
                ///puedo ver las carteras asignadas al usuario
                $level = 1;
            }
            else if ($personal->RelationUser->hasAnyPermission(['asistencia.operativa.supervisor', 'asistencia.operativa.coordinador', 'asistencia.operativa.senior', 'asistencia.operativa.otros'])) {
                //si soy de asistencia operativa
                $level = 4;
            }
            else {
                /// error de busqueda.
                $level = 0;
            }

            switch ($level) {
                case 2:
                    $cartera = SkillModels::select('dir_skill.id', 'nombre_skill')->where('cartera_id', $request->cartera)->wherenotnull('cartera_id')->where('active', 1)->orderby('nombre_skill', 'ASC')->get();
                    $row = $cartera;
                    break;
                case 1:
                    //dd($user->RelationPermisos()->where('cartera_id', $request->cartera)->get());
                    foreach ($user->RelationPermisos()->where('cartera_id', $request->cartera)->get() as $carteras) {
                        $row[$i]["id"] = $carteras->Relationskill->id;
                        $row[$i]["nombre_skill"] = $carteras->Relationskill->nombre_skill;
                        $row[$i]["cod_ceco"] = $carteras->Relationskill->cod_ceco;
                        $i++;
                    }
                    //$row;
                    break;
                case 4:

                    $personal1 = $personal->RelationPersonalacargo()->pluck('cartera_id');
                    $personal2 = $personal->RelationPersonalacargo2()->pluck('cartera_id');

                    $asignados = $personal1->concat($personal2)->unique();

                    $cartera = SkillModels::select('dir_skill.id', 'nombre_skill')
                                    ->whereIn('cartera_id', $asignados)->wherenotnull('cartera_id')
                                    ->where('active', 1)->orderby('nombre_skill', 'ASC')->get();
                    $row = $cartera;
                    break;

                case 0:
                    return json_encode(abort(403, "Lo sentimos, recursos no autorizados."));
                    break;
            }
            return json_encode($row);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }    

    public function SelectorCeco(Request $request) {
        $rs = SkillModels::find($request->skill);
        if ($rs->cod_ceco === "00000") {
            $centros = SkillModels::whereNotnull('cod_ceco')->where('cod_ceco', '!=', '00000')->groupby('cod_ceco')->orderby('nombre_ceco', 'ASC')->get();
        }
        else {
            $centros = $rs;
        }

        return json_encode($centros);
    }
   
    public function SelectorCarterasPermisos(Request $request) {
        $e = 0;
        for ($i = 0; $i < count($request->pcarteras); $i++) {
            $skill = SkillModels::where('cartera_id', $request->pcarteras[$i])
                ->where('active', 1)->orderby('nombre_skill', 'ASC')->get();

            foreach ($skill as $skills) {
                $row[$e]["id"] = $skills->id;
                $row[$e]["nombre_skill"] = $skills->nombre_skill . " - " . $skills->RelationCarteras->RelationGerencias->abrev;
                $row[$e]["cartera_id"] = $skills->cartera_id;
                $e++;
            }
        }
        return json_encode($row);
    }
    
    //aplicable para buscar los estados por region
    //se aplica inicialmente en los cursos    
    public function selectorEstados(Request $request) {
      $objEstados = EstadosModel::where('region_id', $request->region)->where('active',1)->orderBy('nombre_estado','ASC')->get();
      return json_encode($objEstados);
    }
    
    public function selectorCiudades(Request $request) {
      $objEstados = EstadosModel::find($request->estado_id)->RelationCiudad;
      return json_encode($objEstados);
    }
}