@extends('layouts.main')
@section('title', 'Editar Usuario al Sistema')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
                        <li class="breadcrumb-item">Usuarios</li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Editando Credenciales del Usuario</h3>
                        </div>                        
                        <form action="{{route('users.update',$user->id)}}" method="POST" id="users" class="form-horizontal form-label-left" data-parsley-validate >                                                
                            @method('PUT')
                            @csrf                        
                            <!-- /.card-header -->
                            <div class="card-body text-right">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="name">Nombre Y Apellido <span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-6">
                                        <input type="text" name="name" id="name" value="{{$user->name}}" required class="form-control"  value="{{old('name')}}" placeholder="Nombre y Apellido">                                           
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="cedula">Cedula de Identidad<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-6">
                                        <input type="number" name="cedula" id="cedula" value="{{$user->cedula}}" required class="form-control"  value="{{old('cedula')}}" placeholder="Cedula de identidad">                                           
                                    </div>
                                </div>                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="username">Usuario de Acceso<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-6">
                                        <input type="text" name="username" id="username" disabled value="{{$user->username}}" required class="form-control"  value="{{old('username')}}" placeholder="Usuario">                                           
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="email">Correo Electronico<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-6">
                                        <input type="email" name="email" id="email" required value="{{$user->email}}" class="form-control"  value="{{old('email')}}" placeholder="XX@directagroup.net">                                           
                                    </div>
                                </div> 
                                    <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="estatus">Estatus<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-6">
                                        <select name="estatus" id="estatus" required class="form-control">         
                                            <option value="">SELECCIONE</option>
                                            @foreach(['1'=>'Activo','2'=>'Inactivo','3'=>"Nuevo"] as $key => $estatus)   
                                            <option value="{{$key}}" @if($key === $user->getRaworiginal('estatus_id')) selected @endif >{{$estatus}}</option>                                        
                                            @endforeach
                                        </select>
                                    </div>
                                </div>                                 
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="roles">Asignación de Rol<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-6">
                                        <select name="roles" id="roles" required class="form-control select2" data-href="{{route('users.permissions.add')}}">         
                                            <option value="">SELECCIONE</option>
                                            @foreach($roles as $key => $rol)   
                                            <option value="{{$rol->id}}" @if(in_array($rol->id, $roleuser)) selected @endif >{{$rol->name}}</option>                                        
                                            @endforeach
                                        </select>
                                    </div>
                                </div> 
                                <p class="text-center">Podra Asignar Permisos adicionales al usuario que no se encuentren asociados al rol Seleccionado.</p>
                                <div class="form-group row">                                   
                                    <label class="col-sm-3 col-form-label" for="permisos">Permisos Adicionales</label>                                 
                                    <div  class="input-group col-md-6">
                                        <select name="permisos[]" id="permisos" data-allow-clear="true" @if(empty($permisosadicionales)) disabled @endif multiple class="form-control select2">                                                   
                                            <option value="">[Seleccione]</option>
                                            @foreach($modelos as $opgroup)
                                            <optgroup label="{{$opgroup}}"> 
                                                @foreach($permission as $key => $permisos)   
                                                @if($permisos->model === $opgroup)
                                                <option value="{{$permisos->id}}" 
                                                        @if(in_array($permisos->id, $permisosadicionales)) selected @endif 
                                                    @if(in_array($permisos->id, $permisosrol)) disabled @endif 
                                                    >{{$permisos->name}}</option> 
                                                @endif                                            
                                                @endforeach
                                            </optgroup>
                                            @endforeach 
                                            <optgroup label="Sin Categoria"> 
                                                @foreach($permission as $key => $permisos)   
                                                @if($permisos->model === null)
                                                <option value="{{$permisos->id}}" 
                                                        @if(in_array($permisos->id, $permisosadicionales)) selected @endif 
                                                    @if(in_array($permisos->id, $permisosrol)) disabled @endif 
                                                    >{{$permisos->name}}</option> 
                                                @endif                                            
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <p class="text-center"><span style="color:red;"><b>Atención:</b></span> Para Seleccionar mas permisos en simultaneo, presiones la tecla <b>CONTROL+CLIC</b></p>
                                <hr>
                                
                                <div class="row ">
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <div class="form-group row"> 
                                            <div class="col-sm-12 float-center">
                                                <div class="icheck-primary d-inline float-left">
                                                    <input type="checkbox" id="resetpassword" name="resetpassword" data-parsley-required-message='Este campo es obligatorio'>
                                                    <label for="resetpassword">
                                                        Reinicio de Contraseña
                                                    </label>
                                                </div>
                                            </div>

                                        </div> 
                                    </div>  
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <div class="form-group row"> 
                                            <div class="col-sm-12 float-center">
                                                <div class="icheck-primary d-inline float-left">
                                                    <input type="checkbox" id="autorizaskill" name="autorizaskill" data-parsley-required-message='Este campo es obligatorio'>
                                                    <label for="autorizaskill">
                                                        Autorización de Skills
                                                    </label>
                                                </div>
                                            </div>
                                            <div class='col-md-12 text-center'>
                                                @if($carterasautorizadas) <span style="color:red"><b>Alerta:</b></span>Este usuario posee autorización a ciertos Segmentos/Skills.@endif
                                            </div>
                                        </div> 
                                    </div>     
                                </div>
                                <hr>                                
                                @permission('update.users')
                                <div class="card-footer text-center">
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="Guardar"><i class="fas fa-save"></i></button>
                                    </div>
                                </div>
                                @endpermission
                        </form>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
</div>
<!-- /.container-fluid -->
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.css')}}">  
@endpush
@push('scripts')
<script src="{{asset('plugins/select2/js/select2.js')}}"></script>
<script src="{{asset('js/user/users.js')}}" type="text/javascript"></script>
@endpush
