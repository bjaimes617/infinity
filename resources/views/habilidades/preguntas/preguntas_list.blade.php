@extends('layouts.main')
@section('title', 'Habilidades|Preguntas|Añadir')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_habilidades','menu-is-opening menu-open')
@section('habilidades','active')
@section('evaluaciones.habilidades.preguntas','active')
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
                        <li class="breadcrumb-item">Habilidades</li>
                        <li class="breadcrumb-item">Preguntas</li>
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
                        Generar Preguntas para la Categoria - <b>{{$categoria->nombre_categoria}}</b>
                    </h3>
                </div> 
                <div class="card-body">
                    <form id="preguntas_store" method="post" action="{{route('habilidades.preguntas.store')}}" class="form-horizontal form-label-left">
                        @csrf
                        @method('PUT')
                        <input type="hidden" value="{{$evaluacion->id}}" name="evaluacion_id" id="evaluacion_id">
                        <input type="hidden" value="{{$categoria->id}}" name="categoria_id" id="categoria_id">
                        <input type="hidden" value="{{$questions->count()}}" name="cantidad_preguntas" id="cantidad_preguntas">
                        <input type="hidden" value="{{$questions->count()}}" name="cantidad_preguntas_true" id="cantidad_preguntas_true">
                        <input type="hidden" value="" name="total_sum" id="total_sum">
                        <input type="hidden" name="codigos" id="codigos">
                        <input type="hidden" name="id_ques" id="id_ques">
                        <div class="row mb-3 category">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valor_max" class="col-form-label">Valor Maximo <span class="required" style="color:red;"> * </span> </label> 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                        </div>                                          
                                        <input type="number" value="{{$categoria->valor_max}}" step="0.001" min="0.00" required class="required form-control valor-base" id="valor_max" name="valor_max">                                           
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distribucion" class="col-form-label">Distribución Categoria <span class="required" style="color:red;"> * </span> </label>
                                    <div class="input-group">                                       
                                        <select id="distribucion" name="distribucion" class="form-control select2" required onchange="distribuir();">
                                            <option value="1" @if($categoria->distribucion == 1) selected @endif>Automatica</option>
                                            <option value="2" @if($categoria->distribucion == 2) selected @endif>Manual</option>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success btn-block" id="add-question" onclick="add_question();" 
                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." ><i class="fa fa-question"></i> Agregar Pregunta</button>
                                <input type="hidden" name="sumatoria" id="sumatoria">
                            </div>
                            @foreach($questions as $preg => $question)
                            <div  class="col-md-12" id="pregunta-{{$preg+1}}">
                                <input type="hidden" name="cod_pregunta{{$preg+1}}" id="cod_pregunta{{$preg+1}}" value="{{$question->cod_pregunta}}">
                                <input type="hidden" name="id_pregunta{{$preg+1}}" id="id_pregunta{{$preg+1}}" value="{{$question->id}}">
                                <div class="row pregunta">
                                    <input type="hidden" class="valor-base-h" id="valor-base-h" name="valor-base-h" value="{{$categoria->valor_max}}">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="nombre_pregunta{{$preg+1}}" class="col-form-label">Pregunta <span class="required" style="color:red;"> * </span> </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                                </div>
                                                <input name="nombre_pregunta{{$preg+1}}" value="{{$question->nombre_pregunta}}" id="nombre_pregunta{{$preg+1}}" required class="required form-control">
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="valor_pregunta{{$preg+1}}" class="col-form-label">Valor  <span class="required" style="color:red;"> * </span> </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                                </div>                                          
                                                <input type="number" step="0.001" min="0.00" value="{{$question->valor_pregunta}}" @if($categoria->distribucion == 1) readonly @endif required class="required form-control valor-pregunta" id="valor_pregunta{{$preg+1}}" name="valor_pregunta{{$preg+1}}">                                           
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="deleted" class="col-form-label">&nbsp;</label> 
                                            <div class="input-group mb-3">                                      
                                                <button type="button" class="btn btn-danger btn-block delete" id="deleted" onclick="Rowdeleted({{$preg+1}});" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." title="Eliminar Pregunta"><i class="fas fa-minus-circle"></i></button>                                                    
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div id="pastecontent" class="col-md-12"></div>
                            <div id="pastecontent" class="col-md-12"></div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-block" id="guardar_preguntas" @if($questions->count() < 1) disabled @endif><i class="fa fa-save"></i> Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>                   
        </div>
        <!-- /.container-fluid-->
    </section>
    <div id="copycontent" style="display:none;">
        <div class="row mb-3 pregunta">
            <input type="hidden" name="label3" id="label3">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="label1" class="col-form-label">Tipo</label> <span class="required" style="color:red;"> * </span>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        </div>
                        <input name="label1" id="label1" required class="required form-control" placeholder="Nombre de la Pregunta">
                    </div>  
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="label2" class="col-form-label">Valor</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                        </div>                                          
                        <input type="number" value="0.0" step="0.001" min="0.00" required class="required form-control valor-pregunta" oninput="sumar();" id="label2" name="label2">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="" class="col-form-label">&nbsp;</label> 
                    <div class="input-group">                                      
                        <button type="button" class="btn btn-danger btn-block delete" id="deleted" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Eliminar Esta Pregunta"><i class="fas fa-minus-circle"></i></button>                                                    
                    </div> 
                </div>
            </div>
        </div>
    </div>
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
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/habilidades/preguntas.js')}}"></script>
@endpush