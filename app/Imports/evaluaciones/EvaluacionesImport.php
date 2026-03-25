<?php

namespace App\Imports\evaluaciones;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use App\Models\User;
use App\Models\evaluacion\EvaluacionModel;
use App\Models\evaluacion\IndicadorModel;
use App\Models\evaluacion\RespuestaModel;
use App\Models\evaluacion\SegmentoModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Exceptions\ValidateAditionals;
use Exception;

class EvaluacionesImport implements ToCollection, WithHeadingRow, WithValidation {

    /**
     * @param Collection $collection
     */
    use Importable;

    public function __construct($valores) {
       
        $this->user_id = $valores[0];
        $this->desde = $valores[1];
        $this->hasta = $valores[2];
        $this->anio = $valores[3];
        $this->mes = $valores[4];
        $this->tipo_periodo_seg = $valores[5];
        $this->periodo_quincenal = $valores[6];
        $this->periodo_semanal = $valores[7];
        $this->area_trabajo = $valores[8];
        $this->carteras = $valores[9];
        $this->skill_asig = $valores[10];
        $this->cargo_eval = $valores[11];
        $this->indicador_count = $valores[12];
    }

    public function collection(Collection $rows) {

        //ARRAYS PARA ALMACENAR LOS INDICADORES CON SU META
        $data = [];
        $msj_indicador = [];
        $msj_max_values = [];
        $msj_indicador_errors = [];
        $evaluaciones = [];
        $evaluado = [];

        //dd($rows);
        //VALIDACIONES DEL RULES SE HACEN EN EL COLLECTION YA QUE PUEDE HABER N CANTIDAD DE INDICADORES
       
            foreach ($rows as $index => $row) {

                foreach ($row as $key => $value) {

                    if (preg_match('/^indicador/', $key)) {
                        if ($row[$key] == "" || $row[$key] == null) {
                            array_push($msj_indicador_errors,"Error en la linea " . ($index + 2) . " El nombre del indicador no puede estar vacio");
                        }
                    }

                    if (preg_match('/^meta/', $key)) {
                        if ($row[$key] != 0) {
                            if ($row[$key] == "" || $row[$key] == null) {                             
                                 array_push($msj_indicador_errors,"Error en la linea " . ($index + 2) . " La meta no puede estar vacia");
                            }
                        }

                        $val = str_replace(',', '.', $row[$key]);
                        $val = str_replace('%', '', $val);

                        if (!is_numeric($val)) {
                            $msj_indicador_errors[] = "Error en la linea " . ($index + 2) . " El valor de la meta no es correcto";
                        }

                        if ($val < 0) {

                            $msj_indicador_errors[] = "Error en la linea " . ($index + 2) . " El valor de la meta no puede ser negativo";
                        }
                    }

                    if (preg_match('/^puntaje/', $key)) {
                        if ($row[$key] != 0) {
                            if ($row[$key] == "" || $row[$key] == null) {
                                array_push($msj_indicador_errors,"Error en la linea " . ($index + 2) . " el puntaje no puede estar vacio");                            
                            }
                        }

                        $val_p = str_replace(',', '.', $row[$key]);
                        $val_p = str_replace('%', '', $val_p);

                        if (!is_numeric($val_p)) {                            
                            array_push($msj_indicador_errors,"Error en la linea " . ($index + 2) . " El valor del puntaje no es correcto");  
                        }

                        if ($val_p < 0) {
                            array_push($msj_indicador_errors,"Error en la linea " . ($index + 2) . " El valor del puntaje no puede ser negativo");                         
                        }
                    }
                }
            }
            if (!empty($msj_indicador_errors)) {
                throw new ValidateAditionals($msj_indicador_errors);
            }
            
            $desde = $this->desde;
            $hasta = $this->hasta;

            //VALIDAMOS SI LA PERSONA HA SIDO EVALUADA EN EL PERIODO SELECCIONADO

            foreach ($rows as $row) {

                $personal_id = PersonalModel::select('id')->where('cedula_empleado', $row['cedula'])->first();
                $persona = $personal_id->id;
                // Realizamos la consulta para verificar si ya existe una evaluación en el rango
                $evaluacionExistente = EvaluacionModel::where('personal_id', $persona)
                        ->where('cargo_id', $this->cargo_eval)->where('skill_id', $this->skill_asig)
                        ->where('area_trabajo_id', $this->area_trabajo)
                        ->where('cartera_id', $this->carteras)
                        ->where(function ($query) use ($desde, $hasta) {
                            $query->whereBetween('desde', [$this->desde, $this->hasta])
                                    ->orWhereBetween('hasta', [$this->desde, $this->hasta])
                                    ->orWhere(function ($query) use ($desde, $hasta) {
                                        $query->where('desde', '<=', $this->desde)
                                                ->where('hasta', '>=', $this->hasta);
                                    });
                        })
                        ->exists();

                if ($evaluacionExistente) {                    
                    array_push($evaluado,"La persona con la cedula " . $row['cedula'] . " ya tiene una evaluación registrada para el periodo " . $this->tipo_periodo_seg . " seleccionado");                            
                }
            }
          
            if (!empty($evaluado)) {
                throw new ValidateAditionals($evaluado);
            }

            foreach ($rows as $row) {
                $indicators = []; // array para almacenar indicadores
                // Recorre las filas y almacena los indicadores, metas y puntajes
                foreach ($row as $key => $value) {
                    if (preg_match('/^indicador/', $key)) {
                        $index = str_replace('indicador', '', $key);
                        $indicators[$index]['indicador'] = $value;
                        // Eliminar el signo de porcentaje y cambiar comas por puntos en 'meta'
                        $indicators[$index]['meta'] = str_replace(['%', ','], ['', '.'], $row["meta{$index}"]); // Elimina % y reemplaza , con . en un solo paso
                        // Cambiar comas por puntos en 'puntaje'
                        $indicators[$index]['puntaje'] = str_replace(['%', ','], ['', '.'], $row["puntaje{$index}"]); // Elimina % y reemplaza , con . en un solo paso
                    }
                }
                // Añadir el arrayca de indicadores a los datos
                $data[] = $indicators;
            }
            //dd($data);
            //VALIDAR QUE REALMENTE EXISTAN Y SEAN LOS CORRECTOS EN BD
            foreach ($data as $evaluacion) {
                foreach ($evaluacion as $indicadorData) {
                    $indicador = $indicadorData['indicador'];
                    $meta = $indicadorData['meta'];

                    $sql = IndicadorModel::select('eval_indicadores.indicador', 'eval_segmentos.skill_id',
                                    'eval_segmentos.cargo_id', 'eval_indicadores.eval_segmentos_id')
                            ->leftJoin('eval_segmentos', 'eval_indicadores.eval_segmentos_id', '=', 'eval_segmentos.id')
                            ->where('eval_segmentos.skill_id', $this->skill_asig)
                            ->where('eval_segmentos.cargo_id', $this->cargo_eval)
                            ->where('eval_segmentos.area_trabajo_id', $this->area_trabajo)
                            ->where('eval_segmentos.cartera_id', $this->carteras)
                            ->where('eval_indicadores.indicador', $indicador)
                            ->where('eval_indicadores.meta', $meta)
                            ->exists();

                    // Si no existe el indicador con su meta en la base de datos genero un error 

                    if (!$sql) {
                        array_push($msj_indicador,"El indicador " . $indicador . " con la meta " . $meta . " no es correcto");                         
                     
                    }
                }
            }
            if (!empty($msj_indicador)) {
                throw new ValidateAditionals($msj_indicador);
            }

            //VALIDAR SI EL INDICADOR ES PORCENTUAL EL MAXIMO SEA 100% Y SI ES NUMERICO SE DEJA LIBRE EL VALOR
            foreach ($data as $puntuacion) {
                foreach ($puntuacion as $indicadorData) {
                    $puntos = $indicadorData['puntaje'];
                    $indicador = $indicadorData['indicador'];
                    $meta = $indicadorData['meta'];

                    $sql_meta = IndicadorModel::select('eval_indicadores.indicador', 'eval_segmentos.skill_id',
                                    'eval_segmentos.cargo_id', 'eval_indicadores.eval_segmentos_id', 'eval_indicadores.meta', 'eval_indicadores.tipo')
                            ->leftJoin('eval_segmentos', 'eval_indicadores.eval_segmentos_id', '=', 'eval_segmentos.id')
                            ->where('eval_segmentos.skill_id', $this->skill_asig)
                            ->where('eval_segmentos.cargo_id', $this->cargo_eval)
                            ->where('eval_segmentos.area_trabajo_id', $this->area_trabajo)
                            ->where('eval_segmentos.cartera_id', $this->carteras)
                            ->where('eval_indicadores.indicador', $indicador)
                            ->where('eval_indicadores.meta', $meta)
                            ->first();

                    $meta_bd = $sql_meta->meta;
                    $tipo_bd = $sql_meta->tipo;

                    if ($tipo_bd == 1) {
                        if ($puntos > 100) {
                            array_push($msj_max_values,"El valor " . $puntos . " sobrepasa el 100% en el indicador porcentual " . $indicador);                                  
                        }
                    }
                }
            }
            if (!empty($msj_max_values)) {
                throw new ValidateAditionals($msj_max_values);
            }
            
            try {
            //dd("paso todas las validaciones");
            foreach ($rows as $key => $row) {
             
                //CREAMOS LA EVALUACION Y DEVOLVEMOS EL ID
                $eval = $this->EvaluacionCreate($row['cedula']);
                //BUSCAMOS EL ID DEL SEGMENTO POR EL NOMBRE DEL INDICADOR EL CUAL ES UNICO
                $seg = $this->SearchSegmento($row['indicador1']);
                //BUSCAMOS EL ID DE LOS INDICADORES Y SU META SEGUN EL ID DEL SEGMENTO RECIBIDO
                $indi = $this->SearchIndicadores($seg);
                //GENERAMOS LAS RESPUESTAS DE LAS EVALUACIONES CREADAS
                $this->RespuestasCreate($indi, $eval, $seg, $key, $data);
            }
            \Session::flash('success', 'Personal evaluado exitosamente, por favor verifique los datos');
            return true;
        } catch (\Exception $e) {          
            throw new ValidateAditionals([$e->getMessage()]);
        }
    }

    private function EvaluacionCreate($cedula) {

        //REGISTRAMOS LA EVALUACION      
        $evaluacion = new EvaluacionModel();
        $evaluacion->desde = $this->desde;
        $evaluacion->hasta = $this->hasta;
        $evaluacion->mes = $this->mes;
        $evaluacion->anio = $this->anio;
        $evaluacion->tipo_periodo = $this->tipo_periodo_seg;

        if (strtolower($this->tipo_periodo_seg) == "semanal") {

            switch ($this->periodo_semanal) {

                case 1:
                    $evaluacion->periodo = 1;
                    break;
                case 2:
                    $evaluacion->periodo = 2;
                    break;
                case 3:
                    $evaluacion->periodo = 3;
                    break;
                case 4:
                    $evaluacion->periodo = 4;
                    break;
                case 5:
                    $evaluacion->periodo = 5;
                    break;
                default:
                    $evaluacion->periodo = null;
            }
        } elseif (strtolower($this->tipo_periodo_seg) == "quincenal") {

            switch ($this->periodo_quincenal) {

                case 1:
                    $evaluacion->periodo = 1;
                    break;
                case 2:
                    $evaluacion->periodo = 2;
                    break;
                default:
                    $evaluacion->periodo = null;
            }
        } else {
            $evaluacion->periodo = null;
        }

        $personal_sql = PersonalModel::select('id')->where('cedula_empleado', $cedula)->first();
        $personal_id = $personal_sql->id;
        $evaluacion->personal_id = $personal_id;
        $evaluacion->user_id = $this->user_id;
        $evaluacion->cargo_id = $this->cargo_eval;
        $evaluacion->skill_id = $this->skill_asig;
        $evaluacion->area_trabajo_id = $this->area_trabajo;
        $evaluacion->cartera_id = $this->carteras;
        $evaluacion->save();
        return $evaluacion->id;
    }

    private function SearchSegmento($indicador) {

        $indicador_sql = IndicadorModel::select('eval_indicadores.indicador', 'eval_segmentos.skill_id',
                                'eval_segmentos.cargo_id', 'eval_indicadores.eval_segmentos_id', 'eval_indicadores.id')
                        ->leftJoin('eval_segmentos', 'eval_indicadores.eval_segmentos_id', '=', 'eval_segmentos.id')
                        ->where('eval_segmentos.skill_id', $this->skill_asig)
                        ->where('eval_segmentos.cargo_id', $this->cargo_eval)
                        ->where('eval_segmentos.area_trabajo_id', $this->area_trabajo)
                        ->where('eval_segmentos.cartera_id', $this->carteras)
                        ->where('eval_indicadores.indicador', $indicador)->first();

        $segmento_id = $indicador_sql->eval_segmentos_id;
        return $segmento_id;
    }

    private function SearchIndicadores($segmento) {

        $ind_sql = IndicadorModel::select('id', 'meta')->where('eval_segmentos_id', $segmento)->get()->toArray();
        return $ind_sql;
    }

    private function SearchIndicadoresSegment($segmento, $respuesta) {
        $ind_s = IndicadorModel::select('id', 'meta', 'indicador', 'tipo')->where('eval_segmentos_id', $segmento)->where('indicador', $respuesta['indicador'])->where('meta', $respuesta['meta'])->first();

        return $ind_s;
    }

    private function RespuestasCreate($in, $eval, $segment, $key, $data) {

        foreach ($data[$key] as $respuesta) {

            $res = new RespuestaModel();
            $res->segmento_id = $segment;
            $indica = $this->SearchIndicadoresSegment($segment, $respuesta);
            $res->indicador = $indica->indicador;
            $res->tipo = $indica->tipo;
            $res->meta = $indica->meta;
            $res->puntaje = $respuesta['puntaje'];
            $res->evaluacion_id = $eval;
            $res->save();
        }
        return true;
    }

    public function rules(): array {
        return[
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (BlacklistModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('Esta Persona Se encuentra en Lista Negra.');
                    } else if (!ctype_digit($value)) {
                        $onFailure('La Cedula de Identidad debe ser solo númerico');
                    }
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula no existe en el listado de personal.');
                    }
                }
            }
        ];
    }
}
