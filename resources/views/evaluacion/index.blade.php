@extends('layouts.main')
@section('title', 'Evaluaciones|Crear')
@section('menu_evaluaciones','menu-is-opening menu-open')
@section('evaluaciones','active')
@section('evaluacion.create','active')
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
                        <li class="breadcrumb-item active">Crear</li>
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
                        Crear Evaluación
                    </h3>
                </div>    
                <div class='card-body'>
                    <form action='{{route('evaluacion.consult.personal')}}' method='POST' id="CreateEvaluacionPersonal" class='form-horizontal form-label-left'>
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
                                <button type="submit"class="btn btn-lg btn-primary btn-block"  data-toggle="tooltip" data-placement="top" title="Procesar Indicadores"> <i class=" fa fa-search"></i></button>   
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
<script src="{{asset('js/evaluacion/evaluaciones.js')}}" type="text/javascript"></script>
@endpush
