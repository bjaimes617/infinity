@extends('layouts.main')
@section('title', 'Gestion de personal')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('personal.list','active')
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
                        <li class="breadcrumb-item active">Listado</li>
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
                    <h3 class="card-title">Listado de personal por Gerencia </h3>                          
                    <div class="btn-group float-right">                                
                        @permission('personal.create')
                        <a href="{{route('personal.create')}}" style="position: relative;" class="btn btn-success " id="add" data-toggle="tooltip" data-placement="top" title="Añadir" ><i class="fa fa-plus-circle"></i></a>
                        @endpermission
                        @permission('personal.delete')
                        <a href="{{route('personal.delete')}}"class="btn btn-danger float-right" id="add" data-toggle="tooltip" data-placement="top" title="Ver ELiminados"><i class="fa fa-trash"></i></a>
                        @endpermission
                    </div>                          
                </div>
                <!-- /.card-header -->
                <div class="card-body">  
                    @csrf     
                    <input type="hidden" name="url" value="{{route('buscador.listado')}}" id="url">
                    <div class="row mb-3">
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
                                <label class="col-form-label" for="estatus">Estatus del Personal <span style="color:red">*</span></label>  
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                    </div>
                                    <select name="estatus[]" id="estatus" multiple required class="form-control select2" data-placeholder="[TODOS]">                                                                                         
                                        @foreach($status as $statu)
                                        <option value="{{$statu->id}}">{{$statu->estatus}}</option> 
                                        @endforeach 
                                    </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">                                      
                                <div class="input-group">                                      
                                    <button type="button" class="btn btn-primary btn-block" id="busquedalp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="display:none;" id="tablelistado" >                                                           
                        <p><h6>  Estos resultados son extraidos de forma integra del Listado de Personal de Directa Group</h6></p>                               
                        <table id="datatable-dir_personal" class="table compact table-sm text-sm table-bordered table-striped text-center">
                            <thead>
                                <tr>                                            
                                    <th>Cedula</th>
                                    <th>Nombre y Apellido</th>
                                    <th>Fecha de Ingreso</th>
                                    <th>Gerencia</th>                                    
                                    <th>Campaña</th> 
                                    <th>Skill</th> 
                                    <th>Cargo</th>   
                                    <th>Situ. Contractual</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>                                
                    </div>
                </div>
                <!-- /.card-body -->
            </div>           
        </div>
        <!-- /.row -->
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/gestion_personal/gestion.js')}}" type="text/javascript"></script>
@endpush