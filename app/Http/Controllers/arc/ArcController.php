<?php

namespace App\Http\Controllers\arc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\arc\ArcPeriodoModel;
use App\Models\recibos\AsigDeducModel;
use App\Models\recibos\ItemsModels;
use App\Models\recibos\PeriodosModels;
use App\Models\recibos\RecibosNominaModel;
use App\Models\personal\PersonalModel;
use Illuminate\Support\Facades\DB;

class ArcController extends Controller {

    public function create() {

        return view('arc.create');
    }

    public function store(Request $request) {

        $row = array();
        $nom = null;
        
        if (PersonalModel::where('cedula_empleado', $request->cedula)->exists()) {           
            
            $datos = PersonalModel::where('cedula_empleado', $request->cedula)->first();
            
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

            /*DIGA
            * 1 SUELDO
            * 71 ANTICIPO DE UTILIDADES
            * 19 utilidades
            * 21 vaciones 
            * 22 vacaciones lott
            * 24 vacaones festivas
            * 26 bono vacacional
            */  
            /*DG
            * 378 SUELDO
            * 397 ANTICIPO UTILIDADES
            * 396 utilidades
             * 399 vacaciones
             * 400 vacaciones lott
             * 402 vacaciones feriadas lott
             * 404 bono vacacioneal
            */
        
            $remun = 0;
            $porcentaje = 0;
            $itotales = 0;
            $row["suma_pagada"] = 0;
            $row["suma_impuesto"] = 0;

            foreach ($quincenas as $quince) {

                $row["mes"]         = $quince["mes"];
                $row["remuneracion"]= $quince["total_mes"];
                $row["suma_pagada"] = $row["suma_pagada"] + $quince["total_mes"];

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

                //dd($impuestos);
                foreach ($impuestos as $islr) {
                   
                    if ($islr->mes == $quince["mes"]) {
                        $row["porcentaje"]          = $islr->porcentaje;
                        $row["impuesto_retenido"]   = $islr->impuesto;

                        $porcentaje = $porcentaje + $islr->porcentaje;

                        $itotales = $itotales + $islr->impuesto;
                        $row["suma_impuesto"] = $row["suma_impuesto"] + $islr->impuesto;
                    }
                }
                $nom[] = $row;
            }
        
            $suma_pagada = $row['suma_pagada'];
            $cedula = $request->cedula;
            $anio =  $request->anio;
             if(RecibosNominaModel::leftJoin('rp_periodo','rp_nomina.rp_periodo_id','rp_periodo.id')->where('cedula',$request->cedula)->where('anio', $request->anio)->exists()){
                $impuestos_totales = $itotales;
                return \PDF::loadView('arc.pdf.index', compact('nom', 'cedula', 'datos', 'remun', 'impuestos_totales','anio', 'porcentaje', 'suma_pagada'))->setPaper('letter')->stream($cedula . ' ' . $request->anio . ' ' . 'ARC.pdf', array('Attachment' => 0));
            } else {
                \Session::flash('info', 'Información de pago no disponible para este personal');
                return back();
            }
        } else {

            \Session::flash('error', 'Este personal no se encuentra registrado');
            return back();
        }
    }
}
