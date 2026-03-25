<?php

namespace App\Http\Controllers\personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\personal\PersonalModel;
use carbon\carbon;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = [];
        $today = Carbon::today()->toDateString();
       $endDate = Carbon::today()->addDays(15)->toDateString();

        $data = PersonalModel::select('primer_nombre','primer_apellido','finalizacion_1','finalizacion_2','skill_id','id')
        ->whereNotIn('situacion_contractual',[3,2])
        ->whereNotIn('estatus_id',[2,6])
        ->where(function($query) use ($today,$endDate) {
            $query->whereBetween('finalizacion_1', [$today, $endDate])
            ->orWhereBetween('finalizacion_2', [$today, $endDate]);
        })->get();
        
        $now = Carbon::now()->startofDay();      

        $events = $data->map(function($record) use ($now) {

            $finalizacion1 = $record->finalizacion_1 ? Carbon::parse($record->finalizacion_1)->startofDay() : null;
            $finalizacion2 = $record->finalizacion_2 ? Carbon::parse($record->finalizacion_2)->startofDay() : null;
           
            $descripcion = null;
            $chosenDate = null;
            if ($finalizacion1 && $finalizacion2) {
                $dias1 = abs($now->diffInDays($finalizacion1, false));
                $dias2 = abs($now->diffInDays($finalizacion2, false));
                
                #Esto si el registro posee ambas fechas       
                if ($dias1 >= 0 && ($dias2 < 0 || $dias1 <= $dias2)) {
                    $chosenDate = $finalizacion1;
                    $descripcion = "== Renovacion 1er. Contrato ==";
                } else {
                    $chosenDate = $finalizacion2;
                    $descripcion = "== Finalizacion 2do. Contrato ==";
    }
            } elseif ($finalizacion1) {
                #si solo posee finalizacion 1
                $chosenDate = $finalizacion1;   
                $descripcion =  "== Renovacion 1er. Contrato ==";          
            } elseif ($finalizacion2) {
                #si solo posee finalizacion 2
                $chosenDate = $finalizacion2; 
                $descripcion =  "== Finalizacion 2do. Contrato ==";               
            }
            $skill = $record->Relationskill->nombre_skill;
            return [
                'title' => $record->primer_nombre .'//'. $record->primer_apellido.'//'.$skill.'//'.$descripcion,
                'start' => $chosenDate->format('Y-m-d'),
                'url'   => route('personal.edit', $record->id),
            ];
        });
      
       return view('personal.calendar.index')->with(["event"=>$events]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function CountsEvents()
    {
        //$actual = carbon::create('2026-03-04');
        $actual = Carbon::now()->format('Y-m-d');

        $dataCount = PersonalModel::whereNotIn('situacion_contractual', [3])
        ->whereNotIn('estatus_id', [2, 6])
        ->whereNull('fecha_egreso') 
        ->where(function($query) use ($actual) {
            $query->whereDate('finalizacion_1', $actual)
                ->orWhereDate('finalizacion_2', $actual);
        })
        ->count();
        return $dataCount;
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
