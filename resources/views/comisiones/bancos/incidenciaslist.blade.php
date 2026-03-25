@extends('layouts.main')
@section('title', 'Banco Consolidado')
@section('menu_comisiones','menu-is-opening menu-open')
@section('comisiones','active')
@section('comisiones.bank','active')
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
                        <li class="breadcrumb-item">Gestión de Comisiones</li>
                        <li class="breadcrumb-item active">Listado de Incidencias</li>
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
                    <h3 class="card-title">Listado de personal con incidencias del lote banco <b>{{$nombre_lote}}</b> </h3>
                </div>    
                <div class="card-body">
                    <form action="{{route('change.incidencia')}}" method="POST" id="changeincidencias" class="form-horizontal form-label-left">
                        @csrf 
                        @method('PUT')
                        <input type='hidden' name='lotebanco' id='lotebanco' value="{{$id}}">
                        <div class="row mb-3"> 
                            <div class='col-md-12'><p><b style="color: red">Nota:</b> En esta ventana se mostrará los pagos con incidencias y podrán ser cambiados al estatus de 
                            <b>PAGADO </b>
                             Al Cambiar el Estatus, solo aplicara al registro principal del trabajador, y la incidencia quedara declarada al Lote Bancario.</p>          </div>
                            <div class="col-md-12">       
                                <br />
                                <h6>  Estos resultados son extraidos de forma integra</h6>
                                <table id="datatable-incidencias" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                    <thead>
                                    <th> 
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="select_all">
                                            <label for="select_all" class="custom-control-label"></label>
                                        </div>
                                    </th>
                                    <th>Cedula</th>                                                
                                    <th>Nombre y Apellido</th>                                
                                    <th>Gerencia</th>                                
                                    <th>Campaña</th> 
                                    <th>Skill</th>
                                    <th>Fecha</th>
                                    <th>Estatus</th>
                                    <th>Incidencia</th>
                                    </thead>
                                    <tbody>
                                        @foreach($incidencias as $incidencia)
                                        <tr>
                                            <td><div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input check" name="incidencias[]" type="checkbox" id="incidencias{{$incidencia->id}}" value="{{$incidencia->id}}">
                                                    <label for="incidencias{{$incidencia->id}}" class="custom-control-label"></label></div>
                                            </td>
                                            <td>{{$incidencia->cedula}}</td>
                                            <td>{{$incidencia->relationPersonal->primer_nombre . " " . $incidencia->relationPersonal->segundo_nombre . " " . $incidencia->relationPersonal->primer_apellido . " " . $incidencia->relationPersonal->segundo_apellido}}</td>
                                            <td>{{$incidencia->relationGerencia->nombre_campana}}</td>
                                            <td>{{$incidencia->relationCampana->nombre_cartera}}</td>
                                            <td>{{$incidencia->relationSkill->nombre_skill}}</td>
                                            <td>{{ trans('meses.'.$incidencia->mes)." ".$incidencia->anio}}</td>
                                            <td>{{$incidencia->estatus}}</td>
                                            <td>{{$incidencia->observaciones}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                                
                            </div>
                            
                            <div class="col-md-12">                                           
                                <button type="submit" class="btn btn-primary btn-block" id="" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Cambiar a Pagados"><i class="fas fa-credit-card"></i> Reversar Estatus</button>
                            </div> 
                            
                        </div>
                    </form>
                </div> 
            </div>
        </div> 
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
@endpush

@push('scripts')   
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/comisiones/comisiones.js')}}" type="text/javascript"></script>
@endpush
