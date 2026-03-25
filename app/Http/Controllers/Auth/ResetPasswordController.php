<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Models\PasswordUpdate;
use Illuminate\Http\Request;
use App\Models\User;

class ResetPasswordController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset requests
      | and uses a simple trait to include this behavior. You're free to
      | explore this trait and override any methods you wish to tweak.
      |
     */

use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    public function checkNewPassword(Request $request, $email) {

        if ($request->ajax()) {
            $user = User::where('email', $email)->first();
            $lastPassword = PasswordUpdate::where('user_id', '=', $user->id)->orderBy('id', 'desc')->take(config('app.lastpassword'))->get();
            if (count($lastPassword) > 0 && $user->estatus_id != 3) {
                foreach ($lastPassword as $pass) {
                    if (\Hash::check($request->password, $pass->password)) {
                        return 'false';
                    }
                }
                return 'true';
            } else {
                return 'true';
            }
        }
    }
}
