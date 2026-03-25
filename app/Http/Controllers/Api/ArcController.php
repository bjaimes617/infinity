<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\arc\ArcPeriodoModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\recibos\AsigDeducModel;
use App\Models\recibos\ItemsModels;
use App\Models\recibos\PeriodosModels;
use App\Models\recibos\RecibosNominaModel;
use App\Models\personal\PersonalModel;
use Illuminate\Support\Facades\DB;

class ArcController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
 
        try{
                $data = array();
                $row = array();
                
                $nomina = RecibosNominaModel::where('cedula',$request->cedula)
                    ->whereBetween('created_at',array($request->anio.'-01-01 00:00:01', $request->anio.'-12-31 23:59:59'))
                    ->get();

                $quincenas = AsigDeducModel::select(DB::raw("rp_asignaciones_deducciones.monto, MONTH(STR_TO_DATE(rp_periodo.mes,'%M')) as month_number, "
                    . "SUM(rp_asignaciones_deducciones.monto) as total_mes"), 'rp_nomina.cedula', 'rp_periodo.mes')
                    ->leftJoin('rp_nomina', 'rp_asignaciones_deducciones.rp_nomina_id', '=', 'rp_nomina.id')
                    ->leftJoin('rp_periodo', 'rp_nomina.rp_periodo_id', '=', 'rp_periodo.id')
                    ->where('anio', $request->anio)                
                    ->where('cedula', $request->cedula)
                    ->whereIn('rp_asignaciones_deducciones.rp_items_id', [1,378,21,22,24,26,399,400,402,404,396,399,400,402,404,21,22,24,26,71,19,397])
                    ->groupBy('month_number')
                    ->orderBy('month_number', 'asc')
                    ->get();
        

                $remun = 0;
                $porcentaje = 0;
                $itotales = 0;
                $row["suma_pagada"] = 0;
                $row["suma_impuesto"] = 0;

                foreach ($quincenas as $quince) {                
                    $row["mes"] = $quince["mes"];
                    $row["remuneracion"] = $quince["total_mes"];
                    $row["suma_pagada"]  =  $row["suma_pagada"] + $quince["total_mes"];
                    $remun = $remun + $quince["total_mes"];

                    $impuestos = AsigDeducModel::select(DB::raw("rp_periodo.mes, SUM(rp_asignaciones_deducciones.monto) as impuesto,"
                        . "  MONTH(STR_TO_DATE(rp_periodo.mes,'%M')) as month_number"), 'rp_nomina.cedula', 
                    'rp_asignaciones_deducciones.auxi_num as porcentaje')
                    ->leftJoin('rp_nomina', 'rp_asignaciones_deducciones.rp_nomina_id', '=', 'rp_nomina.id')
                    ->leftJoin('rp_periodo', 'rp_nomina.rp_periodo_id', '=', 'rp_periodo.id')
                    ->where('anio', $request->anio)
                    ->where('rp_periodo.mes', $quince["mes"])
                    ->where('cedula', $request->cedula)
                    ->whereIn('rp_asignaciones_deducciones.rp_items_id', [216,598])
                    ->groupBy('month_number')
                    ->orderBy('month_number', 'asc')
                    ->get();
                
                    foreach ($impuestos as $islr) {
                        
                        if($islr->mes == $quince["mes"]){
                        $row["porcentaje"]          = $islr->porcentaje;
                        $row["impuesto_retenido"]   = $islr->impuesto;
                        $porcentaje                 = $porcentaje + $islr->porcentaje;
                        $itotales                   = $itotales + $islr->impuesto;
                        $row["suma_impuesto"]       = $row["suma_impuesto"] + $islr->impuesto;
                        }
                    }    
                $data[] = $row;        
            }

        $suma_pagada = $row['suma_pagada'];
        $datos = PersonalModel::where('cedula_empleado',$request->cedula)->first();
            if(RecibosNominaModel::where('cedula',$request->cedula)->whereBetween('created_at',array($request->anio.'-01-01 00:00:01', $request->anio.'-12-31 23:59:50'))->exists()){

            $impuestos_totales = $itotales;
                return response()->json([0 => $data, 1 => $datos, 2 => $remun, 3 => $impuestos_totales, 4 => $porcentaje, 5 => $suma_pagada],200);

            }else{        
                return response()->json(['Ocurrio un problema al intentar Generar La constancia: No se encontraron datos'],500);
            }
        }catch (\Exception $e) {
            return response()->json("excepcion",$e->getMessage());
            //return response()->json(['Ocurrio un problema al intentar Generar La constancia SRV: '.$e->getMessage()],500);
        }

    }

 //return response()->json(['Ocurrio un problema al intentar Generar La constancia'], 500);
        //return response()->json(['datos' => $datos],500);

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
