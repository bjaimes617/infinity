<?php

namespace App\Http\Middleware;

use Closure;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class CheckPassword {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //dd(Auth()->user()->getrawOriginal('estatus_id'));
        switch (\Auth()->user()->getrawOriginal('estatus_id')) {
            case 1:
                // dd("aca");
                $start_date = Carbon::create(Auth()->user()->password_updated_at);
                if (Auth()->user()->password_updated_at == null) {
                    Session::put('password', 'Se requiere Actualizar Su contraseña de Acceso');
                    return redirect(RouteServiceProvider::CHANGEPASSWORD);
                } else {

                    $end_date = Carbon::now();
                    $different_days = $start_date->diffInDays($end_date);
                    //dd("probando",$different_days,config('app.daysrestore'),$different_days >= config('app.daysrestore'));
                    if ($different_days >= config('app.daysrestore')) {
                        Session::put('password', 'Su contraseña ha expirado');
                        return redirect(RouteServiceProvider::CHANGEPASSWORD);
                    } else {
                        return $next($request);
                    }
                }
                break;
            case 3:
                Session::put('password', 'Inicio de Sesión por primera vez');
                return redirect(RouteServiceProvider::CHANGEPASSWORD);
                break;
            default:
                 return abort(500,"Peticion Detenida. Problemas de Seguridad Detectadosó");
                break;
        }
    }

}
