@extends('layouts.main')
@section('title', 'Añadir Permisos del Sistema')
@section('content')
<div class="content-wrapper">    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
                        <li class="breadcrumb-item">Permisos</li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Añadir Nuevo permiso al sistema </h3>
                        </div>
                        <form action="{{route('permissions.store')}}" method="POST" id="permission" class="form-horizontal form-label-left" data-parsley-validate >                        
                           @csrf
                            <!-- /.card-header -->
                            <div class="card-body text-center">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="name">Nombre del Permiso <span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="name" id="name" required class="form-control"  value="{{old('name')}}" placeholder="Nombre del permiso">                                           
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="modulo">Modulo <span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="modulo" id="modulo" required class="form-control"  value="{{old('modulo')}}" placeholder="Modulo del sistema">                                           
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="accion">Acción <span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="accion" id="accion" required class="form-control"  value="{{old('accion')}}" placeholder="Accion a realizar">                                           
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="subaccion">Sub Acción</label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="subaccion" id="subaccion" class="form-control"  value="{{old('subaccion')}}" placeholder="Sub Accion a realizar">                                           
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="descripcion">Descripcion <span style="color:red">*</span></label>                                 
                                    <div  class="input-group col-md-5">
                                        <input type="text" name="descripcion" id="descripcion" required class="form-control"  value="{{old('descripcion')}}" placeholder="Descripcion del permiso">                                           
                                    </div>
                                </div>                  
                            </div>        
                            <input type="hidden" id="slug" name="slug">
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
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
@endpush
@push('scripts')
<script src="{{asset('js/permissions/permisos.js')}}" type="text/javascript"></script>
@endpush
