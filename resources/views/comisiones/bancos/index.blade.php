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
                        <li class="breadcrumb-item active">Consolidado Bancario</li>
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
                    <h3 class="card-title"> <i class="fas fa-cubes"></i> Consolidados de Bancarios</h3>
                </div>    
                <div class="card-body">
                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                @permission('comisiones.consolidado.pagar')
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-pagar-tab" data-toggle="pill" href="#custom-tabs-pagar" role="tab" 
                                       aria-controls="custom-tabs-pagar" aria-selected="false"><i class="fa fa-credit-card"></i>&nbsp;Pagar</a>
                                </li>
                                @endpermission
                                @permission('comisiones.consolidado.observaciones')
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-observaciones-tab" data-toggle="pill" href="#custom-tabs-one-observaciones" role="tab" aria-controls="custom-tabs-one-observaciones" aria-selected="false"><i class="fas fa-eye"></i>&nbsp;Carga de Incidencias  </a>
                                </li>
                                @endpermission
                                @permission('comisiones.consolidado.banco')
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-bank-tab" data-toggle="pill" href="#custom-tabs-one-bank" role="tab" aria-controls="custom-tabs-one-bank" aria-selected="true"><i class="fas fa-file"></i>&nbsp;Reporte de Bancos</a>
                                </li>
                                @endpermission
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                @permission('comisiones.consolidado.pagar')
                                <!-- PESTAÑA DE PAGAR -->
                                <div class="tab-pane active" id="custom-tabs-pagar" role="tabpanel" aria-labelledby="custom-tabs-pagar-tab">
                                    <form action="{{route('comisiones.bank.change.estatus')}}" method="POST" id="processconsolidado" class="form-horizontal form-label-left">
                                        @csrf    
                                        @permission('comisiones.consolidado.pagar')
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="periodo">Periodo <span style="color:red"> *</span></label>                          
                                                    <div class="input-group"> 
                                                        <input type="text" name="periodo" id="periodo" required autocomplete="false" class="form-control timeaudit" >                                                           
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-primary btn-block"
                                                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                                    data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fas fa-search"></i></button>
                                                        </div>                                                      
                                                    </div>
                                                </div> 
                                            </div>                                            
                                        </div>
                                        @endpermission
                                    </form>
                                    <form action="{{route('comisiones.bank.changing')}}" method="POST" id="storeconsolidado" enctype="multipart/form-data" class="form-horizontal form-label-left">
                                        @csrf                                          
                                        <div class="row mb-3 tableaudit" style="display:none;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="banco">Banco <span style="color:red">*</span></label>                                 
                                                    <div class="input-group">                                       
                                                        <select name="banco[]" id="banco" required multiple class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>
                                                            @foreach($bancos as $banco)
                                                            <option value="{{$banco->bancos}}">{{$banco->bancos}}</option> 
                                                            @endforeach   
                                                        </select>                                           
                                                    </div> 
                                                </div> 
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fechapago">Fecha de pago <span style="color:red"> *</span></label>                          
                                                    <div class="input-group"> 
                                                        <input type="text" name="fechapago" id="fechapago" autocomplete="false" class="form-control calendarfecha" required>    

                                                    </div>
                                                </div> 
                                            </div> 
                                            <div class="col-md-12">                                                           
                                                <p><h6>  Estos resultados son extraidos de forma integra</h6></p>                               
                                                <table id="datatable-pagar" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                    <thead>
                                                    <th> 
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox" id="select_all">
                                                            <label for="select_all" class="custom-control-label"></label>
                                                        </div>
                                                    </th>
                                                    <th>Lote</th>                                                
                                                    <th>Fecha</th>                                
                                                    <th>Estatus</th> 
                                                    <th>Acciones</th>
                                                    </thead>
                                                </table>                                
                                            </div>
                                            @permission('comisiones.consolidado.pagar')
                                            <div class="col-md-12">                                           
                                                <button type="submit" class="btn btn-primary btn-block" id="storeconsolidadopay" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Procesar Consolidado"><i class="fas fa-credit-card"></i> Pagar</button>
                                            </div> 
                                            @endpermission
                                        </div>
                                    </form>
                                </div>
                                @endpermission
                                @permission('comisiones.consolidado.observaciones')
                                <!-- PESTAÑA DE CARGAR INCIDENCIAS -->
                                <div class="tab-pane" id="custom-tabs-one-observaciones" role="tabpanel" aria-labelledby="custom-tabs-one-observaciones-tab">
                                    <form action="{{route('comisiones.bank.consult')}}" method="POST" id="procesarincidencias" class="form-horizontal form-label-left">
                                        @csrf    
                                        <div class="row mb-3">                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fechacarga">Periodo <span style="color:red"> *</span></label>                          
                                                    <div class="input-group"> 
                                                        <input type="text" name="fechacarga" id="fechacarga" required  autocomplete="false" class="form-control timeaudit" >    
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-primary btn-block"
                                                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                                    data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fas fa-search"></i></button>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>                                            
                                        </div>
                                    </form>
                                    <form action="{{route('comisiones.bank.incidencias')}}" method="POST" enctype="multipart/form-data" id="uploadincidencias" class="form-horizontal form-label-left">
                                        @csrf  
                                        <div class="row mb-3 tablepagados" style="display:none;">      
                                            <div class="col-md-12">                                                           
                                                <p><h6>  Estos resultados son extraidos de forma integra </h6></p>                               
                                                <table id="datatable-pagados" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                    <thead>
                                                    <th> 
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox" id="select_all">
                                                            <label for="select_all" class="custom-control-label"></label>
                                                        </div>
                                                    </th>
                                                    <th>Lote</th>                                                
                                                    <th>Periodo</th>                                
                                                    <th>Banco</th>                                
                                                    <th>Fecha de pago</th> 
                                                    <th>Estatus</th>
                                                    <th>Acciones</th>
                                                    </thead>
                                                </table>                                
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="incidencias">Archivo de Incidencias a Procesar... <span class="required" style="color:red;">*</span></label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" required id="incidencias" name="incidencias">
                                                            <label class="custom-file-label" for="incidencias">Buscar Archivo en...</label>
                                                        </div>
                                                        <div class="input-group-append">
                                                            <button type="submit"  class="btn btn-primary"><i class="fa fa-upload"></i> Cargar Incidencias</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p>  <b style="color: red">Nota:</b> Para colocar las incidencias deberá descargar 
                                                    este formato y colocar las cedulas respectivas y las incidencias. Descargue aqui 
                                                    <a class="btn btn-info"  href="{{Storage::disk('comisiones')->url('formatos/ObservacionesBanco.xlsx')}}"><i class="fa fa-file"></i></a> </p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @endpermission
                                @permission('comisiones.consolidado.banco')
                                <div class="tab-pane" id="custom-tabs-one-bank" role="tabpanel" aria-labelledby="custom-tabs-one-bank-tab">
                                    <form action="{{route('comisiones.bank.report')}}" method="POST" enctype="multipart/form-data" id="donwloadreportes" class="form-horizontal form-label-left">
                                        @csrf    
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="estatus">Estatus <span style="color:red">*</span></label>                                 
                                                    <div class="input-group">                                       
                                                        <select name="estatus" id="estatus" required class="form-control select2">                                                                                                              
                                                            @foreach($estatusbancarios as $key => $estatus)
                                                            <option value="{{$key}}">{{$estatus}}</option> 
                                                            @endforeach   
                                                        </select>                                           
                                                    </div> 
                                                </div> 
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fechadescarga">Periodo <span style="color:red"> *</span></label>                          
                                                    <div class="input-group"> 
                                                        <input type="text" name="fechadescarga" id="fechadescarga" required  autocomplete="false" class="form-control timeaudit" >    
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-primary btn-block"
                                                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                                    data-toggle="tooltip" data-placement="top" title="Descargar"><i class="fas fa-download"></i></button>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>                                            
                                        </div>
                                    </form>
                                </div>
                                @endpermission
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
