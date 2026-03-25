@extends('layouts.main')
@section('title', 'Usuarios del Sistema|Perfil de Usuario')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Seguridad</li>
                        <li class="breadcrumb-item">Usuarios</li>
                        <li class="breadcrumb-item active">Perfil</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">                        
                        <div class="card-body">  

                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle"
                                             src="{{Auth()->user()->RelationToPersonal != null ? \Storage::disk('personal')->url(\Auth()->user()->RelationToPersonal->image) : asset('img/user.png') }}"
                                             alt="User profile picture">
                                    </div>
                                    <h3 class="profile-username text-center">{{Auth::User()->name}}</h3>
                                    <p class="text-muted text-center">{{Auth::User()->roles()->first()->name}}</p>
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">                                         
                                            <b>Token Autenticación Externa: </b> <a class="float-right"> {!! Auth::User()->tokens()->first() != null ? $tokens : "" !!}</a>
                                        </li>                                     
                                        <li class="list-group-item">
                                            <b>Posee Usuario en Autoservicio: </b> 
                                            <a class="float-right">
                                                @switch($validationUser)                                            
                                                @case(false)
                                                <span class="badge badge-warning">NO ACTIVO</span>
                                                @break
                                                @case(true)
                                                <span class="badge badge-success">ACTIVO</span>
                                                @break
                                                @case(0)
                                                  <span class="badge badge-danger">CONEXION NO DISPONIBLE</span>
                                                @break
                                                @endswitch
                                            </a>
                                        </li>  
                                        <li class="list-group-item">
                                            <b>Ultima Actualización de Contraseña: </b> <a class="float-right">{{Auth::User()->password_updated_at != null ? date('d/m/Y',strtotime(Auth::User()->password_updated_at)) : "-"}}</a>
                                        </li>                                        
                                    </ul>
                                    <div class="text-center"><h3> Credenciales de Usuario</h3></div>
                                    <br>
                                    <div class="text-center"><h6>@if(Session::has('password')){{Session::get('password')}}@endif. <br> {{trans('passwords.password.text')}}</h6> </div>
                                    <form action="{{route('users.password.update')}}" method="POST" id="form_update_password" >                                    
                                        @csrf
                                        <br>
                                        <div class="form-group">
                                            <div  class="input-group">
                                                <input type="password" name="password" id="password" required                                                       
                                                       data-check="{{route('users.checknewpassword')}}"                                                             
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
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
                                        </div>
                                    </form> 
                                </div>                                
                            </div> 

                        </div>

                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
            <!-- /.card -->

        </div>
    </section>
</div>
@endsection
@push('scripts')
<script src="{{asset('js/user/password.js')}}" type="text/javascript"></script>
@endpush
