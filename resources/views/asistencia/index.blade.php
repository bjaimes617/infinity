@extends('layouts.main')
@section('title', 'Asistencia Operativa|Diaria')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_asistencia','menu-is-opening menu-open')
@section('asistencia','active')
@section('asistencia.index','active')
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
                        <li class="breadcrumb-item">Asistencia Operativa</li>
                        <li class="breadcrumb-item active">{{$personal->RelationAreas->nombre_campana}}</li>
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
                    <h3 class="card-title">Asistencia diaria del Personal asignado a: <b>{{strtolower(ucfirst( $personal->primer_nombre))}} {{strtolower(ucfirst( $personal->primer_apellido))}}</b></h3>
                    &nbsp;<a class="float-right">Cargo Actual: <b>{{$personal->RelationCargos->nombre_cargo}}</b></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('asistencia.search.create')}}" method="POST" id="asistenciasearchcursos"  class="form-horizontal form-label-left">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="fecha">¿A quien se le Tomara la Asistencia? <span style="color:red">*</span></label>  
                                            <div class="input-group">     
                                                @foreach($buttonDisplay as $key => $content)
                                                <button type="submit" value="{{$key}}" name="asistencia" class="btn btn-primary btn-flat buttonselect">
                                                    <i class="fas fa-user"></i> {{$content}}
                                                </button>                                                  
                                                @endforeach
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                            <div class="input-group"> 
                                                <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                                <input type="text" name="fecha" id="fecha" autocomplete="off" required class="form-control daterangedialy">                                                                                              
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                           <form method="POST" action="{{route('asistencia.store')}}" id="FormAsistencia" class="form-horizontal form-label-left" data-parsley-validate>                                                        
                               <div id="display_asistencia">
                                   
                               </div>
                           </form>
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
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>

<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>

<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>

<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/asistencia/plugins.js')}}"></script>
@endpush
