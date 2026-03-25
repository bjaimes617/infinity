@extends('layouts.login')
@section('title','Inicio de Session')
@section('content') 
<div class="card card-outline card-primary">          
    <div class="card-header text-center">
        <a href="" class="h1"><img src="{{asset('img/LOGO DG AZULYMAGENTA.png')}}" alt="DirectaGroupLogo Logo"  height="55" width="210" style="opacity: .8"></a>
    </div>
    <form   @auth
             action="{{route('users.password.update')}}"
             @else 
             action="{{route('password.request')}}"
             @endauth
             method="POST" id="form_update_password">        
        @csrf
        <div class="card-body">
            @guest
            <input type="hidden" name="email" value="{{ $email }}">          
            <input type="hidden" name="token" value="{{ $token }}">
            @endauth
            <div class="text-center"><b>@if(Session::has('password')){{Session::get('password')}}@endif.</b> <br> Por politicas de seguridad es necesario que ingrese una nueva contraseña, <br> la misma debe contener una letra mayuscula, un número y como mínimo 8 caracteres de longitud.</div>
            <br>
            <div class="form-group">
                <div  class="input-group">
                    <input type="password" name="password" id="password" required 
                           @auth
                           data-check="{{route('users.checknewpassword')}}"
                           @else 
                           data-check="{{route('guest.checknewpassword',$email)}}"
                           @endauth           
                           class="form-control"  value="{{old('password')}}" placeholder="Contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>                
                    </div>        
                </div>
            </div>
            <div class="form-group">
                <div  class="input-group">
                    <input type="password" name="confirmpassword" id="confirmpassword" required                                       
                           class="form-control"  value="{{old('confirmpassword')}}" placeholder="Confirme su Contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>                
                    </div>        
                </div>
            </div>            
        </div>
        <div class="card-footer">            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-block" >Conectar</button>
            </div>
            <!-- /.col -->
        </div>
    </form> 
</div>
@endsection
@push('styles')
<!--Custom Style-->

@endpush
@push('scripts')
<script src="{{asset('js/user/password.js')}}" type="text/javascript"></script>
@endpush