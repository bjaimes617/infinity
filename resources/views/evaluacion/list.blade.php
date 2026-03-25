@extends('layouts.main')
@section('title', 'Gestion de personal|Evaluaciones|Listado')
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
                    <strong>Listado de Personal a Evaluar</strong>
                    <div class="alert-text">
                        Seleccione el personal asignado para realizar la evaluación <b>
                            @if($tipo_periodo == "Diario")
                            Diaria 
                            @else
                            {{strtoupper($tipo_periodo)}}
                            @endif </b>
                    </div>    
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class='col-md-12'>
                            <table id="Tablepersonal" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Cedula</th>
                                        <th>Nombre</th>
                                        <th>Cargo</th>
                                        <th>Skill</th>
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

<div class="modal fade" id="editpersonal" tabindex="-1" aria-labelledby="editpersonal_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="exampleModalXlLabel">Evaluacion del Personal</h5>
                <button class="btn-close btn-sm btn btn-danger" type="button"  class="close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">                
                <div class="card">
                    <div class="card-header"><strong>Datos del Personal evaluado</strong></div>
                    <div class="card-body">
                        <div class="example">
                            <div class="tab-content rounded-bottom">
                                <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-912">
                                    <div class="col-md-12">
                                        @if(strtolower($tipo_periodo) == "semanal")
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <label>Nombre del Personal</label>
                                                    <input class="form-control" type="text" id="editnombre_personal" name="nombre_personal" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <label>Mes y año</label>
                                                    <input class="form-control" type="text"  value="{{$mes}}" disabled>
                                                </div>
                                            </div>                                               
                                            <div class="col-md-6">
                                                <label>Skill</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_skill}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Cargo</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_cargo}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        @if($semana != null)
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label>Numero de Semana</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$fecha_evaluacion}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @elseif(strtolower($tipo_periodo) == "mensual")
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <label>Nombre del Personal</label>
                                                    <input class="form-control" type="text" id="editnombre_personal" name="nombre_personal" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <label>Mes y año</label>
                                                    <input class="form-control" type="text"  value="{{$mes}}" disabled>
                                                </div>
                                            </div>                                        
                                            <div class="col-md-6">
                                                <label>Skill</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_skill}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Cargo</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_cargo}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif(strtolower($tipo_periodo) == "quincenal")
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <label>Nombre del Personal</label>
                                                    <input class="form-control" type="text" id="editnombre_personal" name="nombre_personal" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <label>Mes y año</label>
                                                    <input class="form-control" type="text"  value="{{$mes}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <label>Periodo</label>
                                                    @if($quincena == 1)
                                                    <input class="form-control" type="text" value="1era Quincena" disabled>
                                                    @else
                                                    <input class="form-control" type="text" value="2da Quincena" disabled>
                                                    @endif
                                                </div>
                                            </div>                                        
                                            <div class="col-md-6">
                                                <label>Skill</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_skill}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Cargo</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_cargo}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <label>Nombre del Personal</label>
                                                    <input class="form-control" type="text" id="editnombre_personal" name="nombre_personal" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Fecha de Evaluación</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$fecha_evaluacion}}" disabled>
                                                </div>
                                            </div>                                        
                                            <div class="col-md-6">
                                                <label>Skill</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_skill}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Cargo</label>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="text" value="{{$nombre_cargo}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                   
                <form action='{{route('evaluacion.register')}}' id="indicador_form"  method='POST'>
                    @csrf
                    <input type="hidden" id="editpersonal_id" name="personal_id">
                    <input type="hidden" id="editcargo_id" name="cargo_id">
                    <input type="hidden" id="editskill_id" name="skill_id">      
                    <input type="hidden" name="fecha_evaluacion" value="{{$fecha_evaluacion}}" id="fecha_evaluacion">
                    <input type="hidden" name="count" id="count" value="{{$indicador_count}}">
                    <input type="hidden" name="tipo_periodo" id="tipo_periodo" value="{{$tipo_periodo}}">
                    <input type="hidden" name="num_semana" id="num_semana" value="{{$num_semana}}">
                    <input type="hidden" name="num_quincena" id="num_quincena" value="{{$quincena}}">
                    <input type="hidden" id="cartera" name="cartera" value="{{$cartera}}">
                    <input type="hidden" id="area_sel" name="area_sel" value="{{$area_sel}}">                   
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Indicadores que se deben evaluar.</strong></div>
                                <div class="card-body">
                                    <div class="example">
                                        <div class="tab-content rounded-bottom">
                                            <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-912">
                                                <div class="table-responsive">
                                                    <table id="indicador" class="table table-sm table-striped text-center table-bordered" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Indicador</th>
                                                                <th>Meta</th>
                                                                <th>Puntaje <span style="color:red">*</span></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php $i = 1; @endphp
                                                        @foreach($indicadores as $indicador)                                                       
                                                        <input type="hidden" name="indicador_id{{$i}}" id="indicador_id{{$i}}" value="{{$indicador->id}}">                                                         
                                                        <input type="hidden" name="meta{{$i}}" id="meta{{$i}}" value="{{$indicador->meta}}">
                                                        <tr>
                                                            <td>{{$indicador->indicador}}</td>
                                                            <td>{{$indicador->meta}} @if($indicador->tipo == 1) % @endif</td>
                                                            <td class="form-group">                                                               
                                                                <div class="input-group"> 
                                                                    <input type="number" name="puntaje{{$i}}" id="puntaje{{$i}}" class="form-control" step=".01" placeholder="0.00" min="0.00" @if($indicador->tipo == 1) max="100" @endif autocomplete="off" required>
                                                                </div>                                                                
                                                            </td>
                                                        </tr>
                                                        @php $i++; @endphp
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='card-footer'>
                                 <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
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
@include('evaluacion.partials.DatatableEvaluacion')
@endpush
