@extends('layouts.main')
@section('title', 'Preselección de Candidatos')
@section('menu_reclutasimple','menu-is-opening menu-open')
@section('reclutasimple','active')
@section('menu_personal','menu-is-opening menu-open')
@section('reclutasimple.preselelccion','active')
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
                        <li class="breadcrumb-item">Reclutamiento</li>
                        <li class="breadcrumb-item active">Preseleccion</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>     
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Precandidatos</h3>
                </div>
                <div class="card-body">
                    <form action='{{route('reclutamiento.Showpreseleccion')}}' method='post' id="ShowCandidatos" class="form-horizontal form-label-left">
                        <div class="row mb-3">                        
                            @csrf 
                            <div class="col-md-6"> 
                                <div class="form-group">                                        
                                    <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                    <div class="input-group"> 
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="text" name="fecha" id="fecha" autocomplete="off" required class="form-control daterange">         
                                        <div class="input-group-prepend">
                                            <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </form>
                     <div class="row mb-3" id="tablelistado" style="display:none;">             
                        <div class="col-md-12" >                                                           
                            <p><h6> Estos Datos extraidos integro desde la Base de datos.</h6></p>                               
                            <table id="datatable-prospectos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>                                            
                                        <th>Cedula</th>
                                        <th>Nombre y Apellido</th>   
                                        <th>Edad</th>  
                                        <th>Exp. en Ventas</th>  
                                        <th>Cargo Postulado</th>   
                                        <th>Detalle Exp.</th>   
                                        <th>Tlf. Celular</th> 
                                        <th>Tlf. Habitación</th> 
                                        <th>Email</th> 
                                        <th>Serv. Anterior</th> 
                                        <th>Dirección</th> 
                                        <th>acciones</th> 
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
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush

@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}" type="text/javascript"></script>

<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>

<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}" type="text/javascript"></script>

<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/jquery-validation/jquery.validate.js')}}" type="text/javascript"></script>   
<script src="{{asset('plugins/jquery-validation/localization/messages_'.env('APP_LOCALE').'.js')}}"></script>    
<script src="{{asset('js/reclutamiento/plugins.js')}}" type="text/javascript"></script>

@endpush
