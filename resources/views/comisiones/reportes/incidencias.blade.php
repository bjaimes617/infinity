@extends('layouts.main')
@section('title', 'Exportar Incidencias')
@section('menu_comisiones','menu-is-opening menu-open')
@section('comisiones','active')
@section('menu_reportes_comisiones','menu-is-opening menu-open')
@section('menu_reportes_comisiones_active','active')
@section('reporte.incidencias','active')
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
                        <li class="breadcrumb-item">Gestión de Comisiones</li>
                        <li class="breadcrumb-item active">Exportar Incidencias</li>
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
                    <h3 class="card-title">Exportar Incidencias Bancarias </h3>
                </div>
                <div class="card-body">
                    <form action="{{route('comisiones.incidencias.show')}}" method="POST" id="showreporteincidencias" class="form-horizontal form-label-left" enctype ='multipart/form-data'>
                        @csrf  
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="auditmes">Periodo <span style="color:red"> *</span></label>                          
                                    <div class="input-group"> 
                                        <input type="text" name="periodo" id="periodo" autocomplete="false" class="form-control timeaudit" required>    
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div> 
                            </div>                           
                        </div>                       
                    </form>  
                    <div class="row mb-3">                       
                        <div class="col-md-12 tableaudit" style="display:none;">                                                           
                            <p><h6>  Estos resultados son extraidos de forma integra</h6></p>                               
                            <table id="datatable-incidencias" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>                                   
                                <th>Lote</th>
                                <th>Periodo</th>
                                <th>Banco</th>
                                <th>Fecha Pago</th> 
                                <th>Estatus</th>
                                <th>Acciones</th>      
                                </thead>
                            </table>                                
                        </div>

                    </div>
                </div>
            </div>        
        </div>        
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
@endpush

@push('scripts')   
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/comisiones/comisiones.js')}}" type="text/javascript"></script>
@endpush