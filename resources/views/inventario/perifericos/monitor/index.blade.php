@extends('layouts.main')
@section('title', 'Inventario de Monitores')
@section('menu_inventario','menu-is-opening menu-open')
@section('inventario','active')
@section('menu_periferico','menu-is-opening menu-open')
@section('menu_monitores','menu-is-opening menu-open')
@section('inventario.perifericos.monitor','active')
@section('inventario.index.monitor','active')
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
                        <li class="breadcrumb-item active">Monitores</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tv"></i>
                                Información de Monitores
                            </h3>
                        </div>                        
                        <div class="card-body">
                            <div class="row mb-3">                                
                                <div class="col-md-12"> 
                                    <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6>                             
                                    <div class="ln_solid"></div>
                                    <table id="datatable-inventario" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                        <thead>
                                            <tr>                                            
                                                <th>Marca</th>
                                                <th>Asignados</th>                                                    
                                                <th>Disponibles</th>
                                                <th>Malos</th>
                                                <th>Totales</th>                                                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monitor as $monitors)
                                            <tr>
                                                <td>{{$monitors->marca}}</td>
                                                <td><b style='color:blue;'>{{$monitors->assigned}}</b></td>
                                                <td><b style='color:green;'>{{$monitors->available}}</b></td>
                                                <td><b style='color:red;'>{{$monitors->bad}}</b></td>
                                                <td>{{ $monitors->assigned + $monitors->available +$monitors->bad}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>                                     
                                </div> 
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
