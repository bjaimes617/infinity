@extends('layouts.main')
@section('title', 'Evaluaciones|Editar')
@section('menu_evaluaciones','menu-is-opening menu-open')
@section('evaluaciones','active')
@section('evaluacion.edit','active')
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
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">                            
                            <div class="alert-text">
                                Listado de Evaluaciones Realizadas en el Periodo {{$tipo_periodo}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="Tablevaluaciones" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Nombre y Apellido </th>
                                            <th>Cargo </th>
                                            <th>Evaluado el</th>        
                                            <th>Tipo de Evaluacion</th>
                                            <th>Rango de Evaluacion</th>
                                            <th>Evaluacio Por</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($valuaciones as $evaluacion)
                                        <tr>
                                            <td>{{$evaluacion->relationPersonal->primer_nombre .' '.$evaluacion->relationPersonal->primer_apellido}}</td>
                                            <td>{{$evaluacion->relationCargo->nombre_cargo}}</td>
                                            <td>{{date('d/m/Y',strtotime($evaluacion->created_at))}}</td>                                            
                                            <td>{{$evaluacion->tipo_periodo}}</td>  
                                            <td>{{date('d/m/Y',strtotime($evaluacion->desde))}} al {{date('d/m/Y',strtotime($evaluacion->hasta))}}</td>
                                            <td>{{$evaluacion->relationUser->username}}</td>
                                            <td>  
                                                <div class="btn-group"> 
                                                    <button data-href="{{route('edit.bring.personal',$evaluacion->id)}}" 
                                                            onclick="ShowEvaluacionEdit({{$evaluacion->id}});" 
                                                            id="button{{$evaluacion->id}}"
                                                            data-nombre="{{$evaluacion->relationPersonal->primer_nombre .' '.$evaluacion->relationPersonal->primer_apellido}}"
                                                            data-cargo="{{$evaluacion->relationCargo->nombre_cargo}}"
                                                            data-periodo="{{$evaluacion->tipo_periodo}}"
                                                            data-mes_anio="{{date('m/Y',strtotime($evaluacion->desde))}}"
                                                            class="btn btn-primary btn-sm" >
                                                        <i class="fas fa-edit"></i>                                                          
                                                    </button> 
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <div class="modal fade" id="editeval" tabindex="-1" aria-labelledby="editeval_modal" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title h4" id="exampleModalXlLabel">Modificar evaluación del personal</h5>
                            <button class="btn-close btn btn-danger" type="button" data-coreui-dismiss="modal" aria-label="Close">X</button>
                        </div>
                       
                        <div class="modal-body">
                            <form action="{{route('evaluacion.update')}}" id="UpdateIndicadores" method="post" class="form-horizontal form-label-left" data-parsley-validate >
                            @csrf       
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label>Nombre del Personal</label>
                                                <input class="form-control" type="text" id="editnombre_personal" name="nombre_personal" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label>Mes y año</label>
                                                <input class="form-control" id="mesnaio" type="text" disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Skill</label>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="text" value="{{$nombre_skill}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Cargo</label>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="text"  value="{{$nombre_cargo}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="datarespuestas"></div>
                                    </div>
                                </div>
                                <div class='card-footer'>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Guardar</button>
                                </div>
                            </div>
                            </form>
                        </div>
                       
                    </div>
                </div>
            </div>
            <!-- /.container-fluid-->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush
@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/evaluacion/evaluaciones.js')}}" type="text/javascript"></script>
@endpush