<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
        <meta name="author" content="Deivis Henriquez deivis(at)vozip.net and Braian Jaimes braian.jaimes(at)directagroup.net"/>
        <meta name="copyright" content="Powered by Deivis Henriquez and Braian Jaimes. <?php echo date('Y'); ?>">
        <title>{{env('APP_NAME')}} Platform | @yield('title')</title>
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/jquery-ui/jquery-ui.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap/css/bootstrap.css')}}">         
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('css/adminlte.css')}}">          
    </head>
    <body class="hold-transition login">
        <section class="page-content">
            <div class="login-page">  
                @yield('content')
            </div>            
        </section>
        <footer class="main-footer float-right" style="background: transparent; border-top-color: transparent; float: center">
            <small>&copy; {{date('Y',strtotime(now()))}} <a href="http://directagroup.net" target="_blank">Directa Group</a>. | {{env('APP_NAME')}} is Powered by Gerencia Ingeniería de Software. Para Soporte Escribanos a <a href="mailto:innovacion@directagroup.net?subject=Soporte Unity DG%20">Innovacion@directagroup.net</a></small>
        </footer>
    </body>
    <script src="{{asset('plugins/jquery/jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}" type="text/javascript"></script>    
    <script src="{{asset('plugins/jquery-validation/jquery.validate.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/jquery-validation/localization/messages_'.env('APP_LOCALE').'.js')}}"></script>
    <script src="{{asset('js/adminlte.js')}}" type="text/javascript"></script>             
    @include('notifications.index')
    @stack('scripts')
</html>