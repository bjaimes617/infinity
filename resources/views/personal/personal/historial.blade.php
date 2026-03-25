@extends('layouts.main')
@section('title', 'Historial de personal')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('personal.list','active')
@section('content')
<style type="text/css">
    .item-list {
        max-height: 150px; /* Ajusta la altura máxima según tus necesidades */
        overflow-y: auto;  /* Agrega desplazamiento vertical si el contenido excede la altura */
        margin-top: 5px;   /* Espaciado hacia arriba */
    }

    .list-group-item {
        cursor: pointer; /* Cambia el cursor a puntero para mejor UX */
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Personal</li>
                        <li class="breadcrumb-item active">Historial</li>
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
                            <h3 class="card-title">Historico de Cambios</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <small>Estos datos son extraidos de forma integra desde la base de datos.</small>                            
                            <table id="historico-table" class="table compact table-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Fecha Actualización</th>
                                        <th>Cedula</th>
                                        <th>Modificaciones</th>                                                                               
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historial as $historico)
                                    <tr>
                                        @php                                          
                                        $fecha_formateada = date('d/m/Y h:i a', strtotime($historico->created_at));
                                        @endphp
                                        <td>{{$fecha_formateada}}</td>
                                        <td>{{$historico->cedula_empleado}}</td>                                        
                                        @php 
                                        try{
                                        $data   = json_decode($historico->campos_alt);                                        
                                        $values = implode(", \n ", $data); 
                                        } catch(TypeError $e){
                                            $values = null; 
                                        }
                                        @endphp                                      
                                        <td>{{$historico->modificado_por}}</td>                                      
                                        <td>
                                            <div class="btn-group">
                                            <a href="#" 
                                                class="btn btn-primary btn-sm" 
                                                data-toggle="tooltip"
                                                data-placement="top" 
                                                data-bs-html="true"
                                                title="{{$values}}" 
                                                >
                                              <i class="fas fa-list-alt"></i>
                                            </a>
                                            <a href="{{route('historial.view',$historico->id)}}" class="btn btn-sm btn-success" 
                                               data-toggle="tooltip" data-placement="top" title="Visualizar Cambios">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            </div>
                                        </td>                                       
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
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/gestion_personal/gestion.js')}}" type="text/javascript"></script>
@endpush
