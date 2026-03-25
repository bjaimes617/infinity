@extends('layouts.main')
@section('title', 'Inventario|Reporteria')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones_administracion','menu-is-opening menu-open')
@section('inventario.administracion.reportes','menu-is-opening menu-open')
@section('inventario.administracion.menu_reportes','active')
@section('inventario.administracion','active')
@section('inventario.administracion.reportes.generar','active')
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
                        <li class="breadcrumb-item">Inventario</li>
                        <li class="breadcrumb-item">Administracion</li>
                        <li class="breadcrumb-item active">Reportes</li>
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
                    <h3 class="card-title">Carga Masiva de Inventario</h3>                            
                </div>
                <form action="{{route('inventario.migracion.administracion.reportes.store')}}" id="reporteGenerate" method="POST" class="form-horizontal  form-label-left">
                     @csrf
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="col-form-label" for="stock">Stock a Cargar <span style="color:red">*</span></label>                                 
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                </div>
                                                <select name="stock" id="stock" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($sede as $key => $item)
                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                    @endforeach                                                
                                                </select>                                             
                                            </div> 
                                        </div>
                                    </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="col-form-label" for="type">Tipo de Reporte <span class="required" style="color:red;">*</span></label>
                                    <div class="input-group">
                                        <select name="type" id="type" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>                                            
                                            @foreach($tipo as $key => $tip)
                                            <option value="{{$key}}"> {{$tip}}</option>
                                            @endforeach
                                        </select> 
                                         <div class="input-group-prepend">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Descargar"><i class="fa fa-download"></i></button>                                                    
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div> 
                    </div> 
                </form>
            </div> 
        </div> 
    </section>
</div>
@endsection

@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
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

<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>

<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>

<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/inventario/inventario.js')}}"></script>
@endpush