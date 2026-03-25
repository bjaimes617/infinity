@extends('layouts.main')
@section('title', 'Inventario de Equipos')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_equipo','menu-is-opening menu-open')
@section('inventario.equipo','active')
@section('inventario','active')
@section('inventario.index.equipo','active')
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
                        <li class="breadcrumb-item active">Equipos</li>
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
                    <h3 class="card-title">  Información de Componentes en Los Stocks</h3>                    
                </div>
                <div class="card-body">
                    <form action="{{route('inventario.show.equipo')}}" method="POST" id="IndexSearchEquip" class="form-horizontal form-label-left">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="stock">Stock de Busqueda</label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="stock" id="stock" class="select2 from-control">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($stock as $values)
                                            <option value="{{$values->id}}">{{$values->tipo}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="equipo">Identificador del Equipo</label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-desktop"></i></span>
                                        </div>
                                        <input type="text" id="equipo" name="equipo" class="form-control" placeholder="PC|XXXXX">                                                                                               
                                        <div class="input-group-prepend">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                        </div>
                                    </div> 
                                </div> 
                            </div>
                        </div>
                    </form>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="text-center" id="mapsvg"></div>
                        </div>  
                        <br/>
                        <div class="col-md-12" style="display:none;" id="tablelistado" >  
                            <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
                            <table id="datatable-equipos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>                                            
                                        <th>Equipo</th>
                                        <th>Estatus</th>
                                        <th>Gerencia</th>
                                        <th>Campaña</th>
                                        <th>skill</th>
                                        <th>Responsable</th>
                                        <th>Ubicación</th>                                            
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>                                
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