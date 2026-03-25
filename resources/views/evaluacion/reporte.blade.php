@extends('layouts.main')
@section('title', 'Gestion de personal|Evaluaciones|Reportes')

@section('menu_evaluaciones','menu-is-opening menu-open')
@section('evaluaciones','active')

@section('menu_evaluaciones_administracion','menu-is-opening menu-open')
@section('menu_evaluaciones_administracion_active','active')

@section('menu_evaluaciones_administracion_reportes','menu-is-opening menu-open')
@section('menu_evaluaciones_administracion_reportes_active','active')

@section('evaluaciones','active')
@section('evaluacion.reporte','active')
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
                        <li class="breadcrumb-item">Evaluaciones</li>
                        <li class="breadcrumb-item active">Reporte</li>
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
                    <h3 class="card-title">Reporteria De Evaluaciones Realizadas</h3>  
                </div>
                <!-- /.card-header -->
                <div class="card-body">  
                    <form action='{{route('evaluacion.reporte.download')}}' method='POST' id="create-report" class="form-horizontal">                            
                        @csrf                         
                        <div class="row mb-3">
                            <div class="col-md-4">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <select name="area_trabajo" id="area_trabajo" required  data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($areas as $area)
                                            <option value="{{$area->id}}">{{$area->nombre_campana}}</option> 
                                            @endforeach   
                                        </select>                                           
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="carteras">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                   
                                        <select name="carteras" id="carteras"  required data-href="{{route('selector.carteras')}}" class="form-control select2">
                                            <option value="">[SELECCIONE]</option>                                             
                                        </select>   
                                    </div> 
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                    <div class="input-group">                                   
                                        <select name="skill" id="skill" required data-href="{{route('evaluacion.buscador.cargos')}}" class="form-control select2">
                                            <option value="">[SELECCIONE]</option>
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="cargo">Cargo <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                   
                                        <select name="cargo" id="cargo"  required class="form-control select2">
                                              <option value="">[SELECCIONE]</option>
                                        </select>   
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="rangofecha">Fecha de Evaluación <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                   
                                        <input id="rangofecha" name="rangofecha" required class="form-control rangofechas"> 
                                    </div> 
                                </div>
                            </div>                            
                            <div class="col-sm-12">
                                <div class="form-group">                                      
                                    <div class="input-group mb-3">                                      
                                        <button type="submit" class="btn btn-primary btn-block" id="busquedalp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Descargar"><i class="fa fa-download"></i></button>                                                    
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
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
<script src="{{asset('js/evaluacion/evaluaciones.js')}}" type="text/javascript"></script>
@endpush
