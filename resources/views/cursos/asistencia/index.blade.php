@extends('layouts.main')
@section('title', 'Cursos|Asistencia Manual')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('cursosactive','active')
@section('cursos.asistencia','active')
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
                        <li class="breadcrumb-item active">Asistencia</li>
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
                    <h3 class="card-title">Registro de Asistencia - Modalidad Manual <br> <small>La toma de asistencia solo se podrá realizar una vez el curso este activo y dentro de su rango</small> </h3>                            
                    
                    <div class="float-right"> <a href="{{ route('cursos.asistencia.upload') }}" class="btn btn-primary"><i class="fa fa-upload"></i> Asistencia Masiva</a></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="POST" action="{{route('cursos.asistencia.show')}}" id="asistenciasearchcursos"  class="form-horizontal">                           
                                @CSRF
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="cursoasistencia">Cursos Dictados <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <select id="cursoasistencia" name="cursoasistencia" required class="select2" style="width: 100%;">
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($cursos as $curs)
                                                    <option value="{{$curs->id}}" data-inicio='{{$curs->inicio}}' data-fin='{{$curs->finaliza}}'>DGC{{$curs->id}} | {{date('d/m/Y',strtotime($curs->inicio))}} al {{date('d/m/Y',strtotime($curs->finaliza))}} | {{$curs->Relationskill->nombre_skill}}</option>
                                                    @endforeach
                                                </select>                                                   
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <input type="text" name="fecha" id="fecha" disabled autocomplete="off" required class="form-control daterangedialy">                                              
                                                <div class="input-group-prepend">
                                                    <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12" id="display_asistencia"></div>             
                    </div>
                </div>

            </div>                  
        </div>
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush
