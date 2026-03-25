@extends('layouts.main')
@section('title', 'Cursos|Aprobacion de Participantes')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('cursosactive','active')
@section('cursos.aprobacion.index','active')
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
                        <li class="breadcrumb-item active">Aprobación</li>
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
                    <h3 class="card-title">Aprobación de Participantes </h3>                            
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('cursos.aprobacion.show')}}" method="POST" id="aprobarparticipantes" class="form-horizontal">                         
                                @CSRF
                                <div class="row mb-3">                                                  
                                    <div class="col-md-12">
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
                        <div class="col-md-12" id="displayaprobacion">

                        </div>
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
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush
