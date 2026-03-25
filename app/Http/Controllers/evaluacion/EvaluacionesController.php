<?php

namespace App\Http\Controllers\evaluacion;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models\evaluacion\SegmentoModel;
use App\Models\evaluacion\IndicadorModel;

use App\Exports\Evaluaciones\IndicadorExport;
use App\Exports\Evaluaciones\EvaluacionExport;

use App\Imports\evaluaciones\EvaluacionesImport; 

use App\Models\personal\CargoModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\CentroCostoAsignadoModel;
use App\Models\personal\PersonalModel;
use App\Models\evaluacion\RespuestaModel;
use App\Models\evaluacion\EvaluacionModel;
use App\Models\User;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Exceptions\ValidateAditionals;

use App\Http\Controllers\PoliceEstructure;

class EvaluacionesController extends Controller {

    public function __construct() {
        $this->tipo_periodo = ["diario" => "Diario", "semanal" => "semanal", "mensual" => "Mensual", "quincenal" => "Quincenal"];
    }

    public function index() {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        $cargados = SegmentoModel::groupby('cargo_id')->pluck('cargo_id');
        $cargo = CargoModel::whereIn('id', $cargados)->where('estatus_id', 1)->orderBy('nombre_cargo')->get();
        $segmentos_cargados = SegmentoModel::all();

        return view('evaluacion.administracion.index')->with(["cargo" => $cargo, "areas" => $areas, "segmentos_cargados" => $segmentos_cargados])->render();
    }

    public function create() {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('evaluacion.administracion.create')->with(["areas" => $areas,
                    "periodos" => $this->tipo_periodo])->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
       
        try {
            $segmento = new SegmentoModel();
            $segmento->area_trabajo_id = $request->area_trabajo;
            $segmento->cartera_id = $request->carteras;
            $segmento->skill_id = $request->skill;
            $segmento->cargo_id = $request->cargo;
            $segmento->estatus = 1;
            $segmento->tipo_periodo = strtolower($request->tipo_periodo);
            $segmento->save();

            for ($i = 1;
                    $i <= $request->totalindicadores;
                    $i++) {
                if ($request->input('i' . $i) !== null) {
                    $indicador = new IndicadorModel();
                    $indicador->eval_segmentos_id = $segmento->id;
                    $indicador->indicador = $request->input('i' . $i);
                    $indicador->meta = $request->input('m' . $i);
                    $indicador->tipo = $request->input('t' . $i);
                    $indicador->save();
                }
            }
            \Session::flash('success', "Indicadores de Evaluación registrados correctamente.");
//dd("lsito");
            return redirect()->back();
        } catch (\Exception $e) {
            \Session::flash('error', "Ocurrio un problema durante la ejecución " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show(Request $request) {

        $row = array();
        $data = array();

        if ($request->area_trabajo == "" && $request->carteras == "" && $request->cargo == "") {
            $segmento = SegmentoModel::all();
        }

        if ($request->area_trabajo == "" && $request->carteras == "" && $request->cargo != "") {
            $segmento = SegmentoModel::where('cargo_id', $request->cargo)->get();
        }

        if ($request->area_trabajo != "" && $request->carteras == "" && $request->cargo == "") {
            $segmento = SegmentoModel::where('area_trabajo_id', $request->area_trabajo)->get();
        }

        if ($request->area_trabajo != "" && $request->carteras == "" && $request->cargo != "") {
            $segmento = SegmentoModel::where('cargo_id', $request->cargo)->where('area_trabajo_id', $request->area_trabajo)->get();
        }

        if ($request->area_trabajo != "" && $request->carteras != "" && $request->cargo == "") {
            $segmento = SegmentoModel::where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)->get();
        }

        if ($request->area_trabajo != "" && $request->carteras != "" && $request->cargo != "") {
            $segmento = SegmentoModel::where('cargo_id', $request->cargo)
                            ->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)->get();
        }


        foreach ($segmento as $segment) {

            $row["gerencia"] = $segment->RelationArea->nombre_campana;
            $row["carteras"] = $segment->RelationCartera->nombre_cartera;
            $row["skill"] = $segment->RelationSkill->nombre_skill;
            $row["cargo"] = $segment->RelationCargos->nombre_cargo;
            $row["cantidad"] = count($segment->Relationindicadores);
            $row["tipo"] = $segment->tipo_periodo;

            switch ($segment->estatus) {
                case 1;
                    $active = "checked";
//   $estatus = '<button type="button" class="btn btn-success btn-sm" onclick="disabledsegment(' . $segment->id . ')"  id="segmentactive' . $segment->id . '" data-href="' . route('segment.change.estatus', $segment->id) . '" data-value="0">Activo</button>';
                    break;
                case 2:
                    $active = "";
//  $estatus = '<button type="button" class="btn btn-danger btn-sm" onclick="disabledsegment(' . $segment->id . ')" id="segmentactive' . $segment->id . '" data-href="' . route('segment.change.estatus', $segment->id) . '" data-value="1">Inactivo</button>';
                    break;
            }

            $row["estatus"] = '<input type="checkbox" class="periodoactive" ' . $active . ''
                    . ' onChange="ChangePeriodoActive(' . $segment->id . ')" id="segmentoactive' . $segment->id . '" '
                    . ' name="segmentoactive' . $segment->id . '" data-href="' . route('segment.change.estatus', $segment->id) . '" data-value="' . $segment->id . '" data-bootstrap-switch  data-off-color="danger" data-on-color="success">';

            $editHtml = '            
            <a href="' . route('evaluacion.administracion.edit', $segment->id) . '" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Editar">
                <i class="fa fa-edit"></i>
            </a>';
            $buttonDelte = '<button type="button"  onclick="DeleteSegmento(' . $segment->id . ')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar">
                    <i class="fas fa-minus-circle"></i></button>';

            $formdelete = '<form method="post" action="' . route('evaluacion.administracion.destroy', $segment->id) . '"'
                    . 'id="deleteform' . $segment->id . '" data-cargo="' . $segment->RelationCargos->nombre_cargo . '">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token"  value="' . csrf_token() . '" >
                <div class="btn-group">' . $editHtml . ' ' . $buttonDelte . '</div>
                </form>';

            $row["acciones"] = $formdelete;

            $data[] = $row;
        }
        return json_encode($data);
    }

    public function edit($id) {
        $segmento = SegmentoModel::find($id);
        return view('evaluacion.administracion.edit')->with(["segmento" => $segmento])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
// try {

        $segmento = SegmentoModel::find($id);

///total de inputs cargados en el front             
        for ($totalidad = 1;
                $totalidad <= $request->totalindicadores;
                $totalidad++) {
//validamos si el indicador recorrido es nuevo de ser cierto, registramos
            if ($request->input('nuevo' . $totalidad) == true) {
                $newindicador = new IndicadorModel();
                $newindicador->eval_segmentos_id = $segmento->id;
                $newindicador->indicador = $request->input('i' . $totalidad);
                $newindicador->meta = $request->input('m' . $totalidad);
                $newindicador->tipo = $request->input('t' . $totalidad);
                $newindicador->save();
            }
        }
/// total de segmentos que se actualizaran o eliminaran que son existentes
        foreach ($request->indicadoresPUT as $actualizar) {
            $indicador = $segmento->Relationindicadores()->where('id', $actualizar)->first();
///si no existe en el request se elimina.
            if ($request->input('i' . $actualizar) == null) {
                $indicador->delete();
            } else {
                $indicador->indicador = $request->input('i' . $actualizar);
                $indicador->meta = $request->input('m' . $actualizar);
                $indicador->tipo = $request->input('t' . $actualizar);
                $indicador->save();
            }
        }

        \Session::flash('success', "Indicadores de Evaluación actualizados correctamente.");
        return redirect()->route('evaluacion.administracion.index');
        /* } catch (\Exception $e) {
          \Session::flash('error',$e->getMessage());
          return redirect()->back();
          } */
    }

    public function destroy($id) {
        try {
            SegmentoModel::find($id)->delete();
            \Session::flash('success', "Segmento de Evaluacion eliminado exitosamente.");
            return back();
        } catch (\Exception $e) {
            \Session::flash('error', "Ocurrio un problema durante la ejecución " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function ActiveUpdate(Request $request, $id) {

        $segmento = SegmentoModel::find($id);
        $segmento->estatus = $request->state;
        $segmento->save();
        if ($request->state == 1) {
            return json_encode(["El indicador ha sido activado exitosamente"]);
        } else {
            return json_encode(["El indicador ha sido desactivado exitosamente."]);
        }
    }

    public function NewEvaluacion() {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('evaluacion.index')->with(['areas' => $areas])->render();
    }

    public function consultEvaluacion(Request $request) {

        $skill_asig = $request->skill;
        $cargo_eval = $request->cargo;
        $area_sel = $request->area_trabajo;

        $skill_name = SkillModels::select('nombre_skill')->where('id', $skill_asig)->first();

        $nombre_skill = $skill_name->nombre_skill;
        $cargo_name = CargoModel::select('nombre_cargo')->where('id', $cargo_eval)->first();
        $nombre_cargo = $cargo_name->nombre_cargo;

        //BUSCAMOS EL TIPO DE PERIODO SEGUN EL CARGO Y EL SKILL SELECCIONADO
        //COMPROBAMOS QUE HAYA REGISTROS DE SEGMENTO

        if (!SegmentoModel::where('cargo_id', $cargo_eval)
                        ->where('cartera_id', $request->carteras)
                        ->where('skill_id', $skill_asig)
                        ->where('area_trabajo_id', $area_sel)
                        ->exists()) {

            \Session::flash('error', 'No hay indicadores disponibles para el cargo y skill seleccionado');
            return redirect()->back();
        }

        $tipo_sql = SegmentoModel::where('area_trabajo_id', $area_sel)
                        ->where('cartera_id', $request->carteras)->where('skill_id', $skill_asig)
                        ->where('cargo_id', $cargo_eval)->first();

        $tipo_periodo = $tipo_sql->tipo_periodo;

//SI EL TIPO DE PERIODO ES SEMANAL , CALCULAMOS EL DIA EN EL QUE ESTAMOS SE LO CONCATENAMOS A LA FECHA DE EVALUACION Y MANDAMOS TODO A LA VISTA CON LA SEMANA SELECCIONADA

        $today = Carbon::today(); // Solo la fecha sin la hora
        $customFormat = $today->format('d'); // Formato: Día

        switch (strtolower($tipo_periodo)) {
            case "semanal":
                $particion_fecha = carbon::createfromFormat('m/Y', $request->fechaeval);

                $month = $particion_fecha->copy()->format('m'); //mes seleccionado
                $year = $particion_fecha->copy()->format('Y'); // Año seleccionado
                $semana_selec = $request->periodo; // semana seleccionada
                $semana = "Semana " . $request->periodo;

                $month = intval($month);    //force month to single integer if '0x'
                $suff = array('st', 'nd', 'rd', 'th', 'th', 'th');   //week suffixes
                $end = date('t', mktime(0, 0, 0, $month, 1, $year));   //last date day of month: 28 - 31
                $start = date('w', mktime(0, 0, 0, $month, 1, $year));  //1st day of month: 0 - 6 (Sun - Sat)
                $last = 8 - $start;      //get last day date (Sat) of first week
                $noweeks = ceil((($end - ($last + 1)) / 7) + 1);  //total no. weeks in month
                $output = "";      //initialize string		
                $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
                for ($x = 1;
                        $x < $noweeks + 1;
                        $x++) {
                    if ($x == 1) {
                        $startdate = "01-$monthlabel-$year";
                        $day = $last - 6;
                    } else {
                        $day = $last + 1 + (($x - 2) * 7);
                        $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                        $startdate = "$day-$monthlabel-$year";
                    }
                    if ($x == $noweeks) {
                        $enddate = "$end-$monthlabel-$year";
                    } else {
                        $dayend = $day + 6;
                        $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                        $enddate = "$dayend-$monthlabel-$year";
                    }
                    $output .= "Semana {$x} -> $startdate al $enddate<br />";
                }

                $semanasArray = explode("<br />", $output);
                $num_semana = $request->periodo;

                $fecha_evaluacion = $semanasArray[$num_semana - 1];
                $mes = $request->fechaeval;
                $quincena = null;
                break;
            case "mensual":
                $particion_fecha = carbon::createfromFormat('m/Y', $request->fechaeval);

                $month = $particion_fecha->copy()->format('m'); //mes seleccionado
                $year = $particion_fecha->copy()->format('Y'); // Año seleccionado
// Crear una instancia de Carbon para el primer día del mes seleccionado
                $primerDiaDelMes = Carbon::createFromDate($year, $month, 1);

// Formatear la fecha en el formato d-m-y
                $fecha_evaluacion = $primerDiaDelMes->format('d-m-Y');
                $semana = null;
                $num_semana = null;
                $mes = $request->fechaeval;
                $quincena = null;
                break;
            case "quincenal":

                $particion_fecha = Carbon::createfromFormat('m/Y', $request->fechaeval);

                $month = $particion_fecha->copy()->format('m'); //mes seleccionado
                $year = $particion_fecha->copy()->format('Y'); // Año seleccionado

                if ($request->periodo == "1") {
                    $primerDia = Carbon::createFromDate($year, $month, 1);
                } else {

                    $primerDia = Carbon::createFromDate($year, $month, 16);
                }

                $fecha_evaluacion = $primerDia->format('d-m-Y');
                $semana = null;
                $num_semana = null;
                $mes = $request->fechaeval;
                $quincena = $request->periodo;
                break;
            case "diario":
                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fechaeval);
                $fecha_evaluacion = $fechaCarbon->format('d-m-Y');
                $semana = null;
                $num_semana = null;
                $mes = null;
                $quincena = null;
                break;
        }

        //SACAMOS EL ID DEL SEGMENTO PARA TRAERNOS LOS INDICADORES DEL MISMO
        $segmento = SegmentoModel::select('id', 'estatus')->where('cargo_id', $cargo_eval)
                        ->where('cartera_id', $request->carteras)->where('skill_id', $skill_asig)
                        ->where('area_trabajo_id', $request->area_trabajo)->first();

        $segmento_id = $segmento->id;

        //COMPROBAMOS QUE EL SEGMENTO ESTE ACTIVO
        if ($segmento->estatus != 1) {
            \Session::flash('error', 'Los indicadores seleccionados no estan activos');
            return redirect()->back();
        } else {
            //SACAMOS LOS INDICADORES CORRESPONDIENTES Y LOS MANDAMOS A LA VISTA
            $indicadores = IndicadorModel::where('eval_segmentos_id', $segmento_id)->get();
            //CALCULAMOS CUANTOS INDICADORES TIENE ESE SEGMENTO
            $indicador_count = IndicadorModel::where('eval_segmentos_id', $segmento_id)->count();
            $cartera = $request->carteras;

            return view('evaluacion.list', compact('skill_asig', 'cargo_eval',
                            'fecha_evaluacion', 'indicadores', 'nombre_skill',
                            'nombre_cargo', 'indicador_count', 'semana',
                            'tipo_periodo', 'num_semana',
                            'mes', 'quincena', 'cartera', 'area_sel'));
        }
    }

    public function getPersonal(Request $request, $skill_asig, $cargo_eval, $fecha_evaluacion, $tipo_periodo, $cartera, $area_sel) {

        $fechaActual = Carbon::now();
        $data = array();
        //SACAMOS EL ID DEL PERSONAL AUTENTICADO                
        if (\Auth::user()->hasRole('admin') || \Auth::user()->hasPermission('indicadores.carteras')) {
            //si es distribuido
            if ($request->carteras == 53 && $request->area_trabajo == 2) {
                // Buscamos el skill correspondiente                
                $skillsCecos = SkillModels::find($skill_asig);
                //realizamos al busqueda del ceco para extrar el primer id (esto para los casos de claro y cantv)
                $cecos = SkillModels::where('cod_ceco', $skillsCecos->cod_ceco)->first()->pluck('cod_ceco');

                /// extramos el personal asociado al ceco respectivo que trienen en la ficha distribuido
                $personal_ceco = CentroCostoAsignadoModel::select('personal_id')
                                ->where('skill_id', $cecos)->get()->toArray();

                $personal = PersonalModel::where('cargo_id', $cargo_eval)
                                ->where('skill_id', $skill_asig)
                                ->whereIn('id', $personal_ceco)->where('estatus_id', 1)->get();
            } else {
                ///si es otra campaña diferente a distribuido
                $personal = PersonalModel::where('cargo_id', $cargo_eval)
                                ->where('skill_id', $skill_asig)
                                ->where('estatus_id', 1)->get();
            }
        } else {
            //si no tengo permisos
            $idpersonal = PoliceEstructure::PersonalAsignaded(\Auth::user()->cedula);

            $personal = PersonalModel::wherein('id', $idpersonal)
                            ->where('cargo_id', $cargo_eval)
                            ->where('skill_id', $skill_asig)
                            ->where('estatus_id', 1)->get();
        }
        // dd($personal,$idpersonal,$skill_asig,\Auth::user()->cedula);
        $fecha_evaluacion_format = $fechaActual->copy()->format('Y-m-d');
        //SI LA EVALUACION QUE SE VA A REALIZAR ES SEMANAL DETERMINAMOS LA SEMANA EN LA QUE ESTAMOS DEL MES EN CURSO          
        switch (strtolower($tipo_periodo)) {
            case"semanal":
                $currentWeek = $fechaActual->copy()->weekOfMonth;
                $partes = explode(" -> ", $fecha_evaluacion);
                $fechas_sin_formato = explode(" al ", $partes[1]);

                // Asignar las fechas a variables separadas
                $fecha_inicio_sin_formato = $fechas_sin_formato[0];
                $fecha_fin_sin_formato = $fechas_sin_formato[1];

                $partes_fecha = explode("-", $fecha_inicio_sin_formato);
                $fecha_inicio = $partes_fecha[2] . "-" . $partes_fecha[1] . "-" . $partes_fecha[0];

                $partes_fecha_fin = explode("-", $fecha_fin_sin_formato);
                $fecha_fin = $partes_fecha_fin[2] . "-" . $partes_fecha_fin[1] . "-" . $partes_fecha_fin[0];

                $fechaDesde = Carbon::parse($fecha_inicio);
                $fechaHasta = Carbon::parse($fecha_fin);

                break;
            case"mensual":
                //SI ES MENSUAL NOS TRAEMOS EL MES ACTUAL
                $currentMonth = $fechaActual->format('F');
                $primer_dia_string = $fecha_evaluacion;
                // Crear un objeto Carbon a partir del primer día (asumiendo formato d-m-Y)
                $primer_dia = Carbon::createFromFormat('d-m-Y', $primer_dia_string);

                // Obtener el último día del mes
                $ultimo_dia = $primer_dia->copy()->endOfMonth();

                $fechaDesde = Carbon::parse($primer_dia->format('Y-m-d'));
                $fechaHasta = Carbon::parse($ultimo_dia->format('Y-m-d'));
                break;
            case"diario":
                //SI ES DIARIA NO TRAEMOS LA FECHA ACTUAL
                $currentDay = Carbon::now()->format('d-m-Y');
                $dia_actual = Carbon::createFromFormat('d-m-Y', $fecha_evaluacion)->format('Y-m-d');

                $fechaDesde = Carbon::parse($dia_actual);
                $fechaHasta = Carbon::parse($dia_actual);
                break;
            case"quincenal":
                //SI ES QUINCENAL NOS TRAEMOS LAS QUINCENAS RESPECTIVAS
                // Crear un objeto Carbon a partir del primer día (asumiendo formato d-m-Y)
                $primer_dia = Carbon::createFromFormat('d-m-Y', $fecha_evaluacion);

                // Obtener el día del mes
                $dia_del_mes = $primer_dia->day;

                // Si es la primera quincena (del 1 al 15)
                if ($dia_del_mes >= 1 && $dia_del_mes <= 15) {
                    $ultimo_dia = Carbon::create($primer_dia->year, $primer_dia->month, 15);
                } else { // Si es la segunda quincena (del 16 en adelante)
                    $ultimo_dia = $primer_dia->copy()->endOfMonth();
                }

                // Formatear las fechas a Y-m-d
                $primer_dia_quincena_formateado = Carbon::createFromFormat('d-m-Y', $fecha_evaluacion)->format('Y-m-d');
                $ultimo_dia_quincena1_formateado = $ultimo_dia->format('Y-m-d');

                $fechaDesde = Carbon::parse($primer_dia_quincena_formateado);
                $fechaHasta = Carbon::parse($ultimo_dia_quincena1_formateado);
                break;
        }

        foreach ($personal as $persona) {
            $per = array();
            $nombre_personal = $persona->primer_nombre . " " . $persona->segundo_nombre . " " . $persona->primer_apellido . " " . $persona->segundo_apellido;
            $per['Cedula'] = $persona->cedula_empleado;
            $per['Nombre'] = $persona->primer_nombre . " " . $persona->segundo_nombre . " " . $persona->primer_apellido . " " . $persona->segundo_apellido;
            $per['Cargo'] = $persona->RelationCargos->nombre_cargo;
            $per['Skill'] = $persona->Relationskill->nombre_skill;

            $evaluacionExistente = EvaluacionModel::where('personal_id', $persona->id)
                            ->where('cargo_id', $cargo_eval)->where('skill_id', $skill_asig)
                            ->where('area_trabajo_id', $area_sel)->where('cartera_id', $cartera)
                            ->where(function ($query) use ($fechaDesde, $fechaHasta) {
                                $query->whereBetween('desde', [$fechaDesde, $fechaHasta])
                                        ->orWhereBetween('hasta', [$fechaDesde, $fechaHasta])
                                        ->orWhere(function ($query) use ($fechaDesde, $fechaHasta) {
                                            $query->where('desde', '<=', $fechaDesde)
                                                    ->where('hasta', '>=', $fechaHasta);
                                        });
                            })->exists();

            if ($evaluacionExistente) {

                $actionsHtml = "<button class='btn btn-success' disabled><i class='fa fa-headphones'></i> Evaluado</a>";
            } else {

                $actionsHtml = "<button type='button' id='personal" . $persona->id . "' class='btn btn-primary editpersonal'"
                        . " data-personal_id='" . $persona->id . "' "
                        . "data-nombre_personal='" . $nombre_personal . "' "
                        . "data-cargo_id='" . $cargo_eval . "' "
                        . "data-skill_id='" . $skill_asig . "' title='Evaluar' "
                        . "data-coreui-toggle='modal' "
                        . "data-coreui-target='#editpersonal'>"
                        . "<i class='fa fa-headphones'></i> Evaluar</button>";
            }


            $per['Acciones'] = $actionsHtml;

            $data[] = $per;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
    }

    public function register(Request $request) {
//dd($request->all());
        try {
//USUARIO AUTENTICADO ES QUIEN EVALUA A LA PERSONA
            $user_id = \Auth::user()->id;
            $count = $request->count;
//SACAMOS EL MES Y EL AÑO DE LA FECHA DE EVALUACION    
            $fechaCarbon = Carbon::createFromFormat('d-m-Y', $request->fecha_evaluacion)->startOfMonth();
            $year = $fechaCarbon->copy()->format('Y');
            $mes = $fechaCarbon->copy()->format('F');

//GUARDAMOS LA FECHA DE EVALUACION , EL QUE REALIZO LA EVALUACION Y A QUIEN SE LA HICIERON

            $evaluacion = new EvaluacionModel();
            $evaluacion->mes = $mes;
            $evaluacion->anio = $year;
            $evaluacion->tipo_periodo = strtolower($request->tipo_periodo);
            $evaluacion->area_trabajo_id = CarteraModel::find($request->cartera)->area_trabajo_id;
            $evaluacion->cartera_id = $request->cartera;
            $evaluacion->skill_id = $request->skill_id;
           
            switch (strtolower($request->tipo_periodo)) {
                case "semanal":
                    $partes = explode(" -> ", $request->input('fecha_evaluacion'));
                    $fechas_sin_formato = explode(" al ", $partes[1]);

                    // Asignar las fechas a variables separadas
                    $fecha_inicio_sin_formato = $fechas_sin_formato[0];
                    $fecha_fin_sin_formato = $fechas_sin_formato[1];

                    $partes_fecha = explode("-", $fecha_inicio_sin_formato);
                    $fecha_inicio = $partes_fecha[2] . "-" . $partes_fecha[1] . "-" . $partes_fecha[0];

                    $partes_fecha_fin = explode("-", $fecha_fin_sin_formato);
                    $fecha_fin = $partes_fecha_fin[2] . "-" . $partes_fecha_fin[1] . "-" . $partes_fecha_fin[0];

                    $evaluacion->desde = $fecha_inicio;
                    $evaluacion->hasta = $fecha_fin;
                    $evaluacion->periodo = $request->num_semana;
                    break;
                case "quincenal":
                    // Crear un objeto Carbon a partir del primer día (asumiendo formato d-m-Y)
                    $primer_dia = Carbon::createFromFormat('d-m-Y', $request->input('fecha_evaluacion'));

                    // Obtener el día del mes
                    $dia_del_mes = $primer_dia->copy()->day;
                    // Si es la primera quincena (del 1 al 15)
                    if ($dia_del_mes >= 1 && $dia_del_mes <= 15) {
                        $ultimo_dia = Carbon::create($primer_dia->year, $primer_dia->month, 15);
                        $evaluacion->periodo = 1;
                    } else { // Si es la segunda quincena (del 16 en adelante)
                        $ultimo_dia = $primer_dia->copy()->endOfMonth();
                        $evaluacion->periodo = 2;
                    }

                    // Formatear las fechas a Y-m-d
                    $primer_dia_quincena_formateado = Carbon::createFromFormat('d-m-Y', $request->input('fecha_evaluacion'))->format('Y-m-d');
                    $ultimo_dia_quincena1_formateado = $ultimo_dia->format('Y-m-d');

                    $evaluacion->desde = $primer_dia_quincena_formateado;
                    $evaluacion->hasta = $ultimo_dia_quincena1_formateado;

                    break;
                case "mensual":

                    // Crear un objeto Carbon a partir del primer día (asumiendo formato d-m-Y)
                    $primer_dia = Carbon::createFromFormat('d-m-Y', $request->fecha_evaluacion);

                    // Formatear ambas fechas a Y-m-d
                    $primer_dia_formateado = $primer_dia->copy()->format('Y-m-d');
                    $ultimo_dia_formateado = $primer_dia->copy()->endOfMonth()->format('Y-m-d');

                    $evaluacion->desde = $primer_dia_formateado;
                    $evaluacion->hasta = $ultimo_dia_formateado;
                 
                    break;
                case "diaria":
                    $dia_actual = Carbon::createFromFormat('d-m-Y', $request->input('fecha_evaluacion'))->format('Y-m-d');
                    $evaluacion->desde = $dia_actual;
                    $evaluacion->hasta = $dia_actual;
                    break;
            }

            $evaluacion->personal_id = $request->personal_id;
            $evaluacion->user_id = $user_id;
            $evaluacion->cargo_id = $request->cargo_id;
            $evaluacion->save();
            
            for ($i = 1; $i <= $count; $i++) {
                //BUSCAMOS EN QUE SEGMENTO ESTAMOS SEGUN EL ID INDICADOR QUE SE RECIBA
                $segmento_search = IndicadorModel::select('eval_segmentos_id', 'indicador', 'tipo')
                                ->where('id', $request->input('indicador_id' . $i))->first();
                //GUARDAMOS LAS RESPUESTAS DE LOS INDICADORES
                $respuesta = new RespuestaModel();
                $respuesta->segmento_id = $segmento_search->eval_segmentos_id;
                $respuesta->indicador = strtoupper($segmento_search->indicador);
                $respuesta->tipo = $segmento_search->tipo;
                $respuesta->meta = $request->input('meta' . $i);
                $respuesta->puntaje = $request->input('puntaje' . $i);
                $respuesta->evaluacion_id = $evaluacion->id;
                $respuesta->save();
            }

            return json_encode(['message' => "Evaluacion Registrada exitosamente.", 'data' => $respuesta]);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

    public function EditEvaluacion() {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('evaluacion.edit')->with(["areas" => $areas]);
    }

    public function consultEditEvaluacion(Request $request) {


        if (!SegmentoModel::where('cargo_id', $request->cargo)->where('skill_id', $request->skill)->where('area_trabajo_id', $request->area_trabajo)->where('cartera_id', $request->carteras)->exists()) {
            \Session::flash('error', 'No hay indicadores disponibles para el cargo y skill seleccionado');
            return redirect()->back();
        }

        $segmento = SegmentoModel::where('skill_id', $request->skill)
                        ->where('cargo_id', $request->cargo)
                        ->where('area_trabajo_id', $request->area_trabajo)
                        ->where('cartera_id', $request->carteras)->first();

        switch (strtolower($segmento->tipo_periodo)) {
            case "diario":
                $desde = Carbon::createFromFormat('d/m/Y', $request->fechaeval)->format('Y-m-d');
                $hasta = Carbon::createFromFormat('d/m/Y', $request->fechaeval)->format('Y-m-d');
                break;
            case "mensual":
                $desde = Carbon::createFromFormat('m/Y', $request->fechaeval)->startofMonth()->format('Y-m-d');
                $hasta = Carbon::createFromFormat('m/Y', $request->fechaeval)->endofMonth()->format('Y-m-d');

                $semana = null;
                $num_semana = null;
                $mes = $request->fechaeval;
                $quincena = null;
                break;
            case "quincenal":

                $fecha = carbon::createFromformat('m/Y', $request->fechaeval);

                $year = $fecha->copy()->format('Y');
                $month = $fecha->copy()->format('m');
                $fecha_evaluacion = $fecha->copy()->format('d-m-Y');

                if ($request->periodo == "1") {

                    $primerDia = Carbon::createFromDate($year, $month, 1);

                    $ultimo_dia = Carbon::create($year, $month, 15);
                } else {
                    $primerDia = Carbon::createFromDate($year, $month, 16);
                    $ultimo_dia = $fecha->copy()->endOfMonth();
                }

                $desde = $primerDia->copy()->format('y-m-d');
                $hasta = $ultimo_dia->copy()->format('y-m-d');

                $semana = null;
                $num_semana = null;
                $mes = $request->fechaeval;
                $quincena = $request->periodo;

                break;
            case "semanal":
                //SI ES SEMANAL DEBEMOS SACAR LA PRIMERA FECHA DE ESA SEMANA

                $fecha = carbon::createFromformat('m/Y', $request->fechaeval);
                $month = $fecha->copy()->format('m'); //mes seleccionado               
                $year = $fecha->copy()->format('Y'); // Año seleccionado     
                $semana_selec = $request->periodo; // semana seleccionada

                $semana = "Semana " . $request->periodo;

                $month = intval($month);    //force month to single integer if '0x'
                $suff = array('st', 'nd', 'rd', 'th', 'th', 'th');   //week suffixes
                $end = date('t', mktime(0, 0, 0, $month, 1, $year));   //last date day of month: 28 - 31
                $start = date('w', mktime(0, 0, 0, $month, 1, $year));  //1st day of month: 0 - 6 (Sun - Sat)
                $last = 8 - $start;      //get last day date (Sat) of first week
                $noweeks = ceil((($end - ($last + 1)) / 7) + 1);  //total no. weeks in month
                $output = "";      //initialize string		
                $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
                for ($x = 1; $x < $noweeks + 1; $x++) {
                    if ($x == 1) {
                        $startdate = "01-$monthlabel-$year";
                        $day = $last - 6;
                    } else {
                        $day = $last + 1 + (($x - 2) * 7);
                        $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                        $startdate = "$day-$monthlabel-$year";
                    }
                    if ($x == $noweeks) {
                        $enddate = "$end-$monthlabel-$year";
                    } else {
                        $dayend = $day + 6;
                        $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                        $enddate = "$dayend-$monthlabel-$year";
                    }
                    $output .= "Semana {$x} -> $startdate al $enddate<br />";
                }

                $semanasArray = explode("<br />", $output);
                $num_semana = $request->periodo;
                $mes = $request->fechaeval;
                $fecha_evaluacion = $semanasArray[$num_semana - 1];
                $quincena = null;
                // Dividir la cadena en partes usando "->" y "al" como delimitadores
                $partes = explode(" -> ", $fecha_evaluacion);
                $fechas_sin_formato = explode(" al ", $partes[1]);

                // Asignar las fechas a variables separadas
                $fecha_inicio_sin_formato = $fechas_sin_formato[0];
                $fecha_fin_sin_formato = $fechas_sin_formato[1];

                $partes_fecha = explode("-", $fecha_inicio_sin_formato);
                $fecha_inicio = $partes_fecha[2] . "-" . $partes_fecha[1] . "-" . $partes_fecha[0];

                $partes_fecha_fin = explode("-", $fecha_fin_sin_formato);
                $fecha_fin = $partes_fecha_fin[2] . "-" . $partes_fecha_fin[1] . "-" . $partes_fecha_fin[0];

                $desde = $fecha_inicio;
                $hasta = $fecha_fin;
                break;
        }

        if (\Auth::user()->hasRole('admin') || \Auth::user()->hasPermission('indicadores.carteras')) {
            //si es distribuido
            if ($request->carteras == 53 && $request->area_trabajo == 2) {
                // Buscamos el skill correspondiente                
                $skillsCecos = SkillModels::find($request->skill);
                //realizamos al busqueda del ceco para extrar el primer id (esto para los casos de claro y cantv)
                $cecos = SkillModels::where('cod_ceco', $skillsCecos->cod_ceco)->first()->pluck('cod_ceco');

                /// extramos el personal asociado al ceco respectivo que trienen en la ficha distribuido
                $personal_ceco = CentroCostoAsignadoModel::select('personal_id')
                                ->where('skill_id', $cecos)->get()->toArray();

                $personal = PersonalModel::where('cargo_id', $request->cargo)
                                ->where('skill_id', $request->skill)
                                ->whereIn('id', $personal_ceco)->where('estatus_id', 1)->pluck('id');
            } else {
                ///si es otra campaña diferente a distribuido
                $personal = PersonalModel::where('cargo_id', $request->cargo)
                                ->where('skill_id', $request->skill)
                                ->where('estatus_id', 1)->pluck('id');
            }
        } else {
            //si no tengo permisos
            $personal = PoliceEstructure::PersonalAsignaded(\Auth::user()->cedula);
        }

        $valuaciones = EvaluacionModel::wherein('personal_id', $personal)
                        ->where('cargo_id', $request->cargo)
                        ->where('skill_id', $request->skill)
                        ->where('area_trabajo_id', $request->area_trabajo)
                        ->where('cartera_id', $request->carteras)
                        ->where('desde', $desde)->where('hasta', $hasta)->get();
        /// rellenamos el modal con varios datos
        $nombreskill = SkillModels::find($request->skill)->nombre_skill;
        $nombrecargo = CargoModel::find($request->cargo)->nombre_cargo;

        return view('evaluacion.updatelist')->with(["valuaciones" => $valuaciones,
                    "tipo_periodo" => strtoupper($segmento->tipo_periodo),
                    "nombre_skill" => $nombreskill,
                    "nombre_cargo" => $nombrecargo]);
    }

    public function GetPersonalEdit($evaluacion) {

        try {
            $respuestas = RespuestaModel::where('evaluacion_id', $evaluacion)->get();
            $respuestas_count = $respuestas->count();

            $vista = view('evaluacion.partials.updateIndicadores')
                            ->with(["indicadores" => $respuestas,
                                "indicador_count" => $respuestas_count,
                                "evaluacion_id" => $evaluacion])->render();

            return json_encode($vista);
        } catch (\Exception $e) {
            return json_encode(abort(500, "No es posible desplegar los indicadores " . $e->getMessage()));
        }
    }

    public function updateEvaluacion(Request $request) {

        try {

            foreach ($request->indicador_id as $key => $indicadores) {
                $respuesta = RespuestaModel::find($indicadores);
                $respuesta->puntaje = $request->puntaje[$key];
                $respuesta->save();
            }

            return response()->json(['message' => "Indicadores Actualizados correctaente"]);
        } catch (\Exception $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }

    public function reporteEvaluacion() {

        $user = ENV('DEV_USER_CHANGE', false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();
        //\session()->regenerate();   
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('evaluacion.reporte')->with(["areas" => $areas]);
    }

    public function getReporte(Request $request) {

        try {
            $rango = explode("-", $request->rangofecha);
            $begin = carbon::createfromFormat('d/m/Y', trim($rango[0]))->startofDay();
            $end = carbon::createfromFormat('d/m/Y', trim($rango[1]))->endofDay();

            if (!SegmentoModel::where('cargo_id', $request->cargo)->where('skill_id', $request->skill)->exists()) {
                \Session::flash('error', 'No hay evaluaciones disponibles para el cargo y skill seleccionado');
                return redirect()->back();
            }

            $segmento = SegmentoModel::where('cargo_id', $request->cargo)->where('skill_id', $request->skill)->first();
            $indicadores = IndicadorModel::where('eval_segmentos_id', $segmento->id)->get();
            $indicadores_count = $indicadores->count();

            $user = ENV('DEV_USER_CHANGE', false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();
            $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

            switch ($nivel) {
                case 4:
                    ///Ubicamos los Id de todo el personal que tiene asignado
                    $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                    /// extraemos los skills que estan activos en la plataforma
                    $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                    $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                    $evaluaciones = EvaluacionModel::whereIn('personal_id', $idPersonals)
                            ->where('eval_evaluacion.cargo_id', $request->cargo)
                            ->where('eval_evaluacion.skill_id', $request->skill)
                            ->where('eval_evaluacion.cartera_id', $request->carteras)
                            ->where('eval_evaluacion.area_trabajo_id', $request->area_trabajo)
                            ->wherebetween('eval_evaluacion.desde', array($begin->copy()->format('y-m-d'), $end->copy()->format('y-m-d')))
                            ->get();
                    break;
                case 3:
                    $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                    $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                    $evaluaciones = EvaluacionModel::where('eval_evaluacion.cargo_id', $request->cargo)
                            ->where('eval_evaluacion.skill_id', $request->skill)
                            ->wherein('eval_evaluacion.cartera_id', $carterasautorize)
                            ->where('eval_evaluacion.area_trabajo_id', $request->area_trabajo)
                            ->wherebetween('eval_evaluacion.desde', array($begin->copy()->format('y-m-d'), $end->copy()->format('y-m-d')))
                            ->get();
                    break;
                case 2:

                    $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                    /// area 0 | cartera 1 | skill 2 | personal asignado 3
                    $skills = $carterasPersonal[2];
                    $personal = $carterasPersonal[3];

                    $evaluaciones = EvaluacionModel::whereIn('personal_id', $personal)
                            ->where('eval_evaluacion.cargo_id', $request->cargo)
                            ->where('eval_evaluacion.skill_id', $request->skill)
                            ->where('eval_evaluacion.cartera_id', $request->carteras)
                            ->where('eval_evaluacion.area_trabajo_id', $request->area_trabajo)
                            ->wherebetween('eval_evaluacion.desde', array($begin->copy()->format('y-m-d'), $end->copy()->format('y-m-d')))
                            ->get();

                    break;
                case 1:
                    $evaluaciones = EvaluacionModel::where('eval_evaluacion.cargo_id', $request->cargo)
                            ->where('eval_evaluacion.skill_id', $request->skill)
                            ->where('eval_evaluacion.cartera_id', $request->carteras)
                            ->where('eval_evaluacion.area_trabajo_id', $request->area_trabajo)
                            ->wherebetween('eval_evaluacion.desde', array($begin->copy()->format('y-m-d'), $end->copy()->format('y-m-d')))
                            ->get();
                    break;
                default:
                    ///retorna el array vacio, no posee permisos.
                    \Session::flash('error', abort(500, "No posee permisos para acceder a esta información"));
                    return back();
                    break;
            }

            $datos = [];
            $evaluaciones_report = array();
            foreach ($evaluaciones as $eval) {

                $obpersonal = PersonalModel::withTrashed()->find($eval->personal_id);
                $evaluaciones_report['personal_id'] = $eval->personal_id;
                $evaluaciones_report['eval_id'] = $eval->id;
                $evaluaciones_report['tipoevaluacion'] = $eval->tipo_periodo;
                $evaluaciones_report['periodo'] = $eval->periodo;
                $evaluaciones_report['fecha_evaluacion'] = date('d/m/Y', strtotime($eval->desde)) . " - " . date('d/m/Y', strtotime($eval->hasta));
                $evaluaciones_report['created_at'] = date('d/m/Y', strtotime($eval->created_at));
                $evaluaciones_report['mes'] = date('m/Y', strtotime($eval->desde));
                $evaluaciones_report['nombre_skill'] = $eval->relationSkill->nombre_skill;
                $evaluaciones_report['nombre_cargo'] = $eval->relationCargo->nombre_cargo;
                $evaluaciones_report['cedula_empleado'] = $obpersonal->cedula_empleado;
                $evaluaciones_report['nombre'] = $obpersonal->primer_nombre . " " . $obpersonal->segundo_nombre . " " . $obpersonal->primer_apellido . " " . $obpersonal->segundo_apellido;
                $evaluaciones_report['evaluador'] = $eval->relationUser != null ? $eval->relationUser->username : 'Usuario no Encontrado';
                $evaluaciones_report['resultados'] = [];

                $respuestas = RespuestaModel::where('evaluacion_id', $eval->id)->get();

                foreach ($respuestas as $rss) {
                    $porcentaje = $rss->tipo == 2 ? '%' : null;
                    $repuesta["indicador"] = $rss->indicador;
                    $repuesta["meta"] = $rss->meta . "" . $porcentaje;
                    $repuesta["puntaje"] = $rss->tipo == 2 ? round($rss->puntaje, 2) . "" . $porcentaje : round($rss->puntaje, 2);
                    array_push($evaluaciones_report['resultados'], $repuesta);
                }

                $datos[] = $evaluaciones_report;
            }

            return Excel::download(new EvaluacionExport($datos, $indicadores_count, $indicadores), 'Indicadores ' . $begin->copy()->format('Y-m-d') . ' al ' . $end->copy()->format('Y-m-d') . '.xlsx');
        } catch (\exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function getSemana(Request $request) {

        $semanasArray = array();
        $tipo_periodo = null;
        if (SegmentoModel::select('tipo_periodo')->where('cargo_id', $request->cargo_eval)->where('skill_id', $request->skill_asig)->exists()) {

            $tipo_periodo_sql = SegmentoModel::select('tipo_periodo')->where('cargo_id', $request->cargo_eval)->where('skill_id', $request->skill_asig)->first();
            $tipo_periodo = strtolower($tipo_periodo_sql->tipo_periodo);
        }

        if (strtolower($tipo_periodo) == "semanal") {

            $particion_fecha = carbon::createfromFormat('m/Y', $request->mes);
            $month = $particion_fecha->copy()->format('m'); //mes seleccionado
            $year = $particion_fecha->copy()->format('Y'); // Año seleccionado
            $semana_selec = $request->periodo; // semana seleccionada                
            $month = intval($month);    //force month to single integer if '0x'
            $suff = array('st', 'nd', 'rd', 'th', 'th', 'th');   //week suffixes
            $end = date('t', mktime(0, 0, 0, $month, 1, $year));   //last date day of month: 28 - 31
            $start = date('w', mktime(0, 0, 0, $month, 1, $year));  //1st day of month: 0 - 6 (Sun - Sat)
            $last = 8 - $start;      //get last day date (Sat) of first week
            $noweeks = ceil((($end - ($last + 1)) / 7) + 1);  //total no. weeks in month
            $output = "";      //initialize string		
            $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
            for ($x = 1;
                    $x < $noweeks + 1;
                    $x++) {
                if ($x == 1) {
                    $startdate = "01-$monthlabel-$year";
                    $day = $last - 6;
                } else {
                    $day = $last + 1 + (($x - 2) * 7);
                    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                    $startdate = "$day-$monthlabel-$year";
                }
                if ($x == $noweeks) {
                    $enddate = "$end-$monthlabel-$year";
                } else {
                    $dayend = $day + 6;
                    $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                    $enddate = "$dayend-$monthlabel-$year";
                }
                $output .= "Semana {$x} -> $startdate al $enddate<br />";
            }
            $semanasArray = explode("<br />", $output);          
        }
  
        return json_encode([$tipo_periodo, $semanasArray]);
    }

    public function selectorSemanas(Request $request) {

//FUNCION QUE CALCULA LA CANTIDAD DE SEMANAS EN UN MES

        $mes = $request->mes;
        $particion_fecha = explode("/", $mes);
        $year = $particion_fecha[1]; // Año seleccionado
        $month = $particion_fecha[0]; // Mes seleccionado

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); // Obtener el número de días en el mes seleccionado
        $weeks_in_month = ceil($days_in_month / 7); // Calcular el número de semanas redondeando hacia arriba
//dd($weeks_in_month);

        return json_encode($weeks_in_month);
    }

    public function cargaIndex() {

        //LOS SELECTORES FUNCIONAN MEDIANTE PERMISOS EN LA CARTERA
        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        return view('evaluacion.uploadMasive.index', compact('areas'));
    }

    public function cargaProcess(Request $request) {

        $campana = SkillModels::select('cartera_id')->where('id', $request->skill)->first();
        $campana_id = $campana->cartera_id;

        $gerencia = CarteraModel::select('area_trabajo_id')->where('id', $campana_id)->first();
        $gerencia_id = $gerencia->area_trabajo_id;

        //IDENTIFICAMOS EL TIPO DE PERIODO PARA SACAR LA FECHA DE EVALUACION DEPENDE DEL AREA, CARTERA, SKILL Y CARGO SELECCIONADOS
        if (\Auth::user()->hasRole('admin')) {

            $tipo_periodo_sql = SegmentoModel::select('tipo_periodo')->where('area_trabajo_id', $request->area_trabajo)
                            ->where('cartera_id', $request->carteras)->where('skill_id', $request->skill)
                            ->where('cargo_id', $request->cargo)->first();
        } else {
            $tipo_periodo_sql = SegmentoModel::select('tipo_periodo')->where('area_trabajo_id', $gerencia_id)
                            ->where('cartera_id', $campana_id)->where('skill_id', $request->skill)
                            ->where('cargo_id', $request->cargo)->first();
        }

        $tipo_periodo_seg = strtolower($tipo_periodo_sql->tipo_periodo);
     
         switch (strtolower($tipo_periodo_seg)) {
            case "semanal":
                $particion_fecha = carbon::createfromFormat('m/Y', $request->fechaeval);

                $month = $particion_fecha->copy()->format('m'); //mes seleccionado
                $year = $particion_fecha->copy()->format('Y'); // Año seleccionado
                $semana_selec = $request->periodo; // semana seleccionada
                $semana = "Semana " . $request->periodo;
                
                $month = intval($month);    //force month to single integer if '0x'
                $suff = array('st', 'nd', 'rd', 'th', 'th', 'th');   //week suffixes
                $end = date('t', mktime(0, 0, 0, $month, 1, $year));   //last date day of month: 28 - 31
                $start = date('w', mktime(0, 0, 0, $month, 1, $year));  //1st day of month: 0 - 6 (Sun - Sat)
                $last = 8 - $start;      //get last day date (Sat) of first week
                $noweeks = ceil((($end - ($last + 1)) / 7) + 1);  //total no. weeks in month
                $output = "";      //initialize string		
                $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
                for ($x = 1;
                        $x < $noweeks + 1;
                        $x++) {
                    if ($x == 1) {
                        $startdate = "01-$monthlabel-$year";
                        $day = $last - 6;
                    } else {
                        $day = $last + 1 + (($x - 2) * 7);
                        $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                        $startdate = "$day-$monthlabel-$year";
                    }
                    if ($x == $noweeks) {
                        $enddate = "$end-$monthlabel-$year";
                    } else {
                        $dayend = $day + 6;
                        $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                        $enddate = "$dayend-$monthlabel-$year";
                    }
                    $output .= "Semana {$x} -> $startdate al $enddate<br />";
                }

                $semanasArray = explode("<br />", $output);
                $num_semana = $request->periodo;
                
                $periodo_semanal = $request->periodo;                 
                $fecha_evaluacion = $semanasArray[$num_semana - 1];
             
                $mes = $particion_fecha->copy()->format('F');        
                
                $quincena = null;
                
                $partes = explode(" -> ", $fecha_evaluacion);
                $fechas_sin_formato = explode(" al ", $partes[1]);
                // Asignar las fechas a variables separadas
                $fecha_inicio_sin_formato = $fechas_sin_formato[0];
                $fecha_fin_sin_formato = $fechas_sin_formato[1];
                $partes_fecha = explode("-", $fecha_inicio_sin_formato);
                $fecha_inicio = $partes_fecha[2] . "-" . $partes_fecha[1] . "-" . $partes_fecha[0];
                
                $partes_fecha_fin = explode("-", $fecha_fin_sin_formato);
                $fecha_fin = $partes_fecha_fin[2] . "-" . $partes_fecha_fin[1] . "-" . $partes_fecha_fin[0];

                $desde = $fecha_inicio;
                $hasta = $fecha_fin;
                $periodo_quincenal=null;
                break;
            case "mensual":
                $particion_fecha = carbon::createfromFormat('m/Y', $request->fechaeval);

                $month = $particion_fecha->copy()->format('m'); //mes seleccionado
                $year = $particion_fecha->copy()->format('Y'); // Año seleccionado
// Crear una instancia de Carbon para el primer día del mes seleccionado
                $desde = $particion_fecha->copy()->StartofMonth()->format('Y-m-d');
                $hasta = $particion_fecha->copy()->endOfMonth()->format('Y-m-d');
                
// Formatear la fecha en el formato d-m-y
                $fecha_evaluacion = $desde;
                $semana = null;
                $num_semana = null;
                $mes = $particion_fecha->copy()->format('F');
                $quincena = null;
                $periodo_semanal = null;       
                $periodo_quincenal = null;
                break;
            case "quincenal":
                $particion_fecha = Carbon::createfromFormat('m/Y', $request->fechaeval);

                $month = $particion_fecha->copy()->format('m'); //mes seleccionado
                $year = $particion_fecha->copy()->format('Y'); // Año seleccionado

                if ($request->periodo == "1") {
                    $fecha = Carbon::createFromDate($year, $month, 1);
                    $desde = $fecha->copy()->format('Y-m-d');
                    $hasta = $fecha->copy()->adddays(14)->format('Y-m-d');
                    $periodo_quincenal = 1;
                } else {
                    $fecha = Carbon::createFromDate($year, $month, 16);
                    $desde = $fecha->copy()->format('Y-m-d');
                    $hasta = $fecha->copy()->endOfMonth()->format('Y-m-d');
                    $periodo_quincenal = 2;
                }

                $fecha_evaluacion = $fecha->copy()->format('d-m-Y');
                $semana = null;
                $num_semana = null;
                $periodo_semanal = null;
                $mes        = $particion_fecha->copy()->format('F');
                $quincena   = $request->periodo;
                break;
            case "diario":
                $particion_fecha = Carbon::createFromFormat('d/m/Y', $request->fechaeval);
                
                $desde = $particion_fecha->copy()->format('Y-m-d');
                $hasta = $particion_fecha->copy()->format('Y-m-d');
                $fecha_evaluacion = $particion_fecha->copy()->format('d-m-Y');
                $semana = null;
                $num_semana = null;
                $mes = $desde->copy()->format('F');
                $quincena = null;
                break;
        }
             
        $segmento = SegmentoModel::select('id')->where('cargo_id', $request->cargo)->where('skill_id', $request->skill)
                ->where('area_trabajo_id', $request->area_trabajo)
                ->where('cartera_id', $request->carteras)
                ->first();      
   
        $indicador_count = IndicadorModel::where('eval_segmentos_id', $segmento->id)->count();
        //SACAMOS LA CANTIDAD DE INDICADORES QUE TIENE EL ARCHIVO EXCEL
        // Carga el archivo de Excel
        $path = $request->file('archivo')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $hoja = $spreadsheet->getActiveSheet();

        // Obtén el número total de columnas
        $numColumnas = $hoja->getHighestColumn();
        // Convierte la letra de la última columna en un índice numérico
        $numColumnas = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($numColumnas);
        $contadorIndicadores = 0;

        // Recorre todas las columnas y cuenta los indicadores
        for ($i = 1; $i <= $numColumnas; $i++) {
            // Obtiene el valor de la celda de la primera fila para identificar los indicadores
            $valorCelda = $hoja->getCellByColumnAndRow($i, 1)->getValue();

            // Comprueba si el valor de la celda sigue el patrón 'indicadorX'
            $pattern = "/indicador/i";
            if (preg_match($pattern, $valorCelda)) {
                $contadorIndicadores++;
            }
        }

        if ($contadorIndicadores != $indicador_count) {
            \Session::flash('error', 'La cantidad de indicadores no es correcta se esperan: ' . $indicador_count . '\n y el archivo tiene: ' . $contadorIndicadores);
            return redirect()->back();
        }
        
        $valores = [\Auth::user()->id //0
            , $desde //1
            , $hasta //2
            , $year //3 
            , $mes //4
            , $tipo_periodo_seg //5 
            , $periodo_quincenal //6
            , $periodo_semanal //7
            , $request->area_trabajo //8
            , $request->carteras //9
            , $request->skill //10
            , $request->cargo //11
            , $indicador_count
            ];
        try {
            Excel::import(new EvaluacionesImport($valores), request()->file('archivo'));
            return back();            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) { //retorno de las reglas de validacion
            return back()->withErrors($e->failures());
        } catch (\App\Exceptions\ValidateAditionals $e) { //catch de validaciones adicionales del archivo
            // Errores de tus validaciones personalizadas
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $t) { //errornes comunes
            $mensaje = str_replace("\n", 'and', $t->getMessage());
            \Session::flash('error', "Error: " . $mensaje !== null ? $mensaje : $t->getMessage());
            return back();
        }
    }
    
    public function generarFormato(Request $request) {

        //BUSCAMOS EL TIPO DE PERIODO DEL SEGMENTO SEGUN EL SKILL Y CARGO SELECCIONADO

        $tipo_sql = SegmentoModel::select('tipo_periodo')->where('skill_id', $request->skill_asig)->where('cargo_id', $request->cargo_eval)
                ->where('area_trabajo_id', $request->area_trabajo)
                ->where('cartera_id', $request->carteras)
                ->first();

        $tipo_periodo = $tipo_sql->tipo_periodo;

        //BUSCAMOS LA CAMPAÑA Y LA GERENCIA DEL SKILL SELECCIONADO

        $campana = SkillModels::select('cartera_id')->where('id', $request->skill_asig)->first();
        $gerencia = CarteraModel::select('area_trabajo_id')->where('id', $campana->cartera_id)->first();
      
        $valores = [$request->area_trabajo, $request->carteras, $request->skill_asig, $request->cargo_eval, $tipo_periodo];
        return Excel::download(new IndicadorExport($valores), 'FormatoIndicadores.xlsx');
    }
}
