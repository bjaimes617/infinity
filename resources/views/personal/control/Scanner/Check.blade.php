<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <title>{{env('APP_NAME')}} - Platform | @yield('title')</title>                 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/jquery-ui/jquery-ui.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap/css/bootstrap.css')}}">         
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('css/adminlte.css')}}">   
        <style>
            .circular--portrait {
                position: relative;
                width: 400px;
                height: 400px;
                overflow: hidden;
                border-radius: 50%;
                border: 10px solid #be3a8d;
                left: 50%;
                transform: translateX(-50%);
            }

            .circular--portrait img {
                width: 100%;
                height: auto;
            }

        </style>
    </head>
    <body tyle="background: #e1e1e1">        
        @if($visitante && $empleado != null) <!---VIENE DATOS Y ES VISITANTE--->
        @if($curso)
            @include('personal.control.Scanner.infoParticipantes')
            @else
            @include('personal.control.Scanner.infoVisitantes')
            @endif
        @elseif($visitante==false && $empleado != null) <!---VIENE DATOS Y ES EMPLEADO--->
            @if($empleado->estatus_id != 1 || date('H:i:s',strtotime(now())) <= $horaentrada || date('H:i:s',strtotime(now())) >= $empleado->horario_salida)
                <!---@include('personal.control.Scanner.restringido')---->
                  @if($completed == 0)
                 @include('personal.control.Scanner.incomplete')
                  @else
                @include('personal.control.Scanner.info')
                  @endif
            @else
                @if($paseasede == 1)
                     @if($completed == 0)
                 @include('personal.control.Scanner.incomplete')
                  @else
                @include('personal.control.Scanner.info')
                  @endif
                @else
                    @include('personal.control.Scanner.nosede')
                @endif        
            @endif
        @else <!---si no esta registrado--->
             @include('personal.control.Scanner.noregistrado')
        @endif
    </body>
</html>
    <!---<script type="text/javascript">
    $(document).ready(function() {
        setTimeout('location.reload()', 30000);
    });
    </script>--->