@extends('layouts.main')
@section('title', 'Cursos|Asistencia Masiva')
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
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Registro de Asistencia - Modalidad Masiva</h3>
                </div>
               <div class="card-body">
                 <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header ">                            
                                <h3 class="card-title">Busqueda de Registros</h3>                                                     
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{route('cursos.asistencia.upload.generate')}}" id="asistenciasearchFile"  class="form-horizontal">                           
                                    @CSRF
                                    <div class="row mb-3">                                
                                        <div class="col-md-12">
                                              <label class="col-form-label" for="cursoasistencia">Cursos Dictados <span style="color:red">*</span></label>  
                                            <div class="form-group">             
                                                <select id="cursoasistencia" name="cursoasistencia" required class="select2" style="width: 100%;">
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($cursos as $curs)
                                                    <option value="{{$curs->id}}" data-inicio='{{$curs->inicio}}' data-fin='{{$curs->finaliza}}'>DGC{{$curs->id}} | {{date('d/m/Y',strtotime($curs->inicio))}} al {{date('d/m/Y',strtotime($curs->finaliza))}} | {{$curs->Relationskill->nombre_skill}}</option>
                                                    @endforeach
                                                </select>     
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                            <div class="form-group">                                                                                        
                                                <input type="text" name="fecha" id="fecha" disabled autocomplete="off" required class="form-control daterangedialy">                                                                                                                                             
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="Generar Plantilla"><i class="fa fa-file"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>                         
                    </div>  
                    <div class="col-md-6" id="displayformUpload">
                        <div class="card  card-danger">
                            <div class="card-header ">                            
                                <h3 class="card-title">Cargar Asistencia</h3>                                                     
                            </div>
                            <div class="card-body">
                                <form action="{{ route('cursos.asistencia.upload.file') }}" id="formUpdateasistenciafile" enctype="multipart/form-data" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="valuecurso" name="valuecurso" value="">
                                    <div class="form-group">
                                <label class="col-form-label" for="archivo">Archivo de Asistencia <span class="required" style="color:red;">*</span></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                        <label class="custom-file-label" for="exampleInputFile">Buscar Archivo en...</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="submit"  class="btn btn-primary cargamasiva"><i class="fa fa-ssync"></i> Actualizar Asistencia</button>
                                    </div>
                                </div>
                            </div>    
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h5 class="card-title">Instrucciones de Carga</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li> Realice la busqueda en base al curso que desea tomar la asistencia en la modalidad Asistencia Masiva, esto generara un archivo CSV descargable.</li>
                                    <li><span style="color:red;">NO MODIFIQUE EL NOMBRE DEL ARCHIVO, MODIFIQUE UNICAMENTE EL CONTENIDO DEL ARCHIVO</span></li>
                                    <li> Suministre en la columna <b>"D"</B> el prefijo de la asistencia que a continuacion se le suministra, en caso de ser <b style="color:red">EGRESO O ABANDONO</B> Agregue en la columna <b>"E"  las observaciones</b> correspondientes</B>
                                    <li> <b>A</b>: Asistente</li>
                                    <li> <b>I</b>: Inasistente</li>
                                    <li> <b>NA</b>: Nunca asistio</li>
                                    <li> <b>E</b>: Egreso</li>
                                    <ul>                                         
                                        @foreach($egresos as $egreso)
                                            <li><b>Observaciones:</b> {{$egreso->observaciones}}</li>
                                        @endforeach
                                    </ul>
                                    <li> <b>AB</b>: Abandono</li>
                                    <ul>                                        
                                        @foreach($abandonos as $abandono)
                                            <li><b>Observaciones:</b> {{$abandono->observaciones}}</li>
                                        @endforeach                                
                                    </ul>
                                </ul>
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
