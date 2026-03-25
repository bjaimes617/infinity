@extends('layouts.main')
@section('title', 'Cursos|Editar')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('cursosactive','active')
@section('menu.cursos.administracion','menu-is-opening menu-open')
@section('menu.cursos.administracion.active','active')
@section('cursos.index','active')
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
                        <li class="breadcrumb-item active">Editar</li>
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
                <form method="POST" action="{{route('cursos.update',$curso->id)}}" id="cursos_store"  class="form-horizontal form-label-left">
                    @csrf
                    @method('PUT')
                    <div class="card-body">                              
                        <div class="row">
                            <div class="col-sm-12">                                    
                                <div class="form-group">
                                    <label class=" col-form-label" for="fechacurso">Rango de Fecha <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="text" id="fechacurso" value="{{ date('d/m/Y',strtotime($curso->inicio))." - ".date('d/m/Y',strtotime($curso->finaliza))}}" name="fechacurso" required class="form-control daterange">                                                   
                                    </div> 
                                </div>
                            </div>                                 
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="carteras">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="carteras" id="carteras" required data-href="{{route('selector.carteras')}}" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($carteras as $key => $cartera)
                                            <option value="{{$cartera->id}}" @if($cartera->id == $curso->cartera_id) Selected @endif>{{$cartera->nombre_cartera}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="skill" id="skill" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>   
                                            @foreach($skill as $key => $skills)
                                            <option value="{{$skills->id}}" @if($skills->id == $curso->skill_id) Selected @endif>{{$skills->nombre_skill}}</option>   
                                            @endforeach 
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="horario">Horario <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                        </div>
                                        <select name="horario" id="horario" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($horario as $key => $horarios)
                                            <option value="{{$key}}" @if($key == $curso->horario) Selected @endif>{{$horarios}}</option>   
                                            @endforeach
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>                              
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="sede">Sede <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="sede" id="sede" required class="form-control select2"  data-href="{{route('selector.ciudades')}}">                                                    
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($sedes as $key => $sede)
                                            <option value="{{$sede->nombre_sede}}" @if($sede->nombre_sede == $curso->sede) Selected @endif>{{$sede->real_name}}</option>   
                                            @endforeach
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>                                 

        
                            <div class="col-md-12 region-estado"  @if($regionselect == null) style="display: none;" @endif>
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="region">Region <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                        </div>
                                        <select name="region" id="region" class="form-control select2" data-href="{{route('selector.estados')}}">                                                   
                                            <option value="">[SELECCIONE]</option>    
                                            @foreach($regiones as $region)
                                            <option value="{{$region->id}}" @if($region->id === $regionselect) selected @endif>{{$region->nombre_region}}</option>   
                                            @endforeach
                                        </select>     
                                    </div> 
                                </div> 

                            </div>
                            <div class="col-md-6 region-estado"  @if($regionselect == null) style="display: none;" @endif>
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="estado">Estado <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                                        </div>
                                        <select name="estado" id="estado" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option> 
                                            @if(!empty($estados))
                                            <option value="{{$estados->id}}" selected>{{$estados->nombre_estado}}</option>                                           
                                            @endif
                                        </select>    
                                    </div> 
                                </div>                                    
                            </div>
                            <div class="col-md-6 region-estado"  @if($regionselect == null) style="display: none;" @endif>
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="ciudad">Ciudad <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                                        </div>
                                        <select name="ciudad" id="ciudad" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option> 
                                            @if(!empty($ciudad))
                                            <option value="{{$ciudad->id}}" selected>{{$ciudad->ciudad}}</option>  
                                            @else
                                             <option value="">[SELECCIONE]</option> 
                                            @endif
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
                                            <option value="{{$key}}" @if($key == $curso->convodado) Selected @endif>{{$convocado}}</option>   
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
                                        <input type="text" id="requeridos" value="{{$curso->requeridos}}" name="requeridos" maxlength="2" required class="form-control input-number">                                                   
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
                                            <option value="{{$facilitador->id}}" selected>{{$facilitador->primer_nombre}} {{$facilitador->primer_apellido}}</option>                                                                                       
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>                                
                        </div>                            
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-lg btn-block btn-primary"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
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
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->

<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush