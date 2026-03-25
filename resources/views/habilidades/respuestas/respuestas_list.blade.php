@extends('layouts.main')
@section('title', 'Habilidades|Respuestas|Añadir')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_habilidades','menu-is-opening menu-open')
@section('menu_habilidades_eval','menu-is-opening menu-open')
@section('menu_habilidades_eval_active','active')
@section('evaluaciones.habilidades.respuestas','active')
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
                        <li class="breadcrumb-item">Respuestas</li>
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
                        Generar Respuestas para - <b>{{$pregunta->nombre_pregunta}}</b>
                    </h3>
                </div>   
                <div class='card-body'>
                    <form id="respuestas_store" method="post" action="{{route('habilidades.respuestas.store')}}" class="form-horizontal form-label-left">
                        @csrf
                        @method("PUT")
                        <input type="hidden" value="{{$evaluacion_id}}" name="evaluacion_id" id="evaluacion_id">
                        <input type="hidden" value="{{$categoria_id}}" name="categoria_id" id="categoria_id">
                        <input type="hidden" value="{{$pregunta_id}}" name="pregunta_id" id="pregunta_id">
                        <input type="hidden" value="{{$answers->count()}}" name="cantidad_respuestas" id="cantidad_respuestas">
                        <input type="hidden" value="{{$answers->count()}}" name="cantidad_respuestas_true" id="cantidad_respuestas_true">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="valor_max" class="col-form-label">Valor Maximo de la Pregunta <span class="required" style="color:red;"> * </span> </label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                        </div>                                          
                                        <input type="number" value="{{$pregunta->valor_pregunta}}" step="0.001" min="0.00" max="{{$pregunta->valor_pregunta}}" required class="required form-control valor-base" id="valor_pregunta" name="valor_pregunta" readonly>                                           
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary btn-block" id="add-answer" onclick="add_answer();" 
                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." ><i class="fa fa-head-side-cough"></i> Agregar Respuesta</button>
                                <input type="hidden" name="sumatoria" id="sumatoria">
                            </div>
                            @foreach($answers as $res => $answer)   
                            <div class="col-md-12" id="respuesta-{{$res+1}}">
                                <input type="hidden" name="cod_respuesta{{$res+1}}" id="cod_respuesta{{$res+1}}" value="{{$answer->cod_respuesta}}">
                                <div class="row respuesta">
                                    <input type="hidden" class="valor-base-h" id="valor-base-h" name="valor-base-h" value="{{$pregunta->valor_pregunta}}">
                                    <div class="col-md-5">
                                         <label for="nombre_respuesta{{$res+1}}" class="col-form-label">Respuesta <span class="required" style="color:red;"> * </span> </label>
                                        <div class="form-group">                                           
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                                </div>
                                                <input name="nombre_respuesta{{$res+1}}" value="{{$answer->nombre_respuesta}}" id="nombre_respuesta{{$res+1}}" required class="required form-control">
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="valor_respuesta{{$res+1}}" class="col-form-label">Valor <span class="required" style="color:red;"> * </span> </label>
                                        <div class="form-group">                                           
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                                </div>                                          
                                                <input type="number" step="0.001" min="0.00" placeholder="0.000" max="{{$pregunta->valor_pregunta}}" value="{{$answer->valor_respuesta}}" required class="required form-control valor-respuesta" id="valor_respuesta{{$res+1}}" name="valor_respuesta{{$res+1}}">                                           
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="deleted" class="col-form-label">&nbsp;</label> 
                                            <div class="input-group mb-3">                                      
                                                <button type="button" class="btn btn-warning btn-block delete" id="deleted" onclick="Rowdeleted({{$res+1}});" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." title="Eliminar Respuesta"><i class="fas fa-minus-circle"></i></button>                                                    
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div id="pastecontent" class="col-md-12"></div>
                             <div class="col-md-12">
                                 <button type="submit" class="btn btn-primary btn-block" id="guardar_respuestas" @if($answers->count() < 1) disabled @endif><i class="fa fa-save"></i> Guardar</button>
                             </div>
                        </div>
                    </form>
                </div>
            </div>                
        </div>
    </section>
    <div id="copycontent" style="display:none;">
        <div class="row respuesta">
            <input type="hidden" name="label3" id="label3">
            <div class="col-md-5">
                 <label for="label1" class="col-form-label">Tipo</label> <span class="required" style="color:red;"> * </span>
                <div class="form-group">                   
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        </div>
                        <input name="label1" id="label1" required class="required form-control" placeholder="Nombre de la Respuesta">
                    </div>  
                </div>
            </div>
            <div class="col-md-5">
                <label for="label2" class="col-form-label">Valor</label> <span class="required" style="color:red;"> * </span> 
                <div class="form-group">                
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                        </div>                                          
                        <input type="number" value="0.0" step="0.001" min="0.00" placeholder="0.000" max="{{$pregunta->valor_pregunta}}" required class="required form-control valor-respuesta" id="label2" name="label2">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-2">
                   <label for="" class="col-form-label">&nbsp;</label> 
                <div class="form-group">                 
                    <div class="input-group">                                      
                        <button type="button" class="btn btn-warning btn-block delete" id="deleted" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Eliminar Esta Respuesta"><i class="fas fa-minus-circle"></i></button>                                                    
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
<script src="{{asset('js/habilidades/respuestas.js')}}"></script>
@endpush