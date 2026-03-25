@extends('layouts.main')
@section('title', 'Registro de Equipos en Stock')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_equipo','menu-is-opening menu-open')
@section('inventario.equipo','active')
@section('inventario','active')
@section('inventario.create.equipo','active')
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
                        <li class="breadcrumb-item active">Equipos</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">            
            <input type="hidden" id="rutaautocomplete" value="{{route('inventario.autocompletes')}}">  
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cubes"></i>
                        Registro de Equipo en Stock
                    </h3>
                </div>
                <form method="POST" action="{{route('inventario.store.equipo')}}" id="equipos_create" class='form-horizontal form-label-left'>
                    @csrf
                    <div class="card-body">                                
                        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-content-above-home-tab" data-toggle="pill" href="#custom-content-above-equip" role="tab" aria-controls="custom-content-above-equip" aria-selected="true"><i class="fas fa-desktop"></i> Equipo</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-profile-tab" data-toggle="pill" href="#custom-content-above-components" role="tab" aria-controls="custom-content-above-components" aria-selected="false"><i class="fas fa-layer-group"></i> Componentes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-messages-tab" data-toggle="pill" href="#custom-content-above-periferics" role="tab" aria-controls="custom-content-above-periferics" aria-selected="false"><i class="fas fa-box"></i> Almacenaje</a>
                            </li>              
                        </ul>     
                        <div class="tab-content" id="custom-content-above-tabContent">                                        
                            <div class="tab-pane fade show active" id="custom-content-above-equip" role="tabpanel" aria-labelledby="custom-content-above-home-tab">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class=" col-form-label" for="tipo">Tipo de Equipo <span style="color:red">*</span></label>                                                                             
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-filter"></i></span>
                                                </div>
                                                <select name="tipo" id="tipo" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach( ["1"=>"LAPTOP","2"=>"PC","3"=>"SRV"] as $key => $item)
                                                    <option value="{{$key}}">{{$item}}</option>   
                                                    @endforeach                                                
                                                </select>                                             
                                            </div> 
                                        </div> 
                                    </div>                                   
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="marcamonitor">Marca <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="marcamonitor" name="marcamonitor" autocomplete='off' required class="autocomplete form-control"  placeholder='XXXXX'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="serial">Serial del Equipo <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="serial" name="serial" required class="form-control" placeholder='XXXXX'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="fuente">Fuente de Poder <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <select name="tipo" id="tipo" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach(config('app.EstatusDisks') as $key => $item)
                                                    <option value="{{$key}}">{{$item}}</option>   
                                                    @endforeach                                                
                                                </select>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="slot">Slots de Memoria <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="number" id="slot" name="slot" required min="1" max="5" class="form-control" placeholder='1'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="assignedram">RAM Asignada <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="number" id="assignedram" name="assignedram" required min="1" max="4" class="form-control" placeholder='1'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="disks">Discos Asignados <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="number" id="disks" name="disks" required min="1" max="3" class="form-control" placeholder='1'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="macadress">LAN MacAdress <span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="macadress" name="macadress" class="form-control" placeholder='00:00:00:00:00:00'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="ip">IP LAN Asignada de Sede<span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="ip" name="ip" class="form-control" placeholder='192.xxx.xxx.xxx'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="wlanmacadress">Wireless MacAdress <span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="wlanmacadress" name="wlanmacadress" class="form-control" placeholder='00:00:00:00:00:00'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="wip">IP WLAN Asignada de Sede<span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="wip" name="wip" class="form-control" placeholder='192.xxx.xxx.xxx'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-center"><span style="color:red">Atención:</span> Antes de Asignar los Componentes, debe indicar la Cantidad de Discos y memorias asignadas al Equipo</p>
                                    </div>
                                </div>                                 
                            </div>
                            <div class="tab-pane fade" id="custom-content-above-components" role="tabpanel" aria-labelledby="custom-content-above-profile-tab">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="Stock">Stock <span style="color:red">*</span></label>                                 
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                </div>
                                                <select name="Stock" id="Stock" required class="form-control select2" data-href="{{route("inventario.AsignacionComponentesEquipos")}}">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($sede as $key => $item)
                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                    @endforeach                                                
                                                </select>                                             
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12 tablelistado" style="display: none;">
                                        <h6>  La Asignación de Componentes se Realizara del Stock Seleccionado.</h6>    
                                        <div class="ln_solid"></div>
                                        <table id="datatable-inventario" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                            <thead>
                                                <tr>                                            
                                                    <th>Componentes</th>
                                                    <th>Tipo</th>                                                    
                                                    <th>Capacidad</th>
                                                    <th>Disponibles</th>
                                                    <th>Asignados</th>
                                                    <th>Malos</th>                                                                                                                                               
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="col-md-12"> 
                                        <hr>
                                        <h6>Asignacion General de Componentes</h6>                             
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h4 class="card-title">
                                                    <i class="fas fa-microchip"></i>
                                                    Asignacion de CPU
                                                </h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="col-md-12">                                 
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cpu">CPU <span style="color:red">*</span></label>                                 
                                                        <div class="input-group">                                       
                                                            <select name="cpu" id="cpu" required class="form-control select2">                                                   
                                                                <option value="">[SELECCIONE]</option>                                                                                                            
                                                            </select>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header  bg-purple">
                                                <h4 class="card-title">
                                                    <i class="fas fa-hdd"></i>
                                                    Asignacion de Discos
                                                </h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="col-md-12">                                 
                                                    <div class="form-group">
                                                        <div id="incluirdiscos"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                    </div>
                                    <div class="col-md-4">
                                        <div class="card card-Lightblue">
                                            <div class="card-header bg-lightblue">
                                                <h4 class="card-title">
                                                    <i class="fas fa-memory"></i>
                                                    Asignacion de RAM
                                                </h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="col-md-12">                                 
                                                    <div class="form-group">
                                                        <div id="incluirrams"></div>                                                                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-content-above-periferics" role="tabpanel" aria-labelledby="custom-content-above-messages-tab">
                                <br/>

                                <div class="row mb-3"> 
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="asigstock">Asignar Equipo a Stock <span style="color:red">*</span></label>                                 
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                </div>
                                                <select name="asigstock" id="asigstock" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($sede as $key => $item)
                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                    @endforeach                                                
                                                </select>                                             
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-center"><span style="color:red">Atención:</span> Una vez cargado el equipo al Stock, podra ser seleccionado para asignar a una posición de Sede</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form> 
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
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
<script src="{{asset('js/inventario/autocomplete.js')}}"></script>
<script src="{{asset('js/inventario/inventario.js')}}"></script>
@endpush
