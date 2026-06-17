<?php

return [
    ///recursos para el reinicio de ocntraseña automatica
    'daysrestore'   => env('DAYS_RESTORE', 60),
    'lastpassword'  => env('LAST_PASSWORD', 3),

    ///recursos para el calculo de tiempos de contrato y recontratacion
    'contrato1'  => '90',
    'diaspararecontratar'   => '90',

    'diasCargarComisionesEgresados' => 50,
    ///cortes para los recibos de pago
    'cortes'    => ["1" => "1ra. Quincena", "2" => "2da. Quincena", "3" => "Todo el Mes", "4" => "Pago Unico"],
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    //// modulo de ARI
    'trimestres' => [
        '1' => 'Enero - Marzo',
        '2' => 'Abril - Junio',
        '3' => 'Julio - Septiembre',
        '4' => 'Octubre - Diciembre'
    ],

    //// recursos para los modulos personal y cursos generales.
    //ultimo valor 17
    'horariolaboral' => [
        '1'     => '7:00 AM a 1:00 PM',
        '2'     => '8:00 AM a 2:00 PM',
        '10'    => '8:00 AM a 4:00 PM',
        '3'     => '8:00 AM a 5:00 PM',
        '17'    => '8:30 AM a 05:30 PM',
        '4'     => '9:00 AM a 3:00 PM',
        '9'     => '9:00 AM a 6:00 PM',
        '11'    => '9:00 AM a 5:00 PM',
        '5'     => '10:00 AM a 4:00 PM',
        '16'    => "10:00 AM a 6:00 PM",
        '12'    => '11:00 AM a 7:00 PM',
        '13'    => '11:00 AM a 9:00 PM',
        '14'    => '11:00 AM a 8:00 PM',
        '15'    => "12:00 PM a 8:00 PM",
        '6'     => '1:00 PM a 7:00 PM',
        '7'     => '2:00 PM a 8:00 PM',
        '8'     => '3:00 PM a 9:00 PM'
    ],
    'especialidad'  => ['1' => 'BACHILLER', '2' => 'T.S.U', '3' => 'LIC', '4' => 'ING.', '6' => 'TECNICO MEDIO', '7' => 'MAGISTER', '5' => 'OTRO', '8' => 'ESTUDIANTE'],
    'contractual'   => ['1' => 'Contratado', '2' => 'Fijo', '3' => 'Freelance', '4' => 'Honorarios', '5' => 'Finalizado', '7' => 'Pendiente Por Contrato', '9' => 'Pendiente Por Documentacion'],
    'jornada'       => ["1" => "8 Horas", "2" => "6 Horas"],
    'tiemposContrato' => ["30" => "30", "60" => "60", "90" => "90", "120" => "120", "150" => "150", "180" => "180", "210" => "210", "240" => "240", "270" => "270"],

    'nacionalidad'      => ['' => '-', 'V' => 'V', 'E' => 'E', 'P' => 'P'],
    'hijos'             => ['' => '[Seleccione]', '0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'],
    'horarioTransporte' => ["1" => "AM", "2" => "PM", "3" => "Ambos"],
    'tipoContrato'      => ["1" => "Periodo de Prueba", "2" => "Encargado", "3" => "Titular"],
    ///cursos
    'horariocursos'     => ['1' => '08:00AM - 04:00PM', '2' => '09:00AM - 04:00PM', '3' => '10:00AM - 03:00PM', '4' => '10:00AM - 05:00PM', '5' => '11:00AM - 05:00PM'],
    'convocadocursos'   => ['1' => 'Reclutamiento', '2' => "Operaciones"],
    'EstatusCursos'     => ['0' => 'Cerrado', '1' => "Abierto", '2' => "Inactivo"],
    ///recursos para el modulo de inventario.
    'EstatusDisks'          => ['1' => 'OPERATIVO', '2' => 'CRITICO', '3' => 'NO OPERATIVO'],
    'tipoposicion'          => ['1' => 'Administrativa', '2' => 'Operativa'],
    'EstatusEtiqueta'       => ['1' => 'No Impreso', '2' => 'Impresa', '3' => 'Dañada'],
    'TipoEquipo'            => ["1" => "LAPTOP", "2" => "PC", "3" => "SRV", "4" => "AllOne"],
    'Perifericos'           => ["1" => "TECLADO", "2" => "MOUSE", "3" => "HEATSET", "4" => "T_VIDEO", "5" => "T_INALAMBRICA", "6" => "CORNETAS", "7" => "ADAPTADORES"],
    ///Si se añade algo nuevo en perifericos, incluir en el array del reporte
    'ReportePerifericos'    => ["Posición", "TECLADO", "Cantidad", "MOUSE", "Cantidad", "HEATSET", "Cantidad", "T._VIDEO", "Cantidad", "T._INALAMBRICA", "Cantidad", "CORNETAS", "Cantidad", "ADAPTADORES", "Cantidad"],

    'ConexionPerifericos'       => ["1" => "USB - (Universal Serial Bus)", "2" => "PS/2 - (Personal System/2)", "3" => "JACK 3.5mm", "4" => "PCI Express", "5" => "AGP - (Puerto de Graficos Acelerado)", "6" => "Entrada/Salida"],
    'TipoCable'                 => ["1" => "CABLE DIRECTO", "2" => "ADAPTADOR"],
    'TipoReportesInventarios'   => ["1" => "Contab. Componentes, Equipos, Monitores y Perifericos", "2" => "Detalles por Posiciones", "3" => "Inventario General todas las Sedes (Se omite el Stock)"],
    'TipoUploadsInventarios'    => ["1" => "Stock de Monitores", "2" => "Stock de Equipos", "3" => "Registro de Posiciones"],



    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'America/Caracas',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'es',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'es',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Webwizo\Shortcodes\ShortcodesServiceProvider::class,
        /*
         * 
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,

        Laraveles\Spanish\SpanishServiceProvider::class,
        jeremykenedy\LaravelRoles\RolesServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [
        'PDF'       => Barryvdh\DomPDF\Facade::class,
        'QrCode'    => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
        'Shortcode' => Webwizo\Shortcodes\Facades\Shortcode::class,
        'App'       => Illuminate\Support\Facades\App::class,
        'Arr'       => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
    ],

];
