<?php

namespace App\Http\Controllers\habilidades;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
    protected $cargo_evaluadores;
    protected $cargo_evaluados;

    public function __construct()
    {
        $this->cargo_evaluadores = CargoModel::where(function ($query) {
            $prefixes = ['Ejecutivo%', 'Team%', 'Validador%', 'Back%', 'Agente%', 'Coordinador%', 'Senior%', 'Supervisor%', 'Analista%', 'Gerente%'];
            foreach ($prefixes as $prefix) {
                $query->orWhere('nombre_cargo', 'like', $prefix);
            }
        })
            ->orderBy('nombre_cargo', 'asc')
            ->get();

        $this->cargo_evaluados = [1 => "1er Jefe Inmediato", 2 => "2do Jefe Inmediato", 3 => "Gerente de Area"];
    }

    public function createEvaluacion()
    {
        try {
            // Mandamos todas las gerencias a la vista, las campañas, skills y cargos serán llamados por AJAX
            $gerencias = AreatrabajoModel::where('estatus_id', 1)->get();

            // Mandamos a la vista el 1er, 2do jefe inmediato y el gerente para el otro selector
            return view('habilidades.evaluacion.create')->with([
                "gerencias" => $gerencias,
                "cargo_evaluadores" => $this->cargo_evaluadores,
                "cargo_evaluados" => $this->cargo_evaluados
            ])->render();
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar el formulario de evaluación: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function storeEvaluacion(Request $request)
    {
        try {
            // Validar formato del rango de fechas
            $rango_eval = $request->input('rango_eval');
            if (empty($rango_eval) || !str_contains($rango_eval, ' - ')) {
                Session::flash('error', 'El rango de fechas especificado no es válido.');
                return redirect()->route('habilidades.evaluacion.create')->withInput();
            }

            $fechas = explode(" - ", $rango_eval);
            $startDate = trim($fechas[0]);
            $endDate = trim($fechas[1]);

            // Convertir las fechas al formato Y-m-d
            $startDateFormatted = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

            // Validaciones para distribución manual
            if ($request->distribucion == 2) {
                $total_sum = (float) $request->total_sum;
                $valor_max = (float) $request->valor_max;

                if ($total_sum > $valor_max) {
                    Session::flash('error', 'El valor ' . $total_sum . ' sobrepasa el valor maximo ' . $valor_max . ' de la evaluación');
                    return redirect()->route('habilidades.evaluacion.create')->withInput();
                }

                if ($total_sum < $valor_max) {
                    $puntos_faltantes = $valor_max - $total_sum;
                    Session::flash('info', 'Faltan ' . $puntos_faltantes . ' puntos para completar el valor maximo ' . $valor_max . ' de la evaluación');
                    return redirect()->route('habilidades.evaluacion.create')->withInput();
                }
            }

            DB::beginTransaction();

            // Guardamos la evaluación
            $evaluacion = new EvaluacionModel();
            $evaluacion->nombre_evaluacion = $request->nombre_evaluacion;
            $evaluacion->desde = $startDateFormatted;
            $evaluacion->hasta = $endDateFormatted;
            $evaluacion->valor_max = $request->valor_max;
            $evaluacion->distribucion = $request->distribucion;
            $evaluacion->estatus = 1; // Evaluación activa por defecto
            $evaluacion->evaluados = json_encode($request->cargo_evaluar);
            $evaluacion->save();

            $evaluacion_id = $evaluacion->id;

            // Guardamos las categorías de la evaluación
            $totalCategorias = (int) $request->totalcategorias;
            for ($i = 1; $i <= $totalCategorias; $i++) {
                $nombreCategoria = $request->input('nombre_categoria' . $i);
                if (!empty($nombreCategoria)) {
                    $categoria = new CategoriaModel();
                    $categoria->nombre_categoria = $nombreCategoria;
                    $categoria->valor_max = $request->input('valor_categoria' . $i);
                    $categoria->distribucion = null;
                    $categoria->evaluacion_id = $evaluacion_id;
                    $categoria->cantidad_preguntas = null;
                    $categoria->descripcion = $request->input('descripcion' . $i);
                    $categoria->save();

                    // El código de la categoría: primeras 2 letras de evaluación + id categoría
                    $categoria->cod_categoria = strtoupper(substr($request->nombre_evaluacion, 0, 2) . $categoria->id);
                    $categoria->save();
                }
            }

            // Relaciones de Gerencia, Campaña, Skill, Evaluadores y Evaluados
            $gerencia_ids = (array) $request->input('gerencia', []);
            $campana_ids = (array) $request->input('cartera', []);
            $skill_ids = (array) $request->input('skill', []);
            $evaluadores = (array) $request->input('cargo_evaluador', []);
            $evaluados = (array) $request->input('cargo_evaluar', []);

            // Optimización de consultas: pre-cargar relaciones válidas
            $validSkills = SkillModels::whereIn('id', $skill_ids)
                ->whereIn('cartera_id', $campana_ids)
                ->get(['id', 'cartera_id']);

            $validSkillsMap = [];
            foreach ($validSkills as $vSkill) {
                $validSkillsMap[$vSkill->cartera_id][] = $vSkill->id;
            }

            $validCarteras = CarteraModel::whereIn('id', $campana_ids)
                ->whereIn('area_trabajo_id', $gerencia_ids)
                ->get(['id', 'area_trabajo_id']);

            $validCarterasMap = [];
            foreach ($validCarteras as $vCartera) {
                $validCarterasMap[$vCartera->area_trabajo_id][] = $vCartera->id;
            }

            $evaluacionSkillsBatch = [];
            $now = Carbon::now();

            foreach ($gerencia_ids as $gerencia_id) {
                if (!isset($validCarterasMap[$gerencia_id])) {
                    continue;
                }
                foreach ($validCarterasMap[$gerencia_id] as $campana_id) {
                    if (!isset($validSkillsMap[$campana_id])) {
                        continue;
                    }
                    foreach ($validSkillsMap[$campana_id] as $skill_id) {
                        foreach ($evaluadores as $evaluador) {
                            foreach ($evaluados as $eval) {
                                $evaluacionSkillsBatch[] = [
                                    'evaluacion_id' => $evaluacion_id,
                                    'gerencia_id' => $gerencia_id,
                                    'campana_id' => $campana_id,
                                    'skill_id' => $skill_id,
                                    'cargo_evaluador_id' => $evaluador,
                                    'evaluado' => $eval,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($evaluacionSkillsBatch)) {
                foreach (array_chunk($evaluacionSkillsBatch, 500) as $chunk) {
                    EvaluacionSkillModel::insert($chunk);
                }
            }

            // Guardamos quiénes evalúan y quiénes serán evaluados
            $evaluadosBatch = [];
            foreach ($evaluadores as $evaluador) {
                foreach ($evaluados as $eval) {
                    $evaluadosBatch[] = [
                        'evaluacion_id' => $evaluacion_id,
                        'cargo_evaluador_id' => $evaluador,
                        'evaluado' => $eval,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($evaluadosBatch)) {
                foreach (array_chunk($evaluadosBatch, 500) as $chunk) {
                    EvaluadosModel::insert($chunk);
                }
            }

            // Guardamos los parámetros de la evaluación
            $totalParametros = (int) $request->totalparametros;
            for ($j = 1; $j <= $totalParametros; $j++) {
                $nombreParam = $request->input('nombre_parametro' . $j);
                if (!empty($nombreParam)) {
                    $param = new ParametroModel();
                    $param->nombre_parametro = $nombreParam;
                    $param->parametro1 = $request->input('desde' . $j);
                    $param->parametro2 = $request->input('hasta' . $j);
                    $param->evaluacion_id = $evaluacion_id;
                    $param->hex = $request->input('hex' . $j);
                    $param->save();
                }
            }

            // Guardamos los rangos de la evaluación
            $totalRangos = (int) $request->totalrangos;
            for ($k = 1; $k <= $totalRangos; $k++) {
                $descripcionRango = $request->input('descripcion_rango' . $k);
                if (!empty($descripcionRango)) {
                    $range = new RangoModel();
                    $range->descripcion_rango = $descripcionRango;
                    $range->rango_desde = $request->input('rango_desde' . $k);
                    $range->rango_hasta = $request->input('rango_hasta' . $k);
                    $range->evaluacion_id = $evaluacion_id;
                    $range->save();
                }
            }

            DB::commit();

            Session::flash('success', 'Evaluacion registrada con exito.');
            return back();
        } catch (Throwable $e) {
            DB::rollBack();
            Session::flash('error', 'Ocurrio un error al registrar la evaluación: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    // Genera la vista del listado de evaluaciones creadas
    public function editEvaluacion()
    {
        try {
            $evaluaciones = EvaluacionModel::all();
            return view('habilidades.evaluacion.edit', compact('evaluaciones'));
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar las evaluaciones: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function habEdit($id)
    {
        try {
            $evaluacion = EvaluacionModel::find($id);
            if (!$evaluacion) {
                Session::flash('error', 'La evaluación solicitada no existe.');
                return redirect()->route('habilidades.evaluacion.edit');
            }

            $resultado = ResultadosModel::where('evaluacion_id', $id)->count();

            $gerencias = AreatrabajoModel::where('estatus_id', 1)->get();
            $gerencias_eval = EvaluacionSkillModel::where('evaluacion_id', $id)->distinct()->pluck('gerencia_id')->toArray();

            $carteras_eval = EvaluacionSkillModel::where('evaluacion_id', $id)->distinct()->pluck('campana_id');
            $carteras = CarteraModel::whereIn('id', $carteras_eval)->orderBy('nombre_cartera', 'ASC')->get();

            $skill_select = EvaluacionSkillModel::where('evaluacion_id', $id)->distinct()->pluck('skill_id');
            $skill = SkillModels::whereIn('id', $skill_select)->orderBy('nombre_skill', 'ASC')->get();

            $desde_format = $evaluacion->desde ? Carbon::parse($evaluacion->desde)->format('d/m/Y') : '';
            $hasta_format = $evaluacion->hasta ? Carbon::parse($evaluacion->hasta)->format('d/m/Y') : '';
            $rango = $desde_format . " - " . $hasta_format;

            $evaluadores = EvaluadosModel::where('evaluacion_id', $id)->pluck('cargo_evaluador_id')->toArray();

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
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar la edición de la evaluación: ' . $e->getMessage());
            return redirect()->route('habilidades.evaluacion.edit');
        }
    }

    // Permite editar la evaluación junto con sus categorías, parámetros y rangos
    public function updateEvaluacion(Request $request, $id)
    {
        try {
            $evaluacion = EvaluacionModel::find($id);
            if (!$evaluacion) {
                Session::flash('error', 'La evaluación no existe.');
                return redirect()->route('habilidades.evaluacion.edit');
            }

            // Separamos el rango de fechas y formateamos
            $rango_eval = $request->input('rango_eval');
            if (empty($rango_eval) || !str_contains($rango_eval, ' - ')) {
                Session::flash('error', 'El rango de fechas especificado no es válido.');
                return redirect()->route('hab.edit', ['id' => $id])->withInput();
            }

            $fechas = explode(" - ", $rango_eval);
            $startDate = trim($fechas[0]);
            $endDate = trim($fechas[1]);

            $startDateFormatted = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

            // Validaciones para distribución manual
            if ($request->distribucion == 2) {
                $total_sum = (float) $request->total_sum;
                $valor_max = (float) $request->valor_max;

                if ($total_sum > $valor_max) {
                    Session::flash('error', 'El valor ' . $total_sum . ' sobrepasa el valor maximo ' . $valor_max . ' de la evaluación');
                    return redirect()->route('hab.edit', ['id' => $id])->withInput();
                }

                if ($total_sum < $valor_max) {
                    $puntos_faltantes = $valor_max - $total_sum;
                    Session::flash('info', 'Faltan ' . $puntos_faltantes . ' puntos para completar el valor maximo ' . $valor_max . ' de la evaluación');
                    return redirect()->route('hab.edit', ['id' => $id])->withInput();
                }
            }

            DB::beginTransaction();

            // Actualizamos los datos de la evaluación
            $evaluacion->nombre_evaluacion = $request->nombre_evaluacion;
            $evaluacion->desde = $startDateFormatted;
            $evaluacion->hasta = $endDateFormatted;
            $evaluacion->valor_max = $request->valor_max;
            $evaluacion->distribucion = $request->distribucion;
            $evaluacion->estatus = $request->estatus;
            $evaluacion->evaluados = json_encode($request->cargo_evaluar);
            $evaluacion->save();

            $evaluacion_id = $evaluacion->id;

            // Eliminamos las categorías que han sido deseleccionadas y sus preguntas/respuestas
            if (!empty($request->id_categoria)) {
                $ids_cat = (array) $request->id_categoria;
                $categoriasEliminar = CategoriaModel::where('evaluacion_id', $id)
                    ->whereNotIn('id', $ids_cat)
                    ->pluck('id')
                    ->toArray();

                if (!empty($categoriasEliminar)) {
                    RespuestaModel::whereIn('categoria_id', $categoriasEliminar)->delete();
                    PreguntaModel::whereIn('categoria_id', $categoriasEliminar)->delete();
                    CategoriaModel::whereIn('id', $categoriasEliminar)->delete();
                }
            }

            // Guardamos o actualizamos las categorías
            $totalCategorias = (int) $request->totalcategorias;
            for ($i = 1; $i <= $totalCategorias; $i++) {
                $idCategoria = $request->input('id_categoria' . $i);
                $nombreCategoria = $request->input('nombre_categoria' . $i);

                if (!empty($idCategoria)) {
                    $categoria = CategoriaModel::find($idCategoria);
                    if ($categoria) {
                        $categoria->nombre_categoria = $nombreCategoria;
                        $categoria->descripcion = $request->input('descripcion' . $i);
                        $categoria->valor_max = $request->input('valor_categoria' . $i);
                        $categoria->save();
                    }
                } else {
                    if (!empty($nombreCategoria)) {
                        $categoria = new CategoriaModel();
                        $categoria->nombre_categoria = $nombreCategoria;
                        $categoria->valor_max = $request->input('valor_categoria' . $i);
                        $categoria->distribucion = null;
                        $categoria->evaluacion_id = $evaluacion_id;
                        $categoria->descripcion = $request->input('descripcion' . $i);
                        $categoria->save();

                        $categoria->cod_categoria = strtoupper(substr($request->nombre_evaluacion, 0, 2) . $categoria->id);
                        $categoria->save();
                    }
                }
            }

            // Re-sincronizar tabla pivote EvaluacionSkillModel
            EvaluacionSkillModel::where('evaluacion_id', $id)->delete();

            $gerencias = (array) $request->input('gerencia', []);
            $campanas = (array) $request->input('cartera', []);
            $skills = (array) $request->input('skill', []);
            $evaluadores = (array) $request->input('cargo_evaluador', []);
            $evaluados = (array) $request->input('cargo_evaluar', []);

            $validSkills = SkillModels::whereIn('id', $skills)
                ->whereIn('cartera_id', $campanas)
                ->get(['id', 'cartera_id']);

            $validSkillsMap = [];
            foreach ($validSkills as $vSkill) {
                $validSkillsMap[$vSkill->cartera_id][] = $vSkill->id;
            }

            $validCarteras = CarteraModel::whereIn('id', $campanas)
                ->whereIn('area_trabajo_id', $gerencias)
                ->get(['id', 'area_trabajo_id']);

            $validCarterasMap = [];
            foreach ($validCarteras as $vCartera) {
                $validCarterasMap[$vCartera->area_trabajo_id][] = $vCartera->id;
            }

            $evaluacionSkillsBatch = [];
            $now = Carbon::now();

            foreach ($gerencias as $gerencia_id) {
                if (!isset($validCarterasMap[$gerencia_id])) {
                    continue;
                }
                foreach ($validCarterasMap[$gerencia_id] as $campana_id) {
                    if (!isset($validSkillsMap[$campana_id])) {
                        continue;
                    }
                    foreach ($validSkillsMap[$campana_id] as $skill_id) {
                        foreach ($evaluadores as $evaluador) {
                            foreach ($evaluados as $eval) {
                                $evaluacionSkillsBatch[] = [
                                    'evaluacion_id' => $evaluacion_id,
                                    'gerencia_id' => $gerencia_id,
                                    'campana_id' => $campana_id,
                                    'skill_id' => $skill_id,
                                    'cargo_evaluador_id' => $evaluador,
                                    'evaluado' => $eval,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($evaluacionSkillsBatch)) {
                foreach (array_chunk($evaluacionSkillsBatch, 500) as $chunk) {
                    EvaluacionSkillModel::insert($chunk);
                }
            }

            // Re-sincronizar evaluadores y evaluados
            EvaluadosModel::where('evaluacion_id', $id)->delete();
            $evaluadosBatch = [];
            foreach ($evaluadores as $evaluador) {
                foreach ($evaluados as $eval) {
                    $evaluadosBatch[] = [
                        'evaluacion_id' => $evaluacion_id,
                        'cargo_evaluador_id' => $evaluador,
                        'evaluado' => $eval,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($evaluadosBatch)) {
                foreach (array_chunk($evaluadosBatch, 500) as $chunk) {
                    EvaluadosModel::insert($chunk);
                }
            }

            // Parámetros: eliminar no incluidos y guardar/actualizar
            if (!empty($request->id_parametro)) {
                ParametroModel::where('evaluacion_id', $id)
                    ->whereNotIn('id', (array) $request->id_parametro)
                    ->delete();
            }

            $totalParametros = (int) $request->totalparametros;
            for ($j = 1; $j <= $totalParametros; $j++) {
                $idParam = $request->input('id_parametro' . $j);
                $nombreParam = $request->input('nombre_parametro' . $j);

                if (!empty($idParam)) {
                    $param = ParametroModel::find($idParam);
                    if ($param) {
                        $param->nombre_parametro = $nombreParam;
                        $param->parametro1 = $request->input('desde' . $j);
                        $param->parametro2 = $request->input('hasta' . $j);
                        $param->hex = $request->input('hex' . $j);
                        $param->save();
                    }
                } else {
                    if (!empty($nombreParam)) {
                        $param = new ParametroModel();
                        $param->nombre_parametro = $nombreParam;
                        $param->parametro1 = $request->input('desde' . $j);
                        $param->parametro2 = $request->input('hasta' . $j);
                        $param->evaluacion_id = $evaluacion_id;
                        $param->hex = $request->input('hex' . $j);
                        $param->save();
                    }
                }
            }

            // Rangos: eliminar no incluidos y guardar/actualizar
            if (!empty($request->id_rangos)) {
                RangoModel::where('evaluacion_id', $id)
                    ->whereNotIn('id', (array) $request->id_rangos)
                    ->delete();
            }

            $totalRangos = (int) $request->totalrangos;
            for ($k = 1; $k <= $totalRangos; $k++) {
                $idRango = $request->input('id_rangos' . $k);
                $descripcionRango = $request->input('descripcion_rango' . $k);

                if (!empty($idRango)) {
                    $range = RangoModel::find($idRango);
                    if ($range) {
                        $range->descripcion_rango = $descripcionRango;
                        $range->rango_desde = $request->input('rango_desde' . $k);
                        $range->rango_hasta = $request->input('rango_hasta' . $k);
                        $range->save();
                    }
                } else {
                    if (!empty($descripcionRango)) {
                        $range = new RangoModel();
                        $range->descripcion_rango = $descripcionRango;
                        $range->rango_desde = $request->input('rango_desde' . $k);
                        $range->rango_hasta = $request->input('rango_hasta' . $k);
                        $range->evaluacion_id = $evaluacion_id;
                        $range->save();
                    }
                }
            }

            // Recalcular el valor de las preguntas por categoría
            for ($i = 1; $i <= $totalCategorias; $i++) {
                $idCat = $request->input('id_categoria' . $i);
                if ($idCat) {
                    $preguntas_count = PreguntaModel::where('categoria_id', $idCat)->count();
                    if ($preguntas_count > 0) {
                        $valorCat = (float) $request->input('valor_categoria' . $i);
                        $question_value = $valorCat / $preguntas_count;
                        $question_format = number_format($question_value, 3, '.', '');

                        PreguntaModel::where('categoria_id', $idCat)
                            ->update(['valor_pregunta' => $question_format]);
                    }
                }
            }

            DB::commit();

            Session::flash('success', 'Evaluacion modificada con exito');
            return redirect()->route('hab.edit', ['id' => $id]);
        } catch (Throwable $e) {
            DB::rollBack();
            Session::flash('error', 'Ocurrio un error al actualizar la evaluación: ' . $e->getMessage());
            return redirect()->route('hab.edit', ['id' => $id])->withInput();
        }
    }

    // Permite eliminar una evaluación si no tiene resultados asociados
    public function destroyEvaluacion($id)
    {
        try {
            $evaluacion = EvaluacionModel::find($id);
            if (!$evaluacion) {
                Session::flash('error', 'La evaluación no existe.');
                return back();
            }

            if (ResultadosModel::where('evaluacion_id', $id)->exists()) {
                Session::flash('error', 'No se puede eliminar la evaluación porque ya tiene resultados registrados.');
                return back();
            }

            DB::beginTransaction();

            RespuestaModel::where('evaluacion_id', $id)->delete();
            PreguntaModel::where('evaluacion_id', $id)->delete();
            CategoriaModel::where('evaluacion_id', $id)->delete();
            RangoModel::where('evaluacion_id', $id)->delete();
            ParametroModel::where('evaluacion_id', $id)->delete();
            EvaluadosModel::where('evaluacion_id', $id)->delete();
            EvaluacionSkillModel::where('evaluacion_id', $id)->delete();

            $evaluacion->delete();

            DB::commit();

            Session::flash('success', "Evaluacion eliminada exitosamente.");
            return back();
        } catch (Throwable $e) {
            DB::rollBack();
            Session::flash('error', 'Ocurrio un error al eliminar la evaluación: ' . $e->getMessage());
            return back();
        }
    }

    // Activa o desactiva una evaluación
    public function ActiveUpdate(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $evaluacion = EvaluacionModel::find($id);
            if (!$evaluacion) {
                DB::rollBack();
                return response()->json(['error' => 'Evaluación no encontrada.'], 404);
            }

            $evaluacion->estatus = $request->estatus;
            $evaluacion->save();

            DB::commit();

            $msg = ($request->estatus == 1)
                ? "La evaluacion ha sido activada exitosamente"
                : "La evaluacion ha sido desactivada exitosamente.";

            return response()->json([$msg]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al cambiar estatus: ' . $e->getMessage()], 500);
        }
    }

    // Index de creación o edición de preguntas
    public function createPreguntas()
    {
        try {
            $evaluaciones = EvaluacionModel::all();
            return view('habilidades.preguntas.index')->with(["evaluaciones" => $evaluaciones])->render();
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar vista de preguntas: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Trae las categorías de una evaluación para la tabla
    public function getCategoria(Request $request)
    {
        try {
            $categories = CategoriaModel::where('evaluacion_id', $request->evaluacion)->get();
            $data = [];

            foreach ($categories as $cate) {
                $data[] = [
                    "cod" => $cate->cod_categoria,
                    "categoria" => $cate->nombre_categoria,
                    "acciones" => '<a href="' . route('check.pregunta.list', [$cate->evaluacion_id, $cate->id]) . '" class="btn btn-sm btn-primary" '
                        . 'target="_blank" id="editcategoria' . $cate->id . '" data-toggle="tooltip" data-placement="top"'
                        . ' title="Editar"><i class=" fa fa-edit"></i></a>'
                ];
            }
            return response()->json($data);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error al obtener categorías: ' . $e->getMessage()], 500);
        }
    }

    // Listado de preguntas después de enviar el form
    public function getPreguntasList($evaluacion_id, $categoria_id)
    {
        try {
            $questions = PreguntaModel::where('categoria_id', $categoria_id)->get();
            $evaluacion = EvaluacionModel::find($evaluacion_id);
            $categorias = CategoriaModel::find($categoria_id);

            if (!$evaluacion || !$categorias) {
                Session::flash('error', 'Evaluación o categoría no encontrada.');
                return redirect()->back();
            }

            return view('habilidades.preguntas.preguntas_list')->with([
                'questions' => $questions,
                "evaluacion" => $evaluacion,
                "categoria" => $categorias
            ])->render();
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar lista de preguntas: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Guardar, actualizar o eliminar preguntas
    public function storePreguntas(Request $request)
    {
        $evaluacion_id = $request->evaluacion_id;
        $categoria_id = $request->categoria_id;

        try {
            if (ResultadosModel::where('evaluacion_id', $evaluacion_id)->exists()) {
                Session::flash('error', 'Ya hay evaluaciones registradas, las preguntas no pueden modificarse');
                return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
            }

            if ($request->distribucion == 2) {
                $total_sum = (float) $request->total_sum;
                $valor_max = (float) $request->valor_max;

                if ($total_sum > $valor_max) {
                    Session::flash('error', 'El valor ' . $total_sum . ' sobrepasa el valor maximo ' . $valor_max . ' de la categoria');
                    return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
                }

                if ($total_sum < $valor_max) {
                    $puntos_faltantes = $valor_max - $total_sum;
                    Session::flash('info', 'Faltan ' . round($puntos_faltantes, 3) . ' puntos para completar el valor maximo ' . $valor_max . ' de la categoria');
                    return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
                }
            }

            DB::beginTransaction();

            if (!empty($request->id_ques)) {
                $id_ques = is_array($request->id_ques) ? $request->id_ques : explode(",", $request->id_ques);
                RespuestaModel::whereIn('pregunta_id', $id_ques)->delete();
            }

            if (!empty($request->codigos)) {
                $codigos = is_array($request->codigos) ? $request->codigos : explode(",", $request->codigos);
                PreguntaModel::whereIn('cod_pregunta', $codigos)->delete();
            }

            $evaluacion = EvaluacionModel::find($evaluacion_id);
            $nombre_eval = $evaluacion ? $evaluacion->nombre_evaluacion : '';

            $dis_cat = CategoriaModel::find($categoria_id);
            if (!$dis_cat) {
                throw new \Exception("Categoría no encontrada.");
            }

            $nombre_cat = $dis_cat->nombre_categoria;
            $dis_cat->distribucion = $request->distribucion;
            $dis_cat->valor_max = $request->valor_max;
            $dis_cat->cantidad_preguntas = $request->cantidad_preguntas_true;
            $dis_cat->save();

            $totalPreguntas = (int) $request->cantidad_preguntas;
            for ($i = 1; $i <= $totalPreguntas; $i++) {
                $codPregunta = $request->input('cod_pregunta' . $i);
                $nombrePregunta = $request->input('nombre_pregunta' . $i);

                if (!empty($codPregunta)) {
                    $pregunta = PreguntaModel::where('cod_pregunta', $codPregunta)->first();
                    if ($pregunta) {
                        $pregunta->valor_pregunta = $request->input('valor_pregunta' . $i);
                        $pregunta->nombre_pregunta = $nombrePregunta;
                        $pregunta->distribucion = 2;
                        $pregunta->save();
                    }
                } else {
                    if (!empty($nombrePregunta)) {
                        $pregunta = new PreguntaModel();
                        $pregunta->nombre_pregunta = $nombrePregunta;
                        $pregunta->valor_pregunta = $request->input('valor_pregunta' . $i);
                        $pregunta->distribucion = 2;
                        $pregunta->evaluacion_id = $evaluacion_id;
                        $pregunta->categoria_id = $categoria_id;
                        $pregunta->save();

                        $pregunta_id = $pregunta->id;
                        $pregunta->cod_pregunta = strtoupper(substr($nombre_eval, 0, 2) . substr($nombre_cat, 0, 2) . $pregunta_id);
                        $pregunta->save();
                    }
                }
            }

            DB::commit();

            Session::flash('success', 'Preguntas guardadas con exito');
            return back();
        } catch (Throwable $e) {
            DB::rollBack();
            Session::flash('error', 'Ocurrió un error al guardar las preguntas: ' . $e->getMessage());
            return redirect()->route('check.pregunta.list', ['evaluacion_id' => $evaluacion_id, 'categoria_id' => $categoria_id]);
        }
    }

    // Funciones para administrar las respuestas
    public function createRespuestas()
    {
        try {
            $evaluaciones = EvaluacionModel::all();
            return view('habilidades.respuestas.index')->with(["evaluaciones" => $evaluaciones]);
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar respuestas: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function SearchCategoriaforRespuestas(Request $request)
    {
        try {
            $categories = CategoriaModel::where('evaluacion_id', $request->evaluacion)->get();
            return response()->json($categories);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error al buscar categorías: ' . $e->getMessage()], 500);
        }
    }

    // Trae las preguntas de una categoría
    public function consultPreguntas(Request $request)
    {
        try {
            $preguntas = PreguntaModel::where('evaluacion_id', $request->evaluacion)
                ->where('categoria_id', $request->categoria)
                ->get();

            $data = [];
            foreach ($preguntas as $cate) {
                $data[] = [
                    "cod" => $cate->cod_pregunta,
                    "pregunta" => $cate->nombre_pregunta,
                    "acciones" => '<a href="' . route('check.respuesta.list', [$cate->evaluacion_id, $cate->categoria_id, $cate->id]) . '" class="btn btn-sm btn-primary" '
                        . 'target="_blank" id="editpregunta' . $cate->id . '" data-toggle="tooltip" data-placement="top"'
                        . ' title="Editar"><i class=" fa fa-edit"></i></a>'
                ];
            }
            return response()->json($data);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error al consultar preguntas: ' . $e->getMessage()], 500);
        }
    }

    // Vista para mostrar las respuestas de la pregunta
    public function getRespuestasList($evaluacion_id, $categoria_id, $pregunta_id)
    {
        try {
            $answers = RespuestaModel::where('evaluacion_id', $evaluacion_id)
                ->where('categoria_id', $categoria_id)
                ->where('pregunta_id', $pregunta_id)
                ->get();

            $categoria = CategoriaModel::find($categoria_id);
            $pregunta = PreguntaModel::find($pregunta_id);

            if (!$categoria || !$pregunta) {
                Session::flash('error', 'Categoría o pregunta no encontrada.');
                return redirect()->back();
            }

            return view('habilidades.respuestas.respuestas_list', compact('answers', 'evaluacion_id', 'categoria_id', 'pregunta_id', 'categoria', 'pregunta'));
        } catch (Throwable $e) {
            Session::flash('error', 'Error al cargar lista de respuestas: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Permite almacenar las respuestas, editarlas o eliminarlas
    public function storeRespuestas(Request $request)
    {
        $evaluacion_id = $request->evaluacion_id;
        $categoria_id = $request->categoria_id;
        $pregunta_id = $request->pregunta_id;

        try {
            if (ResultadosModel::where('evaluacion_id', $evaluacion_id)->exists()) {
                Session::flash('error', 'Ya hay evaluaciones registradas, las respuestas no pueden modificarse');
                return back();
            }

            DB::beginTransaction();

            if (!empty($request->codigos)) {
                $codigos = is_array($request->codigos) ? $request->codigos : explode(",", $request->codigos);
                RespuestaModel::whereIn('cod_respuesta', $codigos)->delete();
            }

            $evaluacion = EvaluacionModel::find($evaluacion_id);
            $nombre_eval = $evaluacion ? $evaluacion->nombre_evaluacion : '';

            $category = CategoriaModel::find($categoria_id);
            $nombre_cat = $category ? $category->nombre_categoria : '';

            $question = PreguntaModel::find($pregunta_id);
            $nombre_ques = $question ? $question->nombre_pregunta : '';

            $totalRespuestas = (int) $request->cantidad_respuestas;
            for ($i = 1; $i <= $totalRespuestas; $i++) {
                $codRespuesta = $request->input('cod_respuesta' . $i);
                $nombreRespuesta = $request->input('nombre_respuesta' . $i);

                if (!empty($codRespuesta)) {
                    $respuesta = RespuestaModel::where('cod_respuesta', $codRespuesta)->first();
                    if ($respuesta) {
                        $respuesta->valor_respuesta = $request->input('valor_respuesta' . $i);
                        $respuesta->nombre_respuesta = $nombreRespuesta;
                        $respuesta->save();
                    }
                } else {
                    if (!empty($nombreRespuesta)) {
                        $respuesta = new RespuestaModel();
                        $respuesta->nombre_respuesta = $nombreRespuesta;
                        $respuesta->valor_respuesta = $request->input('valor_respuesta' . $i);
                        $respuesta->evaluacion_id = $evaluacion_id;
                        $respuesta->categoria_id = $categoria_id;
                        $respuesta->pregunta_id = $pregunta_id;
                        $respuesta->save();

                        $respuesta_id = $respuesta->id;
                        $cod_respuesta = strtoupper(substr($nombre_eval, 0, 2) . substr($nombre_cat, 0, 2) . substr($nombre_ques, 1, 3) . $respuesta_id);
                        $respuesta->cod_respuesta = preg_replace('/[^A-Za-z0-9]/', '', $cod_respuesta);
                        $respuesta->save();
                    }
                }
            }

            DB::commit();

            Session::flash('success', 'Respuestas guardadas con exito');
            return back();
        } catch (Throwable $e) {
            DB::rollBack();
            Session::flash('error', 'Ocurrió un error al guardar las respuestas: ' . $e->getMessage());
            return back();
        }
    }

    public function indexReportes()
    {
        try {
            $user = env('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(env('OTHER_USER_ID'), true) :
                \Auth::user();

            $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

            if ($nivel) {
                $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
            } else {
                return abort(403, "No cuenta con el nivel necesario para acceder a estos recursos.");
            }

            $evaluaciones = EvaluacionModel::where('estatus', 1)->get();

            return view('habilidades.reportes')->with(["gerencias" => $areas, "evaluaciones" => $evaluaciones]);
        } catch (Throwable $e) {
            Session::flash('error', 'Error al acceder a los reportes: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function getPersonal(Request $request)
    {
        try {
            $query = ResultadosModel::where('evaluacion_id', $request->evaluacion);
            if (is_array($request->skill) && count($request->skill) > 1) {
                $query->whereIn('skill_campana', $request->skill);
            } elseif ($request->skill) {
                $query->where('skill_campana', $request->skill);
            }

            $personal_ids = $query->distinct()->pluck('personal_id')->toArray();

            $personal = PersonalModel::select('id as id_personal', 'primer_nombre', 'primer_apellido')
                ->whereIn('id', $personal_ids)
                ->distinct()
                ->get();

            return response()->json($personal);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error al obtener personal: ' . $e->getMessage()], 500);
        }
    }

    // Trae el histórico de cargos de la persona
    public function getCargo(Request $request)
    {
        try {
            $skills = is_array($request->skill) ? $request->skill : [$request->skill];

            $historico = ResultadosModel::select('nombre_cargo')
                ->whereIn('skill_campana', $skills)
                ->where('evaluacion_id', $request->evaluacion)
                ->where('personal_id', $request->personal_id)
                ->distinct()
                ->get();

            $sede_act = PersonalModel::select('sede_laboral')->where('id', $request->personal_id)->first();
            $sede = $sede_act ? $sede_act->sede_laboral : '';

            $rango_fecha_sql = EvaluacionModel::select('desde', 'hasta')->where('id', $request->evaluacion)->first();
            $rango_fecha = '';
            if ($rango_fecha_sql && $rango_fecha_sql->desde && $rango_fecha_sql->hasta) {
                $rango_fecha = Carbon::parse($rango_fecha_sql->desde)->format('d/m/Y') . " - " . Carbon::parse($rango_fecha_sql->hasta)->format('d/m/Y');
            }

            return response()->json(['historico' => $historico, 'sede' => $sede, 'rango_fecha' => $rango_fecha]);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error al obtener cargo: ' . $e->getMessage()], 500);
        }
    }

    // Genera los distintos tipos de reportes en Excel
    public function getReporte(Request $request)
    {
        try {
            $date = $request->rango_eval;
            if (empty($date) || !str_contains($date, '-')) {
                Session::flash('error', 'Rango de fechas inválido para el reporte.');
                return redirect()->back();
            }

            $arrayDate = explode("-", $date);
            $arrayBegin = explode("/", trim($arrayDate[0]));
            $begin = trim($arrayBegin[2]) . "-" . trim($arrayBegin[1]) . "-" . trim($arrayBegin[0]);
            $arrayEnd = explode("/", trim($arrayDate[1]));
            $end = trim($arrayEnd[2]) . "-" . trim($arrayEnd[1]) . "-" . trim($arrayEnd[0]);

            $evaluado = PersonalModel::select('primer_nombre', 'primer_apellido', 'cargo_id', 'sede_laboral')
                ->where('id', $request->personal_cargo)
                ->first();

            if (!$evaluado) {
                Session::flash('error', 'El personal evaluado no fue encontrado.');
                return redirect()->back();
            }

            $nombre_evaluado = $evaluado->primer_nombre . " " . $evaluado->primer_apellido;
            $sede_laboral = $evaluado->sede_laboral;

            $skills = is_array($request->skill) ? $request->skill : [$request->skill];

            $datos_array = ResultadosModel::where('evaluacion_id', $request->evaluacion)
                ->where('personal_id', $request->personal_cargo)
                ->whereIn('skill_campana', $skills)
                ->where('nombre_cargo', $request->position_name)
                ->whereBetween('fecha_evaluacion', [$begin, $end])
                ->get()
                ->toArray();

            if (empty($datos_array)) {
                Session::flash('error', 'No se encontraron evaluaciones registradas con estos parametros');
                return redirect()->back();
            }

            $preguntas_eval = PreguntaModel::where('evaluacion_id', $request->evaluacion)->get();

            // Mapa optimizado de categorías por pregunta para evitar N+1 queries
            $preguntasCategoriasMap = PreguntaModel::where('evaluacion_id', $request->evaluacion)
                ->pluck('categoria_id', 'id')
                ->toArray();

            $reportData = [];
            $reportData_num = [];

            $evaluaciones = ResultadosRespuestasModel::with('respuesta')
                ->leftJoin('hab_resultados', 'hab_resultados_respuestas.resultado_id', '=', 'hab_resultados.id')
                ->select(
                    'hab_resultados_respuestas.resultado_id',
                    'hab_resultados_respuestas.pregunta_id',
                    'hab_resultados_respuestas.respuesta_id',
                    'hab_resultados.personal_id',
                    'hab_resultados.skill_campana'
                )
                ->where('hab_resultados.evaluacion_id', $request->evaluacion)
                ->where('hab_resultados.personal_id', $request->personal_cargo)
                ->whereIn('hab_resultados.skill_campana', $skills)
                ->where('hab_resultados.nombre_cargo', $request->position_name)
                ->whereBetween('hab_resultados.fecha_evaluacion', [$begin, $end])
                ->orderBy('hab_resultados_respuestas.resultado_id')
                ->get();

            foreach ($evaluaciones as $evaluacionItem) {
                $resultadoId = $evaluacionItem->resultado_id;
                $preguntaId = $evaluacionItem->pregunta_id;

                if (!isset($reportData[$resultadoId])) {
                    $reportData[$resultadoId] = [
                        'resultado_id' => $resultadoId,
                        'nombre_evaluado' => $nombre_evaluado,
                        'nombre_cargo' => $request->position_name,
                        'sede_laboral' => $sede_laboral
                    ];
                }

                $reportData[$resultadoId]["pregunta_$preguntaId"] = $evaluacionItem->respuesta ? $evaluacionItem->respuesta->nombre_respuesta : '';
            }

            $finalReportData = array_values($reportData);
            foreach ($finalReportData as &$subArray) {
                ksort($subArray);
            }
            unset($subArray);

            $resultado = [];
            foreach ($datos_array as $key => $item) {
                if (isset($finalReportData[$key])) {
                    $resultado[] = array_merge($item, $finalReportData[$key]);
                } else {
                    $resultado[] = $item;
                }
            }

            foreach ($resultado as &$subarray) {
                $preguntaCount = 1;
                $newSubarray = [];
                foreach ($subarray as $key => $value) {
                    if (strpos($key, 'pregunta_') === 0) {
                        $newSubarray['pregunta_' . $preguntaCount] = $value;
                        $preguntaCount++;
                    } else {
                        $newSubarray[$key] = $value;
                    }
                }
                $subarray = $newSubarray;
            }
            unset($subarray);

            $resultadol = [];
            foreach ($resultado as $itemr) {
                $nuevoElemento = [
                    "id" => $itemr["id"] ?? "",
                    "evaluacion_id" => $itemr["evaluacion_id"] ?? "",
                    "personal_id" => $itemr["personal_id"] ?? "",
                    "skill_id" => $itemr["skill_id"] ?? "",
                    "fecha_evaluacion" => $itemr["fecha_evaluacion"] ?? "",
                    "created_at" => $itemr["created_at"] ?? "",
                    "updated_at" => $itemr["updated_at"] ?? "",
                    "nombre_cargo" => $itemr["nombre_cargo"] ?? "",
                    "nombre_evaluado" => $itemr["nombre_evaluado"] ?? $nombre_evaluado,
                    "resultado_id" => $itemr["resultado_id"] ?? "",
                    "sede_laboral" => $itemr["sede_laboral"] ?? $sede_laboral,
                    "preguntas" => []
                ];

                foreach ($itemr as $key => $value) {
                    if (strpos($key, 'pregunta_') === 0) {
                        $nuevoElemento["preguntas"][] = $value;
                    }
                }
                $resultadol[] = $nuevoElemento;
            }

            $form = $resultadol;

            // Reporte RF numérico
            foreach ($evaluaciones as $evaluacion_num) {
                $resultadoId = $evaluacion_num->resultado_id;
                $preguntaId = $evaluacion_num->pregunta_id;

                if (!isset($reportData_num[$resultadoId])) {
                    $reportData_num[$resultadoId] = [
                        'resultado_id' => $resultadoId,
                        'nombre_evaluado' => $nombre_evaluado,
                        'nombre_cargo' => $request->position_name,
                        'sede_laboral' => $sede_laboral
                    ];
                }

                $valorResp = $evaluacion_num->respuesta ? (float) $evaluacion_num->respuesta->valor_respuesta : 0;
                $reportData_num[$resultadoId]["id_$preguntaId"] = $evaluacion_num->pregunta_id;
                $reportData_num[$resultadoId]["pregunta_$preguntaId"] = number_format($valorResp, 3, ',', '');
            }

            $finalReportData_num = array_values($reportData_num);
            foreach ($finalReportData_num as &$subArray_num) {
                ksort($subArray_num);
            }
            unset($subArray_num);

            $resultado_num = [];
            foreach ($datos_array as $key => $itemy) {
                if (isset($finalReportData_num[$key])) {
                    $resultado_num[] = array_merge($itemy, $finalReportData_num[$key]);
                } else {
                    $resultado_num[] = $itemy;
                }
            }

            foreach ($resultado_num as $key => $value) {
                if (strpos($key, 'id_') === 0) {
                    $id_preguntaKey = $key;
                    $preguntaIdVal = $resultado_num[$id_preguntaKey];
                    $id_cat = $preguntasCategoriasMap[$preguntaIdVal] ?? null;

                    if (isset($resultado_num[$id_preguntaKey])) {
                        $resultado_num['preguntas'][] = $resultado_num[$id_preguntaKey] . " categoria_id" . $id_cat;
                    }
                    unset($resultado_num['pregunta_' . $id_preguntaKey]);
                    unset($resultado_num['id_' . $id_preguntaKey]);
                }
            }

            $data = $resultado_num;
            foreach ($data as &$item) {
                $preguntas = [];
                foreach ($item as $key => $value) {
                    if (strpos($key, 'pregunta_') === 0) {
                        $numero_pregunta = str_replace('pregunta_', '', $key);
                        $preguntas[$numero_pregunta] = $value;
                        unset($item[$key]);
                    }
                    if (strpos($key, 'id_') === 0) {
                        unset($item[$key]);
                    }
                }
                $item['preguntas'] = $preguntas;
            }
            unset($item);

            $questions = $data;
            foreach ($questions as &$item) {
                $preguntas = [];
                $i = 0;
                if (isset($item['preguntas']) && is_array($item['preguntas'])) {
                    foreach ($item['preguntas'] as $id_pregunta => $pregunta) {
                        $id_cat = $preguntasCategoriasMap[$id_pregunta] ?? null;
                        $preguntas[$i] = $pregunta . ($id_cat ? ' ' . "categoria_id" . $id_cat : '');
                        $i++;
                    }
                }
                $item['preguntas'] = $preguntas;
            }
            unset($item);

            $por_categoria = $questions;
            foreach ($por_categoria as &$item) {
                $preguntas = $item['preguntas'] ?? [];
                $categoria_ids = [];

                foreach ($preguntas as $pregunta) {
                    if (str_contains($pregunta, ' ')) {
                        list($value, $categoria_id) = explode(' ', $pregunta);
                        $value = (float) str_replace(',', '.', $value);

                        if (!isset($categoria_ids[$categoria_id])) {
                            $categoria_ids[$categoria_id] = [
                                'valores' => [],
                                'suma' => 0
                            ];
                        }
                        $categoria_ids[$categoria_id]['valores'][] = $value;
                        $categoria_ids[$categoria_id]['suma'] += $value;
                    }
                }

                foreach ($categoria_ids as &$categoria) {
                    array_push($categoria['valores'], $categoria['suma']);
                }
                unset($categoria);

                $item['preguntas'] = $categoria_ids;
            }
            unset($item);

            $datas = $por_categoria;

            $preguntas_eval_rf = PreguntaModel::with('relationCategoria')
                ->where('evaluacion_id', $request->evaluacion)
                ->get();

            foreach ($datas as &$evaluacionl) {
                $total = 0;
                if (isset($evaluacionl['preguntas']) && is_array($evaluacionl['preguntas'])) {
                    foreach ($evaluacionl['preguntas'] as $categorial) {
                        $total += end($categorial['valores']);
                    }
                }
                $evaluacionl['resultado_total'] = $total;
            }
            unset($evaluacionl);

            $result = $datas;
            $cantidad = count($result);

            foreach ($result as $index => &$item) {
                if ($index === 0) {
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
                    if (isset($item['preguntas']) && is_array($item['preguntas'])) {
                        foreach ($item['preguntas'] as $cat_id => &$pregunta) {
                            $valores = $pregunta['valores'];
                            $suma = 0;

                            foreach ($datas as $subItem) {
                                if (isset($subItem['preguntas'][$cat_id])) {
                                    $suma += array_sum($subItem['preguntas'][$cat_id]['valores']);
                                }
                            }

                            $promedio = $cantidad > 0 ? $suma / $cantidad : 0;
                            if ($cantidad > 1) {
                                $pregunta['valores'] = array_fill(0, count($valores) - 1, "");
                                $pregunta['valores'][] = $promedio;
                            }
                        }
                        unset($pregunta);
                    }
                } elseif ($index != 0) {
                    unset($result[$index]);
                }
            }
            unset($item);

            $totalSum = 0;
            $data_sum = $result;
            foreach ($data_sum as $evaluacion_total) {
                if (isset($evaluacion_total['preguntas']) && is_array($evaluacion_total['preguntas'])) {
                    foreach ($evaluacion_total['preguntas'] as $categoria_total) {
                        $totalSum += array_sum($categoria_total['valores']);
                    }
                }
            }

            $cantidadEvaluaciones = $cantidad;
            $resultadoPromedio = $cantidadEvaluaciones > 0 ? $totalSum / $cantidadEvaluaciones : 0;
            $ex = $datas;

            if ($cantidadEvaluaciones > 1) {
                $promedios = [];
                foreach ($ex as $itemm) {
                    if (isset($itemm['preguntas']) && is_array($itemm['preguntas'])) {
                        foreach ($itemm['preguntas'] as $categoria_ids => $categorias) {
                            if (!isset($promedios[$categoria_ids])) {
                                $promedios[$categoria_ids] = [];
                            }
                            $promedios[$categoria_ids][] = $categorias['suma'];
                        }
                    }
                }
                foreach ($promedios as $categoria_ids => $sumas) {
                    $promedios[$categoria_ids] = count($sumas) > 0 ? array_sum($sumas) / count($sumas) : 0;
                }

                $array_final = [];
                $primer_elemento = $ex[0] ?? [];

                foreach ($primer_elemento as $key => $value) {
                    if ($key !== 'preguntas') {
                        $primer_elemento[$key] = "";
                    }
                }

                if (isset($primer_elemento['preguntas']) && is_array($primer_elemento['preguntas'])) {
                    foreach ($primer_elemento['preguntas'] as $categoria_ids => &$categorias) {
                        $cantidad_valores = count($categorias['valores']);
                        for ($i = 0; $i < $cantidad_valores; $i++) {
                            $categorias['valores'][$i] = "";
                        }
                        $categorias['valores'][$cantidad_valores - 1] = $promedios[$categoria_ids] ?? 0;
                    }
                    unset($categorias);
                }
                $array_final[] = $primer_elemento;
                $result = $array_final;
            }

            $suma_ultimos_valores = 0;
            $promedio_final = $result;
            foreach ($promedio_final as $items) {
                if (isset($items['preguntas']) && is_array($items['preguntas'])) {
                    foreach ($items['preguntas'] as $categoriap) {
                        $ultimo_valor = end($categoriap['valores']);
                        $suma_ultimos_valores += $ultimo_valor;
                    }
                }
            }
            $resultadoPromedio = $suma_ultimos_valores;

            // Informe final y notas
            $test_name_sql = EvaluacionModel::select('nombre_evaluacion')->where('id', $request->evaluacion)->first();
            $test_name = $test_name_sql ? $test_name_sql->nombre_evaluacion : '';

            $desde = Carbon::createFromFormat('Y-m-d', $begin);
            $hasta = Carbon::createFromFormat('Y-m-d', $end);
            $formatoDesde = $desde->format('j \d\e F \d\e\l Y');
            $formatoHasta = $hasta->format('j \d\e F \d\e\l Y');
            $resultadoFecha = "{$formatoDesde} hasta {$formatoHasta}";

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
            $rango_evaluacion = str_replace(array_keys($meses), array_values($meses), $resultadoFecha);

            $info_datos = PersonalModel::select('primer_nombre', 'primer_apellido', 'sede_laboral')->where('id', $request->personal_cargo)->first();
            $nombre_completo = $info_datos ? ($info_datos->primer_nombre . " " . $info_datos->primer_apellido) : '';
            $sede = $info_datos ? $info_datos->sede_laboral : '';

            $cargo_idc = CargoModel::select('id', 'nombre_cargo')->where('nombre_cargo', $request->position_name)->first();
            $position_name = $cargo_idc ? $cargo_idc->nombre_cargo : $request->position_name;

            $categories = CategoriaModel::select('id', 'nombre_categoria', 'descripcion')
                ->where('evaluacion_id', $request->evaluacion)
                ->get()
                ->toArray();

            $totales = $result;
            $primerElemento = $totales[0] ?? [];
            $preguntas = $primerElemento['preguntas'] ?? [];
            $valoresFinales = [];

            foreach ($preguntas as $categoriaId => $datosCategoria) {
                $valores = $datosCategoria['valores'] ?? [];
                $valorFinal = end($valores);
                $valoresFinales[$categoriaId] = $valorFinal;
            }

            $numericKeysArray = array_values($valoresFinales);
            $valoresFinales = $numericKeysArray;

            foreach ($categories as $index => &$category) {
                if (isset($valoresFinales[$index])) {
                    $category['puntaje'] = $valoresFinales[$index];
                }
            }
            unset($category);

            $parameters = ParametroModel::where('evaluacion_id', $request->evaluacion)->get()->toArray();
            $cates = $categories;
            $parametroBajo = $parameters[0] ?? ['nombre_parametro' => 'Sin Parámetro'];

            foreach ($cates as &$category) {
                $puntaje = $category['puntaje'] ?? 0;
                $comentarioEncontrado = false;

                foreach ($parameters as $parametro) {
                    $parametro1 = floatval($parametro['parametro1']);
                    $parametro2 = floatval($parametro['parametro2']);

                    if ($puntaje >= $parametro1 && $puntaje <= $parametro2) {
                        $category['comentario'] = $parametro['nombre_parametro'];
                        $comentarioEncontrado = true;
                        break;
                    }
                }

                if (!$comentarioEncontrado) {
                    $category['comentario'] = $parametroBajo['nombre_parametro'];
                }
            }
            unset($category);

            $ranges = RangoModel::where('evaluacion_id', $request->evaluacion)->get()->toArray();
            $nota_final = "Fuera del rango";

            foreach ($ranges as $rango) {
                $range1 = floatval($rango['rango_desde']);
                $range2 = floatval($rango['rango_hasta']);

                if (floatval($resultadoPromedio) >= $range1 && floatval($resultadoPromedio) <= $range2) {
                    $nota_final = $rango['descripcion_rango'];
                    break;
                }
            }

            $conteoParametros = [];
            foreach ($parameters as $param) {
                $conteoParametros[$param['nombre_parametro']] = 0;
            }

            foreach ($cates as $comentario) {
                if (isset($comentario['comentario']) && isset($conteoParametros[$comentario['comentario']])) {
                    $conteoParametros[$comentario['comentario']]++;
                }
            }

            $headers_param = array_keys($conteoParametros);
            $data_com = array_values($conteoParametros);

            $partes = explode("hasta", $rango_evaluacion);
            if (count($partes) >= 2) {
                $parte1 = trim($partes[0]);
                $parte2 = trim($partes[1]);
                $rango_evaluacion = ($parte1 === $parte2) ? $parte1 : $rango_evaluacion;
            }

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
        } catch (Throwable $e) {
            Session::flash('error', 'Error al generar el reporte: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Muestra el gráfico o estadísticas del reporte
    public function getGrafico(Request $request)
    {
        try {
            $categories = CategoriaModel::where('evaluacion_id', $request->evaluacion)
                ->pluck('nombre_categoria')
                ->toArray();

            $nombre_eval = EvaluacionModel::select('nombre_evaluacion')->where('id', $request->evaluacion)->first();
            $nombreEvaluacion = $nombre_eval ? $nombre_eval->nombre_evaluacion : '';

            $date = $request->rango_eval;
            if (empty($date) || !str_contains($date, '-')) {
                return response()->json(['titulo' => '', 'labels' => '', 'datos' => '', 'datosGrafico' => '']);
            }

            $arrayDate = explode("-", $date);
            $arrayBegin = explode("/", trim($arrayDate[0]));
            $begin = trim($arrayBegin[2]) . "-" . trim($arrayBegin[1]) . "-" . trim($arrayBegin[0]);
            $arrayEnd = explode("/", trim($arrayDate[1]));
            $end = trim($arrayEnd[2]) . "-" . trim($arrayEnd[1]) . "-" . trim($arrayEnd[0]);

            $evaluado = PersonalModel::select('primer_nombre', 'primer_apellido', 'cargo_id', 'sede_laboral')->where('id', $request->personal_id)->first();
            $nombre_evaluado = $evaluado ? ($evaluado->primer_nombre . " " . $evaluado->primer_apellido) : '';
            $sede_laboral = $evaluado ? $evaluado->sede_laboral : '';

            $skills = is_array($request->skill) ? $request->skill : [$request->skill];

            $datos_array = ResultadosModel::where('evaluacion_id', $request->evaluacion)
                ->where('personal_id', $request->personal_id)
                ->whereIn('skill_campana', $skills)
                ->where('nombre_cargo', $request->position_name)
                ->whereBetween('fecha_evaluacion', [$begin, $end])
                ->get()
                ->toArray();

            if (empty($datos_array)) {
                return response()->json(['titulo' => '', 'labels' => '', 'datos' => '', 'datosGrafico' => '']);
            }

            $preguntasCategoriasMap = PreguntaModel::where('evaluacion_id', $request->evaluacion)
                ->pluck('categoria_id', 'id')
                ->toArray();

            $reportData = [];
            $reportData_num = [];

            $evaluaciones = ResultadosRespuestasModel::with('respuesta')
                ->leftJoin('hab_resultados', 'hab_resultados_respuestas.resultado_id', '=', 'hab_resultados.id')
                ->select(
                    'hab_resultados_respuestas.resultado_id',
                    'hab_resultados_respuestas.pregunta_id',
                    'hab_resultados_respuestas.respuesta_id',
                    'hab_resultados.personal_id',
                    'hab_resultados.skill_campana'
                )
                ->where('hab_resultados.evaluacion_id', $request->evaluacion)
                ->where('hab_resultados.personal_id', $request->personal_id)
                ->whereIn('hab_resultados.skill_campana', $skills)
                ->where('hab_resultados.nombre_cargo', $request->position_name)
                ->whereBetween('hab_resultados.fecha_evaluacion', [$begin, $end])
                ->orderBy('hab_resultados_respuestas.resultado_id')
                ->get();

            foreach ($evaluaciones as $evaluacionItem) {
                $resultadoId = $evaluacionItem->resultado_id;
                $preguntaId = $evaluacionItem->pregunta_id;

                if (!isset($reportData[$resultadoId])) {
                    $reportData[$resultadoId] = [
                        'resultado_id' => $resultadoId,
                        'nombre_evaluado' => $nombre_evaluado,
                        'nombre_cargo' => $request->position_name,
                        'sede_laboral' => $sede_laboral
                    ];
                }

                $reportData[$resultadoId]["pregunta_$preguntaId"] = $evaluacionItem->respuesta ? $evaluacionItem->respuesta->nombre_respuesta : '';
            }

            $finalReportData = array_values($reportData);
            foreach ($finalReportData as &$subArray) {
                ksort($subArray);
            }
            unset($subArray);

            $resultado = [];
            foreach ($datos_array as $key => $item) {
                if (isset($finalReportData[$key])) {
                    $resultado[] = array_merge($item, $finalReportData[$key]);
                } else {
                    $resultado[] = $item;
                }
            }

            foreach ($resultado as &$subarray) {
                $preguntaCount = 1;
                $newSubarray = [];
                foreach ($subarray as $key => $value) {
                    if (strpos($key, 'pregunta_') === 0) {
                        $newSubarray['pregunta_' . $preguntaCount] = $value;
                        $preguntaCount++;
                    } else {
                        $newSubarray[$key] = $value;
                    }
                }
                $subarray = $newSubarray;
            }
            unset($subarray);

            $resultadol = [];
            if (!empty($resultado)) {
                foreach ($resultado as $itemr) {
                    $nuevoElemento = [
                        "id" => $itemr["id"] ?? "",
                        "evaluacion_id" => $itemr["evaluacion_id"] ?? "",
                        "personal_id" => $itemr["personal_id"] ?? "",
                        "skill_id" => $itemr["skill_id"] ?? "",
                        "fecha_evaluacion" => $itemr["fecha_evaluacion"] ?? "",
                        "created_at" => $itemr["created_at"] ?? "",
                        "updated_at" => $itemr["updated_at"] ?? "",
                        "nombre_cargo" => $itemr["nombre_cargo"] ?? "",
                        "nombre_evaluado" => $itemr["nombre_evaluado"] ?? $nombre_evaluado,
                        "resultado_id" => $itemr["resultado_id"] ?? "",
                        "sede_laboral" => $itemr["sede_laboral"] ?? $sede_laboral,
                        "preguntas" => []
                    ];

                    foreach ($itemr as $key => $value) {
                        if (strpos($key, 'pregunta_') === 0) {
                            $nuevoElemento["preguntas"][] = $value;
                        }
                    }
                    $resultadol[] = $nuevoElemento;
                }

                foreach ($evaluaciones as $evaluacion_num) {
                    $resultadoId = $evaluacion_num->resultado_id;
                    $preguntaId = $evaluacion_num->pregunta_id;

                    if (!isset($reportData_num[$resultadoId])) {
                        $reportData_num[$resultadoId] = [
                            'resultado_id' => $resultadoId,
                            'nombre_evaluado' => $nombre_evaluado,
                            'nombre_cargo' => $request->position_name,
                            'sede_laboral' => $sede_laboral
                        ];
                    }

                    $valorResp = $evaluacion_num->respuesta ? (float) $evaluacion_num->respuesta->valor_respuesta : 0;
                    $reportData_num[$resultadoId]["id_$preguntaId"] = $evaluacion_num->pregunta_id;
                    $reportData_num[$resultadoId]["pregunta_$preguntaId"] = number_format($valorResp, 3, ',', '');
                }

                $finalReportData_num = array_values($reportData_num);
                foreach ($finalReportData_num as &$subArray_num) {
                    ksort($subArray_num);
                }
                unset($subArray_num);

                $resultado_num = [];
                foreach ($datos_array as $key => $itemy) {
                    if (isset($finalReportData_num[$key])) {
                        $resultado_num[] = array_merge($itemy, $finalReportData_num[$key]);
                    } else {
                        $resultado_num[] = $itemy;
                    }
                }

                foreach ($resultado_num as $key => $value) {
                    if (strpos($key, 'id_') === 0) {
                        $id_preguntaKey = $key;
                        $preguntaIdVal = $resultado_num[$id_preguntaKey];
                        $id_cat = $preguntasCategoriasMap[$preguntaIdVal] ?? null;

                        if (isset($resultado_num[$id_preguntaKey])) {
                            $resultado_num['preguntas'][] = $resultado_num[$id_preguntaKey] . " categoria_id" . $id_cat;
                        }

                        unset($resultado_num['pregunta_' . $id_preguntaKey]);
                        unset($resultado_num['id_' . $id_preguntaKey]);
                    }
                }

                $data = $resultado_num;
                foreach ($data as &$item) {
                    $preguntas = [];
                    foreach ($item as $key => $value) {
                        if (strpos($key, 'pregunta_') === 0) {
                            $numero_pregunta = str_replace('pregunta_', '', $key);
                            $preguntas[$numero_pregunta] = $value;
                            unset($item[$key]);
                        }
                        if (strpos($key, 'id_') === 0) {
                            unset($item[$key]);
                        }
                    }
                    $item['preguntas'] = $preguntas;
                }
                unset($item);

                $questions = $data;
                foreach ($questions as &$item) {
                    $preguntas = [];
                    $i = 0;
                    if (isset($item['preguntas']) && is_array($item['preguntas'])) {
                        foreach ($item['preguntas'] as $id_pregunta => $pregunta) {
                            $id_cat = $preguntasCategoriasMap[$id_pregunta] ?? null;
                            $preguntas[$i] = $pregunta . ($id_cat ? ' ' . "categoria_id" . $id_cat : '');
                            $i++;
                        }
                    }
                    $item['preguntas'] = $preguntas;
                }
                unset($item);

                $por_categoria = $questions;
                foreach ($por_categoria as &$item) {
                    $preguntas = $item['preguntas'] ?? [];
                    $categoria_ids = [];

                    foreach ($preguntas as $pregunta) {
                        if (str_contains($pregunta, ' ')) {
                            list($value, $categoria_id) = explode(' ', $pregunta);
                            $value = (float) str_replace(',', '.', $value);

                            if (!isset($categoria_ids[$categoria_id])) {
                                $categoria_ids[$categoria_id] = [
                                    'valores' => [],
                                    'suma' => 0
                                ];
                            }
                            $categoria_ids[$categoria_id]['valores'][] = $value;
                            $categoria_ids[$categoria_id]['suma'] += $value;
                        }
                    }

                    foreach ($categoria_ids as &$categoria) {
                        array_push($categoria['valores'], $categoria['suma']);
                    }
                    unset($categoria);

                    $item['preguntas'] = $categoria_ids;
                }
                unset($item);

                $datas = $por_categoria;

                foreach ($datas as &$evaluacionl) {
                    $total = 0;
                    if (isset($evaluacionl['preguntas']) && is_array($evaluacionl['preguntas'])) {
                        foreach ($evaluacionl['preguntas'] as $categorial) {
                            $total += end($categorial['valores']);
                        }
                    }
                    $evaluacionl['resultado_total'] = $total;
                }
                unset($evaluacionl);

                $result = $datas;
                $cantidad = count($result);

                foreach ($result as $index => &$item) {
                    if ($index === 0) {
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
                        if (isset($item['preguntas']) && is_array($item['preguntas'])) {
                            foreach ($item['preguntas'] as $cat_id => &$pregunta) {
                                $valores = $pregunta['valores'];
                                $suma = 0;

                                foreach ($datas as $subItem) {
                                    if (isset($subItem['preguntas'][$cat_id])) {
                                        $suma += array_sum($subItem['preguntas'][$cat_id]['valores']);
                                    }
                                }

                                $promedio = $cantidad > 0 ? $suma / $cantidad : 0;
                                if ($cantidad > 1) {
                                    $pregunta['valores'] = array_fill(0, count($valores) - 1, "");
                                    $pregunta['valores'][] = $promedio;
                                }
                            }
                            unset($pregunta);
                        }
                    } elseif ($index != 0) {
                        unset($result[$index]);
                    }
                }
                unset($item);

                $totalSum = 0;
                $data_sum = $result;
                foreach ($data_sum as $evaluacion_total) {
                    if (isset($evaluacion_total['preguntas']) && is_array($evaluacion_total['preguntas'])) {
                        foreach ($evaluacion_total['preguntas'] as $categoria_total) {
                            $totalSum += array_sum($categoria_total['valores']);
                        }
                    }
                }

                $cantidadEvaluaciones = $cantidad;
                $ex = $datas;

                if ($cantidadEvaluaciones > 1) {
                    $promedios = [];
                    foreach ($ex as $itemm) {
                        if (isset($itemm['preguntas']) && is_array($itemm['preguntas'])) {
                            foreach ($itemm['preguntas'] as $categoria_ids => $categorias) {
                                if (!isset($promedios[$categoria_ids])) {
                                    $promedios[$categoria_ids] = [];
                                }
                                $promedios[$categoria_ids][] = $categorias['suma'];
                            }
                        }
                    }
                    foreach ($promedios as $categoria_ids => $sumas) {
                        $promedios[$categoria_ids] = count($sumas) > 0 ? array_sum($sumas) / count($sumas) : 0;
                    }

                    $array_final = [];
                    $primer_elemento = $ex[0] ?? [];

                    foreach ($primer_elemento as $key => $value) {
                        if ($key !== 'preguntas') {
                            $primer_elemento[$key] = "";
                        }
                    }

                    if (isset($primer_elemento['preguntas']) && is_array($primer_elemento['preguntas'])) {
                        foreach ($primer_elemento['preguntas'] as $categoria_ids => &$categorias) {
                            $cantidad_valores = count($categorias['valores']);
                            for ($i = 0; $i < $cantidad_valores; $i++) {
                                $categorias['valores'][$i] = "";
                            }
                            $categorias['valores'][$cantidad_valores - 1] = $promedios[$categoria_ids] ?? 0;
                        }
                        unset($categorias);
                    }
                    $array_final[] = $primer_elemento;
                    $result = $array_final;
                }

                $valoresFinales = [];
                if (!empty($result) && isset($result[0]['preguntas']) && is_array($result[0]['preguntas'])) {
                    foreach ($result[0]['preguntas'] as $categoria) {
                        $valoresFinales[] = end($categoria['valores']);
                    }
                }

                $parametros = ParametroModel::where('evaluacion_id', $request->evaluacion)->get();

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
                    'labels' => $categories,
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
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error al generar gráfico: ' . $e->getMessage()], 500);
        }
    }

    private function obtenerColorParametro($valor, $parametros)
    {
        if ($parametros->isEmpty()) {
            return ['#333333', 'Sin parámetro'];
        }

        foreach ($parametros as $parametro) {
            if ($valor >= $parametro->parametro1 && $valor <= $parametro->parametro2) {
                return [$parametro->hex, $parametro->nombre_parametro];
            }
        }

        // Si no se encuentra en ningún rango, devolver el color del primer parámetro
        $primer = $parametros->first();
        return [$primer->hex, $primer->nombre_parametro];
    }
}
