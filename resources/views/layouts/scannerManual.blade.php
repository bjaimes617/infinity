<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
        <meta name="viewport" content="width= device-width, initial-scale=1">
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
        
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/scanner/style.css')}}">         
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">         
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">          
    </head>
    <body>
        @yield('content')
    </body>    
    <script src="{{asset('plugins/jquery/jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/adminlte.js')}}" type="text/javascript"></script>
    @include('notifications.index') 
    <script src="{{asset('plugins/scanner/adapter.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/scanner/vue.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/scanner/instascan.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/scanner/jquery-3.4.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/scanner/app.js')}}" type="text/javascript"></script>      
    @stack('script')
</html>
