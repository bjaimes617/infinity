@extends('layouts.main')
@section('title', 'Gestion de personal|Control de Acceso|QR')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('acceso','active')
@section('control.acceso','menu-is-opening menu-open')
@section('control.qr','active')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">                        
                        <li class="breadcrumb-item">Gestión de Personal</li>
                        <li class="breadcrumb-item">Control de Acceso</li>
                        <li class="breadcrumb-item active">QR</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Listado para Generar los QR de Acceso </h3>                           
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">                           
                            <input type="hidden" name="url" value="{{route('control.qr.list')}}" id="url"> 
                            <div class="row">
                                <div class="col-sm-3">                                    
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                            </div>
                                            <select name="area_trabajo" id="area_trabajo" data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                                <option value="">[TODOS]</option>
                                                @foreach($area as $areas)
                                                <option value="{{$areas->id}}">{{$areas->nombre_campana}}</option> 
                                                @endforeach   
                                            </select>                                           
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label" for="carteras">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                        <div class="input-group mb-3">
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
                                        <label class="col-sm-12 col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                        <div class="input-group mb-3">
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
                                        <label class="col-sm-12 col-form-label" for="estatus">Estatus <span style="color:red">*</span></label>  
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                            </div>
                                            <select name="estatus" id="estatus" required class="form-control select2">                                                                                                 
                                                @foreach(["3"=>"No Impreso","1"=>"Emitido","2"=>"Extraviado"] as $key => $statu)
                                                <option value="{{$key}}">{{$statu}}</option> 
                                                @endforeach 
                                            </select>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">                                      
                                        <div class="input-group mb-3">                                      
                                            <button type="button" class="btn btn-primary btn-block" id="busquedalp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                        </div> 
                                    </div>
                                </div>

                                <div class="col-md-12" id="tablelistado" style="display: none;">                                 
                                    <p><h6>  Estos resultados son extraidos de forma integra del Listado de Personal de Directa Group</h6></p>     
                                    <form action="{{route('control.qr.generate')}}" method="POST" id="qr.generator.table" target='_blank' files="false" class="form-horizontal  form-label-left">                                
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table id="datatable-dir_personal" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="select_all"></th>
                                                            <th>Cedula</th>
                                                            <th>Nombres y Apellidos</th>                                            
                                                            <th>Gerencia</th>                                   
                                                            <th>Unidad / Campaña</th>                                   
                                                            <th>Segmento / Skill</th>                                   
                                                            <th>Cargo / Puesto</th>   
                                                            <th>Sede</th>
                                                            <th>Estatus QR</th>                                            
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                          
                                            <div class="col-md-12">
                                                  <br>
                                                <div class="form-group mb-3">
                                                    <div class="input-group mb-3">         
                                                        <button type="submit" class="btn btn-success btn-block" id="qrgererate" data-toggle="tooltip" data-placement="top" title="Generar Codigos QR"><i class="fa fa-qrcode"></i></button>
                                                    </div> 
                                                </div> 
                                            </div>
                                        </div>
                                    </form>
                                </div>
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
<script src="{{asset('js/control_acceso/control.js')}}" type="text/javascript"></script>
@endpush