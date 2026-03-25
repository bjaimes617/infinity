<?php

namespace App\Http\Controllers\reclutamiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\reclutamiento\capEstatusModel;
use App\Models\reclutamiento\capPreseleccionModel;
use App\Models\reclutamiento\capCandidatosModel;

use Carbon\Carbon;
use App\Exports\reclutamiento\CandidatosExport;
use App\Exports\reclutamiento\PreCandidatosExport;

use Maatwebsite\Excel\Facades\Excel;
class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estatus = capEstatusModel::all();
        
        return view('reclutamiento.reportes.index')->with(["estatus"=>$estatus])->render();
    }

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
       
        $fecha = explode('-', $request->fecha);
        
        $init = carbon::createFromformat('d/m/Y',trim($fecha[0]))->startOfDay();
        $end = carbon::createFromformat('d/m/Y',trim($fecha[1]))->endOfDay();
        
        $date = $init->copy()->format('dmY') .' al'. $end->copy()->format('dmY');
        
        switch ($request->tipodereporte){
            case "precandidatos":
                $result = capPreseleccionModel::wherebetween('created_at',array($init,$end))->get();
                
                 return Excel::download(new PreCandidatosExport($result), 'Reporte de Precandidatos registrados ' . $date . '.csv');
            break;
            case "candidatos":
                 $result = capCandidatosModel::wherebetween('created_at',array($init,$end))->get();
                
                  return Excel::download(new CandidatosExport($result), 'Reporte de candidatos ' . $date . '.csv');   
            break;
        }
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
