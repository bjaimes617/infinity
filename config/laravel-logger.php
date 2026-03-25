<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Database Settings
    |--------------------------------------------------------------------------
    */

    'loggerDatabaseConnection'  => env('LARAVEL_LOGGER_DATABASE_CONNECTION', env('DB_CONNECTION', 'mysql')),
    'loggerDatabaseTable'       => env('LARAVEL_LOGGER_DATABASE_TABLE', 'laravel_logger_activity'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Roles Settings - (laravel roles not required if false)
    |--------------------------------------------------------------------------
    */

    'rolesEnabled'   => env('LARAVEL_LOGGER_ROLES_ENABLED', false),
    'rolesMiddlware' => env('LARAVEL_LOGGER_ROLES_MIDDLWARE', 'role:admin'),

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Laravel Logger Middlware
    |--------------------------------------------------------------------------
    */

    'loggerMiddlewareEnabled'   => env('LARAVEL_LOGGER_MIDDLEWARE_ENABLED', true),
    'loggerMiddlewareExcept'    => array_filter(explode(',', trim(env('LARAVEL_LOGGER_MIDDLEWARE_EXCEPT')))),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Authentication Listeners Enable/Disable
    |--------------------------------------------------------------------------
    */
    'logAllAuthEvents'      => false,   // May cause a lot of duplication.
    'logAuthAttempts'       => false,   // Successful and Failed -  May cause a lot of duplication.
    'logFailedAuthAttempts' => true,    // Failed Logins
    'logLockOut'            => true,    // Account Lockout
    'logPasswordReset'      => true,    // Password Resets
    'logSuccessfulLogin'    => true,    // Successful Login
    'logSuccessfulLogout'   => true,    // Successful Logout

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Search Enable/Disable
    |--------------------------------------------------------------------------
    */
    'enableSearch'      => env('LARAVEL_LOGGER_ENABLE_SEARCH', 'false'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Search Parameters
    |--------------------------------------------------------------------------
    */
    // you can add or remove from these options [description,user,method,route,ip]
    'searchFields'  => env('LARAVEL_LOGGER_SEARCH_FIELDS', 'description,user,method,route,ip'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Default Models
    |--------------------------------------------------------------------------
    */

    'defaultActivityModel' => env('LARAVEL_LOGGER_ACTIVITY_MODEL', 'jeremykenedy\LaravelLogger\App\Models\Activity'),
    'defaultUserModel'     => env('LARAVEL_LOGGER_USER_MODEL', 'App\Models\User'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Default User ID Field
    |--------------------------------------------------------------------------
    */

    'defaultUserIDField' => env('LARAVEL_LOGGER_USER_ID_FIELD', 'id'),

    /*
    |--------------------------------------------------------------------------
    | Disable automatic Laravel Logger routes
    | If you want to customise the routes the package uses, set this to true.
    | For more information, see the README.
    |--------------------------------------------------------------------------
    */

    'disableRoutes' => env('LARAVEL_LOGGER_DISABLE_ROUTES', false),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Pagination Settings
    |--------------------------------------------------------------------------
    */
    'loggerPaginationEnabled' => env('LARAVEL_LOGGER_PAGINATION_ENABLED', true),
    'loggerPaginationPerPage' => env('LARAVEL_LOGGER_PAGINATION_PER_PAGE', 25),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Databales Settings - Not recommended with pagination.
    |--------------------------------------------------------------------------
    */

    'loggerDatatables'              => env('LARAVEL_LOGGER_DATATABLES_ENABLED', true),
    'loggerDatatablesCSScdn'        => env('APP_URL').'public/plugins/datatables-responsive/css/responsive.bootstrap4.css',
    'loggerDatatablesresponsible'   => env('APP_URL').'public/plugins/datatables-responsive/js/dataTables.responsive.js',
    'loggerDatatablesinit'          => env('APP_URL').'public/plugins/datatables/jquery.dataTables.js',
    'loggerDatatablesJSboots'       => env('APP_URL').'public/plugins/datatables-bs4/js/dataTables.bootstrap4.js',

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Dashboard Settings
    |--------------------------------------------------------------------------
    */

    'enableSubMenu'     => env('LARAVEL_LOGGER_DASHBOARD_MENU_ENABLED', false),
    'enableDrillDown'   => env('LARAVEL_LOGGER_DASHBOARD_DRILLABLE', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Failed to Log Settings
    |--------------------------------------------------------------------------
    */

    'logDBActivityLogFailuresToFile' => env('LARAVEL_LOGGER_LOG_RECORD_FAILURES_TO_FILE', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Flash Messages
    |--------------------------------------------------------------------------
    */

    'enablePackageFlashMessageBlade' => env('LARAVEL_LOGGER_FLASH_MESSAGE_BLADE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blade settings
    |--------------------------------------------------------------------------
    */

    // The parent Blade file
    'loggerBladeExtended'       => env('LARAVEL_LOGGER_LAYOUT', 'layouts.mainlog'),

    // Switch Between bootstrap 3 `panel` and bootstrap 4 `card` classes
    'bootstapVersion'           => env('LARAVEL_LOGGER_BOOTSTRAP_VERSION', '4'),

    // Additional Card classes for styling -
    // See: https://getbootstrap.com/docs/4.0/components/card/#background-and-color
    // Example classes: 'text-white bg-primary mb-3'
    'bootstrapCardClasses'      => 'card',

    // Blade Extension Placement
    'bladePlacement'            => env('LARAVEL_LOGGER_BLADE_PLACEMENT', 'content'),
    'bladePlacementCss'         => env('LARAVEL_LOGGER_BLADE_PLACEMENT_CSS', 'styles'),
    'bladePlacementJs'          => env('LARAVEL_LOGGER_BLADE_PLACEMENT_JS', 'scripts'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Logger Dependencies - allows for easier builds into other projects
    |--------------------------------------------------------------------------
    */
    // jQuery
    'enablejQueryCDN'           => env('LARAVEL_LOGGER_JQUERY_CDN_ENABLED', true),
    'JQueryCDN'                 => env('LARAVEL_LOGGER_JQUERY_CDN_URL',  env('APP_URL').'public/plugins/jquery/jquery.min.js'),

    // Bootstrap
    'enableBootstrapCssCDN'     => env('LARAVEL_LOGGER_BOOTSTRAP_CSS_CDN_ENABLED', true),
    'bootstrapCssCDN'           => env('LARAVEL_LOGGER_BOOTSTRAP_CSS_CDN_URL', env('APP_URL').'public/plugins/bootstrap/css/bootstrap.css'),
    'enableBootstrapJsCDN'      => env('LARAVEL_LOGGER_BOOTSTRAP_JS_CDN_ENABLED', true),
    'bootstrapJsCDN'            => env('LARAVEL_LOGGER_BOOTSTRAP_JS_CDN_URL', env('APP_URL').'public/plugins/bootstrap/js/bootstrap.js'),
    'enablePopperJsCDN'         => env('LARAVEL_LOGGER_POPPER_JS_CDN_ENABLED', true),
    'popperJsCDN'               => env('LARAVEL_LOGGER_POPPER_JS_CDN_URL',  env('APP_URL').'public/plugins/popper/popper.js'),

    // Font Awesome
    'enableFontAwesomeCDN'      => env('LARAVEL_LOGGER_FONT_AWESOME_CDN_ENABLED', true),
    'fontAwesomeCDN'            => env('LARAVEL_LOGGER_FONT_AWESOME_CDN_URL',  env('APP_URL').'public/plugins/fontawesome-free/css/all.min.css'),

];
