@extends('layouts.main')
@section('title', 'Editar Roles de usuario al Sistema')
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
                        <li class="breadcrumb-item">Roles</li>
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
                            <h3 class="card-title">Añadir Nuevo rol de usuario al sistema </h3>
                        </div>
                        <form action="{{route('roles.update',$role->id)}}" method="POST" id="roles" class="form-horizontal form-label-left" data-parsley-validate >                                                
                            @csrf
                             @method('PUT')
                            <!-- /.card-header -->
                            <div class="card-body text-right">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="name">Nombre del rol <span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="name" id="name" value='{{$role->name}}' required class="form-control"  value="{{old('name')}}" placeholder="Nombre del rol">                                           
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="slug">Identificador<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="slug" id="slug" required value='{{$role->slug}}' class="form-control"  value="{{old('slug')}}" placeholder="slug.slug">                                           
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="descripcion">Descripcion del Rol<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="descripcion" id="descripcion" value='{{$role->description}}' required class="form-control"  value="{{old('slug')}}" placeholder="Descripcion">                                           
                                    </div>
                                </div>                             

                                <div class="form-group row text-center">  
                                    <label class="col-sm-12 col-form-label" for="permisos">Asignación de Permisos<span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-12">
                                        <select name="permisos[]" id="permisos" multiple class="form-control select2 custom-select">                                                   
                                            <option value="">[Seleccione]</option>
                                            @foreach($modelos as $opgroup)
                                            <optgroup label="{{$opgroup}}"> 
                                                @foreach($permission as $key => $permisos)   
                                                @if($permisos->model === $opgroup)
                                                <option value="{{$permisos->id}}"  {{ in_array($permisos->id, $idpermission) ? 'selected="selected"' : '' }}">{{$permisos->name}}</option> 
                                                @endif                                            
                                                @endforeach
                                            </optgroup>
                                            @endforeach 
                                            <optgroup label="Sin Categoria"> 
                                                @foreach($permission as $key => $permisos)   
                                                @if($permisos->model === null)
                                                <option value="{{$permisos->id}}" {{ in_array($permisos->id, $idpermission) ? 'selected="selected"' : '' }}>{{$permisos->name}}</option> 
                                                @endif                                            
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>                                                           
                            </div>  
                            <div class="card-footer text-center">
                                <div class="row">
                                    <button type="submit" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="Guardar"><i class="fas fa-save"></i></button>
                                </div>
                            </div>
                        </form>
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
<script src="{{asset('js/roles/roles.js')}}" type="text/javascript"></script>
@endpush
