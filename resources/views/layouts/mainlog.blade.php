<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
        <meta name="author" content="Deivis Henriquez deivis(at)vozip.net and Braian Jaimes braian.jaimes(at)directagroup.net"/>
        <meta name="copyright" content="Powered by Deivis Henriquez and Braian Jaimes. <?php echo date('Y'); ?>">
        <!-- CSRF Token -->
        <meta name="csrf-token" id="_token" content="{{ csrf_token() }}">
        <title> {{env('APP_NAME')}} | Log del Sistema</title>
        @stack('styles')            
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-scroller/css/scroller.bootstrap4.css')}}">  
        <link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/toastr/toastr.min.css')}}">
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('css/adminlte.css')}}">  
        
        
    </head>
    <body class="hold-transition layout-fixed text-sm">
        <div class="content">

            <!-- Preloader    
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{asset('img/logo directa.png')}}" alt="DirectaGroupLogo" height="50" width="100">
            </div> -->  
            <nav class="navbar navbar-expand navbar-white navbar-light">
                @include('partials.headers')
            </nav>      
            <section class="content">               
                @yield('content')                
            </section>   
            @include('partials.footer')
        </div>
    </body>    
    <script src="{{asset('js/adminlte.js')}}" type="text/javascript"></script>                 
</html>
