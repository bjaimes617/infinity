@extends('layouts.main')
@section('title', 'Gestion de Participantes')
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
                        <li class="breadcrumb-item">Gestión de Participantes</li>
                        <li class="breadcrumb-item active">Listado</li>
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
                    <h3 class="card-title">Buscador de Participante </h3>
                </div>
                <!-- /.card-header -->               
                <div class="card-body">   
                    <div class="row mb-3">
                        <div class="col-md-12 ">
                            <form method="POST" action="{{route('buscador.listado.participante')}}" id="ParticipantesBuscador"  class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">                                    
                                        <div class="form-group">
                                            <label class="col-form-label" for="area_trabajo">Cedula del Participante <span style="color:red">*</span></label>                                 
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div>
                                                <input type="text" name="cedula" id="cedula" class="form-control input-number">                                         
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">                                    
                                        <div class="form-group">
                                            <label class="col-form-label" for="area_trabajo">Nombre del Participante <span style="color:red">*</span></label>                                 
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div>
                                                <input type="text" name="nombre_participante" id="nombre_participante" class="form-control">                                         
                                                <div class="input-group-prepend">
                                                    <button type="submit" class="btn btn-primary" id="busquedapt" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>                        
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-12" id="tablelistado" style="display:none;">                                     
                                    <hr>
                                    <p><h6> Estos resultados son extraidos de forma integra.</h6></p>  
                                    <div class='row'>
                                        <div class='col-md-12'>
                                            <table id="datatableParticipantesCursos" class="text-sm table table-condensed compact table-bordered table-hover text-center">
                                                <thead>
                                                    <tr>                                            
                                                        <th>Cedula</th>                                                
                                                        <th>Nombre Participante</th>
                                                        <th>Telefono Movil</th>
                                                        <th>Ultimo Curso</th>
                                                        <th>Estatus</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>                                    
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                                                           
                </div>
            </div>
            <div class="modal fade" id="viewhistory" tabindex="-1" role="dialog" aria-labelledby="viewhistory_modal" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Historial del Participante <small> <label id="name" ></label></small></h5>&nbsp;
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-history">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">  

                                <table id="datatable-historial" class="table table-bordered table-hover text-center">
                                    <thead>
                                        <tr>                                            
                                            <th>Curso</th>                                                
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Estatus Final</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close-history">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->

<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush