@extends('layouts.main')
@section('title', 'Cursos|Crear')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('cursosactive','active')
@section('menu.cursos.administracion','menu-is-opening menu-open')
@section('menu.cursos.administracion.active','active')
@section('cursos.create','active')
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
                        <li class="breadcrumb-item">Cursos</li>
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
                    <h3 class="card-title">Creación de Cursos para personal Nuevo Ingreso <small>Los campos marcados con  <span style="color:red;"> * </span>  son obligatorios</small></h3>
                    <p class="float-right">                                
                    </p>
                </div>
                <!-- /.card-header -->
                <form method="POST" action="{{route('cursos.store')}}" id="cursos_store"  class="form-horizontal form-label-left">
                    @csrf
                    <div class="card-body">                              
                        <div class="row mb-3">
                            <div class="col-sm-12">                                    
                                <div class="form-group">
                                    <label class=" col-form-label" for="fechacurso">Rango de Fecha <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="text" id="fechacurso" name="fechacurso" required class="form-control daterange">                                                   
                                    </div> 
                                </div>
                            </div>                                 
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="carteras">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="carteras" id="carteras" required data-href="{{route('selector.carteras')}}" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($carteras as $key => $cartera)
                                            <option value="{{$cartera->id}}">{{$cartera->nombre_cartera}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="skill" id="skill" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>                                                
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="horario">Horario <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                        </div>
                                        <select name="horario" id="horario" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($horario as $key => $horarios)
                                            <option value="{{$key}}">{{$horarios}}</option>   
                                            @endforeach
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-6 col-form-label" for="sede">Sede <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="sede" id="sede" required class="form-control select2" data-href="{{ route('cursos.search.salas') }}">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($sedes as $key => $sedes)
                                            <option value="{{$sedes->nombre_sede}}">{{$sedes->real_name}}</option>   
                                            @endforeach
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>   
                            <div class="col-sm-6 sala-input" style="display: none;">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="sala">Sala <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="sala" id="sala" required class="form-control select2" 
                                        data-href="{{ route('cursos.search.validate') }}">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                         
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>                             
                            <div class="col-md-6 region-estado" style="display: none;">
                                <div class="form-group">
                                    <label class="col-sm-6 col-form-label" for="region">Region <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                        </div>
                                        <select name="region" id="region" class="form-control select2" data-href="{{route('selector.estados')}}">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($regiones as $key => $region)
                                            <option value="{{$region->id}}">{{$region->nombre_region}}</option>   
                                            @endforeach
                                        </select>     
                                    </div> 
                                </div>                                    
                            </div>
                            <div class="col-md-6 region-estado" style="display: none;">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="estado">Estado <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                                        </div>
                                        <select name="estado" id="estado" class="form-control select2" data-href="{{route('selector.ciudades')}}">                                                   
                                            <option value="">[SELECCIONE]</option>                                               
                                        </select>    
                                    </div> 
                                </div>                                    
                            </div>
                            <div class="col-md-6 region-estado" style="display: none;">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="ciudad">Ciudad <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                                        </div>
                                        <select name="ciudad" id="ciudad" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>                                               
                                        </select>    
                                    </div> 
                                </div>                                    
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="convocado">Convocado Por <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="convocado" id="convocado" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($convocado as $key => $convocado)
                                            <option value="{{$key}}">{{$convocado}}</option>   
                                            @endforeach
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>     
                            <div class="col-sm-6">                                    
                                <div class="form-group">
                                    <label class=" col-form-label" for="requeridos">Nro. de Participantes <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-wave-square"></i></span>
                                        </div>
                                        <input type="text" id="requeridos" name="requeridos" maxlength="2" required class="form-control input-number">                                                   
                                    </div> 
                                </div>
                            </div>    
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="cargofac">Cargo Del Facilitador <span style="color:red"></span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="cargofac" id="cargofac" data-href="{{route('check.jefes')}}" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($cargos as $key => $cargo)
                                            <option value="{{$cargo->id}}">{{$cargo->nombre_cargo}}</option>   
                                            @endforeach
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="facilitador">Facilitador <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <select name="facilitador" id="facilitador" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>    

                                        </select>                                              
                                    </div> 
                                </div>
                            </div>                                
                        </div>                            
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-lg btn-block btn-primary"  data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
                    </div>
                </form>
            </div>                                  
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>

@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->

<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush