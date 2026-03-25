<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller {

    protected $redirectTo = '/home';

    public function __construct() {
        $this->middleware('guest')->except(['logout']);
    }

    public function index() {

        return view('login.index')->render();
    }

    public function logininit(Request $request) {
        if (User::where('username', '=', $request->username)->exists()) {
            if (User::where('username', '=', $request->username)->where('estatus_id', '=', 1)->orwhere('estatus_id', '=', 3)
                            ->exists()) {
                if (Auth::attempt(['username' => $request->username, 'password' => $request->password], null)) {
                    if (Auth::user()->email_verified_at == null && Auth::user()->estatus_id == 3) {
                        Auth::user()->markEmailAsVerified();
                    }
                   
                    if (!Auth::user()->roles()->exists()) {
                        Auth::logout();
                        abort(403, "Perfil de Acceso no encontrado, No puede Acceder en este momento.");
                    } else {                       
                        return redirect()->route('home');                                             
                        // return redirect()->route('home');
                    }
                } else {
                    \Session::flash('error', 'Usuario y/o contraseña incorrectos');
                    return back();
                }
            } else {
                \Session::flash('info', 'Usuario inactivo, comuniquese con el administrador del sistema.');
                return back();
            }
        } else {
            \Session::flash('error', 'El usuario no existe en la plataforma.');
            return back();
        }
    }
}
