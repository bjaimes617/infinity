@extends('layouts.main')
@section('title', 'Procesar Pagos')
@section('menu_comisiones','menu-is-opening menu-open')
@section('comisiones','active')
@section('comisiones.payment','active')
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
                        <li class="breadcrumb-item active">Procesar Pagos</li>
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
                    <h3 class="card-title">Procesamiento de Pagos </h3>
                     <div class="btn-group float-right">                                
                        @permission('comisiones.estimacion.costos')
                        <button style="position: relative;" class="btn btn-info " data-href="{{ route('comisiones.estimate.payment') }}" data-type="procesar" id="gastos" data-toggle="tooltip" data-placement="top" title="Estimacion de costos del Mes" ><i class="fa fa-calculator"></i></button>
                        @endpermission            
                    </div>  
                </div>
                <div class="card-body">  
                    <form action="{{route('comisiones.payment.list')}}" method="POST" id="processpayment" class="form-horizontal  form-label-left">
                        @csrf  
                        <div class="row mb-3">                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="paymentmes">Periodo <span style="color:red"> *</span></label>                          
                                    <div class="input-group"> 
                                        <input type="text" name="paymentmes" id="paymentmes" autocomplete="false" class="form-control timeaudit" required>    
                                    </div>
                                </div> 
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="estatus" class="col-form-label">Estatus</label><span style="color: red;"> *</span>
                                    <div class="input-group">
                                        <select class="form-control required" id="estatus" name="estatus" required>
                                            <option value="">[SELECCIONE]</option>
                                            <option value="AUDITADO">AUDITADO</option>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="estatus" class="col-form-label">Tipo de Periodo</label><span style="color: red;"> *</span>
                                    <div class="input-group">
                                        <select class="form-control required" id="tipoperiodo" name="tipoperiodo" required>
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($tipo_periodo as $key => $periodo)
                                            <option value="{{$key}}">{{$periodo}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div> 
                                </div>
                            </div>                        
                        </div>
                    </form> 
                    <form action="{{route('com.procesar.pagos')}}" method="POST" id="paymentstore" class="form-horizontal  form-label-left">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label" for="bs_tasa">Cambio según tasa <span style="color:red"> *</span></label>                          
                                <div class="input-group"> 
                                    <input type="number" name="bs_tasa" id="bs_tasa" autocomplete="false" class="form-control" required placeholder="000.00">    
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label" for="lote_banco">Identificador del Lote Bancario <span style="color:red"> *</span></label>                          
                                <div class="input-group"> 
                                    <input type="text" name="lote_banco" id="lote_banco" autocomplete="false" class="form-control" required>    
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-12 tableaudit" style="display:none;">                                                           
                            <p><h6>  Estos resultados son extraidos de forma integra</h6></p>                               
                            <table id="datatable-payments" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                <th> 
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="select_all">
                                        <label for="select_all" class="custom-control-label"></label>
                                    </div>
                                </th>
                                <th>Lote</th> 
                                <th>Gerencia</th>    
                                <th>Campaña</th>
                                <th>Skill</th>
                                <th>Tipo de Periodo</th>  
                                <th>Fecha</th>                                 
                                </thead>
                            </table>                                
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-block"  disabled id="pagosstore"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..."
                            data-toggle="tooltip" data-placement="top" title="Procesar"><i class="fas fa-sync"></i></button>
                    </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div> 
            
            <div class="modal fade" id="gastos-modal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Estimación de Costos Cargadas </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <table id="datatable-gatos" class="table compact table-sm text-sm table-bordered table-striped text-center">                                    
                        </table> 
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>                            
                        </div>
                    </div>
                    <!-- /.modal-content -->
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