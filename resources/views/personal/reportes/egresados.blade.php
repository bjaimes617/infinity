@extends('layouts.main')
@section('title', 'Gestion de personal|Listado Central')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('reportes','active')
@section('personal.reportes','menu-is-opening menu-open')
@section('historico.egresados','active')
<!-- vista no operativa salta directo al controlador-->
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
                        <li class="breadcrumb-item">Gestión de Personal</li>
                        <li class="breadcrumb-item">Reportes</li>
                        <li class="breadcrumb-item active">Historico de Egresados</li>
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
                    <h3 class="card-title">Historico de Egresados</h3>                            
                </div>
                <!-- /.card-header -->
                <div class="card-body">  
                    <form action='{{route('personal.historico.create')}}' method='POST' id="create-report" class="form-horizontal">                            
                        @csrf                         
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <div class="form-group">                                        
                                    <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <input type="text" id="fecha" name="fecha" required clasS="form-control daterange">                                            
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-3">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="area_trabajo" id="area_trabajo" data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                            <option value="">[TODOS]</option>
                                            @foreach($areas as $area)
                                            <option value="{{$area->id}}">{{$area->nombre_campana}}</option> 
                                            @endforeach   
                                        </select>                                           
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-form-label" for="carteras">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="carteras" id="carteras" data-href="{{route('selector.carteras')}}" class="form-control select2">                                                   
                                            <option value="">[TODOS]</option>                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="skill" id="skill" class="form-control select2">                                                   
                                            <option value="">[TODOS]</option>                                                
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-form-label" for="tipo">Tipo de Reporte <span style="color:red">*</span></label>  
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="tipo" id="tipo" required class="form-control select2">                                                   
                                            <option value="">[SLECCIONE]</option> 
                                            @foreach(["1"=>"Personal Ingresado","2"=>"Personal Egresado"] as $key => $statu)
                                            <option value="{{$key}}">{{$statu}}</option> 
                                            @endforeach 
                                        </select>                                            
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">                                      
                                    <div class="input-group">                                      
                                        <button type="submit" class="btn btn-primary btn-block" id="busquedalp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Descargar"><i class="fa fa-download"></i></button>                                                    
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </form>
                </div>                                       
            </div>            
        </div>
    </section>
</div>
@endsection

@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')   
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('js/gestion_personal/reportes.js')}}" type="text/javascript"></script>
@endpush