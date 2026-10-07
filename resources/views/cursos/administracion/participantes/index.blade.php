@extends('layouts.main')
@section('title', 'Cursos|Busqueda por Cursos')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('menu.cursos.participantes','menu-is-opening menu-open')
@section('cursosactive','active')
@section('participantes.index','active')
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
                        <li class="breadcrumb-item">Participantes</li>                        
                        <li class="breadcrumb-item active">Cursos</li>
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
                    <h3 class="card-title">Buscaqueda de participantes por Cursos </h3>
                    <p class="float-right">                                
                    </p>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="POST" id="SearchParticipantescursos"  action="{{route('cursos.index.post')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="mes">Mes y Año <span style="color:red">*</span></label>  
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                </div>
                                                <input type="text" id="mes" name="mes" autocomplete="off" required class="form-control mesdate">                                                                                                                                           
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class=" col-form-label" for="mes">Estatus del Curso <span style="color:red">*</span></label>  
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                </div>
                                                <select name="estatus" id="estatus" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option> 
                                                    @foreach($estatus as $key => $estatu)
                                                    <option value="{{$key}}">{{$estatu}}</option> 
                                                    @endforeach
                                                </select>

                                                <div class="input-group-prepend">
                                                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-12" id="tablelistado" style="display: none;">                                     
                                    <hr>
                                    <p><h6> Estos resultados son extraidos de forma integra.</h6></p>  
                                    <div class='row'>
                                        <div class='col-md-12'>
                                            <table id="datatableParticipantesCursos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                <thead>
                                                    <tr>        
                                                        <th>Cod. Curso</th>                                                
                                                        <th>Inicia</th>
                                                        <th>Finaliza</th>
                                                        <th>Asignados</th>
                                                        <th>Unidad/Campaña</th>
                                                        <th>Segmento/Skill</th>
                                                        <th>Facilitador</th>                                                                                                                                    
                                                        <th>Sede</th> 
                                                        <th>Sala</th> 
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>                                     
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>            
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush

@push('scripts') 
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush