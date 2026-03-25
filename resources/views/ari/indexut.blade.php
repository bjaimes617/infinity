@extends('layouts.main')
@section('title', 'Ejecucion Unidades Tributarias')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_ari','menu-is-opening menu-open')
@section('ari','active')
@section('ut.index','active')
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
                        <li class="breadcrumb-item">Unidad Tributaria</li>
                        <li class="breadcrumb-item active">Activacion</li>
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
                    <h3 class="card-title">Activación de Unidades Tributarias.  <small style="color: red;">La unidad tributaria no podrá ser modificada al ser activada</small>      </h3>
                </div>
                <div class="card-body">                             
                    <div class="row">                               
                        <div class="col-md-12">
                            <div class='row mb-3'>
                                <div class='col-md-12'>
                                    <table id="unidadestable" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Año</th>
                                                <th>Periodo</th>
                                                <th>Valor U.T.</th>
                                                <th>Estatus</th> 
                                                <th>Acciones</th>           
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($unidades as $unidad)
                                            <tr>
                                                <td>{{$unidad->anio}}</td>
                                                @switch($unidad->trimestre)
                                                @case(1)
                                                <td>Enero - Marzo</td>
                                                @break
                                                @case(2)
                                                <td>Abril - Junio</td>
                                                @break
                                                @case(3)
                                                <td>Julio - Septiembre</td>
                                                @break
                                                @case(4)
                                                <td>Octubre - Diciembre</td>
                                                @break
                                                @endswitch
                                                <td>{{$unidad->unidad_tributaria." Bs."}}</td>

                                                @if($trimestre == $unidad->trimestre)
                                                <td>
                                                    @if($unidad->disabled == 0)                                                        
                                                    <input type="checkbox" @if($unidad->active == 1) checked @endif 
                                                    class="unidadactive" id="unidadactive{{$unidad->id}}" 
                                                    name="unidadactive{{$unidad->id}}" 
                                                    data-href="{{route('unidades.activacion.update',$unidad->id)}}" 
                                                    data-value="{{$unidad->id}}" data-bootstrap-switch  
                                                    data-off-color="danger" data-on-color="success">                                                     
                                                    @else                                                                             
                                                    <input type="checkbox" disabled class="unidadactive" 
                                                           id="unidadactive{{$unidad->id}}" 
                                                           name="unidadactive{{$unidad->id}}" 
                                                           data-bootstrap-switch  data-off-color="danger" 
                                                           data-on-color="success">                                                       
                                                    @endif                                       
                                                </td>
                                                @else
                                                <td>                                                                                       
                                                    <input type="checkbox" disabled 
                                                           class="unidadactive" 
                                                           id="unidadactive{{$unidad->id}}" 
                                                           name="unidadactive{{$unidad->id}}" 
                                                           data-bootstrap-switch 
                                                           data-off-color="danger" 
                                                           data-on-color="success"> 
                                                </td>
                                                @endif
                                                <td>  
                                                    @if($unidad->active == 1 || $unidad->disabled == 1)
                                                    <button class="btn btn-primary" disabled><i class="fa fa-edit"></i></button>
                                                    @else
                                                    <a href="{{route('ut.edit',[$unidad->id,$unidad->anio,$unidad->trimestre])}}" class="btn btn-primary" 
                                                       id="edit{{$unidad->id}}" 
                                                       data-toggle="tooltip" data-placement="top" title="Editar" style="color: white;"><i class="fa fa-edit"></i></a>
                                                    @endif
                                                </td>
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
            <!-- /.card -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
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
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/ari/plugins.js')}}"></script>
@endpush