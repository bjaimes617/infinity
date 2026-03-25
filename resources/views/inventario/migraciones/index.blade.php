@extends('layouts.main')
@section('title', 'Migraciones En proceso')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones','menu-is-opening menu-open')
@section('inventario.migraciones','menu-is-opening menu-open')
@section('inventario.posiciones','active')
@section('inventario','active')
@section('migraciones','active')
@section('inventario.index.migracion','active')
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
                        <li class="breadcrumb-item">Inventario</li>
                        <li class="breadcrumb-item active">Migraciones</li>
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
                    <h3 class="card-title">
                        <i class="fas fa-map-marker"></i>
                        Procesar migración de Posiciones
                    </h3>
                </div> 
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12" id="tablelistado" >  
                            <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
                            <table id="datatable-inventario" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>                                            
                                        <th>Codigo</th>
                                        <th>Sede de Salida</th>
                                        <th>Sede Destino</th>                                                    
                                        <th>Cantidad a migrar</th>
                                        <th>Solicitado por</th>                                                
                                        <th>Fecha</th>   
                                        <th>Acciones</th>                                                
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($migrar as $values)                                          
                                    <tr>
                                        <td>{{$values["codigo"]}}</td>
                                        <td>{{$values["salida"]}}</td>
                                        <td>{{$values["destino"]}}</td>
                                        <td>{{$values["cantidad"]}}</td>
                                        <td>{{$values["solicitante"]}}</td>
                                        <td>{{$values["fecha"]}}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary viewmigrations" data-toggle="tooltip" data-placement="top" title="Visualizar Migración" data-href="{{route('inventario.view.migracion',$values["id"])}}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success storageMigration" data-toggle="tooltip" data-placement="top" title="Procesar" data-href="{{route('inventario.update.migracion',$values["id"])}}">
                                                    <i class="fa fa-sync"></i>
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
                <div class="modal fade" id="viewMigracionModal" tabindex="-1" role="dialog" aria-labelledby="viewMigracionModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header sussces">
                                <h4 class="modal-title" id="viewMigracionModal">
                                    Migracion
                                </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-12" >
                                        <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
                                        <table id="datatable-migracion" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                            <thead>
                                            <th>Migrar</th>
                                            <th>Destino</th>
                                            <th>Equipo(s)</th>
                                            <th>Monitor(es)</th>
                                            <th>Periferico(s)</th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            </div>
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
<script src="{{asset('js/inventario/inventario.js')}}"></script>
@endpush