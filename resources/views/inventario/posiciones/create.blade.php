@extends('layouts.main')
@section('title', 'Asignación de Equipos a Posiciones')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones','menu-is-opening menu-open')
@section('inventario.posiciones','active')
@section('inventario','active')
@section('inventario.create.posiciones','active')
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
                        <li class="breadcrumb-item active">Posiciones</li>
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
                        <i class="fas fa-cubes"></i>
                        Asignación dePosiciones
                    </h3>
                </div>                    
                <form action="{{route('inventario.store.posiciones')}}" method="POST" id="FromPosicionAdd" class="form-horizontal form-label-left">
                    @csrf
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-content-above-monitor-tab" data-toggle="pill" href="#custom-content-above-monitor" role="tab" aria-controls="custom-content-above-monitor" aria-selected="true"><i class="fas fa-tv"></i> Monitor</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-desktop-tab" data-toggle="pill" href="#custom-content-above-desktop" role="tab" aria-controls="custom-content-above-desktop" aria-selected="false"><i class="fas fa-desktop"></i> Equipos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-perifericos-tab" data-toggle="pill" href="#custom-content-above-perifericos" role="tab" aria-controls="custom-content-above-perifericos" aria-selected="false"><i class="fas fa-cubes"></i> Perifericos</a>
                            </li>                                        
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-location-tab" data-toggle="pill" href="#custom-content-above-location" role="tab" aria-controls="custom-content-above-location" aria-selected="false"><i class="fas fa-map-marker-alt"></i> Posición</a>
                            </li>                                         
                        </ul> 
                        <div class="tab-content" id="custom-content-above-tabContent"> 
                            <div class="tab-pane fade show active" id="custom-content-above-monitor" role="tabpanel" aria-labelledby="custom-content-above-monitor-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <table id="datatable-inventario" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr>                                            
                                                            <th>Marca</th>
                                                            <th>Asignados</th>                                                    
                                                            <th>Disponibles</th>
                                                            <th>Malos</th>
                                                            <th>Totales</th>                                                                                
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($monitor as $monitors)
                                                        <tr>
                                                            <td>{{$monitors->marca}}</td>
                                                            <td>{{$monitors->assigned}}</td>
                                                            <td>{{$monitors->available}}</td>
                                                            <td>{{$monitors->bad}}</td>
                                                            <td>{{ $monitors->assigned + $monitors->available +$monitors->bad}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>    
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class=" col-form-label" for="monitor">Monitores Disponibles <span style="color:red">*</span></label>                                                                             
                                                    <div class="input-group">                                                        
                                                        <select name="monitor[]" id="monitor" required multiple="multiple" class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>
                                                            @foreach($disponibles as $dispos)
                                                            <option value='{{$dispos->id}}'>MON|{{sprintf("%05d", $dispos->id)}}| {{$dispos->marca}} | {{$dispos->pulgadas}}"</option>
                                                            @endforeach                                               
                                                        </select>                                             
                                                    </div> 
                                                </div> 
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane fade" id="custom-content-above-desktop" role="tabpanel" aria-labelledby="custom-content-above-desktop-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-12">  
                                                <div class="form-group">
                                                    <label class=" col-form-label" for="stockequipo">Stocks Disponibles <span style="color:red">*</span></label>                                                                             
                                                    <div class="input-group">  
                                                        <select name="stockequipo" id="stockequipo" required data-href='{{route("inventario.AsignacionEquiposPosiciones")}}' class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>
                                                            @foreach($sede as $sedes)
                                                            <option value='{{$sedes->id}}'>{{$sedes->tipo}}</option>
                                                            @endforeach                                               
                                                        </select>                                                    
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-12 displayequipo" id="equiplistado" style="display:none;">
                                                <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
                                                <div class="ln_solid"></div>
                                                <table id="datatable-equipos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr>                                            
                                                            <th>Tipo</th>
                                                            <th>Asignados</th>                                                    
                                                            <th>Disponibles</th>
                                                            <th>Malos</th>
                                                            <th>Totales</th>                                                                                
                                                        </tr>
                                                    </thead>
                                                </table>   
                                            </div>
                                            <div class="col-md-12">  
                                                <div class="form-group">                                                    
                                                    <label class=" col-form-label" for="equipos">Asignar Equipo a la posición <span style="color:red">*</span></label>                                                                             
                                                    <div class="input-group">                                                           
                                                        <select id='equipos' name='equipos[]' multiple="multiple" required="required" class="form-control select2">
                                                            <option value="">[SELECCIONE]</option>                                                                  
                                                        </select> 
                                                    </div> 
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane fade" id="custom-content-above-perifericos" role="tabpanel" aria-labelledby="custom-content-above-perifericos-tab">
                                <div class="card">                                               
                                    <!-- /.card-header -->
                                    <div class="card-body">                                                     
                                        <div class="row mb-3">  
                                            <div class="col-md-12">  
                                                <div class="form-group">
                                                    <label class=" col-form-label" for="stockperiferico">Stocks Disponibles <span style="color:red">*</span></label>                                                                             
                                                    <div class="input-group">  
                                                        <select name="stockperiferico" id="stockperiferico" required data-href='{{route("inventario.AsignacionPerifericosPosiciones")}}' class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>
                                                            @foreach($sede as $sedes)
                                                            <option value='{{$sedes->id}}'>{{$sedes->tipo}}</option>
                                                            @endforeach                                               
                                                        </select>                                                    
                                                    </div> 
                                                </div>
                                            </div>   
                                            <div class='col-md-12 displayperifericos' style="display:none;" >
                                                <div class='row mb-3'>
                                                    @foreach($perifericos as $peris)
                                                    <div class="col-md-2 text-center hidencheckbox" id="displaynew{{strtolower($peris->tipo).'_'.$peris->getrawOriginal('conexion')}}"> 
                                                        <div class="form-group">
                                                            <p>{{$peris->tipo}} {{$peris->conexion}} <span class="required" style="color:red;"> * </span></p>                                                         
                                                            <div class="input-group text-center">
                                                                <div class="icheck-primary d-inline">                                                                
                                                                    <input type="checkbox" class="check" 
                                                                           name="new{{strtolower($peris->tipo).'_'.$peris->getrawOriginal('conexion')}}" 
                                                                           id="new{{strtolower($peris->tipo).'_'.$peris->getrawOriginal('conexion')}}">                                                                                                                                
                                                                    <label for="new{{strtolower($peris->tipo).'_'.$peris->getrawOriginal('conexion')}}"></label>
                                                                </div>
                                                                <span id="new{{strtolower($peris->tipo).'_'.$peris->getrawOriginal('conexion')}}msg" class="clearmsg"></span>
                                                            </div>
                                                        </div>  
                                                        <input type="hidden" name="nombreinputsperifericos[]" value="{{strtolower($peris->tipo).'_'.$peris->getrawOriginal('conexion')}}">                                                
                                                    </div>  
                                                    @endforeach  
                                                </div>
                                            </div>
                                            <div class="col-md-12 displayperifericos" style="display:none;">
                                                <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
                                                <div class="ln_solid"></div>
                                                <table id="datatable_perifericos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr>                                            
                                                            <th>Periferico</th>
                                                            <th>Conexión</th>   
                                                            <th>Asignados</th>
                                                            <th>Disponibles</th>                                                                                
                                                            <th>Malos</th>                                                                                                                                               
                                                            <th>Total</th>                                                                                                                                               
                                                        </tr>
                                                    </thead>
                                                </table>   
                                            </div>
                                        </div>                                    
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="custom-content-above-location" role="tabpanel" aria-labelledby="custom-content-above-location-tab">
                                @php $mapsede = array();
                                $mapsede = $sede; 
                                unset($mapsede[0]) @endphp
                                <div class="card"> 
                                    <div class="card-body">  
                                        <div class="row mb-3">
                                            <div class="col-md-12">  
                                                <div class="form-group">
                                                    <label class=" col-form-label" for="mapsede">Sedes <span style="color:red">*</span></label>                                                                             
                                                    <div class="input-group">  
                                                        <select name="mapsede" id="mapsede" required data-href='{{route("inventario.GetMapSVG")}}' class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>
                                                            @foreach($mapsede as $key => $seds)
                                                            <option value='{{$seds->id}}'>{{$seds->tipo}}</option>
                                                            @endforeach                                               
                                                        </select>                                                    
                                                    </div> 
                                                </div>                                                          
                                            </div> 
                                            <div class="col-md-12 text-center" id="mapsvg"></div>
                                            <div class="col-md-12 mapashow" style="display:none;">
                                                <div class="ln_solid"></div>                                                            
                                                <p>LEYENDA DE COLORES:<br><span style=" margin-bottom: 0; color:#FF0A0A"><b>ROJO</b></span>: Posición Ocupada <br> <span style=" margin-bottom: 0; color:#058203"><b>VERDE</b></span>: Posición Disponible <br> <span style=" margin-bottom: 0; color:#BE3A8D"><b>MAGENTA</b></span>: Posición Seleccionada</p>                                                           
                                            </div>
                                            <div class="col-md-12 mapashow" style="display:none;">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="posicionsel">Posiciones Seleccionadas <span style="color:red">*</span></label>                                                                             
                                                    <div class="input-group">  
                                                        <select name="posicionsel[]" id="posicionsel" multiple="multiple" required="required" class="form-control select2"></select>                                                    
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                                    <div class="input-group">                                       
                                                        <select name="area_trabajo" id="area_trabajo" required data-href="{{route('inventario.selectareas')}}" class="form-control select2">                                                   
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
                                                        <select  name='carteras' required  id="carteras" data-href="{{route('selector.carteras')}}" class="form-control select2">                                                                                                                                                 
                                                         <option value="">[SELECCIONE]</option>
                                                        </select>                                             
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="skill[]" id="skill" multiple="multiple" required="required" class="form-control select2">                                                                                                                                                      
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="gerentecargo">Gerente a Cargo <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="gerentecargo" id="gerentecargo" required class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>                                                
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="responsable">C.I. Responsable <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <input name='responsable' id='responsable' required class="form-control" placeholder='1XXXXXXXX'>                                           
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="cargo">Cargo del Responsable <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="cargo" id="cargo" required class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>                                                
                                                            @foreach($cargo as $cargos)
                                                            <option value="{{$cargos->id}}">{{$cargos->nombre_cargo}}</option>          
                                                            @endforeach
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="tipo">Posición De tipo <span style="color:red">*</span></label>                                                         
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                        </div>
                                                        <select name="tipo" id="tipo" required class="form-control select2">                       
                                                            <option value="">[SELECCIONE]</option>                                                
                                                            @foreach(config('app.tipoposicion') as $key => $tipos)
                                                            <option value="{{$key}}">{{$tipos}}</option>          
                                                            @endforeach    
                                                        </select>           
                                                    </div> 
                                                </div>
                                            </div>

                                        </div>
                                    </div>
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
