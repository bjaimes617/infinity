<?php

namespace App\Http\Controllers\habilidades;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Charts\SampleChart;
use ConsoleTVs\Charts\Facades\Chart;

use App\Models\habilidades\CategoriaModel;
use App\Models\habilidades\EvaluacionModel;
use App\Models\habilidades\EvaluacionSkillModel;
use App\Models\habilidades\EvaluadosModel;
use App\Models\habilidades\PreguntaModel;
use App\Models\habilidades\RespuestaModel;
use App\Models\habilidades\ParametroModel;
use App\Models\habilidades\ResultadosModel;
use App\Models\habilidades\RangoModel;
use App\Models\habilidades\ResultadosRespuestasModel;

use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CargoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\User;

use App\Http\Controllers\PoliceEstructure;
use App\Exports\Habilidades\ReporteExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class HabilidadController extends Controller
{

    public function __construct()
    {
        $this->cargo_evaluadores = CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
            ->orwhere('nombre_cargo', 'like', 'Team%')
            ->orwhere('nombre_cargo', 'like', 'Validador%')
            ->orwhere('nombre_cargo', 'like', 'Back%')
            ->orwhere('nombre_cargo', 'like', 'Agente%')
            ->orwhere('nombre_cargo', 'like', 'Coordinador%')
            ->orwhere('nombre_cargo', 'like', 'Senior%')
            ->orwhere('nombre_cargo', 'like', 'Supervisor%')
            ->orwhere('nombre_cargo', 'like', 'Analista%')
            ->orwhere('nombre_cargo', 'like', 'Gerente%')
            ->orderBy('nombre_cargo', 'asc')
            ->get();

        $this->cargo_evaluados = [1 => "1er Jefe Inmediato", 2 => "2do Jefe Inmediato", 3 => "Gerente de Area"];
    }

    public function createEvaluacion()
    {

        //MANDAMOS TODAS LAS GERENCIAS A LA VISTA, LAS CAMPAÑAS, SKILLS Y CARGOS SERAN LLAMADOS POR AJAX
        $gerencias = AreatrabajoModel::where('estatus_id', 1)->get();

        //MANDAMOS A LA VISTA EL 1ER, 2DO JEFE INMEDIATO Y EL GERENTE PARA EL OTRO SELECTOR        
        return view('habilidades.evaluacion.create')->with([
            "gerencias" => $gerencias,
            "cargo_evaluadores" => $this->cargo_evaluadores,
            "cargo_evaluados" => $this->cargo_evaluados
        ])->render();
    }

    public function storeEvaluacion(Request $request)
    {
        try {
            //SEPARAMOS EL RANGO DE FECHAS Y FORMATEAMOS 
            $rango_eval = $request->input('rango_eval');
            $fechas = explode(" - ", $rango_eval);
            $startDate = $fechas[0];
            $endDate = $fechas[1];

            // Convertir las fechas al formato Y-m-d
            $startDateFormatted = Carbon::createFromFormat('d/m/Y', trim($startDate))->format('Y-m-d');
            $endDateFormatted = Carbon::createFromFormat('d/m/Y', trim($endDate))->format('Y-m-d');

            //ESTAS VALIDACIONES SOLO APLICAN PARA DISTRIBUCION MANUAL

            if ($request->distribucion == 2) {

                //VARIABLE PARA GUARDAR LA SUMA TOTAL DEL VALOR DE LAS CATEGORIAS
                $total_sum = $request->total_sum;
                //SI EL RESULTADO DE LA SUMATORIA DEL VALOR DE LAS CATEGORIAS ES MAYOR AL VALOR MAXIMO DE LA EVALUACION GENERAMOS UN ERROR
                if (floatval($total_sum) > floatval($request->valor_max)) {
                    \Session::flash('error', 'El valor ' . $total_sum . ' sobrepasa el valor maximo ' . $request->valor_max . ' de la evaluación');
                    return redirect()->route('habilidades.evaluacion.create');
                }
                //SI EL RESULTADO DE LA SUMATORIA ES MENOR AL VALOR MAXIMO GENERAMOS UN MENSAJE DE AVISO DE LOS PUNTOS QUE FALTAN
                if (floatval($total_sum) < floatval($request->valor_max)) {
                    $puntos_faltantes = floatval($request->valor_max) - floatval($total_sum);
                    \Session::flash('info', 'Faltan ' . $puntos_faltantes . ' puntos para completar el valor maximo ' . $request->valor_max . ' de la evaluación');
                    return redirect()->route('habilidades.evaluacion.create');
                }
            }

            //GUARDAMOS LA EVALUACION
            $evaluacion = new EvaluacionModel();
            $evaluacion->nombre_evaluacion = $request->nombre_evaluacion;
            $evaluacion->desde = $startDateFormatted;
            $evaluacion->hasta = $endDateFormatted;
            $evaluacion->valor_max = $request->valor_max;
            $evaluacion->distribucion = $request->distribucion;
            $evaluacion->estatus = 1; //evaluacion activa por defecto
            $evaluacion->evaluados = json_encode($request->cargo_evaluar);
            $evaluacion->save();

            //GUARDAMOS LAS CATEGORIAS DE LA EVALACION REGISTRADA JUNTO CON SU ID
            $evaluacion_id = $evaluacion->id;
            for ($i = 1; $i <= $request->totalcategorias; $i++) {
                if ($request->input('nombre_categoria' . $i) !== null) {
                    $categoria = new CategoriaModel();
                    $categoria->nombre_categoria = $request->input('nombre_categoria' . $i);

                    /* if(floor(floatval($request->input('valor_categoria'.$i))) == floatval($request->input('valor_categoria'.$i))){
                      $categoria->valor_max = $request->input('valor_categoria'.$i);
                      }else{
                      $categoria->valor_max = rtrim(rtrim(floatval($request->input('valor_categoria'.$i)), '0'), '.');
                      } */

                    $categoria->valor_max = $request->input('valor_categoria' . $i);

                    //DISTRIBUCION ES NULA YA QUE SE ACTUALIZA EN EL AÑADIDO DE PREGUNTAS
                    $categoria->distribucion = null;

                    $categoria->evaluacion_id = $evaluacion_id;
                    $categoria->cantidad_preguntas = null; //cantidad de preguntas nulas por el momento
                    $categoria->descripcion = $request->input('descripcion' . $i);
                    $categoria->save();

                    //EL COD DE LA CATEGORIA SE CONFORMARA POR LAS DOS PRIMERAS LETRAS DE LA EVALUACION Y EL ID DE LA CATEGORIA
                    $categoria_id = $categoria->id;
                    $categoria->cod_categoria = strtoupper(substr($request->nombre_evaluacion, 0, 2) . $categoria_id);
                    $categoria->save();
                }
            }
            //GUARDAMOS LA EVALUACION CORRESPONDIENTE A SU GERENCIA , CAMPAÑA Y SKILL
            // Obtener las selecciones
            $gerencia_ids = $request->input('gerencia');
            $campana_ids = $request->input('cartera');
            $skill_ids = $request->input('skill');

            $evaluadores = $request->input('cargo_evaluador');
            $evaluados = $request->input('cargo_evaluar');

            // Guardar los registros en la tabla pivote hab_evaluacion_skill
            foreach ($gerencia_ids as $gerencia_id) {
                foreach ($campana_ids as $campana_id) {
                    foreach ($skill_ids as $skill_id) {
                        foreach ($evaluadores as $evaluador) {
                            foreach ($evaluados as $eval) {

                                // Verificar que el skill pertenece a la campaña
                                if (SkillModels::where('id', $skill_id)->where('cartera_id', $campana_id)->exists()) {
                                    //Verificar que la campaña pertenece a la gerencia
                                    if (CarteraModel::where('id', $campana_id)->where('area_trabajo_id', $gerencia_id)->exists()) {

                                        $evaluacion_skill = new EvaluacionSkillModel();
                                        $evaluacion_skill->evaluacion_id = $evaluacion_id;
                                        $evaluacion_skill->gerencia_id = $gerencia_id;
                                        $evaluacion_skill->campana_id = $campana_id;
                                        $evaluacion_skill->skill_id = $skill_id;
                                        $evaluacion_skill->cargo_evaluador_id = $evaluador;
                                        $evaluacion_skill->evaluado = $eval;
                                        $evaluacion_skill->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //GUARDAMOS QUIENES EVALUAN Y QUIENES SERAN EVALUADOS
            $evaluadores = $request->input('cargo_evaluador');
            $evaluados = $request->input('cargo_evaluar');
            foreach ($evaluadores as $evaluador) {
                foreach ($evaluados as $eval) {
                    $cargos = new EvaluadosModel();
                    $cargos->evaluacion_id = $evaluacion_id;
                    $cargos->cargo_evaluador_id = $evaluador;
                    $cargos->evaluado = $eval;
                    $cargos->save();
                }
            }
            //GUARDAMOS LOS PARAMETROS DE LA EVALUACION ESPECIFICADOS
            for ($j = 1; $j <= $request->totalparametros; $j++) {
                if ($request->input('nombre_parametro' . $j) !== null) {
                    $param = new ParametroModel();
                    $param->nombre_parametro = $request->input('nombre_parametro' . $j);
                    $param->parametro1 = $request->input('desde' . $j);
                    $param->parametro2 = $request->input('hasta' . $j);
                    $param->evaluacion_id = $evaluacion_id;
                    $param->hex = $request->input('hex' . $j);
                    $param->save();
                }
            }
            //GUARDAMOS LOS RANGOS DE LA EVALUACION ESPECIFICADA
            for ($k = 1; $k <= $request->totalrangos; $k++) {
                if ($request->input('descripcion_rango' . $k) !== null) {
                    $range = new RangoModel();
                    $range->descripcion_rango = $request->input('descripcion_rango' . $k);
                    $range->rango_desde = $request->input('rango_desde' . $k);
                    $range->rango_hasta = $request->input('rango_hasta' . $k);
                    $range->evaluacion_id = $evaluacion_id;
                    $range->save();
                }
            }

            \Session::flash('success', 'Evaluacion registrada con exito.');
            return back();
        } catch (\exception $e) {
            \Session::flash('error', 'Ocurrio un error ' . $e->getMessage());
            return back();
        }
    }

    //GENERA LA VISTA DEL LISTADO DE EVALUACIONES CREADAS
    public function editEvaluacion()
    {
        $evaluaciones = EvaluacionModel::all();
        return view('habilidades.evaluacion.edit', compact('evaluaciones'));
    }

    public function habEdit($id)
    {

        $resultado = ResultadosModel::where('evaluacion_id', $id)->count();
        $evaluacion = EvaluacionModel::find($id);

        $gerencias = AreatrabajoModel::where('estatus_id', 1)->get();
        $gerencias_eval = EvaluacionSkillModel::select('gerencia_id')->where('evaluacion_id', $id)->distinct()->pluck('gerencia_id')->toArray();

        $carteras_eval = EvaluacionSkillModel::select('campana_id')->where('evaluacion_id', $id)->distinct()->pluck('campana_id');
        $carteras = CarteraModel::whereIn('id', $carteras_eval)->orderby('nombre_cartera', 'ASC')->get();

        $skill_select = EvaluacionSkillModel::select('skill_id')->where('evaluacion_id', $id)->distinct()->pluck('skill_id');

        $skill = SkillModels::whereIn('id', $skill_select)->OrderBy('nombre_skill', 'ASC')->get();

        $desde_format = date('d/m/Y', strtotime($evaluacion->desde));
        $hasta_format = date('d/m/Y', strtotime($evaluacion->hasta));
        //CONCATENAMOS PARA MANDAR EL VALOR COMPLETO DEL RANGO DE FECHAS A LA VISTA
        $rango = $desde_format . " - " . $hasta_format;
        // quienes son los que evaluaran
        $evaluadores = EvaluadosModel::select('cargo_evaluador_id')->where('evaluacion_id', $id)->pluck('cargo_evaluador_id')->toArray();

        $categorias = CategoriaModel::where('evaluacion_id', $id)->get();
        $parametros = ParametroModel::where('evaluacion_id', $id)->get();
        $rangos_eval = RangoModel::where('evaluacion_id', $id)->get();

        return view('habilidades.evaluacion.editevaluacion')->with([
            "evaluacion" => $evaluacion,
            "cargo_evaluadores" => $this->cargo_evaluadores,
            "evaluadores_seleccion" => $evaluadores,
            "cargo_evaluados" => $this->cargo_evaluados,
            "gerencias" => $gerencias,
            "gerencias_seleccionadas" => $gerencias_eval,
            "carteras" => $carteras,
            "skills" => $skill,
            "rango_evaluacion" => $rango,
            "categorias" => $categorias,
            "parametros" => $parametros,
            "rangos" => $rangos_eval,
            "resultados" => $resultado
        ])->render();
    }

    //PERMITE EDITAR LA EVALUACION JUNTO CON SUS CATEGORIAS, PARAMETROS Y RANGOS
    public function updateEvaluacion(Request $request, $id)
    {
        try {
            //  dd($request->all());
            //SEPARAMOS EL RANGO DE FECHAS Y FORMATEAMOS 
            $rango_eval = $request->input('rango_eval');
            $fechas = explode(" - ", $rango_eval);
            $startDate = $fechas[0];
            $endDate = $fechas[1];

            // Convertir las fechas al formato Y-m-d
            $startDateFormatted = Carbon::createFromFormat('d/m/Y', trim($startDate))->format('Y-m-d');
            $endDateFormatted = Carbon::createFromFormat('d/m/Y', trim($endDate))->format('Y-m-d');

            //ESTAS VALIDACIONES SOLO APLICAN PARA DISTRIBUCION MANUAL

            switch ($request->distribucion) {
                case 2:
                    $total_sum = $request->total_sum;
                    if (floatval($total_sum) > floatval($request->valor_max)) {
                        \Session::flash('error', 'El valor ' . $total_sum . ' sobrepasa el valor maximo ' . $request->valor_max . ' de la evaluación');
                        return redirect()->route('hab.edit', ['id' => $id]);
                    }

                    if (floatval($total_sum) < floatval($request->valor_max)) {
                        $puntos_faltantes = floatval($request->valor_max) - floatval($total_sum);
                        \Session::flash('info', 'Faltan ' . $puntos_faltantes . ' puntos para completar el valor maximo ' . $request->valor_max . ' de la evaluación');
                        return redirect()->route('hab.edit', ['id' => $id]);
                    }
                    break;
            }

            //ACTUALIZAMOS LOS DATOS DE LA EVALUACION
            $evaluacion = EvaluacionModel::find($id);
            $evaluacion->nombre_evaluacion = $request->nombre_evaluacion;
            $evaluacion->desde = $startDateFormatted;
            $evaluacion->hasta = $endDateFormatted;
            $evaluacion->valor_max = $request->valor_max;
            $evaluacion->distribucion = $request->distribucion;
            $evaluacion->estatus = $request->estatus; //activa o inactiva
            $evaluacion->evaluados = json_encode($request->cargo_evaluar);
            $evaluacion->save();

            //BORRAREMOS LAS CATEGORIAS QUE HAN SIDO ELIMINADAS
            //BORRA TODO LO QUE NO ESTE EN EL IN
            if (!empty($request->id_categoria)) {
                $ids_cat = $request->id_categoria;
                RespuestaModel::whereNotIn('categoria_id', $ids_cat)->delete();
                PreguntaModel::whereNotIn('categoria_id', $ids_cat)->delete();
                CategoriaModel::whereNotIn('id', $ids_cat)->delete();
            }

            //GUARDAMOS LAS CATEGORIAS DE LA EVALACION REGISTRADA JUNTO CON SU ID
            $evaluacion_id = $evaluacion->id;
            for ($i = 1; $i <= $request->totalcategorias; $i++) {
                //SI SE CONSIGUE UNA CATEGORIA QUE YA TENGA ASIGNADO UN CODIGO ACTUALIZAMOS LA MISMA EN CASO DE ALGUN CAMBIO
                if ($request->input('id_categoria' . $i) !== null) {
                    $categoria = CategoriaModel::find($request->input('id_categoria' . $i));
                    $categoria->nombre_categoria = $request->input('nombre_categoria' . $i);
                    $categoria->descripcion = $request->input('descripcion' . $i);
                    $categoria->valor_max = $request->input('valor_categoria' . $i);
                    $categoria->save();
                } else {
                    //INSERTANDO NUEVA CATEGORIA
                    if ($request->input('nombre_categoria' . $i) !== null) {
                        $categoria = new CategoriaModel();
                        $categoria->nombre_categoria = $request->input('nombre_categoria' . $i);
                        $categoria->valor_max = $request->input('valor_categoria' . $i);
                        //DISTRIBUCION ES NULA YA QUE SE ACTUALIZA EN EL AÑADIDO DE PREGUNTAS
                        $categoria->distribucion = null;
                        $categoria->evaluacion_id = $evaluacion_id;
                        $categoria->descripcion = $request->input('descripcion' . $i);
                        $categoria->save();
                        //EL COD DE LA CATEGORIA SE CONFORMARA POR LAS DOS PRIMERAS LETRAS DE LA EVALUACION Y EL ID DE LA CATEGORIA
                        $categoria_id = $categoria->id;
                        $categoria->cod_categoria = strtoupper(substr($request->nombre_evaluacion, 0, 2) . $categoria_id);
                        $categoria->save();
                    }
                }
            }

            //GUARDAMOS LA EVALUACION CORRESPONDIENTE A SU GERENCIA , CAMPAÑA Y SKILL
            // Obtener las selecciones
            $gerencias = $request->input('gerencia');
            $campanas = $request->input('cartera');
            $skills = $request->input('skill');

            //dd($gerencia_ids,$campana_ids,$skill_ids);
            //ELIMINAMOS LOS REGISTROS DE LA TABLA PIVOTE ANTES DE VOLVER A INSERTAR

            EvaluacionSkillModel::where('evaluacion_id', $id)->delete();

            $evaluadores = $request->input('cargo_evaluador');
            $evaluados = $request->input('cargo_evaluar');

            // Guardar los registros en la tabla pivote hab_evaluacion_skill
            foreach ($gerencias as $gerencia_id) {
                foreach ($campanas as $campana_id) {
                    foreach ($skills as $skill_id) {
                        foreach ($evaluadores as $evaluador) {
                            foreach ($evaluados as $eval) {

                                // Verificar que el skill pertenece a la campaña
                                if (SkillModels::where('id', $skill_id)->where('cartera_id', $campana_id)->exists()) {
                                    //Verificar que la campaña pertenece a la gerencia
                                    if (CarteraModel::where('id', $campana_id)->where('area_trabajo_id', $gerencia_id)->exists()) {
                                        $evaluacion_skill = new EvaluacionSkillModel();
                                        $evaluacion_skill->evaluacion_id = $evaluacion_id;
                                        $evaluacion_skill->gerencia_id = $gerencia_id;
                                        $evaluacion_skill->campana_id = $campana_id;
                                        $evaluacion_skill->skill_id = $skill_id;
                                        $evaluacion_skill->cargo_evaluador_id = $evaluador;
                                        $evaluacion_skill->evaluado = $eval;
                                        $evaluacion_skill->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //GUARDAMOS QUIENES EVALUAN Y QUIENES SERAN EVALUADOS
            $evaluadores = $request->input('cargo_evaluador');
            $evaluados = $request->input('cargo_evaluar');
            //ELIMINAMOS LOS REGISTROS ACTUALES DE LOS EVALUADORES ANTES DE VOLVER A INSERTAR

            EvaluadosModel::where('evaluacion_id', $id)->delete();
            foreach ($evaluadores as $evaluador) {
                foreach ($evaluados as $eval) {
                    $cargos = new EvaluadosModel();
                    $cargos->evaluacion_id = $evaluacion_id;
                    $cargos->cargo_evaluador_id = $evaluador;
                    $cargos->evaluado = $eval;
                    $cargos->save();
                }
            }

            //SI VIENEN VALORES EN EL ARREGLO DE PARAMETROS PROCEDEMOS A ELIMINAR
            if (!empty($request->id_parametro)) {
                ParametroModel::whereNotIn('id', $request->id_parametro)->delete();
            }
            //GUARDAMOS LOS PARAMETROS DE LA EVALUACION ESPECIFICADOS
            for ($j = 1; $j <= $request->totalparametros; $j++) {
                //SI SE CONSIGUE UN PARAMETRO QUE YA TIENE ID ASIGNADO LO ACTUALIZAMOS
                if ($request->input('id_parametro' . $j) !== null) {
                    $param = ParametroModel::find($request->input('id_parametro' . $j));
                    $param->nombre_parametro = $request->input('nombre_parametro' . $j);
                    $param->parametro1 = $request->input('desde' . $j);
                    $param->parametro2 = $request->input('hasta' . $j);
                    $param->hex = $request->input('hex' . $j);
                    $param->save();
                } else {
                    //SE GUARDA EL NUEVO PARAMETRO
                    if ($request->input('nombre_parametro' . $j) !== null) {
                        $param = new ParametroModel();
                        $param->nombre_parametro = $request->input('nombre_parametro' . $j);
                        $param->parametro1 = $request->input('desde' . $j);
                        $param->parametro2 = $request->input('hasta' . $j);
                        $param->evaluacion_id = $evaluacion_id;
                        $param->hex = $request->input('hex' . $j);
                        $param->save();
                    }
                }
            }

            if (!empty($request->id_rangos)) {
                RangoModel::whereNotIn('id', $request->id_rangos)->delete();
            }
            //GUARDAMOS LOS RANGOS DE LA EVALUACION ESPECIFICADA     

            for ($k = 1; $k <= $request->totalrangos; $k++) {
                //SI SE CONSIGUE UN RANGO QUE YA TENGA ASIGNADO UN ID ACTUALIZAMOS
                if ($request->input('id_rangos' . $k) !== null) {
                    $range = RangoModel::find($request->input('id_rangos' . $k));
                    $range->descripcion_rango = $request->input('descripcion_rango' . $k);
                    $range->rango_desde = $request->input('rango_desde' . $k);
                    $range->rango_hasta = $request->input('rango_hasta' . $k);
                    $range->save();
                } else {
                    //INSERTANDO NUEVO RANGO       
                    if ($request->input('descripcion_rango' . $k) !== null) {
                        $range = new RangoModel();
                        $range->descripcion_rango = $request->input('descripcion_rango' . $k);
                        $range->rango_desde = $request->input('rango_desde' . $k);
                        $range->rango_hasta = $request->input('rango_hasta' . $k);
                        $range->evaluacion_id = $evaluacion_id;
                        $range->save();
                    }
                }
            }

            ////UNA VEZ ACTUALIZADA LA CATEGORIA, SE PROCEDE A RECALCULAR EL VALOR DE LAS PREGUNTAS
            for ($i = 1; $i <= $request->totalcategorias; $i++) {
                //PRIMERO VALIDAMOS QUE LA CATEGORIA TENGA PREGUNTAS
                if (PreguntaModel::where('categoria_id', $request->input('id_categoria' . $i))->exists()) {
                    //AVERIGUAMOS LA CANTIDAD DE PREGUNTAS QUE TIENE LA CATEGORIA
                    $preguntas_count = PreguntaModel::where('categoria_id', $request->input('id_categoria' . $i))->count();
                    //DIVIDIMOS EL VALOR DE LA CATEGORIA ENTRE LA CANTIDAD DE PREGUNTAS
                    $question_value = (float) $request->input('valor_categoria' . $i) / intval($preguntas_count);
                    $question_format = number_format($question_value, 3);
                    //ACTUALIZAMOS EL VALOR DE LAS PREGUNTAS DE LA CATEGORIA
                    PreguntaModel::where('categoria_id', $request->input('id_categoria' . $i))
                        ->update(['valor_pregunta' => $question_format]);
                }
            }

            \Session::flash('success', 'Evaluacion modificada con exito');
            return redirect()->route('hab.edit', ['id' => $id]);
        } catch (Throwable $e) {
            \Session::flash('error', 'Ocurrio un error ' . $e->getMessage());
            return redirect()->route('hab.edit', ['id' => $id]);
        }
    }

    //FUNCION QUE PERMITE ELIMINAR UNA EVALUACION COMPLETA SI NO TIENE REGISTROS
    public function destroyEvaluacion($id)
    {

        try {
            //COMPROBAMOS QUE HAYA RESPUESTAS REGISTRADAS EN LA EVALUACION
            if (RespuestaModel::where('evaluacion_id', $id)->exists()) {
                RespuestaModel::where('evaluacion_id', $id)->delete();
            }
            //COMPROBAMOS QUE HAYA PREGUNTAS REGISTRADAS EN LA EVALUACION
            if (PreguntaModel::where('evaluacion_id', $id)->exists()) {
                PreguntaModel::where('evaluacion_id', $id)->delete();
            }

            //ELIMINAMOS LAS CATEGORIAS DE LA EVALUACION
            CategoriaModel::where('evaluacion_id', $id)->delete();
            //ELIMINAMOS REGISTROS DE LA TABLAS PIVOTE QUE CONTENGAS LA EVALUACION_ID
            RangoModel::where('evaluacion_id', $id)->delete();
            ParametroModel::where('evaluacion_id', $id)->delete();
            EvaluadosModel::where('evaluacion_id', $id)->delete();
            EvaluacionSkillModel::where('evaluacion_id', $id)->delete();

            //POR ULTIMO ELIMINAMOS LA EVALUACION
            EvaluacionModel::where('id', $id)->delete();

            \Session::flash('success', "Evaluacion eliminada exitosamente. ");
            return back();
        } catch (\Exception $e) {
            \Session::flash('error', 'Ocurrio un error ' . $e->getMessage());
            return back();
        }
    }

    //ACTIVA O DESACTIVA UNA EVALUACION DE HAB. BLANDAS
    public function ActiveUpdate(Request $request, $id)
    {
        $evaluacion = EvaluacionModel::find($id);
        $evaluacion->estatus = $request->estatus;
        $evaluacion->save();
        if ($request->estatus == 1) {
            return json_encode(["La evaluacion ha sido activada exitosamente"]);
        } else {
            return json_encode(["La evaluacion ha sido desactivada exitosamente."]);
        }
    }

    //INDEX DE CREACIÓN O EDICION DE PREGUNTAS
    public function createPreguntas()
    {
        //MANDAMOS A LA VISTA LAS EVALUACIONES CREADAS , LAS CATEGORIAS VIENEN POR AJAX
        $evaluaciones = EvaluacionModel::all();
        return view('habilidades.preguntas.index')->with(["evaluaciones" => $evaluaciones])->render();
    }

    //FUNCION QUE TRAE LAS CATEGORIAS DE UNA EVALUACION y las lanza a la tabla

    public function getCategoria(Request $request)
    {

        $categories = CategoriaModel::where('evaluacion_id', $request->evaluacion)->get();

        $row = array();
        $data = array();

        foreach ($categories as $cate) {
            $row["cod"] = $cate->cod_categoria;
            $row["categoria"] = $cate->nombre_categoria;
            $row["acciones"] = '<a href="' . route('check.pregunta.list', [$cate->evaluacion_id, $cate->id]) . '" class="btn btn-sm btn-primary" '
                . 'target="_blank" id="editcategoria' . $cate->id . '"  data-toggle="tooltip" data-placement="top"'
                . ' title="Editar"><i class=" fa fa-edit"></i></a>';
            $data[] = $row;
        }
        return json_encode($data);
    }

    //PERMITE GENERAR EL LISTADO DE LAS PREGUNTAS DESPUES DE ENVIAR EL FORM
    public function getPreguntasList($evaluacion_id, $categoria_id)
    {

        $questions = PreguntaModel::where('categoria_id', $categoria_id)->get();
        $evaluacion = EvaluacionModel::find($evaluacion_id);
        $categorias = CategoriaModel::find($categoria_id);

        return view('habilidades.preguntas.preguntas_list')->with([
            'questions' => $questions,
            "evaluacion" => $evaluacion,
            "categoria" => $categorias
        ])->render();
    }

    //PERMITE GUARDAR, ACTUALIZAR O ELIMINAR LAS PREGUNTAS EN LA BASE DE DATOS
    public function storePreguntas(Request $request)
    {

        $evaluacion_id = $request->evaluacion_id;
        $categoria_id = $request->categoria_id;

        //SI HAY RESULTADOS PARA ESTA EVALUACION NO PERMITIMOS MODIFICAR LAS PREGUNTAS
        if (ResultadosModel::where('evaluacion_id', $evaluacion_id)->exists()) {
            \Session::flash('error', 'Ya hay evaluaciones registradas, las preguntas no pueden modificarse');
            return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
        }
        //ESTAS VALIDACIONES SOLO APLICAN PARA DISTRIBUCION MANUAL
        if ($request->distribucion == 2) {
            //VARIABLE PARA GUARDAR LA SUMA TOTAL DEL VALOR DE LAS PREGUNTAS
            $total_sum = $request->total_sum;
            //SI EL RESULTADO DE LA SUMATORIA DEL VALOR DE LAS PREGUNTAS ES MAYOR AL VALOR MAXIMO DE LA CATEGORIA GENERAMOS UN ERROR
            if (floatval($total_sum) > floatval($request->valor_max)) {
                \Session::flash('error', 'El valor ' . $total_sum . ' sobrepasa el valor maximo ' . $request->valor_max . ' de la categoria');
                return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
            }
            //SI EL RESULTADO DE LA SUMATORIA ES MENOR AL VALOR MAXIMO GENERAMOS UN MENSAJE DE AVISO DE LOS PUNTOS QUE FALTAN
            if (floatval($total_sum) < floatval($request->valor_max)) {
                $puntos_faltantes = floatval($request->valor_max) - floatval($total_sum);
                \Session::flash('info', 'Faltan ' . round($puntos_faltantes, 3) . ' puntos para completar el valor maximo ' . $request->valor_max . ' de la categoria');
                return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
            }
        }
        //SI VIENEN VALORES EN EL ARREGLO DE IDS PREGUNTA PROCEDEMOS A ELIMINAR LAS RESPUESTAS A ESAS PREGUNTAS
        if (!empty($request->id_ques)) {
            $id_ques = explode(",", $request->id_ques);
            RespuestaModel::whereIn('pregunta_id', $id_ques)->delete();
        }

        //SI VIENEN VALORES EN EL ARREGLO DE CODIGO PREGUNTA PROCEDEMOS A ELIMINAR ESAS PREGUNTAS
        if (!empty($request->codigos)) {
            $codigos = explode(",", $request->codigos);
            PreguntaModel::whereIn('cod_pregunta', $codigos)->delete();
        }

        //BUSCAMOS EL NOMBRE DE LA EVALUACION POR SU ID
        $evaluacion_name = EvaluacionModel::select('nombre_evaluacion')->where('id', $evaluacion_id)->first();
        $nombre_eval = $evaluacion_name->nombre_evaluacion;

        //BUSCAMOS EL NOMBRE DE LA CATEGORIA POR SU ID
        $category_name = CategoriaModel::select('nombre_categoria')->where('id', $categoria_id)->first();
        $nombre_cat = $category_name->nombre_categoria;

        //ACTUALIZAMOS EL VALOR DE LA DISTRIBUCION Y EL VALOR MAXIMO DE LA CATEGORIA
        $dis_cat = CategoriaModel::where('id', $categoria_id)->first();
        $dis_cat->distribucion = $request->distribucion;
        $dis_cat->valor_max = $request->valor_max;
        $dis_cat->save();

        //COLOCAMOS LA CANTIDAD DE PREGUNTAS DE LA CATEGORIA

        $cat = CategoriaModel::where('id', $categoria_id)->first();
        $cat->cantidad_preguntas = $request->cantidad_preguntas_true;
        $cat->save();

        for ($i = 1; $i <= $request->cantidad_preguntas; $i++) {
            //SI SE CONSIGUE UNA PREGUNTA QUE YA TENGA ASIGNADO UN CODIGO ACTUALIZAMOS LA MISMA EN CASO DE ALGUN CAMBIO
            if ($request->input('cod_pregunta' . $i) != "" || $request->input('cod_pregunta' . $i) != null) {
                $pregunta = PreguntaModel::where('cod_pregunta', $request->input('cod_pregunta' . $i))->first();
                $pregunta->valor_pregunta = $request->input('valor_pregunta' . $i);
                $pregunta->nombre_pregunta = $request->input('nombre_pregunta' . $i);

                //DISTRIBUCION DE LA PREGUNTA SERA MANUAL
                $pregunta->distribucion = 2;
                $pregunta->save();
            } else {
                //INSERTANDO NUEVA PREGUNTA
                if ($request->input('nombre_pregunta' . $i) !== null) {
                    $pregunta = new PreguntaModel();
                    $pregunta->nombre_pregunta = $request->input('nombre_pregunta' . $i);
                    $pregunta->valor_pregunta = $request->input('valor_pregunta' . $i);
                    //DISTRIBUCION DE LA PREGUNTA SERA MANUAL
                    $pregunta->distribucion = 2;

                    $pregunta->evaluacion_id = $evaluacion_id;
                    $pregunta->categoria_id = $categoria_id;
                    $pregunta->save();

                    $pregunta_id = $pregunta->id;
                    //EL COD DE LA PREGUNTA SE CONFORMARA POR LAS DOS PRIMERAS LETRAS DE LA EVALUACION, DOS PRIMERAS DE LA CATEGORIA Y EL ID DE LA PREGUNTA 
                    $pregunta->cod_pregunta = strtoupper(substr($nombre_eval, 0, 2) . substr($nombre_cat, 0, 2) . $pregunta_id);
                    $pregunta->save();
                }
            }
        }

        \Session::flash('success', 'Preguntas guardadas con exito');
        return back();
    }

    //FUNCIONES PARA ADMINISTRAR LAS RESPUESTAS
    public function createRespuestas()
    {
        //MANDAMOS A LA VISTA LAS EVALUACIONES CREADAS , LAS CATEGORIAS Y PREGUNTAS VIENEN POR AJAX
        $evaluaciones = EvaluacionModel::all();
        return view('habilidades.respuestas.index')->with(["evaluaciones" => $evaluaciones]);
    }

    public function SearchCategoriaforRespuestas(Request $request)
    {

        $categories = CategoriaModel::where('evaluacion_id', $request->evaluacion)->get();
        return json_encode($categories);
    }

    //FUNCION QUE TRAE LAS PREGUNTAS DE UNA CATEGORIA

    public function consultPreguntas(Request $request)
    {

        $preguntas = PreguntaModel::where('evaluacion_id', $request->evaluacion)->where('categoria_id', $request->categoria)->get();
        $row = array();
        $data = array();

        foreach ($preguntas as $cate) {
            $row["cod"] = $cate->cod_pregunta;
            $row["pregunta"] = $cate->nombre_pregunta;
            $row["acciones"] = '<a href="' . route('check.respuesta.list', [$cate->evaluacion_id, $cate->categoria_id, $cate->id]) . '" class="btn btn-sm btn-primary" '
                . 'target="_blank" id="editpregunta' . $cate->id . '"  data-toggle="tooltip" data-placement="top"'
                . ' title="Editar"><i class=" fa fa-edit"></i></a>';
            $data[] = $row;
        }
        return json_encode($data);
    }

    //VISTA PARA MOSTRAR LAS RESPUESTAS DE LA PREGUNTA
    public function getRespuestasList($evaluacion_id, $categoria_id, $pregunta_id)
    {

        $answers = RespuestaModel::where('evaluacion_id', $evaluacion_id)
            ->where('categoria_id', $categoria_id)
            ->where('pregunta_id', $pregunta_id)->get();

        $categoria = CategoriaModel::where('id', $categoria_id)->first();
        $pregunta = PreguntaModel::where('id', $pregunta_id)->first();

        return view('habilidades.respuestas.respuestas_list', compact('answers', 'evaluacion_id', 'categoria_id', 'pregunta_id', 'categoria', 'pregunta'));
    }

    //PERMITE ALMACENAR LA RESPUESTAS, EDITARLAS O ELIMINARLAS
    public function storeRespuestas(Request $request)
    {

        $evaluacion_id = $request->evaluacion_id;
        $categoria_id = $request->categoria_id;
        $pregunta_id = $request->pregunta_id;

        if (ResultadosModel::where('evaluacion_id', $evaluacion_id)->exists()) {
            \Session::flash('error', 'Ya hay evaluaciones registradas, las respuestas no pueden modificarse');
            return back();
        }

        //SI VIENEN VALORES EN EL ARREGLO DE CODIGO RESPUESTA PROCEDEMOS A ELIMINAR ESAS RESPUESTAS

        if (!empty($request->codigos)) {
            $codigos = explode(",", $request->codigos);
            RespuestaModel::whereIn('cod_respuesta', $codigos)->delete();
        }

        //BUSCAMOS EL NOMBRE DE LA EVALUACION POR SU ID
        $evaluacion_name = EvaluacionModel::select('nombre_evaluacion')->where('id', $evaluacion_id)->first();
        $nombre_eval = $evaluacion_name->nombre_evaluacion;

        //BUSCAMOS EL NOMBRE DE LA CATEGORIA POR SU ID
        $category_name = CategoriaModel::select('nombre_categoria')->where('id', $categoria_id)->first();
        $nombre_cat = $category_name->nombre_categoria;

        //BUSCAMOS EL NOMBRE DE LA PREGUNTA POR SU ID
        $question_name = PreguntaModel::select('nombre_pregunta')->where('id', $pregunta_id)->first();
        $nombre_ques = $question_name->nombre_pregunta;

        for ($i = 1; $i <= $request->cantidad_respuestas; $i++) {

            //SI SE CONSIGUE UNA RESPUESTA QUE YA TENGA ASIGNADO UN CODIGO ACTUALIZAMOS LA MISMA EN CASO DE ALGUN CAMBIO

            if ($request->input('cod_respuesta' . $i) != "" || $request->input('cod_respuesta' . $i) != null) {

                $respuesta = RespuestaModel::where('cod_respuesta', $request->input('cod_respuesta' . $i))->first();
                $respuesta->valor_respuesta = $request->input('valor_respuesta' . $i);
                $respuesta->nombre_respuesta = $request->input('nombre_respuesta' . $i);
                $respuesta->save();
            } else {

                //INSERTANDO NUEVA RESPUESTA

                if ($request->input('nombre_respuesta' . $i) !== null) {
                    $respuesta = new RespuestaModel();
                    $respuesta->nombre_respuesta = $request->input('nombre_respuesta' . $i);
                    $respuesta->valor_respuesta = $request->input('valor_respuesta' . $i);
                    $respuesta->evaluacion_id = $evaluacion_id;
                    $respuesta->categoria_id = $categoria_id;
                    $respuesta->pregunta_id = $pregunta_id;
                    $respuesta->save();

                    $respuesta_id = $respuesta->id;
                    //EL COD DE LA RESPUESTA SE CONFORMARA POR LAS DOS PRIMERAS LETRAS DE LA EVALUACION, DOS PRIMERAS DE LA CATEGORIA, DOS PRIMERAS LETRAS DE LA PREGUNTA Y EL ID DE LA RESPUESTA 
                    $cod_respuesta = strtoupper(substr($nombre_eval, 0, 2) . substr($nombre_cat, 0, 2) . substr($nombre_ques, 1, 3) . $respuesta_id);
                    $respuesta->cod_respuesta = preg_replace('/[^A-Za-z0-9]/', '', $cod_respuesta);
                    $respuesta->save();
                }
            }
        }

        \Session::flash('success', 'Respuestas guardadas con exito');
        return back();
    }

    //PERMITE GENERAR EL LISTADO DE LAS RESPUESTAS DESPUES DE ENVIAR EL FORM

    public function indexReportes()
    {

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

        $evaluaciones = EvaluacionModel::where('estatus', 1)->get();

        return view('habilidades.reportes')->with(["gerencias" => $areas, "evaluaciones" => $evaluaciones]);
    }

    public function getPersonal(Request $request)
    {

        $sql = ResultadosModel::select('personal_id')->where('evaluacion_id', $request->evaluacion);
        if (count($request->skill) > 1) {
            $personal_ids = $sql->whereIn('skill_campana', $request->skill)->distinct()->get()->toArray();
        } else {
            $personal_ids = $sql->where('skill_campana', $request->skill)->distinct()->get()->toArray();
        }


        $personal = PersonalModel::leftJoin('hab_resultados', 'dir_personal.id', '=', 'hab_resultados.personal_id')
            ->select('dir_personal.id as id_personal', 'dir_personal.primer_nombre', 'dir_personal.primer_apellido')
            ->whereIn('dir_personal.id', $personal_ids)->distinct()->get();

        return json_encode($personal);
    }

    //FUNCION PARA TRAER EL HISTORICO DE CARGOS DE LA PERSONA
    public function getCargo(Request $request)
    {

        $historico = ResultadosModel::select('nombre_cargo')->whereIn('skill_campana', $request->skill)
            ->where('evaluacion_id', $request->evaluacion)->where('personal_id', $request->personal_id)->distinct()->get();

        //TAMBIEN DEBEMOS TRAERNOS LA SEDE DE LA PERSONA QUE SE EVALUO
        $sede_act = PersonalModel::select('sede_laboral')->where('id', $request->personal_id)->first();
        $sede = $sede_act->sede_laboral;
        $rango_fecha_sql = EvaluacionModel::select('desde', 'hasta')->where('id', $request->evaluacion)->first();
        $rango_fecha = date("d/m/Y", strtotime($rango_fecha_sql->desde)) . " - " . date("d/m/Y", strtotime($rango_fecha_sql->hasta));

        return response()->json(['historico' => $historico, 'sede' => $sede, 'rango_fecha' => $rango_fecha]);
    }

    //FUNCION QUE GENERA LOS DISTINTOS TIPOS DE REPORTES
    public function getReporte(Request $request)
    {

        $date = $request->rango_eval;
        $arrayDate = explode("-", $date);
        $arrayBegin = explode("/", $arrayDate[0]);
        $begin = trim($arrayBegin[2]) . "-" . trim($arrayBegin[1]) . "-" . trim($arrayBegin[0]);
        $arrayEnd = explode("/", $arrayDate[1]);
        $end = trim($arrayEnd[2]) . "-" . trim($arrayEnd[1]) . "-" . trim($arrayEnd[0]);
        $data = array();

        //SACAMOS EL NOMBRE ,APELLIDO , CARGO Y SEDE DEL EVALUADO

        $evaluado = PersonalModel::select('primer_nombre', 'primer_apellido', 'cargo_id', 'sede_laboral')->where('id', $request->personal_cargo)->first();
        $nombre_evaluado = $evaluado->primer_nombre . " " . $evaluado->primer_apellido;
        $sede_laboral = $evaluado->sede_laboral;

        //SACAMOS LA CANTIDAD DE PREGUNTAS DE LA EVALUACION SELECCIONADA
        $preguntas_count = PreguntaModel::where('evaluacion_id', $request->evaluacion)->count();
        //SACAMOS EL CARGO DEL EVALUADO
        $cargo_eval = CargoModel::select('nombre_cargo')->where('id', $evaluado->cargo_id)->first();
        $nombre_cargo = $cargo_eval->nombre_cargo;
        //EL PRIMER CASO SE REFIERE AL REPORTE DE LAS RESPUESTAS DEL FORMULARIO EN AUTOSERVICIO
        //SACAMOS TODAS LAS PREGUNTAS DE LA EVALUACION SELECCIONADA
        $preguntas_eval = PreguntaModel::where('evaluacion_id', $request->evaluacion)->get();
        //OBTENEMOS LOS DATOS DE LA EVALUACION Y EL PERSONAL EVALUADO

        $datos = ResultadosModel::where('evaluacion_id', $request->evaluacion)->where('personal_id', $request->personal_cargo)
            ->whereIn('skill_campana', $request->skill)->where('nombre_cargo', $request->position_name)->whereBetween('fecha_evaluacion', array("$begin", "$end"))->get();

        $datos_array = ResultadosModel::where('evaluacion_id', $request->evaluacion)->where('personal_id', $request->personal_cargo)
            ->whereIn('skill_campana', $request->skill)->where('nombre_cargo', $request->position_name)->whereBetween('fecha_evaluacion', array("$begin", "$end"))->get()->toArray();

        //GENERAMOS UN ARRAY DE LAS EVALUACIONES REALIZADAS SOBRE EL PERSONAL Y SUS RESPUESTAS
        $reportData = [];
        $reportData_num = [];

        $evaluaciones = ResultadosRespuestasModel::leftJoin('hab_resultados', 'hab_resultados_respuestas.resultado_id', '=', 'hab_resultados.id')
            ->select(
                'hab_resultados_respuestas.resultado_id',
                'hab_resultados_respuestas.pregunta_id',
                'hab_resultados_respuestas.respuesta_id',
                'hab_resultados.personal_id',
                'hab_resultados.skill_campana'
            )
            ->where('hab_resultados.evaluacion_id', $request->evaluacion)
            ->where('hab_resultados.personal_id', $request->personal_cargo)
            ->whereIn('hab_resultados.skill_campana', $request->skill)
            ->where('hab_resultados.nombre_cargo', $request->position_name)
            ->whereBetween('hab_resultados.fecha_evaluacion', array("$begin", "$end"))
            ->orderBy('hab_resultados_respuestas.resultado_id')
            ->get();

        //dd($evaluaciones);

        foreach ($evaluaciones as $evaluacion) {
            $resultadoId = $evaluacion->resultado_id;
            $preguntaId = $evaluacion->pregunta_id;
            $respuestaId = $evaluacion->respuesta_id;
            // Si el resultado_id no está en el array, lo inicializamos
            if (!isset($reportData[$resultadoId])) {
                $reportData[$resultadoId] = [
                    'resultado_id' => $resultadoId,
                    'nombre_evaluado' => $nombre_evaluado,
                    'nombre_cargo' => $request->position_name,
                    'sede_laboral' => $sede_laboral
                ];
            }

            // Agregamos la respuesta a la fila correspondiente
            $reportData[$resultadoId]["pregunta_$preguntaId"] = $evaluacion->respuesta->nombre_respuesta;
        }

        //dd($evaluaciones,$reportData,"aqui");
        // Convertimos el array a un formato más adecuado para la exportación
        $finalReportData = array_values($reportData);
        foreach ($finalReportData as $subArray) {
            // Utilizamos ksort para ordenar el array por clave
            ksort($subArray);
        }

        $resultado = [];
        if (empty($datos_array)) {
            \Session::flash('error', 'No se encontraron evaluaciones registradas con estos parametros');
            return redirect()->back();
        }

        // Asumiendo que $data1 y $data2 tienen la misma cantidad de elementos
        foreach ($datos_array as $key => $item) {
            $resultado[] = array_merge($item, $finalReportData[$key]); // Combina ambos arrays
        }

        foreach ($resultado as $subarray) {
            $preguntaCount = 1; // Contador para las preguntas
            foreach ($subarray as $key => $value) {
                // Verifica si la clave comienza con "pregunta_"
                if (strpos($key, 'pregunta_') === 0) {
                    // Renombra la clave a "pregunta_X"
                    unset($subarray[$key]); // Elimina la clave original
                    $subarray['pregunta_' . $preguntaCount] = $value; // Añade la nueva clave
                    $preguntaCount++; // Incrementa el contador
                }
            }
        }

        $resultadol = [];
        foreach ($resultado as $itemr) {
            // Creamos un nuevo elemento que solo contendrá la información básica
            $nuevoElemento = [
                "id" => $itemr["id"],
                "evaluacion_id" => $itemr["evaluacion_id"],
                "personal_id" => $itemr["personal_id"],
                "skill_id" => $itemr["skill_id"],
                "fecha_evaluacion" => $itemr["fecha_evaluacion"],
                "created_at" => $itemr["created_at"],
                "updated_at" => $itemr["updated_at"],
                "nombre_cargo" => $itemr["nombre_cargo"],
                "nombre_evaluado" => $itemr["nombre_evaluado"],
                "resultado_id" => $itemr["resultado_id"],
                "sede_laboral" => $itemr["sede_laboral"],
                "preguntas" => [] // Inicializamos el subarray de preguntas
            ];

            // Recorremos las keys del item y añadimos las preguntas al subarray
            foreach ($itemr as $key => $value) {
                if (strpos($key, 'pregunta_') === 0) { // Verificamos si la clave comienza con 'pregunta_'
                    $nuevoElemento["preguntas"][] = $value; // Añadimos la respuesta a las preguntas
                }
            }

            // Agregamos el nuevo elemento al resultado
            $resultadol[] = $nuevoElemento;
        }

        $ev = $resultadol;
        $resultado = $ev;
        $form = $resultado;

        if (empty($datos_array)) {
            \Session::flash('error', 'No se encontraron evaluaciones registradas con estos parametros');
            return redirect()->back();
        }

        //FIN DEL PRIMER CASO
        //EL SEGUNDO CASO SE REFIERE AL REPORTE RF QUE MUESTRA LAS RESPUESTAS DEL FORMULARIO DE FORMA NUMERICA MAS EL TOTAL DE CADA CATEGORIA
        foreach ($evaluaciones as $evaluacion_num) {
            $resultadoId = $evaluacion_num->resultado_id;
            $preguntaId = $evaluacion_num->pregunta_id;
            $respuestaId = $evaluacion_num->respuesta_id;

            // Si el resultado_id no está en el array, lo inicializamos
            if (!isset($reportData_num[$resultadoId])) {
                $reportData_num[$resultadoId] = [
                    'resultado_id' => $resultadoId,
                    'nombre_evaluado' => $nombre_evaluado,
                    'nombre_cargo' => $request->position_name,
                    'sede_laboral' => $sede_laboral
                ];
            }
            // Agregamos la respuesta numerica a la fila correspondiente
            $reportData_num[$resultadoId]["id_$preguntaId"] = $evaluacion_num->pregunta_id;
            $reportData_num[$resultadoId]["pregunta_$preguntaId"] = number_format((float) $evaluacion_num->respuesta->valor_respuesta, 3, ',', '');
        }

        // Convertimos el array a un formato más adecuado para la exportación
        $finalReportData_num = array_values($reportData_num);

        foreach ($finalReportData_num as &$subArray_num) {
            // Utilizamos ksort para ordenar el array por clave
            ksort($subArray_num);
        }
        $resultado_num = [];

        // Asumiendo que $data1 y $data2 tienen la misma cantidad de elementos
        foreach ($datos_array as $key => $itemy) {
            $resultado_num[] = array_merge($itemy, $finalReportData_num[$key]); // Combina ambos arrays
        }
        //AQUI AGRUPAMOS LAS PREGUNTAS JUNTO CON SUS IDS DE CATEGORIA EN DOS SUBARRAYS

        foreach ($resultado_num as $key => $value) {
            if (strpos($key, 'id_') === 0) { // Verifica si la clave comienza con 'id_'
                $id_preguntaKey = $key;
                $id_cat_sql = PreguntaModel::select('categoria_id')->where('id', $resultado_num[$id_preguntaKey])->where('evaluacion_id', $request->evaluacion)->first();

                $id_cat = $id_cat_sql->categoria_id;
                if (isset($resultado_num[$id_preguntaKey])) {
                    $resultado_num['preguntas'][] = $resultado_num[$id_preguntaKey] . " categoria_id" . $id_cat;
                    preg_match('/categoria_id(\d+)/', $resultado_num[$id_preguntaKey] . " categoria_id" . $id_cat, $matches);
                    $numero = (int) $matches[1];
                }
                unset($resultado_num['pregunta_' . $id_preguntaKey]);
                unset($resultado_num['id_' . $id_preguntaKey]);
            }
        }

        $data = $resultado_num;
        foreach ($data as &$item) {
            $preguntas = []; // Nuevo array para las preguntas                    
            foreach ($item as $key => $value) {
                if (strpos($key, 'pregunta_') === 0) { // Identificar claves "pregunta_"
                    $numero_pregunta = str_replace('pregunta_', '', $key); // Extraer el número de pregunta
                    $preguntas[$numero_pregunta] = $value; // Agregar la pregunta al nuevo array
                    unset($item[$key]); // Eliminar la clave "pregunta_" original del array $item
                }
                if (strpos($key, 'id_') === 0) { //Eliminar las claves id_
                    unset($item[$key]);
                }
            }
            $item['preguntas'] = $preguntas; // Agregar el array de preguntas al array $item
        }
        $questions = $data;
        foreach ($questions as &$item) {
            $preguntas = [];
            // Reindexar las preguntas desde 0 y obtener categorías
            $i = 0;
            foreach ($item['preguntas'] as $id_pregunta => $pregunta) {
                //dd($id_pregunta);
                $id_cat_sql = PreguntaModel::select('categoria_id')->where('id', $id_pregunta)->where('evaluacion_id', $request->evaluacion)->first();
                $id_cat = $id_cat_sql ? $id_cat_sql->categoria_id : null; // Manejar el caso de que no se encuentre la categoría

                $preguntas[$i] = $pregunta . ($id_cat ? ' ' . "categoria_id" . $id_cat : ''); // Concatenar pregunta y categoría (si existe)
                $i++;
            }
            $item['preguntas'] = $preguntas;
        }

        $por_categoria = $questions;
        //AQUI SACAMOS LA SUMATORIA TOTAL POR CATEGORIA
        foreach ($por_categoria as &$item) {
            $preguntas = $item['preguntas'];
            $categoria_ids = [];
            // Recorremos las preguntas para separar por categoria_id
            foreach ($preguntas as $pregunta) {
                // Separamos el valor y el id de categoria
                list($value, $categoria_id) = explode(' ', $pregunta);
                // Limpiamos el valor de la coma y lo convertimos a float
                $value = (float) str_replace(',', '.', $value);
                // Inicializamos el array si no existe
                if (!isset($categoria_ids[$categoria_id])) {
                    $categoria_ids[$categoria_id] = [
                        'valores' => [],
                        'suma' => 0
                    ];
                }
                // Agregamos el valor al array correspondiente
                $categoria_ids[$categoria_id]['valores'][] = $value;
                $categoria_ids[$categoria_id]['suma'] += $value;
            }

            // Recorremos las categorias y agregamos la suma al final de 'valores'
            foreach ($categoria_ids as &$categoria) {
                array_push($categoria['valores'], $categoria['suma']);
            }
            // Guardamos los resultados en las preguntas
            $item['preguntas'] = $categoria_ids;
        }

        $datas = $por_categoria;
        //dd($datas);
        //ESTA CONSULTA CONTIENE LAS PREGUNTAS Y LA CATEGORIA PARA LA CABECERA DEL REPORTE RF
        $preguntas_eval_rf = PreguntaModel::with('relationCategoria')
            ->where('evaluacion_id', $request->evaluacion)
            ->get();
        //AQUI SACAMOS EL TOTAL GENERAL POR EVALUACION
        foreach ($datas as &$evaluacionl) {
            $total = 0;
            // Iterar sobre las categorías en la evaluación
            foreach ($evaluacionl['preguntas'] as $categorial) {
                // Sumar el último valor del subarray 'valores'
                $total += end($categorial['valores']);
            }
            // Guardar el resultado al final de cada evaluación
            $evaluacionl['resultado_total'] = $total;
        }

        $result = $datas; // Inicializar el resultado con el array original                   
        $cantidad = count($result); //CANTIDAD DE EVALUACIONES
        //AQUI SACAMOS LA SUMATORIA TOTAL DE CADA CATEGORIA ENTRE EN TOTAL DE EVALUACIONES
        foreach ($result as $index => &$item) {
            if ($index === 0) { // Solo modificar el primer elemento
                // SI LA CANTIDAD DE EVALUACION SON MAYORES A 1 VACIAMOS LOS INDICES
                if ($cantidad > 1) {
                    $item['id'] = "";
                    $item['evaluacion_id'] = "";
                    $item['personal_id'] = "";
                    $item['skill_id'] = "";
                    $item['fecha_evaluacion'] = "";
                    $item['created_at'] = "";
                    $item['updated_at'] = "";
                    $item['nombre_cargo'] = "";
                    $item['nombre_evaluado'] = "";
                    $item['resultado_id'] = "";
                    $item['sede_laboral'] = "";
                    $item['resultado_total'] = "";
                }
                // Reemplazar valores y calcular promedios
                foreach ($item['preguntas'] as $cat_id => &$pregunta) {
                    $valores = $pregunta['valores'];
                    $suma = 0;

                    // Calcular la suma de los valores de las dos evaluaciones
                    foreach ($datas as $subItem) {
                        if (isset($subItem['preguntas'][$cat_id])) {
                            $suma += array_sum($subItem['preguntas'][$cat_id]['valores']);
                        }
                    }

                    // Calcular el promedio
                    $promedio = $suma / $cantidad;
                    // Reemplazar valores con ""
                    if ($cantidad > 1) {
                        $pregunta['valores'] = array_fill(0, count($valores) - 1, "");
                        $pregunta['valores'][] = $promedio; // Añadir el promedio al final
                    }
                }
            } elseif ($index != 0) {
                unset($result[$index]);
            }
        }

        //dd($result,"aqui result");

        $totalSum = 0;
        $data_sum = $result;
        //dd($data_sum);    
        // Itera sobre las evaluaciones (en este caso solo hay una)
        foreach ($data_sum as $evaluacion_total) {
            // Itera sobre las preguntas
            foreach ($evaluacion_total['preguntas'] as $categoria_total) {
                // Suma los valores de cada categoria
                $totalSum += array_sum($categoria_total['valores']);
            }
        }
        // Cantidad de evaluaciones
        $cantidadEvaluaciones = $cantidad;
        // Calcula el resultado promedio
        $resultadoPromedio = $cantidadEvaluaciones > 0 ? $totalSum / $cantidadEvaluaciones : 0;
        $ex = $datas;
        if ($cantidadEvaluaciones > 1) {
            // 1. Calcular promedios y limpiar datos
            $promedios = []; // Array para almacenar los promedios por categoría

            foreach ($ex as $itemm) {
                foreach ($itemm['preguntas'] as $categoria_ids => $categorias) {
                    if (!isset($promedios[$categoria_ids])) {
                        $promedios[$categoria_ids] = [];
                    }
                    $promedios[$categoria_ids][] = $categorias['suma']; // Almacenar la suma de cada evaluación
                }
            }
            foreach ($promedios as $categoria_ids => $sumas) {
                $promedio = array_sum($sumas) / count($sumas);
                $promedios[$categoria_ids] = $promedio; // Reemplazar el array de sumas con el promedio
            }
            //dd($promedios);
            // 2. Estructurar el array final
            $array_final = [];
            // Tomar el primer elemento del array original como base
            $primer_elemento = $ex[0];

            // Limpiar todos los campos excepto "preguntas"
            foreach ($primer_elemento as $key => $value) {
                if ($key !== 'preguntas') {
                    $primer_elemento[$key] = "";
                }
            }
            // Limpiar los valores de las categorías y asignar promedios
            foreach ($primer_elemento['preguntas'] as $categoria_ids => &$categorias) {
                $cantidad_valores = count($categorias['valores']);
                for ($i = 0; $i < $cantidad_valores; $i++) {
                    $categorias['valores'][$i] = "";
                }
                $categorias['valores'][$cantidad_valores - 1] = $promedios[$categoria_ids]; // Asignar el promedio
            }
            $array_final[] = $primer_elemento;
            $result = $array_final;
            //dd($result);
        }

        $suma_ultimos_valores = 0;
        $promedio_final = $result;
        foreach ($promedio_final as $items) {
            foreach ($items['preguntas'] as $categoriap) {
                // Obtener el último valor del sub-array "valores"
                $ultimo_valor = end($categoriap['valores']); // La función end() devuelve el último elemento del array
                // Sumar el último valor a la variable acumuladora
                $suma_ultimos_valores += $ultimo_valor;
            }
        }
        $resultadoPromedio = $suma_ultimos_valores;

        //FIN DEL SEGUNDO CASO
        // EL TERCER CASO SE REFIERE AL INFORME QUE CONTIENE LA NOTA FINAL DEL EVALUADO
        //AVERIGUAMOS EL NOMBRE DE LA EVALUACION Y EL RANGO DE FECHAS SELECCIONADO
        $test_name_sql = EvaluacionModel::select('nombre_evaluacion')->where('id', $request->evaluacion)->first();

        $test_name = $test_name_sql->nombre_evaluacion;

        //FORMATEAMOS LAS FECHAS PARA EL REPORTE
        // Crear instancias de Carbon a partir de las fechas
        $desde = Carbon::createFromFormat('Y-m-d', $begin);
        $hasta = Carbon::createFromFormat('Y-m-d', $end);
        // Formatear las fechas
        $formatoDesde = $desde->format('j \d\e F \d\e\l Y');
        $formatoHasta = $hasta->format('j \d\e F \d\e\l Y');
        // Obtener el año en formato corto
        $anioDesde = $desde->format('y');
        $anioHasta = $hasta->format('Y');
        // Construir el string final
        $resultado = "{$formatoDesde} hasta {$formatoHasta}";

        // Reemplazar el año corto solo en la primera fecha
        $resultado = str_replace($anioDesde, $anioDesde, $resultado);
        $resultado = str_replace($anioHasta, $anioHasta, $resultado);
        // Array con los meses en inglés y su equivalente en español
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre',
        ];

        // Reemplazar los meses en el string
        $rango_evaluacion = str_replace(array_keys($meses), array_values($meses), $resultado);
        //AVERIGUAMOS EL NOMBRE , APELLIDO Y SEDE DEL EVALUADO
        $info_datos = PersonalModel::select('primer_nombre', 'primer_apellido', 'sede_laboral')->where('id', $request->personal_cargo)->first();

        $nombre_completo = $info_datos->primer_nombre . " " . $info_datos->primer_apellido;
        $sede = $info_datos->sede_laboral;
        //EL CARGO LO SACAMOS DEL ENVIADO EN EL FORM
        $cargo_idc = CargoModel::select('id')->where('nombre_cargo', $request->position_name)->first();
        $position_id = $cargo_idc->id;
        $position = CargoModel::select('nombre_cargo')->where('id', $position_id)->first();
        $position_name = $position->nombre_cargo;
        //CREAREMOS UN ARREGLO QUE ALMACENE LAS CATEGORIAS DE LA EVALUACION
        $categories = CategoriaModel::select('id', 'nombre_categoria', 'descripcion')->where('evaluacion_id', $request->evaluacion)
            ->get()->toArray();
        //USAMOS EL ARRAY RESULT QUE CONTIENE LA SUMATORIA TOTAL POR CATEGORIA PARA COLOCAR EL PUNTAJE EN CADA CATEGORIA

        $totales = $result;

        // Acceder al primer (y único) elemento del array principal
        $primerElemento = $totales[0];

        // Acceder al array "preguntas"
        $preguntas = $primerElemento['preguntas'];

        // Crear un nuevo array para almacenar los valores finales
        $valoresFinales = [];

        // Iterar sobre cada categoria_id en el array "preguntas"
        foreach ($preguntas as $categoriaId => $datosCategoria) {
            // Acceder al subarray "valores"
            $valores = $datosCategoria['valores'];

            // Obtener el último valor del subarray "valores"
            // array_slice($valores, -1) devuelve un array con el último elemento
            // array_pop() extrae y devuelve el último elemento del array
            $valorFinal = end($valores);

            // Guardar el valor final en el nuevo array usando el categoria_id como clave
            $valoresFinales[$categoriaId] = $valorFinal;
        }

        $numericKeysArray = array_values($valoresFinales);

        $valoresFinales = $numericKeysArray;

        // Agregar el puntaje a cada categoría
        foreach ($categories as $index => &$category) {
            if (isset($valoresFinales[$index])) {
                $category['puntaje'] = $valoresFinales[$index];
            }
        }


        //PARA LOS COMENTARIOS DEBEMOS CONSULTAR LOS PARAMETROS DE LA EVALUACION Y TENER UN ARRAY DE ELLOS

        $parameters = ParametroModel::where('evaluacion_id', $request->evaluacion)->get()->toArray();

        $cates = $categories;

        $parametroBajo = $parameters[0];

        // Iterar sobre cada categoría

        foreach ($cates as &$category) {
            $puntaje = $category['puntaje'];
            $comentarioEncontrado = false;

            // Iterar sobre cada parámetro de evaluación
            foreach ($parameters as $parametro) {

                $parametro1 = floatval($parametro['parametro1']);
                $parametro2 = floatval($parametro['parametro2']);

                // Verificar si el puntaje está dentro del rango
                if ($puntaje >= $parametro1 && $puntaje <= $parametro2) {
                    // Asignar el nombre del parámetro al campo comentario
                    $category['comentario'] = $parametro['nombre_parametro'];
                    $comentarioEncontrado = true;
                    break; // Salir del bucle si se encontró un comentario
                }
            }

            // Si no se encontró un comentario, puedes asignar un valor por defecto
            if (!$comentarioEncontrado) {
                $category['comentario'] = $parametroBajo['nombre_parametro'];
            }
        }

        // PARA LAS CONCLUSIONES FINALES DEBEMOS TRAERNOS LOS RANGOS DE LA EVALUACION SELECCIONADA

        $ranges = RangoModel::where('evaluacion_id', $request->evaluacion)->get()->toArray();

        foreach ($ranges as $rango) {

            $range1 = floatval($rango['rango_desde']);
            $range2 = floatval($rango['rango_hasta']);
            $rangoEncontrado = false;

            //verifico si la puntuación final esta dentro de los rangos

            if (floatval($resultadoPromedio) >= $range1 && floatval($resultadoPromedio) <= $range2) {
                // Asignar la conclusion final a la variable nota_final
                $nota_final = $rango['descripcion_rango'];
                $rangoEncontrado = true;
                break;
            }
            // Si estan fuera del rango, generamos un aviso en el reporte
            if (!$rangoEncontrado) {
                $nota_final = "Fuera del rango";
            }
        }
        //POR ULTIMO CONTAMOS LA CANTIDAD DE PARAMETROS QUE HAYA EN LA EVALUACION, BAJO, MEDIOS, ALTOS, ETC.
        // Contar ocurrencias
        $parametros = $parameters;
        $comentarios = $cates;

        $conteoParametros = [];
        foreach ($parametros as $param) {
            $conteoParametros[$param['nombre_parametro']] = 0;
        }

        foreach ($comentarios as $comentario) {
            if (isset($conteoParametros[$comentario['comentario']])) {
                $conteoParametros[$comentario['comentario']]++;
            }
        }

        $headers_param = array_keys($conteoParametros);
        $data_com = array_values($conteoParametros);

        // Dividir el string en partes usando "hasta" como delimitador
        //SI SE CONSULTA SOLO UN DIA
        $partes = explode("hasta", $rango_evaluacion);

        // Eliminar espacios en blanco al principio y al final de cada parte
        $parte1 = trim($partes[0]);
        $parte2 = trim($partes[1]);

        // Comparar las partes y eliminar la repetida
        if ($parte1 === $parte2) {
            $resultadof = $parte1;
        } else {
            $resultadof = $rango_evaluacion; // Mantener el string original si no son iguales
        }

        $rango_evaluacion = $resultadof;
        //MANDAMOS LOS DATOS AL EXPORT EXCEL
        return Excel::download(
            new ReporteExport(
                $preguntas_eval,
                $form,
                $datas,
                $preguntas_eval_rf,
                $result,
                $cantidad,
                $resultadoPromedio,
                $test_name,
                $rango_evaluacion,
                $nombre_completo,
                $sede,
                $position_name,
                $cates,
                $nota_final,
                $headers_param,
                $data_com,
                $ex
            ),
            '' . $nombre_evaluado . '.xlsx'
        );
    }

    //FUNCION QUE PERMITE MOSTRAR EL GRAFICO O LAS ESTADISTICAS DEL REPORTE
    public function getGrafico(Request $request)
    {

        //OBTENEMOS LAS CATEGORIAS DE LA EVALUACION PARA COLOCARLAS EN EL EJE X
        $categories = CategoriaModel::select('nombre_categoria')->where('evaluacion_id', $request->evaluacion)->get()->toArray();

        //EL TITULO SERA EL NOMBRE DE LA EVALUACION
        $nombre_eval = EvaluacionModel::select('nombre_evaluacion')->where('id', $request->evaluacion)->first();
        $nombreEvaluacion = $nombre_eval->nombre_evaluacion;

        //PARA LOS DATOS DE LAS BARRAS CONSULTAMOS LA PUNTUACION FINAL EN CADA CATEGORIA
        //AQUI FORMATEAMOS EL RANGO DE FECHAS
        $date = $request->rango_eval;
        $arrayDate = explode("-", $date);

        $arrayBegin = explode("/", $arrayDate[0]);
        $begin = trim($arrayBegin[2]) . "-" . trim($arrayBegin[1]) . "-" . trim($arrayBegin[0]);
        $arrayEnd = explode("/", $arrayDate[1]);
        $end = trim($arrayEnd[2]) . "-" . trim($arrayEnd[1]) . "-" . trim($arrayEnd[0]);
        $data = array();

        //SACAMOS EL NOMBRE ,APELLIDO , CARGO Y SEDE DEL EVALUADO

        $evaluado = PersonalModel::select('primer_nombre', 'primer_apellido', 'cargo_id', 'sede_laboral')->where('id', $request->personal_id)->first();
        $nombre_evaluado = $evaluado->primer_nombre . " " . $evaluado->primer_apellido;
        $sede_laboral = $evaluado->sede_laboral;

        //OBTENEMOS LOS DATOS DE LA EVALUACION Y EL PERSONAL EVALUADO

        $datos = ResultadosModel::where('evaluacion_id', $request->evaluacion)->where('personal_id', $request->personal_id)
            ->whereIn('skill_campana', $request->skill)->where('nombre_cargo', $request->position_name)
            ->whereBetween('fecha_evaluacion', array("$begin", "$end"))->get();

        $datos_array = ResultadosModel::where('evaluacion_id', $request->evaluacion)->where('personal_id', $request->personal_id)
            ->whereIn('skill_campana', $request->skill)->where('nombre_cargo', $request->position_name)
            ->whereBetween('fecha_evaluacion', array("$begin", "$end"))->get()->toArray();

        //GENERAMOS UN ARRAY DE LAS EVALUACIONES REALIZADAS SOBRE EL PERSONAL Y SUS RESPUESTAS

        $reportData = [];
        $reportData_num = [];

        $evaluaciones = ResultadosRespuestasModel::leftJoin('hab_resultados', 'hab_resultados_respuestas.resultado_id', '=', 'hab_resultados.id')
            ->select(
                'hab_resultados_respuestas.resultado_id',
                'hab_resultados_respuestas.pregunta_id',
                'hab_resultados_respuestas.respuesta_id',
                'hab_resultados.personal_id',
                'hab_resultados.skill_campana'
            )
            ->where('hab_resultados.evaluacion_id', $request->evaluacion)
            ->where('hab_resultados.personal_id', $request->personal_id)
            ->whereIn('hab_resultados.skill_campana', $request->skill)
            ->where('hab_resultados.nombre_cargo', $request->position_name)
            ->whereBetween('hab_resultados.fecha_evaluacion', array("$begin", "$end"))
            ->orderBy('hab_resultados_respuestas.resultado_id')
            ->get();

        foreach ($evaluaciones as $evaluacion) {
            $resultadoId = $evaluacion->resultado_id;
            $preguntaId = $evaluacion->pregunta_id;
            $respuestaId = $evaluacion->respuesta_id;
            // Si el resultado_id no está en el array, lo inicializamos
            if (!isset($reportData[$resultadoId])) {
                $reportData[$resultadoId] = [
                    'resultado_id' => $resultadoId,
                    'nombre_evaluado' => $nombre_evaluado,
                    'nombre_cargo' => $request->position_name,
                    'sede_laboral' => $sede_laboral
                ];
            }

            // Agregamos la respuesta a la fila correspondiente
            $reportData[$resultadoId]["pregunta_$preguntaId"] = $evaluacion->respuesta->nombre_respuesta;
        }

        // Convertimos el array a un formato más adecuado para la exportación
        $finalReportData = array_values($reportData);

        foreach ($finalReportData as &$subArray) {
            // Utilizamos ksort para ordenar el array por clave
            ksort($subArray);
        }

        $resultado = [];

        //dd($finalReportData,$datos_array);
        // Asumiendo que $data1 y $data2 tienen la misma cantidad de elementos
        foreach ($datos_array as $key => $item) {
            $resultado[] = array_merge($item, $finalReportData[$key]); // Combina ambos arrays
        }

        foreach ($resultado as &$subarray) {
            $preguntaCount = 1; // Contador para las preguntas
            foreach ($subarray as $key => $value) {
                // Verifica si la clave comienza con "pregunta_"
                if (strpos($key, 'pregunta_') === 0) {
                    // Renombra la clave a "pregunta_X"
                    unset($subarray[$key]); // Elimina la clave original
                    $subarray['pregunta_' . $preguntaCount] = $value; // Añade la nueva clave
                    $preguntaCount++; // Incrementa el contador
                }
            }
        }

        $resultadol = [];

        if (!empty($resultado)) {

            foreach ($resultado as $itemr) {
                // Creamos un nuevo elemento que solo contendrá la información básica
                $nuevoElemento = [
                    "id" => $itemr["id"],
                    "evaluacion_id" => $itemr["evaluacion_id"],
                    "personal_id" => $itemr["personal_id"],
                    "skill_id" => $itemr["skill_id"],
                    "fecha_evaluacion" => $itemr["fecha_evaluacion"],
                    "created_at" => $itemr["created_at"],
                    "updated_at" => $itemr["updated_at"],
                    "nombre_cargo" => $itemr["nombre_cargo"],
                    "nombre_evaluado" => $itemr["nombre_evaluado"],
                    "resultado_id" => $itemr["resultado_id"],
                    "sede_laboral" => $itemr["sede_laboral"],
                    "preguntas" => [] // Inicializamos el subarray de preguntas
                ];

                // Recorremos las keys del item y añadimos las preguntas al subarray
                foreach ($itemr as $key => $value) {
                    if (strpos($key, 'pregunta_') === 0) { // Verificamos si la clave comienza con 'pregunta_'
                        $nuevoElemento["preguntas"][] = $value; // Añadimos la respuesta a las preguntas
                    }
                }

                // Agregamos el nuevo elemento al resultado
                $resultadol[] = $nuevoElemento;
            }

            $ev = $resultadol;

            $resultado = $ev;

            $form = $resultado;

            foreach ($evaluaciones as $evaluacion_num) {

                $resultadoId = $evaluacion_num->resultado_id;
                $preguntaId = $evaluacion_num->pregunta_id;
                $respuestaId = $evaluacion_num->respuesta_id;

                // Si el resultado_id no está en el array, lo inicializamos
                if (!isset($reportData_num[$resultadoId])) {
                    $reportData_num[$resultadoId] = [
                        'resultado_id' => $resultadoId,
                        'nombre_evaluado' => $nombre_evaluado,
                        'nombre_cargo' => $request->position_name,
                        'sede_laboral' => $sede_laboral
                    ];
                }

                // Agregamos la respuesta numerica a la fila correspondiente
                $reportData_num[$resultadoId]["id_$preguntaId"] = $evaluacion_num->pregunta_id;
                $reportData_num[$resultadoId]["pregunta_$preguntaId"] = number_format((float) $evaluacion_num->respuesta->valor_respuesta, 3, ',', '');
            }

            // Convertimos el array a un formato más adecuado para la exportación
            $finalReportData_num = array_values($reportData_num);

            foreach ($finalReportData_num as &$subArray_num) {
                // Utilizamos ksort para ordenar el array por clave
                ksort($subArray_num);
            }

            $resultado_num = [];

            // Asumiendo que $data1 y $data2 tienen la misma cantidad de elementos
            foreach ($datos_array as $key => $itemy) {
                $resultado_num[] = array_merge($itemy, $finalReportData_num[$key]); // Combina ambos arrays
            }

            foreach ($resultado_num as $key => $value) {
                if (strpos($key, 'id_') === 0) { // Verifica si la clave comienza con 'id_'
                    $id_preguntaKey = $key;
                    $id_cat_sql = PreguntaModel::select('categoria_id')->where('id', $resultado_num[$id_preguntaKey])->where('evaluacion_id', $request->evaluacion)->first();

                    $id_cat = $id_cat_sql->categoria_id;

                    if (isset($resultado_num[$id_preguntaKey])) {
                        $resultado_num['preguntas'][] = $resultado_num[$id_preguntaKey] . " categoria_id" . $id_cat;
                        preg_match('/categoria_id(\d+)/', $resultado_num[$id_preguntaKey] . " categoria_id" . $id_cat, $matches);
                        $numero = (int) $matches[1];
                    }

                    unset($resultado_num['pregunta_' . $id_preguntaKey]);
                    unset($resultado_num['id_' . $id_preguntaKey]);
                }
            }

            $data = $resultado_num;

            foreach ($data as &$item) {
                $preguntas = []; // Nuevo array para las preguntas

                foreach ($item as $key => $value) {
                    if (strpos($key, 'pregunta_') === 0) { // Identificar claves "pregunta_"
                        $numero_pregunta = str_replace('pregunta_', '', $key); // Extraer el número de pregunta
                        $preguntas[$numero_pregunta] = $value; // Agregar la pregunta al nuevo array
                        unset($item[$key]); // Eliminar la clave "pregunta_" original del array $item
                    }
                    if (strpos($key, 'id_') === 0) { //Eliminar las claves id_
                        unset($item[$key]);
                    }
                }

                $item['preguntas'] = $preguntas; // Agregar el array de preguntas al array $item
            }

            $questions = $data;

            foreach ($questions as &$item) {
                $preguntas = [];

                // Reindexar las preguntas desde 0 y obtener categorías
                $i = 0;
                foreach ($item['preguntas'] as $id_pregunta => $pregunta) {
                    //dd($id_pregunta);
                    $id_cat_sql = PreguntaModel::select('categoria_id')->where('id', $id_pregunta)->where('evaluacion_id', $request->evaluacion)->first();
                    $id_cat = $id_cat_sql ? $id_cat_sql->categoria_id : null; // Manejar el caso de que no se encuentre la categoría

                    $preguntas[$i] = $pregunta . ($id_cat ? ' ' . "categoria_id" . $id_cat : ''); // Concatenar pregunta y categoría (si existe)
                    $i++;
                }

                $item['preguntas'] = $preguntas;
            }

            $por_categoria = $questions;

            foreach ($por_categoria as &$item) {
                $preguntas = $item['preguntas'];

                $categoria_ids = [];

                // Recorremos las preguntas para separar por categoria_id
                foreach ($preguntas as $pregunta) {
                    // Separamos el valor y el id de categoria
                    list($value, $categoria_id) = explode(' ', $pregunta);

                    // Limpiamos el valor de la coma y lo convertimos a float
                    $value = (float) str_replace(',', '.', $value);

                    // Inicializamos el array si no existe
                    if (!isset($categoria_ids[$categoria_id])) {
                        $categoria_ids[$categoria_id] = [
                            'valores' => [],
                            'suma' => 0
                        ];
                    }

                    // Agregamos el valor al array correspondiente
                    $categoria_ids[$categoria_id]['valores'][] = $value;
                    $categoria_ids[$categoria_id]['suma'] += $value;
                }

                // Recorremos las categorias y agregamos la suma al final de 'valores'
                foreach ($categoria_ids as &$categoria) {
                    array_push($categoria['valores'], $categoria['suma']);
                }

                // Guardamos los resultados en las preguntas
                $item['preguntas'] = $categoria_ids;
            }

            $datas = $por_categoria;

            //AQUI SACAMOS EL TOTAL GENERAL POR EVALUACION

            foreach ($datas as &$evaluacionl) {
                $total = 0;

                // Iterar sobre las categorías en la evaluación
                foreach ($evaluacionl['preguntas'] as $categorial) {
                    // Sumar el último valor del subarray 'valores'
                    $total += end($categorial['valores']);
                }

                // Guardar el resultado al final de cada evaluación
                $evaluacionl['resultado_total'] = $total;
            }

            $result = $datas; // Inicializar el resultado con el array original

            $cantidad = count($result); //CANTIDAD DE EVALUACIONES
            //AQUI SACAMOS LA SUMATORIA TOTAL DE CADA CATEGORIA ENTRE EN TOTAL DE EVALUACIONES

            foreach ($result as $index => &$item) {
                if ($index === 0) { // Solo modificar el primer elemento
                    // SI LA CANTIDAD DE EVALUACION SON MAYORES A 1 VACIAMOS LOS INDICES
                    if ($cantidad > 1) {

                        $item['id'] = "";
                        $item['evaluacion_id'] = "";
                        $item['personal_id'] = "";
                        $item['skill_id'] = "";
                        $item['fecha_evaluacion'] = "";
                        $item['created_at'] = "";
                        $item['updated_at'] = "";
                        $item['nombre_cargo'] = "";
                        $item['nombre_evaluado'] = "";
                        $item['resultado_id'] = "";
                        $item['sede_laboral'] = "";
                        $item['resultado_total'] = "";
                    }

                    // Reemplazar valores y calcular promedios
                    foreach ($item['preguntas'] as $cat_id => &$pregunta) {
                        $valores = $pregunta['valores'];
                        $suma = 0;

                        // Calcular la suma de los valores de las dos evaluaciones
                        foreach ($datas as $subItem) {
                            if (isset($subItem['preguntas'][$cat_id])) {
                                $suma += array_sum($subItem['preguntas'][$cat_id]['valores']);
                            }
                        }

                        // Calcular el promedio
                        $promedio = $suma / $cantidad;

                        // Reemplazar valores con ""

                        if ($cantidad > 1) {
                            $pregunta['valores'] = array_fill(0, count($valores) - 1, "");
                            $pregunta['valores'][] = $promedio; // Añadir el promedio al final
                        }
                    }
                } elseif ($index != 0) {

                    unset($result[$index]);
                }
            }

            $totalSum = 0;
            $data_sum = $result;

            // Itera sobre las evaluaciones (en este caso solo hay una)
            foreach ($data_sum as $evaluacion_total) {
                // Itera sobre las preguntas
                foreach ($evaluacion_total['preguntas'] as $categoria_total) {
                    // Suma los valores de cada categoria
                    $totalSum += array_sum($categoria_total['valores']);
                }
            }

            // Cantidad de evaluaciones
            $cantidadEvaluaciones = $cantidad;

            // Calcula el resultado promedio
            $resultadoPromedio = $cantidadEvaluaciones > 0 ? $totalSum / $cantidadEvaluaciones : 0;

            $ex = $datas;

            if ($cantidadEvaluaciones > 1) {

                // 1. Calcular promedios y limpiar datos

                $promedios = []; // Array para almacenar los promedios por categoría

                foreach ($ex as $itemm) {
                    foreach ($itemm['preguntas'] as $categoria_ids => $categorias) {
                        if (!isset($promedios[$categoria_ids])) {
                            $promedios[$categoria_ids] = [];
                        }
                        $promedios[$categoria_ids][] = $categorias['suma']; // Almacenar la suma de cada evaluación
                    }
                }


                foreach ($promedios as $categoria_ids => $sumas) {
                    $promedio = array_sum($sumas) / count($sumas);
                    $promedios[$categoria_ids] = $promedio; // Reemplazar el array de sumas con el promedio
                }

                //dd($promedios);
                // 2. Estructurar el array final

                $array_final = [];

                // Tomar el primer elemento del array original como base
                $primer_elemento = $ex[0];

                // Limpiar todos los campos excepto "preguntas"
                foreach ($primer_elemento as $key => $value) {
                    if ($key !== 'preguntas') {
                        $primer_elemento[$key] = "";
                    }
                }

                //dd($primer_elemento);
                // Limpiar los valores de las categorías y asignar promedios
                foreach ($primer_elemento['preguntas'] as $categoria_ids => &$categorias) {
                    $cantidad_valores = count($categorias['valores']);
                    for ($i = 0; $i < $cantidad_valores; $i++) {
                        $categorias['valores'][$i] = "";
                    }
                    $categorias['valores'][$cantidad_valores - 1] = $promedios[$categoria_ids]; // Asignar el promedio
                }

                //dd($primer_elemento['preguntas']);

                $array_final[] = $primer_elemento;

                $result = $array_final;
            }

            $suma_ultimos_valores = 0;

            $promedio_final = $result;

            foreach ($promedio_final as $items) {
                foreach ($items['preguntas'] as $categoriap) {
                    // Obtener el último valor del sub-array "valores"
                    $ultimo_valor = end($categoriap['valores']); // La función end() devuelve el último elemento del array
                    // Sumar el último valor a la variable acumuladora
                    $suma_ultimos_valores += $ultimo_valor;
                }
            }

            $resultadoPromedio = $suma_ultimos_valores;

            //SOLO SACAMOS LOS VALORES FINALES PARA INCLUIRLOS EN EL GRAFICO
            $valoresFinales = [];

            foreach ($result[0]['preguntas'] as $categoria) {
                $valoresFinales[] = end($categoria['valores']);
            }

            $json_categories = json_encode($categories);

            // Decodificar el JSON a un array asociativo
            $dataj = json_decode($json_categories, true);

            // Crear un nuevo array con solo los nombres de las categorías
            $nombresCategorias = array_column($dataj, 'nombre_categoria');

            //CONSULTAMOS LOS PARAMETROS PARA ASIGNAR EL COLOR A CADA BARRA DEL GRAFICO

            $parametros = ParametroModel::where('evaluacion_id', $request->evaluacion)->get();

            // Procesar los datos para el gráfico
            $datosGrafico = [];
            foreach ($valoresFinales as $resultadoc) {
                $color = $this->obtenerColorParametro($resultadoc, $parametros);
                $datosGrafico[] = [
                    'valor' => $resultadoc,
                    'color' => $color[0],
                    'nombre_parametro' => $color[1]
                ];
            }


            return response()->json([
                'titulo' => $nombreEvaluacion,
                'labels' => $nombresCategorias,
                'datos' => $valoresFinales,
                'datosGrafico' => $datosGrafico
            ]);
        } else {

            return response()->json([
                'titulo' => '',
                'labels' => '',
                'datos' => '',
                'datosGrafico' => ''
            ]);
        }
    }

    private function obtenerColorParametro($valor, $parametros)
    {
        foreach ($parametros as $parametro) {
            if ($valor >= $parametro->parametro1 && $valor <= $parametro->parametro2) {
                return array($parametro->hex, $parametro->nombre_parametro);
            }
        }
        // Si no se encuentra en ningún rango, devolver el color del primer parámetro
        return array($parametros->first()->hex, $parametros->first()->nombre_parametro);
    }
}
