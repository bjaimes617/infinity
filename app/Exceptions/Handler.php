<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;

use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
            //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register() {
        
        $this->reportable(function (Throwable $exception) {
            
        });
    }

    public function render($request, Throwable $exception) {
        $userLevelCheck = $exception instanceof \jeremykenedy\LaravelRoles\App\Exceptions\RoleDeniedException ||
                $exception instanceof \jeremykenedy\LaravelRoles\App\Exceptions\RoleDeniedException ||
                $exception instanceof \jeremykenedy\LaravelRoles\App\Exceptions\PermissionDeniedException ||
                $exception instanceof \jeremykenedy\LaravelRoles\App\Exceptions\LevelDeniedException;

        if ($userLevelCheck) {
            if ($request->expectsJson()) {
                return Response::json(array(
                            'error' => 403,
                            'message' => 'Unauthorized.'
                                ), 403);
            }
            abort(403,$exception->getMessage());
        }

        return parent::render($request, $exception);
    }
    
    protected function unauthenticated($request, AuthenticationException $exception)
{
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Token inválido o no proporcionado'], 401);
    }

    // Para peticiones no JSON, puedes redirigir o abortar
    abort(401, 'No autorizado');
}
}
