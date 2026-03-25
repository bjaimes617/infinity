<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\personal\PersonalModel;
use Carbon\Carbon;

class BlockRegistredPersonal {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        ///// validamos si existe el request cedula | r1
        if (isset($request->cedula_empleado)) {
            //// validamos si existe registro | r2
            if (PersonalModel::where('cedula_empleado', $request->cedula_empleado)->exists()) {
                $objPersonal = PersonalModel::where('cedula_empleado', $request->cedula_empleado)->first();
                $situacion = $objPersonal->getrawOriginal('situacion_contractual');
                // SI LA SITUACION CONTRACTUAL NO ES FREELANCE HACEMOS LA COMPROBACION DE LOS 90 DIAS
                if($situacion != 3){
                ////validamos si en el request viene la fecha de egreso | r3
                if (!empty($request->fegreso)) {
                    $arrayBegin = explode("/", $request->fegreso);
                    $egreso = trim($arrayBegin[2]) . "-" . trim($arrayBegin[1]) . "-" . trim($arrayBegin[0]);

                    ///validamos si el expediente tiene fecha de egreso almacenada para descartar actualizacion
                    if ($objPersonal->fecha_egreso != null) {
                        $fecha1 = Carbon::today();
                        $fecha2 = Carbon::parse($objPersonal->fecha_egreso);
                        ///validamos cuantos dias han pasado desde que fue egresado
                        $diferencia_en_dias = $fecha1->diffInDays($fecha2);
                        //dd($diferencia_en_dias <= config('app.diaspararecontratar'), \Auth::user()->hasPermission(['personal.update.blockregistred']));
                        if ($diferencia_en_dias <= config('app.diaspararecontratar')) {
                            ///si es menor a 90 dias
                            if (\Auth::user()->hasPermission(['personal.update.blockregistred'])) {
                                ///el usuario posee permiso para actualizar la fecha de egreso y el registro es menor a 90 dias
                                return $next($request);
                            } else {
                                \Session::flash('error', "El expediente no puede ser Actualizado. Personal con Menos de 90 Días");
                                return redirect()->back();
                            }
                        } else {
                            //dd("r1");
                            return $next($request);
                        }
                    } else {
                        //dd("r2");
                        return $next($request);
                    }
                } else {                    
                    ///en caso de que la fecha de ingreso este vacia | expedientes activos actualizables normal | r3
                    if ($objPersonal->fecha_egreso != null) {
                        $fecha1 = Carbon::today();
                        $fecha2 = Carbon::parse($objPersonal->fecha_egreso);
                        ///validamos cuantos dias han pasado desde que fue egresado
                        $diferencia_en_dias = $fecha1->diffInDays($fecha2);
                        //dd($diferencia_en_dias <= config('app.diaspararecontratar'), \Auth::user()->hasPermission(['personal.update.blockregistred']));
                        if ($diferencia_en_dias <= config('app.diaspararecontratar')) {
                            ///si es menor a 90 dias
                            if (\Auth::user()->hasPermission(['personal.update.blockregistred'])) {
                                ///el usuario posee permiso para actualizar la fecha de egreso y el registro es menor a 90 dias
                                return $next($request);
                            } else {
                                \Session::flash('error', "El expediente no puede ser Actualizado. Personal con Menos de 90 Días");
                                return redirect()->back();
                            }
                        } else {                            
                            return $next($request);
                        }
                    } else {                        
                        return $next($request);
                    }
                }
            }else{
                //SI LA SITUACION CONTRACTUAL ES 3 CONTINUAMOS
                return $next($request);
            }
            } else {
                //// si no existe registro | r2
                return $next($request);
            }
        } else {            
            ///en caso de no estar declarado la cedula en el request, se continua con la ruta | r1
            return $next($request);
        }
    }

}
