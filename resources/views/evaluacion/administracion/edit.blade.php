@extends('layouts.main')
@section('title', 'Evaluaciones|Administracion|Registradas')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i>
                        Edicion de Indicadores Operativos para el Cargo de <b> {{$segmento->RelationCargos->nombre_cargo}}</b>
                        <input type="hidden" id="nombrecargo" value="{{$segmento->RelationCargos->nombre_cargo}}">
                    </h3>
                </div>
                <form action="{{route('evaluacion.administracion.update',$segmento->id)}}" method="post" id="indicadores_update" class="form-horizontal form-label-left">                    
                    <div class="card-body">                    
                        @csrf 
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-1">
                                <div class="form-group">
                                    &nbsp;
                                    <div class="input-group">                                      
                                        <button type="button" class="btn btn-primary btn-block"
                                                id="add" 
                                                data-conteo="{{count($segmento->Relationindicadores)}}" 
                                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Agregar Nuevo Indicador"><i class="fas fa-plus-circle"></i></button>                                                    
                                    </div> 
                                </div>
                            </div>
                            <div id="pastecontent" class="col-md-11 justify-content-center">
                                @foreach($segmento->Relationindicadores as $keys => $segment)   
                                <input type="hidden" name="indicadoresPUT[]" value="{{$segment->id}}">
                                <div class="row" id='rowinputs{{$segment->id}}'>                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="i{{$segment->id}}">Indicador <span style="color:red">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="i{{$segment->id}}"
                                                       id="i{{$segment->id}}" required   value='{{$segment->indicador}}'                           
                                                       placeholder="Nombre Del Indicador">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label class="col-form-label" for="m{{$segment->id}}">Valor de la Meta <span style="color:red">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <input type="number" class="form-control" step="0.01" 
                                                           name="m{{$segment->id}}"
                                                           id="m{{$segment->id}}" required     
                                                           value='{{$segment->meta}}'  
                                                           placeholder="Numeros Enteros o con Decimales">
                                                </div>
                                                <select name="t{{$segment->id}}" id="t{{$segment->id}}" required class="form-control">   
                                                    <option value="">[SELECCIONE]</option>
                                                    <option value="1" @if($segment->tipo == "1") selected @endif>Numerico </option>
                                                    <option value="2" @if($segment->tipo == "2") selected @endif>Porcentual</option>                               
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">    
                                            &nbsp;
                                            <div class="input-group">                                      
                                                <button type="button" onclick="Rowdeleted({{$segment->id}})" class="btn btn-danger btn-block delete" 
                                                        id="deleted" 
                                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                        data-toggle="tooltip" data-placement="top" title="Eliminar Este Indicador">
                                                    <i class="fas fa-minus-circle"></i>
                                                </button>                                                                                                        
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center"  id="sendform">
                        <button type="submit"class="btn btn-lg btn-primary btn-block"  data-toggle="tooltip" data-placement="top" title="Procesar Indicadores"> <i class=" fa fa-save"></i></button>   
                    </div>
                    <input type="hidden" id="totalindicadores" name="totalindicadores" value="{{count($segmento->Relationindicadores)}}">
                </form>
            </div>
        </div>  
    </section> 

    <div id="copycontent" style="display:none;">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-form-label" for="label1">Tipo <span style="color:red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="label1"
                               id="label1" required                               
                               placeholder="Nombre Del Indicador">
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label class="col-form-label" for="label2">Valor <span style="color:red">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <input type="number" class="form-control" step="0.01" 
                                   name="label2"
                                   id="label2" required                               
                                   placeholder="Numeros Enteros o con Decimales">
                        </div>
                        <select name="label3" id="label3" required class="form-control">   
                            <option value="">[SELECCIONE]</option>
                            <option value="1">Numerico </option>
                            <option value="2">Porcentual</option>                            
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" name="nuevo" id="nuevo">
            <div class="col-md-1">
                <div class="form-group">    
                    &nbsp;
                    <div class="input-group">                                      
                        <button type="button" class="btn btn-danger btn-block delete" 
                                id="deleted" 
                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                data-toggle="tooltip" data-placement="top" title="Eliminar Este Indicador">
                            <i class="fas fa-minus-circle"></i>
                        </button>                                                                                                        
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('scripts')
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/evaluacion/evaluaciones.js')}}" type="text/javascript"></script>
@endpush