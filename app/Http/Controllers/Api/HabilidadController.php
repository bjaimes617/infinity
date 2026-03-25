<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\habilidades\EvaluacionModel;
use App\Models\habilidades\EvaluacionSkillModel;
use App\Models\habilidades\EvaluadosModel;
use App\Models\personal\CargoModel;
use App\Models\personal\PersonalModel;
use App\Models\habilidades\CategoriaModel;
use App\Models\habilidades\PreguntaModel;
use App\Models\habilidades\RespuestaModel;
use App\Models\habilidades\ResultadosModel;
use App\Models\habilidades\ResultadosRespuestasModel;


class HabilidadController extends Controller
{
    //ESTA FUNCION ENVIA EL LISTADO DE LAS EVALUACIONES DISPONIBLES A AUTOSERVICIO

    public function index(Request $request){

        //RECIBIMOS LA CEDULA DEL TRABAJADOR DESDE AUTOSERVICIO

        $cedula_user = $request->cedula_user;

        //AVERIGUAMOS QUE CARGO, SKILL , 1ER JEFE INMEDIATO, 2DO JEFE INMEDIATO Y GERENTE DE AREA TIENE LA PERSONA
        
        $personal = PersonalModel::select('cargo_id','skill_id','jefe_inmediato','jefe_inmediato_segundo','gerente_area_id','primer_nombre','primer_apellido')->where('cedula_empleado',$cedula_user[0])->first();

        $comp_name = $personal->primer_nombre." ".$personal->primer_apellido;

        $cargo_user = CargoModel::select('id','nombre_cargo')->where('id',$personal->cargo_id)->first();

        $cargo_id = $cargo_user->id;

        $nombre_cargo = $cargo_user->nombre_cargo;

        $skill_id = $personal->skill_id;

        //ID PERSONAL DEL JEFE INMEDIATO , JEFE INMEDIATO SEGUNDO Y GERENTE DE AREA

        $boss = $personal->jefe_inmediato;

        $boss2 = $personal->jefe_inmediato_segundo;

        $boss3 = $personal->gerente_area_id;

        //BUSCAMOS EL ID DEL CARGO DEL JEFE INMEDIATO

        $id_jefe = PersonalModel::select('cargo_id')->where('id',$boss)->first();

        $cargo_boss = $id_jefe->cargo_id;
        
         //BUSCAMOS EL ID DEL CARGO DEL JEFE INMEDIATO SEGUNDO

         $id_jefe2 = PersonalModel::select('cargo_id')->where('id',$boss2)->first();

         $cargo_boss2 = $id_jefe2->cargo_id;

          //BUSCAMOS EL ID DEL CARGO DEL GERENTE DE AREA

        $id_jefe3 = PersonalModel::select('cargo_id')->where('id',$boss3)->first();

        $cargo_boss3 = $id_jefe3->cargo_id;

        //CONSULTAMOS QUE EVALUACIONES PERTENECEN AL SKILL Y CARGO DE LA PERSONA

        $evaluaciones = EvaluacionSkillModel::select('evaluacion_id')->where('skill_id',$skill_id)->where('cargo_evaluador_id',$cargo_id)->get()->toArray();

        $eval = EvaluacionModel::whereIn('id',$evaluaciones)->where('desde', '<=', $cedula_user[1])
        ->where('hasta', '>=', $cedula_user[1])->where('estatus',1)->get()->toArray();

        $datos = array();

        //BUSCAMOS TODOS LOS JEFES DEL USUARIO DE AUTOSERVICIO Y CREAMOS UN SUBARRAY DENTRO DE CADA EVALUACION CON ELLOS

foreach ($eval as $evaluacion) { // Usamos & para modificar el array original

    $evaluados = json_decode($evaluacion['evaluados']); // Decodifica el JSON

    $jefes = [];
    $nombresJefes = []; // Array para almacenar los nombres de los jefes
    $evaluados_already = []; //Array para almacenar las personas evaluadas

    if(ResultadosModel::select('personal_id')->where('evaluacion_id',$evaluacion['id'])->where('nombre_evaluador',$comp_name)->exists()){

        $listado_evaluados = ResultadosModel::select('personal_id')->where('evaluacion_id',$evaluacion['id'])
        ->where('nombre_evaluador',$comp_name)->get();

        foreach($listado_evaluados as $lista){
            
            $already_eval = $lista->personal_id;
            $evaluados_already[] = $already_eval;

        }

        //return json_encode($evaluados_already);
    }

    if (in_array(1, $evaluados)) {
        $jefeInmediato_id = PersonalModel::select('jefe_inmediato')->where('cedula_empleado',$cedula_user[0])->first();
        $jefe1_id = $jefeInmediato_id->jefe_inmediato; 

        $jefeInmediato = PersonalModel::select('primer_nombre','primer_apellido','id')->where('id',$jefe1_id)->first();
        if ($jefeInmediato) {
            $nombreCompleto = $jefeInmediato->primer_nombre . ' ' . $jefeInmediato->primer_apellido;
            if (!in_array($nombreCompleto, $nombresJefes)) { 
            $jefes[] = [
                'id' => $jefeInmediato->id,
                'nombre_completo' => $jefeInmediato->primer_nombre . ' ' . $jefeInmediato->primer_apellido,
            ];
            $nombresJefes[] = $nombreCompleto; // Agrega el nombre al array de nombres
          }
        }
    }

    if (in_array(2, $evaluados)) {
        $jefeInmediato2_id = PersonalModel::select('jefe_inmediato_segundo')->where('cedula_empleado',$cedula_user[0])->first();
        $jefe2_id = $jefeInmediato2_id->jefe_inmediato_segundo; 

        $jefeInmediato2 = PersonalModel::select('primer_nombre','primer_apellido','id')->where('id',$jefe2_id)->first();
        if ($jefeInmediato2) {
            $nombreCompleto = $jefeInmediato2->primer_nombre . ' ' . $jefeInmediato2->primer_apellido;
            if (!in_array($nombreCompleto, $nombresJefes)) { 
            $jefes[] = [
                'id' => $jefeInmediato2->id,
                'nombre_completo' => $jefeInmediato2->primer_nombre . ' ' . $jefeInmediato2->primer_apellido,
            ];
            $nombresJefes[] = $nombreCompleto; // Agrega el nombre al array de nombres
          }
        }
    }

    if (in_array(3, $evaluados)) {
        $jefeInmediato3_id = PersonalModel::select('gerente_area_id')->where('cedula_empleado',$cedula_user[0])->first();
        $jefe3_id = $jefeInmediato3_id->gerente_area_id; 

        $jefeInmediato3 = PersonalModel::select('primer_nombre','primer_apellido','id')->where('id',$jefe3_id)->first();
        if ($jefeInmediato3) {
            $nombreCompleto = $jefeInmediato3->primer_nombre . ' ' . $jefeInmediato3->primer_apellido;
            if (!in_array($nombreCompleto, $nombresJefes)) { 
            $jefes[] = [
                'id' => $jefeInmediato3->id,
                'nombre_completo' => $jefeInmediato3->primer_nombre . ' ' . $jefeInmediato3->primer_apellido,
            ];
            $nombresJefes[] = $nombreCompleto; // Agrega el nombre al array de nombres
          }
        }
    }

    $evaluacion['jefes'] = $jefes; // Agrega el subarray "jefes" a la evaluación
    $evaluacion['evaluados_already'] = $evaluados_already; // Agrega el subarray "evaluados_already" a la evaluación
    $datos[] = $evaluacion;
}

        //ENVIAMOS A AUTOSERVICIO LAS EVALUACIONES DISPONIBLES PARA EL CARGO Y LOS JEFES INMEDIATOS Y GERENTE DE AREA DE LA PERSONA

        return response()->json([200, $datos, 2 => $nombre_cargo, 3 => $cargo_id,
         4 => $boss, 5 => $cargo_boss, 6 => $cedula_user[0], 7 => $boss2, 8 => $boss3,
        9 => $cargo_boss2, 10 => $cargo_boss3, 11 => $cedula_user[2]]);

    }

    //FUNCION QUE ENVIA LAS CATEGORIAS, PREGUNTAS , RESPUESTAS  Y OTROS DATOS ADICIONALES DE LA EVALUACION SELECCIONADA A AUTOSERVICIO

    public function show(Request $request){
        
        //nombre de la evaluacion

        $datos = $request->datos_evaluacion;

        //Averiguamos que skill tiene el usuario que realizo la evaluacion

        $skill_campana = PersonalModel::select('skill_id')->where('cedula_empleado',$datos[1])->first();
        $skill_verdadero = $skill_campana->skill_id;

        $evaluacion = EvaluacionModel::select('nombre_evaluacion')->where('id',$datos[0])->first();

        $nombre_evaluacion = $evaluacion->nombre_evaluacion;

        //nombre y apellido del jefe inmediato

        $jefe = PersonalModel::select('primer_nombre','primer_apellido','id')->where('id',$datos[2])->first();

        $primer_nombre = $jefe->primer_nombre;

        $primer_apellido = $jefe->primer_apellido;

        $nombre_jefe = $jefe->primer_nombre." ".$jefe->primer_apellido;
        $personal_id = $jefe->id;

        //BUSCAMOS EL CARGO ID DEL USUARIO QUE ESTA EVALUANDO

        $position_id_sql = PersonalModel::select('cargo_id')->where('cedula_empleado',$datos[1])->first();

        $position_id = $position_id_sql->cargo_id;

        //MI CARGO EJERCIDO

        $cargo_user = CargoModel::select('nombre_cargo')->where('id',$position_id)->first();

        $nombre_cargo = $cargo_user->nombre_cargo;

        //BUSCAMOS EL CARGO ID DEL JEFE

        $position_boss_sql = PersonalModel::select('cargo_id')->where('id',$datos[2])->first();

        $position_id_boss = $position_boss_sql->cargo_id;

        //nombre del cargo del jefe inmediato

        $cargo_jefe = CargoModel::select('nombre_cargo')->where('id',$position_id_boss)->first();

        $nombre_cargo_jefe = $cargo_jefe->nombre_cargo;

        //categorias, preguntas y respuestas de la evaluacion

        $encuesta = CategoriaModel::with('preguntas.respuestas')->where('evaluacion_id',$datos[0])->get();

         //ENVIAMOS A AUTOSERVICIO LA EVALUACION PARA MOSTRARLA Y SER LLENADA

         return response()->json([200, $nombre_evaluacion, 2 => $nombre_jefe, 3 => $nombre_cargo, 4 => $nombre_cargo_jefe, 5 => $encuesta, 6 => $personal_id, 7 => $skill_verdadero]);

    }

    //FUNCION QUE ALMACENA LAS PREGUNTAS Y RESPUESTAS DE LA EVALUACION REALIZADA

    public function store(Request $request){

        $datos_eval = $request->resul;

        $evaluacion_id = $datos_eval[1];

        $personal_id = $datos_eval[2];

        $resultados = $datos_eval[0];

        $skill_verdadero = $datos_eval[3];

        $nombre_cargo = $datos_eval[4];

        $cedula = $datos_eval[5];

        //averiguamos el skill_id del personal

        $skill_sql = PersonalModel::select('skill_id')->where('id',$personal_id)->first();

        $skill_id = $skill_sql->skill_id;

        //averiguamos nombre y apellido del evaluador 

        $name_sql = PersonalModel::select('primer_nombre','primer_apellido')->where('cedula_empleado',$cedula)->first();

        $complete_name = $name_sql->primer_nombre." ".$name_sql->primer_apellido;

        //PRIMERO REGISTRAMOS LA EVALUACION REALIZADA 

        $test = new ResultadosModel();

        $test->evaluacion_id = $evaluacion_id;
        $test->personal_id = $personal_id;
        $test->skill_id = $skill_id;
        $test->skill_campana = $skill_verdadero;
        $test->nombre_cargo = $nombre_cargo;
        $test->nombre_evaluador = $complete_name;
        $test->fecha_evaluacion = date('Y-m-d');
        $test->save();

        //TOMAMOS EL ID DE LA EVALUACION REALIZADA COMO ID RESULTADO

        $resultado_id = $test->id;

        //GUARDAMOS LAS PREGUNTAS Y RESPUESTAS
        
        foreach($resultados as $resultado){

        $respuestas_test = new ResultadosRespuestasModel();
        $respuestas_test->resultado_id = $resultado_id;
        $respuestas_test->pregunta_id = $resultado['pregunta_id'];
        $respuestas_test->respuesta_id = $resultado['respuesta_id'];
        $respuestas_test->save();

        }

        //DEVOLVEMOS LA RESPUESTA JSON EXITOSA JUNTO CON UN MENSAJE

        return response()->json([200, "Evaluación enviada exitosamente"]);
    }
}
