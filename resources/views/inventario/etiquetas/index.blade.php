@extends('layouts.main')
@section('title', 'Inventario|Administracion|Etiquetas')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones_administracion','menu-is-opening menu-open')
@section('inventario.administracion','active')
@section('inventario.migracion.administracion.etiqueta.index','active')

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
                        <li class="breadcrumb-item">Administracion</li>
                        <li class="breadcrumb-item active">Etiquetas</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generador de Etiquetas</h3>                            
                </div>                                  
                <div class="card-body"> 
                    <form action="{{route('inventario.migracion.administracion.etiqueta.search')}}" method="POST" id="qrSearch" class="form-horizontal form-label-left">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="col-form-label" for="etiquetas">Estatus Etiquetas <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                        
                                        <select name="etiquetas" id="etiquetas" required class="select2 from-control">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($etiqueta as $eti => $values)
                                            <option value="{{$eti}}">{{$values}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 
                            </div> 
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="col-form-label" for="tipo">Tipo <span style="color:red">*</span></label>                                
                                    <div class="input-group">                                       
                                        <select name="tipo" id="tipo" required class="select2 from-control">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($tipo as $key => $values)
                                            <option value="{{$key}}">{{$values}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-prepend">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                        </div>
                                    </div> 
                                </div> 
                            </div>
                        </div>
                    </form>

                    <div class="row mb-3" style="display:none;" id="tablelistado">  
                        <form action="{{route('inventario.migracion.administracion.etiqueta.create')}}" method="POST" id="qr_generate" class="form-horizontal form-label-left">
                            @csrf
                            <input type="hidden" name="tipoqr" id="tipoqr">  
                            <div class="col-md-12">
                                <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
                                <table id="datatable-etiquetas" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                    <thead>
                                        <tr>                                            
                                            <th>
                                                <div class="icheck-primary d-inline">                                                                
                                                    <input type="checkbox" class="checkboxdis" name="select_all" id="select_all">                                                                                                                                
                                                    <label for="select_all"></label>
                                                </div>
                                            </th>
                                            <th>Identificador General</th>
                                            <th>Estatus de le Etiqueta</th>
                                        </tr>
                                    </thead>
                                </table>                                
                            </div>
                            <div class="col-md-12 text-center">
                                <br/>
                                <p><span style="color:red;">Instrucciones:</span> Seleccione uno (1) o más identificadores que se le generara la etiqueta. Una vez procesada la petición, el(los) Identificador(es) cambiara su estatus de etiqueta a <b>Impresa</b></p>
                                <p><span style="color:red;">LAS CONDICIONES NO ESTAN HABILITADAS PARA CAMBIAR EL ESTATUS DE LOS IDENTIFICADORES SELECCIONADOS </span></p>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Descargar etiquetas"><i class="fa fa-download"></i></button>
                                </div>
                            </div>
                        </form>
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