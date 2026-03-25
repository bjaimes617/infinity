<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\recibos\AsigDeducModel;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller {
    
    public function __construct() {
        
        
        $this->middleware(['auth', 'verified', 'check.password']);
    }
    
    public function index() {       
       
       
         $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

         if (!$user->roles()->exists()) {
            abort(403, "Perfil de Acceso no encontrado, No puede Acceder.");
        } else {
            return view('home.index')->render();
        }
    }
}