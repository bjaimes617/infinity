@extends('layouts.main')
@section('title', 'Ejecucion Recibos de Pago')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menurecibo','menu-is-opening menu-open')
@section('reciboactive','active')
@section('recibos.administracion.reporte.index','active')
@section('menu.recibos.administracion','menu-is-opening menu-open')
@section('recibos.administracion','active')
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
                        <li class="breadcrumb-item active">Reportes</li>
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
                            <h3 class="card-title">Reporte de Recibos de Pago.</h3>
                        </div>                                  
                        <div class="card-body">                               

                            <form method="POST" action="{{route('recibos.administracion.reporte.create')}}" id="ReportesGenerates"class="form-horizontal form-label-left" data-parsley-validate>
                                @CSRF
                                <div class="row">
                                    <div class="col-md-4"> 
                                        <div class="form-group">
                                            <label for="tprecibo">Nomina <span style="color:red">*</span>  </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-th-list"></i></span>
                                                </div>
                                                <select class="form-control required" name="nomina" id="nomina" required>                                          
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($nomina as $nom)
                                                    <option value="{{$nom->id}}"> {{$nom->nombre_nomina}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" >  
                                        <div class="form-group">
                                            <label for="tprecibo">Tipo de Recibo <span style="color:red">*</span>  </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-th-list"></i></span>
                                                </div>
                                                <select class="form-control required" name="tprecibo" id="tprecibo" disabled required>
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($tiponomina as $tiponomina)
                                                    <option value="{{trim($tiponomina->codigo)}}" data-nomina="{{$tiponomina->nomina_id}}"> {{$tiponomina->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="corte">Periodo <span style="color:red">*</span>  </label>
                                            <div class="input-group">                               
                                                <div class="input-group">                                           
                                                    <input id="mes" name="mes" class="form-control mesdate" autocomplete="off" required>                                             
                                                </div>
                                            </div> 
                                        </div>    
                                    </div>                                 
                                    <div class="col-md-12">                                       
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-primary btn-block" id="BuscarPeriodo" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Descargar Reporte"><i class="fas fa-download"></i></button>
                                        </div>                                                                 
                                    </div>
                                </div>
                            </form>                            
                        </div>
                    </div>                   
                </div>
            </div>            
        </div>
    </section>
</div>

@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
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
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('js/recibos/active.js')}}"></script>
@endpush