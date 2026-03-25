@extends('layouts.main')
@section('title', 'Gestion de personal|Reportes|Replicas')
@section('gestion_personal', 'menu-is-opening menu-open')
@section('gestion_personal_active', 'active')
@section('menu_personal', 'menu-is-opening menu-open')
@section('personal', 'active')
@section('personal.reportes', 'menu-is-opening menu-open')
@section('reportes', 'active')
@section('reporte.replicas', 'active')
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
                            <li class="breadcrumb-item">Gestión de Personal</li>
                            <li class="breadcrumb-item">Reportes</li>
                            <li class="breadcrumb-item active">Replicas</li>
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
                        <h3 class="card-title">Acceso a la Replica Diaria de Expedientes </h3>
                        <div class="btn-group float-right">
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">  
                        <form action="{{ route('reporte.replicas.view') }}" id="ShowFileReplicas" method="post" class="">
                            @csrf                                     
                            <div class="row mb-3">                                
                                <div class="col-md-6">
                                    <div class="form-group">                                        
                                        <label class="col-form-label" for="fecha">Periodo de Busqueda <span style="color:red">*</span></label>  
                                        <div class="input-group"> 
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <input type="text" name="fecha" id="fecha" autocomplete="off" required class="form-control mesdate">     
                                            <div class="input-group-prepend">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..."
                                                data-toggle="tooltip" data-placement="top" title="Buscar"><i
                                                    class="fa fa-search"></i></button>
                                            </div>                                                                                         
                                        </div> 
                                    </div>
                                </div>                             
                            </div>
                        </form>
                        <div class="row mb-3">     
                            <div class="col-md-12" style="display:none;" id="tablelistado">
                                <p>
                                <h6> Estos resultados son extraidos de forma integra del Listado de Personal de Directa Group
                                </h6>
                                </p>
                                <table id="datatable-replicas_expedientes"
                                    class="table compact table-sm text-sm table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Dia del mes Replicado</th>
                                            <th>Nombre del Archivo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.row -->
            <!-- /.container-fluid -->
        </section>
    </div>
@endsection
@push('styles')
    <link media="all" type="text/css" rel="stylesheet"href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">    
    <link media="all" type="text/css" rel="stylesheet"href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet"href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">

    <link media="all" type="text/css" rel="stylesheet"href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet"href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>


    <script src="{{ asset('plugins/select2/js/select2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/gestion_personal/gestion.js') }}" type="text/javascript"></script>
@endpush
