<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ari\UnidadTributariaModel;
use App\Models\personal\PersonalModel;
use App\Models\ari\HistoricoAriModel;
use Carbon\Carbon;

class AriController extends Controller {

    public function index(Request $request) {

        //RECIBIMOS EL AÑO, EL TRIMESTRE ACTUAL Y LA CEDULA DEL TRABAJADOR PARA HACER LA COMPROBACION

        $personal = PersonalModel::where('cedula_empleado', $request->cedula)->first();
        $anio_trimestre = $request->anio_trimestre;
        // COMPROBAMOS SI EXISTE UN PERIODO ACTIVO EN ESE AÑO Y EN ESE TRIMESTRE
        if (UnidadTributariaModel::where('anio', $anio_trimestre[0])->where('trimestre', $anio_trimestre[1])->where('active', 1)->exists()) {

            // EVALUAMOS SI EXISTE EL PERSONAL_ID EN EL HISTORICO
            if (HistoricoAriModel::where('anio', $anio_trimestre[0])->where('personal_id', $personal->id)->exists()) {

                $fecha_act = Carbon::now();
                $fecha = Carbon::now(); // Obtener la fecha actual
                $fechaInicialProximoAnio = $fecha->addYear()->startOfYear(); // Obtener la fecha inicial del próximo año

                $ari = HistoricoAriModel::where('personal_id', $personal->id)->where('anio', $anio_trimestre[0])->where('periodo', $anio_trimestre[1])->first();

                //SACAMOS EL PERIODO ACTUAL Y EL AÑO ACTUAL

                $periodo_actual = $anio_trimestre[1];

                $anio_actual = $anio_trimestre[0];

                //SI NO ESTAMOS EN EL ULTIMO PERIODO SUMAMOS 1 PARA COMPROBAR EL SIGUIENTE, DE LO CONTRARIO LO INICIALIZAMOS EN 1 

                if ($periodo_actual != 4) {

                    $periodo_actual = $periodo_actual + 1;
                } else {

                    $periodo_actual = 1;

                    $anio_actual = (int) $anio_actual + 1;
                }

                //COMPROBAMOS SI EXISTE EL PERIODO SIGUIENTE , SI NO EXISTE NO LO COMPROBAMOS

                if (UnidadTributariaModel::where('anio', $anio_actual)->where('trimestre', $periodo_actual)->exists()) {

                    //SACAMOS EL VALOR ACTIVO PARA HABILITAR O DESHABILITAR EL BOTON DE ACT. SEGUN EL CASO

                    $active_consult = UnidadTributariaModel::select('active', 'unidad_tributaria')->where('anio', $anio_actual)->where('trimestre', $periodo_actual)->first();

                    $active = $active_consult->active;

                    $unidad_tributaria = $active_consult->unidad_tributaria;

                    //SI ESE PERSONAL TIENE UNO O MAS DE UN HISTORICO EN EL AÑO SIGNIFICA QUE TIENE VARIACION DE LO CONTRARIO NO TIENE

                    if (HistoricoAriModel::where('personal_id', $personal->id)->where('anio', $anio_trimestre[0])->count() > 1) {

                        $variacion = 1;
                    } else {

                        $variacion = 0;
                    }

                    return response()->json([201, $ari, 2 => $personal, 3 => $active, 4 => $unidad_tributaria, 5 => $variacion]);
                } else {

                    $active = "";

                    $unidad_tributaria = "";

                    $variacion = "";

                    return response()->json([201, $ari, 2 => $personal, 3 => $active, 4 => $unidad_tributaria, 5 => $variacion]);
                }
            } else {
                //SI NO EXISTE EL PERSONAL_ID EN EL HISTORICO MANDAMOS A LLENAR FORM NUEVO POR PRIMERA VEZ
                $unidad = UnidadTributariaModel::where('anio', $anio_trimestre[0])->where('trimestre', $anio_trimestre[1])->first();

                return response()->json([200, $unidad, 2 => $personal]);
            }
        } else {
            //SI NO HAY NINGUN PERIODO ACTIVO EN EL AÑO MANDAMOS MENSAJE DE ERROR
            return response()->json([500, "No hay periodos activos para el año en curso"]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        //return json_encode($request->all());

        $now = Carbon::now();
        $trimestre_con = ceil($now->quarter);

        $trimestre = (int) $trimestre_con;

        $personal = PersonalModel::where('cedula_empleado', $request->cedula)->first();

        $historico = new HistoricoAriModel();

        $historico->personal_id = $personal->id;
        $historico->anio = $request->anio;
        $historico->unidad_tributaria = $request->unidad_tributaria;
        $historico->nombre_empresa = $request->nombre_empresa;
        $historico->nombre_empresa2 = $request->nombre_empresa2;
        $historico->nombre_empresa3 = $request->nombre_empresa3;
        $historico->nombre_empresa4 = $request->nombre_empresa4;
        $historico->cantidad_organismo = $request->cantidad_organismo;
        $historico->cantidad_organismo2 = $request->cantidad_organismo2;
        $historico->cantidad_organismo3 = $request->cantidad_organismo3;
        $historico->cantidad_organismo4 = $request->cantidad_organismo4;
        $historico->total_estimado = $request->total_estimado;
        $historico->remun_deter = $request->remun_deter;
        $historico->valor_concepto1 = $request->valor_concepto1;
        $historico->valor_concepto2 = $request->valor_concepto2;
        $historico->valor_concepto3 = $request->valor_concepto3;
        $historico->valor_concepto4 = $request->valor_concepto4;
        $historico->total_desgravamenes = $request->total_desgravamenes;
        $historico->total_ut = $request->total_ut;
        $historico->remun_menos_totalut = $request->remun_menos_totalut_h;
        $historico->rebaja_persona_natural = $request->rebaja_persona_natural;
        $historico->carga_familiar = $request->carga_familiar;
        $historico->total_rebajas = $request->total_rebajas;
        $historico->impuesto_anio_gravable = $request->impuesto_anio_gravable_h;
        $historico->total_ut_familiar = $request->total_ut_familiar;
        $historico->porcentaje_reten_ini = $request->porcentaje_reten_ini;
        $historico->impuesto_estimado = $request->impuesto_estimado;
        $historico->deter_porcentaje = $request->deter_porcentaje;
        $historico->impuesto_fecha = $request->impuesto_fecha;
        $historico->remun_fecha = $request->remun_fecha;
        $historico->impuesto_anio_anterior = null;
        $historico->monto_fijo_des = $request->monto_fijo_des;

        $now = Carbon::now();
        $trimestre_con = ceil($now->quarter);

        $trimestre = (int) $trimestre_con;

        $historico->periodo = $trimestre;

        $historico->save();

        $historico_actual = HistoricoAriModel::where('id', $historico->id)->first();

        $periodo_actual = $historico_actual->periodo;

        $anio_actual = Carbon::now()->format('Y');

        if ($periodo_actual != 4) {

            $periodo_actual = $periodo_actual + 1;
        } else {

            $periodo_actual = 1;

            $anio_actual = (int) $anio_actual + 1;
        }

        //COMPROBAMOS SI EXISTE EL PERIODO SIGUIENTE , SI NO EXISTE NO LO COMPROBAMOS

        if (UnidadTributariaModel::where('anio', $anio_actual)->where('trimestre', $periodo_actual)->exists()) {

            //SACAMOS EL VALOR ACTIVO PARA HABILITAR O DESHABILITAR EL BOTON DE ACT. SEGUN EL CASO

            $active_consult = UnidadTributariaModel::select('active', 'unidad_tributaria')->where('anio', $anio_actual)->where('trimestre', $periodo_actual)->first();

            $active = $active_consult->active;

            $unidad_tributaria = $active_consult->unidad_tributaria;

            if (HistoricoAriModel::where('personal_id', $personal->id)->where('anio', $request->anio)->count() > 1) {
                $variacion = 1;
            } else {
                $variacion = 0;
            }
            return response()->json([200, $historico_actual, 2 => $personal, 3 => $active, 4 => $unidad_tributaria, 5 => $variacion]);
        } else {

            //SACAMOS EL VALOR ACTIVO PARA HABILITAR O DESHABILITAR EL BOTON DE ACT. SEGUN EL CASO
            $active = "";
            $unidad_tributaria = "";
            if (HistoricoAriModel::where('personal_id', $personal->id)->where('anio', $request->anio)->count() > 1) {
                $variacion = 1;
            } else {
                $variacion = 0;
            }
            return response()->json([200, $historico_actual, 2 => $personal, 3 => $active, 4 => $unidad_tributaria, 5 => $variacion]);
        }
    }

    public function consult(Request $request) {

        $now = Carbon::now();
        $trimestre_con = ceil($now->quarter);

        $trimestre = (int) $trimestre_con;

        $historico_actual = HistoricoAriModel::where('id', $request->historial)
                ->where('personal_id', $request->personal)
                ->where('anio', $request->anio)
                ->where('periodo', $trimestre)
                ->first();
        $personal = PersonalModel::where('id', $request->personal)->first();

        //SI ESE PERSONAL TIENE UNO O MAS DE UN HISTORICO EN EL AÑO SIGNIFICA QUE TIENE VARIACION DE LO CONTRARIO NO TIENE

        if (HistoricoAriModel::where('personal_id', $request->personal)->where('anio', $request->anio)->count() > 1) {

            $variacion = 1;
        } else {

            $variacion = 0;
        }

        return response()->json([200, $historico_actual, 2 => $personal, 3 => $variacion]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }
}
