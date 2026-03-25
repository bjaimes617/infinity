@extends('layouts.main')
@section('title', 'Asignaciones y Deducciones')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menurecibo','menu-is-opening menu-open')
@section('reciboactive','active')
@section('menurecibo','menu-is-opening menu-open')
@section('menu.recibos.ajustes','menu-is-opening menu-open')
@section('recibos.ajustes','active')
@section('reciboactive','active')
@section('recibos.items','active')

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
                        <li class="breadcrumb-item">Recibo de Pagos</li>
                        <li class="breadcrumb-item active">Asignaciones y Deducciones</li>
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
                    <h3 class="card-title">Listado de Asignaciones y Deducciones</h3><a href="{{route('recibos.items.create')}}" class="btn btn-primary float-right" id="add" data-toggle="tooltip" data-placement="top" title="Añadir"><i class="fa fa-plus-circle"></i></a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">                            
                                <p><h6>  Estos resultados son extraidos de forma integra del Listado de Personal de Directa Group</h6></p>                               
                            <table id="items_table" class="table compact table-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Nomina</th>         
                                        <th>Código Item</th>
                                        <th>Descripcion</th>                                        
                                        <th>Tipo</th>                                      
                                        <th>Creado</th>                                        
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>                                        
                                        <td>{{$item->RelationNomina->nombre_nomina}}</td>   
                                        <td>{{$item->coditem}}</td>
                                        <td>{{$item->descripcion}}</td>                                        
                                        <td>{{$item->tipo}}</td>    
                                        <td>{{date('d/m/Y',strtotime($item->created_at))}}</td>            
                                        <td> <a class="btn btn-primary btn-sm" href="{{route('recibos.items.edit',$item->id)}}"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="Editar"></i></a> </td>            
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush

@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/recibos/items.js')}}" type="text/javascript"></script>
@endpush
