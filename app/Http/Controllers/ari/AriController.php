<?php

namespace App\Http\Controllers\ari;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ari\UnidadTributariaModel;
use App\Models\ari\HistoricoAriModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Exports\AriExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\PoliceEstructure;

class AriController extends Controller {

    public function CreateUt() {

        $anio = Carbon::now()->format('Y');

        $trimestres = config('app.trimestres');

        if (UnidadTributariaModel::where('anio', $anio)->exists()) {

            $trimestre_base = UnidadTributariaModel::where('anio', $anio)->get();

            foreach ($trimestre_base as $trime) {

                $num            = $trime->trimestre;
                $indice_ocultar = array_search($trimestres[$num], $trimestres);
                unset($trimestres[$indice_ocultar]);
            }

            return view('ari.createut')->with(["trimestres" => $trimestres]);
        } else {

            return view('ari.createut')->with(["trimestres" => $trimestres]);
        }
    }

    public function StoreUt(Request $request) {

     
        $unidad = new UnidadTributariaModel();

        $unidad->unidad_tributaria = $request->unidad_tributaria;
        $unidad->anio       = $request->anio;
        $unidad->active     = 0;
        $unidad->trimestre  = $request->trimestre;
        $unidad->disabled   = 0;
        $unidad->save();

        Session::flash('success', 'Unidad Tributaria registrada con exito.');
        return redirect()->route('generate.ut');
    }

    public function ActiveUtIndex() {

        $unidades = UnidadTributariaModel::all();

        $now = Carbon::now();
        $trimestre_con = ceil($now->quarter);
        //MANDAMOS A LA VISTA EL TRIMESTRE ACTUAL PARA EVITAR LA ACTIVACION DEL MISMO ANTES DE TIEMPO
        $trimestre = (int) $trimestre_con;

        return view('ari.indexut')->with(['unidades'=>$unidades, 'trimestre'=>$trimestre])->render();
    }

    public function ActiveUpdate($id, $type) {
        try {
            $unidad = UnidadTributariaModel::find($id);
            $unidad->active = $type;
            $unidad->save();
            if ($type == 1) {
                return json_encode(["type" => "success", "message" => "El periodo ha sido activado. Se podra Acceder acceder a la planilla ARI."]);
            } else {
                return json_encode(["type" => "info", "message" => "El periodo ha sido desactivado. No se podra Acceder a la planilla ARI."]);
            }
        } catch (\Throwable $e) {
            return json_encode(abort(500, "Ocurrio un problema " . $e->getMessage()));
        }
    }

    public function edit($id, $anio, $periodo) {

        $unidad = UnidadTributariaModel::find($id);
        $trimestres = config('app.trimestres');
        //$unidades = UnidadTributariaModel::all();
        
        if (UnidadTributariaModel::where('anio', $anio)->where('trimestre', $periodo)->where('active', '1')->exists()) {            
            Session::flash('index', 'El periodo esta activo y no puede modificarse.');
            return back();
        } else {           
            return view('ari.editut')->with(["unidad"=> $unidad,"trimestres" => $trimestres]);
        }
    }

    public function update(Request $request, $id) {
      
        $unidades = UnidadTributariaModel::all();

        $unidad = UnidadTributariaModel::find($id);
        $unidad->unidad_tributaria = number_format($request->unidad_tributaria, 2);
        $unidad->save();

        Session::flash('success', 'Unidad Tributaria modificada con exito.');
        return redirect()->route('ut.index', ['unidades' => $unidades]);
    }

    public function generateIndex() {

        $trimestres = config('app.trimestres');
        return view('ari.generate')->with(["trimestres" => $trimestres])->render();
    }

    public function generate(Request $request) {
       
        if (PersonalModel::where('cedula_empleado', $request->cedula)->exists()) {
            $personal_id = PersonalModel::select('id')->where('cedula_empleado', $request->cedula)->first();          

            if (HistoricoAriModel::where('personal_id', $personal_id->id)
                    ->where('anio', $request->anio)->where('periodo', $request->trimestre)->exists()) {

                $historico_id = HistoricoAriModel::select('id')->where('personal_id', $personal_id->id)
                    ->where('anio', $request->anio)->where('periodo', $request->trimestre)->first();


                if (HistoricoAriModel::where('personal_id', $personal_id->id)->where('anio', $request->anio)->count() > 1) {
                    $variacion = 1;
                } else {

                    $variacion = 0;
                }

                return view('ari.planilla')->with(['personal'=>$personal_id->id, 
                    'historico'=>$historico_id->id, 'anio'=>$request->anio, 'variacion'=>$variacion]);
            } else {

                Session::flash('error', 'El trabajador no posee ARI para el periodo especificado.');
                return redirect()->route('generate.ari');
            }
        } else {

            Session::flash('error', 'La cedula especificada no existe.');
            return redirect()->route('generate.ari');
        }
    }

    public function GenerateAriView(Request $request, $historial, $personal, $anio, $variacion) {

        $historico  = HistoricoAriModel::where('id', $historial)->first();
        $personal   = PersonalModel::where('id', $personal)->first();

        if (HistoricoAriModel::where('personal_id', $personal->id)->where('anio', $anio)->count() > 1) {
            $variacion = 1;
        } else {
            $variacion = 0;
        }

        return view('ari.ari')->with(['historico'=>$historico, 'personal'=>$personal, 'variacion'=>$variacion])->render();
    }

    public function reporte() {

        $user = User::find(\Auth::user()->id);
        
          $trimestres = config('app.trimestres');
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        //dd($nivel);
        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }
      
      
        return view('ari.reportes.index')->with(["areas"=>$areas,"trimestres"=>$trimestres])->render();
    }

    public function ari_reporte(Request $request) {
        try {
            if ($request->trimestre != null || $request->trimestre != "") {

                $impuesto = HistoricoAriModel::select('*')->where('anio', $request->anio)->where('periodo', $request->trimestre)
                        ->leftJoin('dir_personal', 'ari_historico.personal_id', '=', 'dir_personal.id');
            } else {

                $impuesto = HistoricoAriModel::select('*')->where('anio', $request->anio)->leftJoin('dir_personal', 'ari_historico.personal_id', '=', 'dir_personal.id');
            }

            if ($request->area_trabajo == null && $request->carteras == null) {

                $sql = $impuesto->Orderby('dir_personal.cartera_id', 'ASC');
            }

            if ($request->area_trabajo != null && $request->carteras == null) {
                $sql = $impuesto->where('dir_personal.area_trabajo_id', $request->area_trabajo)
                        ->Orderby('dir_personal.cartera_id', 'ASC');
            }

            if ($request->area_trabajo != null && $request->carteras != null) {
                $sql = $impuesto->where('dir_personal.area_trabajo_id', $request->area_trabajo)
                        ->where('dir_personal.cartera_id', $request->carteras)
                        ->Orderby('dir_personal.cartera_id', 'ASC');
            }

            $result = $sql->get();

            $trimestre = $request->trimestre != null ? config('app.trimestres')[$request->trimestre] : "";

            if ($trimestre == "") {

                return Excel::download(new AriExport($result), 'Reporte ARI ' . $request->anio . '.xlsx');
            } else {

                return Excel::download(new AriExport($result), 'Reporte ARI ' . $request->anio . '  ' . $trimestre . '.xlsx');
            }
        } catch (\Throwable $e) {
            Session::flash('error', 'Ocurrio un problema ' . $e->getMessage());
            return back();
        }
    }
}
