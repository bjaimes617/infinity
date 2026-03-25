<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */



///check personal in external  viginalncia
Route::post('/Personal/active/DG/{id}','Api\PersonalApiController@show',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

///check personal en autoservicio y elimina el usuaruio
Route::post('/Personal/review/lp/external','Api\PersonalApiController@CheckPersonalAutoservice',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

////  valida si el personal existe en la ficha para procesar la restauracion
Route::post('/Personal/validate/ficha/personal','Api\PersonalApiController@ValidatePersonal',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));


/// para descargar los registros de condominios externos
Route::post('/Condominio/external/generate','Api\CondominioApiController@index',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

///check datos de recibos y retorna el pdf
Route::post('/Personal/generate/resource/payments/','Api\PersonalApiController@ResourcePayments',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

///generar constancias de trabajos desde auto servicios
Route::post('/Personal/constancias/generate','Api\PersonalApiController@ConstanciasCreate',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

Route::post('/Personal/validate/constancias/show','Api\PersonalApiController@ConstanciaShow',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

//////para generar las ARC desde Autoservicio
Route::post('/arc/generate/data','Api\ArcController@index',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));        

////---------------ARI
///consulta de unidad tributaria
Route::post('/ari/search/tribute','Api\AriController@index',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));
///para guardar historial del ARI
Route::post('/ari/create/historial','Api\AriController@store',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));
///para consultar el historial ARI
Route::post('/ari/consult/historial','Api\AriController@consult',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

Route::middleware('activity')->post('/habilidades/search/assessment','Api\HabilidadController@index',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));


////---------------HABILIDADES/////
//para mostrar la evaluacion de habilidades blandas con sus categorias, preguntas y respuestas
Route::middleware('activity')->post('/habilidades/assessment/show','Api\HabilidadController@show',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

//almacena los resultados de la evaluacion con sus preguntas y respuestas de hab. blandas
Route::middleware('activity')->post('/habilidades/assessment/store','Api\HabilidadController@store',      
        header('Access-Control-Allow-Methods: POST'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));


////---------------RECLUTAMIENTO/////
///formulario de preseleccion 
Route::middleware('activity')->post('/reclutamientoSimple/preseleccion/storage','reclutamiento\ReclutamientoController@Preseleccionstore',      
        header('Access-Control-Allow-Methods: post'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

///Buscar datos de los candidatos preseleccionados
Route::middleware('activity')->post('/reclutamiento/validate/candidato','reclutamiento\ReclutamientoController@SearchRegisterCandidato',      
        header('Access-Control-Allow-Methods: post'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));

///formulario de captacion planilla preempleo
Route::middleware('activity')->post('/reclutamientoSimple/Storage','reclutamiento\ReclutamientoController@store',      
        header('Access-Control-Allow-Methods: post'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization"));
 

Route::middleware(['auth:sanctum','activity'])->post('/listado/personal/general/dg','Api\PersonalApiController@ApiListadoGeneral');
/*
Route::middleware(['auth:sanctum','activity'])->post('/listado/personal/general/dg','Api\PersonalApiController@ApiListadoGeneral',      
        header('Access-Control-Allow-Methods: POST'),
        header('Accept: application/json'),
        header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-XSRF-TOKEN, token, Authorization"));*/