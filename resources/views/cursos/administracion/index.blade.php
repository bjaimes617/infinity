@extends('layouts.main')
@section('title', 'Cursos|Listado')
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
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">           
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-header">                            
                            <h3 class="card-title">Listado de Cursos Planificados <small>La modificación de los cursos solo se podra realizar antes de dar inicio.</small></h3>
                            <a href="{{route('cursos.create')}}" class="btn btn-primary float-right" id="add" data-toggle="tooltip" data-placement="top" title="Añadir"><i class="fa fa-plus-circle"></i></a>
                        </div>
                        <div class="card-body">                             
                            <div class="row">                               
                                <div class="col-md-12">
                                    <form method="POST" action="{{route('cursos.search')}}" id="searchcursos"  class="form-horizontal">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-group">                                        
                                                    <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                        </div>
                                                        <input type="text" id="fecha" name="fecha" required class="form-control daterange">                                            
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                                      
                                                        <select name="skill" id="skill" class="form-control select2" style="width: 100%;">                                                   
                                                            <option value="">[TODOS]</option> 
                                                            @foreach($skills as $skill)
                                                                @if($skill->Relationskill != null)
                                                                <option value="{{$skill->skill_id}}">{{$skill->Relationskill->nombre_skill}}</option> 
                                                                @endif
                                                            @endforeach
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="estatus">Estatus Del Curso <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                                      
                                                        <select name="estatus" id="estatus" class="form-control select2" style="width: 100%;">                                                   
                                                            <option value="">[TODOS]</option> 
                                                            @foreach($estatus as $key => $estatus)
                                                            <option value="{{$key}}">{{$estatus}}</option> 
                                                            @endforeach
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                      
                                                    <div class="input-group">                                      
                                                        <button type="submit" class="btn btn-primary btn-block" id="busquedalp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12" id="tablelistado" style="display:none;">                                     
                                    <hr>
                                    <p><h6>  Estos resultados son extraidos de forma integra.</h6></p>  
                                    <div class='row'>
                                        <div class='col-md-12'>
                                            <table id="datatable-cursos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                <thead>
                                                    <tr>        
                                                        <th>Cod. Curso</th>                                                
                                                        <th>Inicia</th>
                                                        <th>Finaliza</th>
                                                        <th>Requeridos</th>                                                
                                                        <th>Segmento/Skill</th>
                                                        <th>Facilitador</th>                                                                                                                                    
                                                        <th>Sede</th> 
                                                        <th>Sala</th> 
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
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>            
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