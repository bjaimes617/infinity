@extends('layouts.login')
@section('title','Inicio de Session')
@section('content') 
<div class="login-box">
    <div class="card card-outline card-primary">          
        <div class="card-header text-center">
            <a class="h1"><img src="{{asset('img/LOGO DG AZULYMAGENTA.png')}}" alt="DirectaGroupLogo Logo" width="290"></a>
        </div>
        <form action="{{route('loginin')}}" id="inicio" method="post">
            @csrf
            <div class="card-body">
                <p class="login-box-msg">Conectate con nuestra plataforma</p>            
                <div class="form-group">
                    <div  class="input-group">                   
                        <input type="text" name="username" id="username" required class="form-control" value="{{old('username')}}" placeholder="Usuario">
                        <div class="input-group-append"> 
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>                    
                    </div>  
                </div>   
                <div class="form-group">
                    <div  class="input-group">
                        <input type="password" name="password" id="password" required class="form-control"  value="{{old('password')}}" placeholder="Contraseña">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>                
                        </div>        
                    </div>
                </div>
                <div class="row float-right">            
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Conectar <i class="fas fa-sign-in-alt"></i></button>
                    </div>
                    <!-- /.col -->
                </div>
                <p class="mb-1">      
                    @if(ENV('SERVICE_MAIL_ACTIVE')) 
                    <a href=#" data-toggle="modal"  data-target="#modal-default">Olvide mi Contraseña</a> 
                    @endif                  
                </p> 
            </div>     
        </form>        
    </div>
    
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <img class="" src="{{asset('img/LOGO DG AZULYMAGENTA.png')}}" alt="DirectaGroupLogo Logo" width="210">
                </div>
                <form action="{{route('password.email')}}" id="form_email" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="login-box-msg">Recuperar Contraseña</p>
                        <div class="form-group">
                            <div  class="input-group">
                                <input type="email" name="email" id="email" @if(!ENV('SERVICE_MAIL_ACTIVE')) disabled @endif required class="form-control"  value="{{old('email')}}" placeholder="Example@example.com">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </div>                
                                </div>        
                            </div>
                        </div>
                        <p class="text-center">Para Restaurar tu contraseña de acceso, ingresa por favor tu dirección de correo electronico registrado.</p>                                                        
                        <p class="text-center">@if(!ENV('SERVICE_MAIL_ACTIVE')) 
                            {{config('mail.service_mail_status.false.text1')}}
                            <span  style="{{config('mail.service_mail_status.false.option')}}"><b>{{config('mail.service_mail_status.false.text2')}}</b></span>
                            {{config('mail.service_mail_status.false.text3')}}
                            @endif
                        </p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" @if(!ENV('SERVICE_MAIL_ACTIVE')) disabled @endif >Procesar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
@endsection
@Push('scripts')
<script src="{{asset('js/login/validate.init.js')}}" type="text/javascript"></script>
@endpush