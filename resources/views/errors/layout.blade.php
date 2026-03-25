<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
        <title>@yield('title')</title>
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}"> 
        <link media="all" type="text/css" rel="stylesheet" href="{{asset('css/adminlte.css')}}">       
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 2rem;
                padding: 20px;
            }
            .contenedor {
                display: flex; /* Activa Flexbox */
                justify-content: center; /* Centra horizontalmente */
                align-items: center; /* Centra verticalmente */
                height: 100%; /* Asegúrate de que el contenedor tenga altura definida */
            }

            .contenedor img {
                width: 50%;
                max-width: 50%; /* Ajusta el tamaño de la imagen al contenedor */
                height: auto; /* Mantén la proporción de la imagen */
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class='contenedor'>
                    @yield('images_error')
                </div>
                <div class="title">                   
                    <p>@yield('message')</p>                  
                    @auth
                    <a href="{{url()->previous()}}" class="btn btn-primary"><i class="fas fa-chevron-circle-left"></i> Regresar</a>
                    @endauth
                    @guest
                    <a href="{{route('login')}}" class="btn btn-primary"> <i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                    @endguest
                </div>                 
            </div>
        </div>
    </body>
    <script src="{{asset('js/adminlte.js')}}" type="text/javascript"></script>
</html>
