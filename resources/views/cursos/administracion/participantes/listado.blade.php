@extends('layouts.main')
@section('title', 'Participantes del Curso|Listado')
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
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">                            
                            <h3 class="card-title">Listado de Participantes del Curso <b>@foreach($cursos as $curs)DGC{{$curs->id}} | {{date('d/m/Y',strtotime($curs->inicio))}} al {{date('d/m/Y',strtotime($curs->finaliza))}} | {{$curs->Relationskill->nombre_skill}} | Fac:{{$curs->RelationPersonal->primer_nombre}} {{$curs->RelationPersonal->primer_apellido}} @endforeach</b></h3>
                            <p class="float-right">                                
                            </p>
                        </div>
                        <div class="card-body">  
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token"> 

                            <div class="row">
                                <div class="col-md-12" id="tablelistado">                                     
                                    <small>  Estos resultados son extraidos de forma integra desde la base de Datos.</small>                                      
                                    <div class="ln_solid"></div>
                                    <table id="datatable-participantes" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                        <thead>
                                            <tr>        
                                                <th>Cedula</th>                                                
                                                <th>Nombre Participante</th>
                                                <th>Telefono Movil</th>
                                                <th>Estatus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($participantes as $par)

                                            <tr>
                                                <td>{{$par->cedula_empleado}}</td>                                                
                                                <td>{{$par->primer_nombre}} {{$par->primer_apellido}}</td>
                                                <td>{{$par->telefono_movil}}</td>
                                                <td>{{$par->RelationEstatus->estatus}}</td>                            
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
