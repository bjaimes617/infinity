<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
        <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
        <meta name="author" content="Deivis Henriquez deivis(at)vozip.net and Braian Jaimes braian.jaimes(at)directagroup.net"/>
        <meta name="copyright" content="Powered by Deivis Henriquez and Braian Jaimes. <?php echo date('Y'); ?>">
        <!-- CSRF Token -->       
        <meta name="csrf-token" id="_token" content="{{ csrf_token() }}">
        <title>{{env('APP_NAME')}} - Platform | @yield('title')</title>                 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/jquery-ui/jquery-ui.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap/css/bootstrap.css')}}">         
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('css/adminlte.css')}}">     
        @stack('styles')             
    </head>
    <body>
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{asset('img/logo directa.png')}}" alt="DirectaGroupLogo" height="50" width="90">
        </div>
        <!-- /.navbar --> 
        @yield('content') 
        <!-- el contect tiene la finalizacion del content wrapper -->
    </body>    
    <script src="{{asset('plugins/jquery/jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}" type="text/javascript"></script>    
    <script src="{{asset('plugins/jquery-validation/jquery.validate.js')}}" type="text/javascript"></script>   
    <script src="{{asset('plugins/jquery-validation/localization/messages_'.env('APP_LOCALE').'.js')}}"></script>    
    @include('notifications.index')  
    <script src="{{asset('js/adminlte.js')}}" type="text/javascript"></script>
    @stack('scripts')  
</html>