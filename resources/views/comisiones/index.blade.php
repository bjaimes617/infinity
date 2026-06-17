@extends('layouts.main')
@section('title', 'Gestion de Comisiones|Importar')
@section('menu_comisiones','menu-is-opening menu-open')
@section('comisiones','active')
@section('comisiones.import','active')
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
                        <li class="breadcrumb-item">Variales/Comisiones</li>
                        <li class="breadcrumb-item active">Comisiones y/o Variables</li>
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
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i>
                        Registrar Comisiones Y Variables
                    </h3>
                </div>    
                <div class="card-body">
                    <form action="{{route('comisiones.store')}}" method="POST" id="comisionesstore" class="form-horizontal form-label-left" enctype ='multipart/form-data'>
                         @csrf  
                        <div class="row mb-3">
                            <div class="col-md-4">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <select name="area_trabajo" id="area_trabajo" required data-href="{{route('selector.areas')}}" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
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
                                        <select name="carteras" id="carteras" required data-href="{{route('selector.carteras')}}" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                    <div class="input-group">                                       
                                        <select name="skill" id="skill" required class="form-control select2" data-href ="{{route('determinar.tipo.pago')}}">                                                   
                                            <option value="">[SELECCIONE]</option>                                                
                                        </select>                                              
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label  class="col-form-label" for="tipo_periodo">Tipo de Periodo</label><span style="color: red;"> *</span>
                                    <div class="input-group">                                       
                                        <select class="form-control" id="tipo_periodo" name="tipo_periodo" data-href="{{route('selector.previouscurrent')}}" disabled>
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($esquema as $key => $esquemas)
                                            <option value="{{$key}}">{{$esquemas}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="mes">Periodo <span style="color:red"> *</span></label>                          
                                    <div class="input-group"> 
                                        <input type="text" name="mes" id="mes" class="form-control datetime"
                                               disabled data-valormes="{{$timeMes}}" 
                                               data-requiredpermission="{{$permiso}}" 
                                               data-href="{{route('selector.corte')}}"
                                               required>
                                        <div class="input-group-prepend">
                                            <select class="form-control" id="periodo" name="periodo">
                                                <option value="">[CORTE]</option>
                                            </select> 
                                        </div>

                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="archivo">Archivo a Procesar... <span class="required" style="color:red;">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                            <label class="custom-file-label" for="archivo">Buscar Archivo en...</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit"  class="btn btn-primary cargamasiva"><i class="fa fa-upload"></i> Procesar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                       
                    </form>
                    <div class="row mb-2">
                        <div class="col-md-12">                                             
                            <table class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>                                  
                                <th COLSPAN="2" class="text-center">FORMATOS DISPONIBLES PARA DESCARGAR</th>      
                                </thead>
                                <tbody>                        
                                    <tr>
                                        <td><B> A.) Carga Operaciones y Unidades de Apoyo Quincenal</B></span></td>
                                        <td>
                                            <a class="btn btn-info btn-block enlaces disabled" id="A" href="{{\Storage::disk('comisiones')->url('formatos/OperacionesApoyoQuincenal.xlsx')}}">
                                                <i class="fa fa-download"></i> Formato Para Carga Variables / Comisiones Apoyo Quincenal</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><B> B.) Carga Operaciones Semanal Portabilidad Ejecutivos</B></span></td>
                                        <td>
                                            <a class="btn btn-info btn-block enlaces disabled" id="B" href="{{\Storage::disk('comisiones')->url('formatos/OperacionesSemanal.xlsx')}}">
                                                <i class="fa fa-download"></i> Formato de Comisiones Semanal Ejecutivos</a>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td><B> C.) Carga Variables </B></span></td>
                                        <td>
                                            <a class="btn btn-info btn-block enlaces disabled"  id="C" href="{{\Storage::disk('comisiones')->url('formatos/Variables.xlsx')}}">
                                                <i class="fa fa-download"></i> Formato Para Carga Variables</a>
                                        </td>
                                    </tr>                                     
                                    <!--    <td><apan style="color:#003399"><B> D.) Encabezados para Cargar la efectividad</B></apan></td>
                                    <td><a class="btn btn-unity" style="width:380px;" href="{{asset('/source/storage/app/public/formato efectividad.csv')}}"><i class="fa fa-download"></i> Formato para Efectividad (morosidad)</a></td>
                                    </tr>-->
                                </tbody>
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