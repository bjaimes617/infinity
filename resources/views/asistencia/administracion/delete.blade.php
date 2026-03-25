@extends('layouts.main')
@section('title', 'Asistencia Operativa|Administracion|Eliminacion')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_asistencia','menu-is-opening menu-open')
@section('asistencia','active')
@section('menu_administracion','menu-is-opening menu-open')
@section('asistencia.administracion','active')
@section('asistencia.administracion.remove','active')
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
                        <li class="breadcrumb-item">Asistencia Operativa</li>
                        <li class="breadcrumb-item active">Eliminar</li>
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
                    <h3 class="card-title">Eliminacion de registros de asistencias <small>Se mostrara las asistencias agrupadas por cada 1er. Lider Inmediato</small></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form method="POST" action="{{route('asistencia.administracion.show.group')}}" id="formadministraciondeleted"  class="form-horizontal form-label-left">
                        <div class="row mb-3">    
                            @csrf 
                            <div class="col-md-12"> 
                                <div class="form-group">                                        
                                    <label class="col-form-label" for="fecha">Rango de Fecha <span style="color:red">*</span></label>  
                                    <div class="input-group"> 
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="text" name="fecha" id="fecha" autocomplete="off" required class="form-control daterange">                                                                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-4">                                    
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
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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
                            <div class="col-md-12">
                                <div class="form-group">                                      
                                    <div class="input-group">                                      
                                        <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                    </div> 
                                </div>
                            </div>                                              
                        </div>
                    </form> 
                    <div class="row mb-3">
                        <div class="col-md-12" style="display:none;" id="tablelistado" >                                                           
                            <p><h6>  Estos resultados son extraidos de forma integra de las asistencias del personal</h6></p>                               
                            <table id="datatable-delete" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>                                            
                                        <th>Cedula</th>
                                        <th>Nombres y Apellidos</th>                                          
                                        <th>&Aacute;rea de Trabajo</th>                                    
                                        <th>Unidad/Campa&ntilde;a</th> 
                                        <th>Segmento/Skill</th>                                         
                                        <th>Día Asistente</th>
                                        <th>Prefijo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>                                
                        </div>
                    </div>
                </div>
                <!-- /.card -->
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
<script src="{{asset('js/asistencia/plugins.js')}}"></script>
@endpush