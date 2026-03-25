@extends('layouts.main')
@section('title', 'Modificaciones de Posiciones')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones','menu-is-opening menu-open')
@section('inventario.migraciones','menu-is-opening menu-open')
@section('inventario.posiciones','active')
@section('inventario','active')
@section('migraciones','active')
@section('inventario.create.migracion','active')
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
                        <li class="breadcrumb-item active">Migraciones</li>
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
                        <i class="fas fa-map-marker"></i>
                        Generar migración de Posiciones
                    </h3>
                </div>   
                <form action="{{route('inventario.store.migracion')}}" method="POST" id="migrationCreate" class="form-horizontal form-label-left">
                    @csrf
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class=" col-form-label" for="stocksalida">Sede de Salida <span style="color:red">*</span></label>                                                                             
                                    <div class="input-group">                                                        
                                        <select name="stocksalida" id="stocksalida" required class="form-control select2 sedemigration" data-href="{{route('inventario.search.migracion')}}">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($stock as $dispos)
                                            <option value='{{$dispos->id}}'>{{$dispos->tipo}}</option>
                                            @endforeach                                               
                                        </select>                                             
                                    </div> 
                                </div> 
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class=" col-form-label" for="sedesalida">Posiciones De la Sede Seleccionada <span style="color:red">*</span></label>                                                                             
                                    <div class="input-group">                                                        
                                        <select name="sedesalida[]" id="sedesalida" required multiple="multiple" class="form-control select2" >                                                   
                                            <option value="">[SELECCIONE]</option>                                                                                        
                                        </select>                                             
                                    </div> 
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <h6><span style="color:red;"><b>Atención: </b></span> Mantenga presionado la tecla "CTL" y Seleccione las Posiciones en el orden en que desea realizar la migración y mantenga seleccionado los items que seran movidos en caso de estar vinculados</h6>
                        </div>
                        <hr>
                        <div class="col-md-12" >                           
                            <h6 class="text-center"><b>Equipos Electronicos Principales</b></h6>
                            <div class="row mb-2"  style="font-size:12px; ">                                       
                                <div class="col-md-6 text-center"> 
                                    <div class="form-group ">
                                      <label class=" col-form-label" for="equipo">Equipo(s) <span style="color:red">*</span></label>                                          
                                        <div class="input-group justify-content-center">
                                            <div class="icheck-primary d-inline">                                                                
                                                <input type="checkbox" checked class="checkboxdis" 
                                                       name="equipo" 
                                                       id="equipo">                                                                                                                                
                                                <label for="equipo"></label>
                                            </div>                                        
                                        </div>
                                    </div>                                      
                                </div>  
                                <div class="col-md-6 text-center"> 
                                    <div class="form-group ">
                                      <label class=" col-form-label" for="monitor">Monitor(es) <span style="color:red">*</span></label>                                          
                                        <div class="input-group justify-content-center">
                                            <div class="icheck-primary d-inline">                                                                
                                                <input type="checkbox" checked class="checkboxdis" 
                                                       name="monitor" 
                                                       id="monitor">                                                                                                                                
                                                <label for="monitor"></label>
                                            </div>                                        
                                        </div>
                                    </div>                                      
                                </div>                                
                            </div> 
                            <hr>
                            <h6 class="text-center"><b>Perifericos Disponibles</b></h6>
                            <div class="row mb-2" style="font-size:12px; ">
                                @foreach($perifericos as $clave => $valor)                                                                      
                                <div class="col-md-2 text-center"> 
                                    <div class="form-group">
                                        <label class="col-form-label" for="{{$clave}}">{{$valor}} <span style="color:red">*</span></label>                                          
                                         <div class="input-group  justify-content-center">
                                        <div class="icheck-primary d-inline">                                                                
                                            <input type="checkbox" class="checkboxdis" checked name="{{$clave}}" id="{{$clave}}">                                                                                                                                
                                            <label for="{{strtolower($clave)}}"></label>
                                        </div> 
                                             </div> 
                                    </div> 
                                </div>                                         
                                @endforeach
                            </div>
                            <hr>                                   
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class=" col-form-label" for="stockdestino">Sede de Destino <span style="color:red">*</span></label>                                                                             
                                    <div class="input-group">                                                        
                                        <select name="stockdestino" id="stockdestino" required class="form-control select2 sedemigration" data-href="{{route('inventario.search.migracion')}}">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($stock as $dispos)
                                            <option value='{{$dispos->id}}'>{{$dispos->tipo}}</option>
                                            @endforeach                                               
                                        </select>                                             
                                    </div> 
                                </div> 
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class=" col-form-label" for="sededestino">Posiciones De la Sede Seleccionada <span style="color:red">*</span></label>                                                                             
                                    <div class="input-group">                                                        
                                        <select name="sededestino[]" id="sededestino" required multiple="multiple" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>                                                                                        
                                        </select>                                             
                                    </div> 
                                </div> 
                            </div>
                            <div class="col-md-12">
                                    <div class="form-group">  
                                        <label class=" col-form-label" for="infopos">¿Migrar la Información de  las posiciones Seleccionadas a la posición de destino? <span style="color:red">*</span></label>                                                                                                                     
                                        <select class="form-control" id="infopos" name='infopos' required>
                                            <option value="S" selected>SI</option>
                                            <option value="N">NO</option>
                                        </select>  
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group">                                      
                            <div class="input-group">                                      
                                <button type="submit" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Procesar"><i class="fa fa-save"></i></button>                                                    
                            </div> 
                        </div>
                    </div>
                </form>
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
<script src="{{asset('js/inventario/autocomplete.js')}}"></script>
<script src="{{asset('js/inventario/inventario.js')}}"></script>
@endpush
