<?php

namespace App\Http\Controllers\constancias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\constancias\ConstanciasModel;
use App\Exports\ConstanciasExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReporteController extends Controller
{

    public function index(){

        return view('constancias.reporte');
    }

    public function generarReporte(Request $request){
       
       $rango = $request->fecha;

        $arrays = explode("-", $rango); //Separo las dos fechas en dos arreglos
        $inicio = carbon::createfromFormat('d/m/Y',trim($arrays[0]))->StartofDay();
        $final  = carbon::createfromFormat('d/m/Y',trim($arrays[1]))->EndofDay();
        
        $constancias = ConstanciasModel::whereBetween('created_at',array($inicio->copy()->format('Y-m-d H:i:s'),$final->copy()->format('Y-m-d H:i:s')))->get();
       
        return Excel::download(new ConstanciasExport($constancias), 'Reporte de constancias del '.$inicio->copy()->format('dmY').' al '.$final->copy()->format('dmY').' .xls');

    }
    
}
