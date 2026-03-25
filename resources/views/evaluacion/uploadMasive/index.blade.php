@extends('layouts.main')
@section('title', 'Gestion de personal|Evaluaciones|Masiva')
@section('menu_evaluaciones','menu-is-opening menu-open')
@section('evaluaciones','active')
@section('evaluacion.masiva.index','active')
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
                        <li class="breadcrumb-item">Evaluaciones</li>
                        <li class="breadcrumb-item active">Carga Masiva</li>
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
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i>
                        Carga Masiva de Evaluaciones
                    </h3>
                </div>    
                <div class='card-body'>
                    <form action='{{route('evaluacion.masiva.process')}}' method='POST' id="evaluacion_masiva" class='form-horizontal form-label-left' enctype ='multipart/form-data'>
                        <div class='row mb-3'>                        
                            @csrf                            
                            <div class="col-md-4">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <select name="area_trabajo" id="area_trabajo" required data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
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
                                        <select name="skill" id="skill" required data-href="{{route('cargos.evaluar.permisos')}}" class="form-control select2">
                                            <option value="">[SELECCIONE]</option>
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="cargo">Cargos a Evaluar <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                   
                                        <select name="cargo" id="cargo" required data-href="{{route('bring.periodo')}}" class="form-control select2">
                                            <option value="">[SELECCIONE]</option>
                                        </select>   
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="fechaeval">Fecha de Evaluacion <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                   
                                        <input type="text" name="fechaeval" id="fechaeval" value="{{date('m/Y',strtotime(now()))}}" autocomplete="off" class="form-control mesdate" data-href="{{route('selector.semana')}}">
                                        <div class="input-group-prepend" id="appendperiodo" style="display: none;">
                                            <select class="form-control" id="periodo" name="periodo">
                                                <option value="">[CORTE]</option>
                                                <option value="1">1era Quincena</option>
                                                <option value="2">2da Quincena</option>
                                            </select>  
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="archivo">Archivo a Procesar... <span class="required" style="color:red;">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                            <label class="custom-file-label" for="archivo">Buscar Archivo en...</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary cargamasiva" disabled ><i class="fa fa-upload"></i> Procesar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>   
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button"class="btn btn-lg btn-warning btn-block" 
                                id="generar_formato" onclick="generar();"  data-href="{{route('generar.formato')}}"
                                data-toggle="tooltip" data-placement="top" title="Descarga el Formato de evaluacion Seleccionado"><i class="fas fa-file"></i> Generar Formato</button>   
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush
@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/evaluacion/evaluaciones.js')}}" type="text/javascript"></script>
@endpush
