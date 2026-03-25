@extends('layouts.main')
@section('title', 'Evaluaciones|Administracion|Registradas')
@section('menu_evaluaciones','menu-is-opening menu-open')
@section('evaluaciones','active')
@section('menu_evaluaciones_administracion','menu-is-opening menu-open')
@section('menu_evaluaciones_administracion_active','active')
@section('evaluaciones.administracion.index','active')
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
                        <li class="breadcrumb-item">Administracion</li>
                        <li class="breadcrumb-item active">Registrados</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">
            <div class='card'>
                <div class="card-header">
                    <h3 class="card-title">Evaluaciones de personal Registradas </h3>                                                                        
                </div>
                <div class='card-body'>
                    <form action='{{route('evaluacion.administracion.show')}}' method='POST' id="ShowEvaluaciones" class='form-horizontal form-label-left'>
                        <div class='row mb-3'>                        
                            @csrf     
                            <div class="col-md-4">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="cargo">Cargos Asignados <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                     
                                        <select name="cargo" id="cargo" data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                            <option value="">[TODOS]</option>
                                            @foreach($cargo as $cargos)
                                            <option value="{{$cargos->id}}">{{$cargos->nombre_cargo}}</option> 
                                            @endforeach   
                                        </select>                                           
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-4">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">

                                        <select name="area_trabajo" id="area_trabajo" data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                            <option value="">[TODOS]</option>
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
                                        <select name="carteras" id="carteras" data-href="{{route('selector.carteras')}}" class="form-control select2">                                                   
                                            <option value="">[TODOS]</option>                                                
                                        </select>     
                                        <div class="input-group-prepend">
                                            <button type="submit" class="btn btn-primary" id="busqueda" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                        </div>
                                    </div> 
                                </div>
                            </div>                    
                        </div>
                    </form>                 
                    <div class="row mb-3">
                        <div class="col-md-12" style="display:none;" id="tablelistado">                                                           
                            <h6>  Estos resultados son extraidos de forma integra del Listado de Personal de Directa Group </h6>                              
                            <table id="datatable-evaluaciones" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>                                            
                                        <th>Gerencia</th>
                                        <th>Cartera/Campaña</th>
                                        <th>Segmento/Skill</th>
                                        <th>Cargo aplicado</th>                                    
                                        <th>Cant. de Indicadores</th>
                                        <th>Tipo de Periodo</th>
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
    </section>    
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/evaluacion/evaluaciones.js')}}" type="text/javascript"></script>
@endpush