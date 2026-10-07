@extends('layouts.main')
@section('title', 'Consultar Pagos Por Personal')
@section('menu_comisiones','menu-is-opening menu-open')
@section('comisiones','active')
@section('menu_reportes_comisiones','menu-is-opening menu-open')
@section('menu_reportes_comisiones_active','active')
@section('comisiones.export','active')
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
                        <li class="breadcrumb-item active">Consultar Pagos Por Personal</li>
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
                    <h3 class="card-title">Consultar Pagos Por Personal </h3>
                </div>
                <div class="card-body">
                    <form action="{{route('comisiones.export.process')}}" method="POST" id="donwloadreportes" class="form-horizontal form-label-left" enctype ='multipart/form-data'>
                        @csrf  
                        <div class="row mb-3">
                            <div class="col-md-6">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="area_trabajo_report">Gerencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <select name="area_trabajo_report" id="area_trabajo_report" required data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                            <option value="0">[TODOS]</option>
                                            @foreach($areas as $area)
                                            <option value="{{$area->id}}">{{$area->nombre_campana}}</option> 
                                            @endforeach   
                                        </select>                                           
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="carteras_report">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                        
                                        <select name="carteras_report" id="carteras_report" required class="form-control select2">                                                   
                                            <option value="0">[TODOS]</option>                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label  class="col-form-label" for="tipo_periodo_report">Tipo de Periodo</label><span style="color: red;"> *</span>
                                    <div class="input-group">                                       
                                        <select class="form-control select2" id="tipo_periodo_report" required name="tipo_periodo_report">
                                            <option value="todos" selected>[TODOS]</option>
                                            @foreach($esquema as $key => $esquemas)
                                            <option value="{{$key}}">{{$esquemas}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="cedula_empleado">Cedula del Empleado <span style="color:red"> *</span></label>                          
                                    <div class="input-group"> 
                                        <input type="text" name="cedula_empleado" id="cedula_empleado" class="form-control input-number" placeholder="123456789" required>
                                       
                                    </div>
                                </div> 
                            </div> 
                            <div class="col-md-12">                                           
                                <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..."
                                        data-toggle="tooltip" data-placement="top" title="Procesar Consolidado"><i class="fas fa-download"></i> Descargar</button>
                            </div> 
                        </div>                       
                    </form>   
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