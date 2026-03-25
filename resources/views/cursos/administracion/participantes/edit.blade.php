@extends('layouts.main')
@section('title', 'Participante|Editar')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('menu.cursos.participantes','menu-is-opening menu-open')
@section('cursosactive','active')
@section('buscar.index.participantes','active')
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
                        <li class="breadcrumb-item">Participantes</li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">                            
                    <h3 class="card-title">Edición del Participante <small>Los campos marcados con  <span style="color:red;"> * </span>  son obligatorios</small></h3>
                    <p class="float-right">                                
                    </p>
                </div>                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="POST" id="create-participantes"  action="{{route('participante.update',$participante->id)}}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="cedula">Cedula <span style="color:red">*</span></label>  
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div>
                                                <input type="text" id="cedula" name="cedula" value="{{$participante->cedula_empleado}}" autocomplete="off" required class="form-control input-number">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="telefono_movil">Telefono <span style="color:red">*</span></label>  
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                </div>
                                                <input type="text" id="telefono_movil" name="telefono_movil"  value="{{$participante->telefono_movil}}" autocomplete="off" required class=" input-number form-control">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="primer_nombre">Primer Nombre <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <input type="text" id="primer_nombre" name="primer_nombre" value="{{$participante->primer_nombre}}" autocomplete="off" required class="form-control">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="segundo_nombre">Segundo Nombre </label>  
                                            <div class="input-group">
                                               
                                                <input type="text" id="segundo_nombre" name="segundo_nombre"  value="{{$participante->segundo_nombre}}" autocomplete="off" class="form-control">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="primer_nombre">Primer Apellido <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <input type="text" id="primer_apellido" name="primer_apellido" value="{{$participante->primer_apellido}}" autocomplete="off" required class="form-control">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="segundo_nombre">Segundo Apellido </label>  
                                            <div class="input-group">
                                               
                                                <input type="text" id="segundo_apellido" name="segundo_apellido"  value="{{$participante->segundo_apellido}}" autocomplete="off" class="form-control">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="mes">Estatus del Participante <span style="color:red">*</span></label>  
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                </div>
                                                <select name="estatus_id" id="estatus_id" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option> 
                                                    @foreach($estatus as $key => $estatu)
                                                    <option value="{{$estatu->id}}" @if($participante->estatus_id == $estatu->id) selected @endif >{{$estatu->estatus}}</option> 
                                                    @endforeach
                                                </select>                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Actualizar"><i class="fa fa-sync"></i></button>                                                    
                                    </div>
                                </div>            
                            </form>
                        </div>            
                    </div>
                </div>
            </div>       
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush

@push('scripts') 
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush