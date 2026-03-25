@extends('layouts.main')
@section('title', 'Modificaciones de Posiciones')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones','menu-is-opening menu-open')
@section('inventario.posiciones','active')
@section('inventario','active')
@section('inventario.index.posiciones','active')
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
    <input type="hidden" id="rutaAsignarEquipos"        value="{{route('inventario.AsignacionEquiposPosiciones')}}">     
    <input type="hidden" id="rutaAsignarPerifericos"    value="{{route('inventario.AsignacionPerifericosPosiciones')}}">     
    <section class="content">        
        <div class="container-fluid">                                    
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-map-marker"></i>
                        Modificación de Posicion sede:
                        @switch ($posicion->sede) @case(2) PLA| @break @case(3) SIE| @break @case(4) AUT| @break @case(5) MAC| @break @endswitch{{$posicion->number}}
                    </h3>
                </div>      
                <div class="card-body">                            
                    <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                        @if(!$posicion->PosicionMonitores->isEmpty()) 
                        <li class="nav-item">
                            <a class="nav-link " id="custom-content-above-monitor-tab" data-toggle="pill" href="#custom-content-above-monitor" role="tab" aria-controls="custom-content-above-monitor" aria-selected="true"><i class="fas fa-tv"></i> Monitor</a>
                        </li>                                
                        @endif                                
                        @if(!$posicion->PosicionEquipos->isEmpty())                                 
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-content-above-desktop-tab" data-toggle="pill" href="#custom-content-above-desktop" role="tab" aria-controls="custom-content-above-desktop" aria-selected="false"><i class="fas fa-desktop"></i> Equipos</a>
                        </li>                                
                        @endif
                        @if(!$posicion->PosicionPerifericos->isEmpty())
                        <li class="nav-item">
                            <a class="nav-link" id="custom-content-above-perifericos-tab" data-toggle="pill" href="#custom-content-above-perifericos" role="tab" aria-controls="custom-content-above-perifericos" aria-selected="false"><i class="fas fa-cubes"></i> Perifericos</a>
                        </li>
                        @endif
                        @if($posicion->PosicionMonitores->isEmpty() || $posicion->PosicionEquipos->isEmpty() || $posicion->PosicionPerifericos->isEmpty()) 
                        <li class="nav-item">
                            <a class="nav-link" id="custom-content-above-link-tab" data-toggle="pill" href="#custom-content-above-link" role="tab" aria-controls="custom-content-above-link" aria-selected="false"><i class="fas fa-plus-circle"></i> Vincular</a>
                        </li>                                 
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" id="custom-content-above-unlink-tab" data-toggle="pill" href="#custom-content-above-unlink" role="tab" aria-controls="custom-content-above-unlink" aria-selected="false"><i class="fas fa-unlink"></i> Desvincular/Actualizar</a>
                        </li>                                         
                    </ul>
                    <div class="tab-content" id="custom-content-above-tabContent">        
                        <!--MODIFICACION DEL MONITOR-->
                        @if(!$posicion->PosicionMonitores->isEmpty())  
                        <div class="tab-pane fade" id="custom-content-above-monitor" role="tabpanel" aria-labelledby="custom-content-above-desktop-tab">                                                    
                            <div class="row">
                                <div class="col-md-11">
                                    <div class="row mb-3">
                                        @foreach($posicion->PosicionMonitores as $key => $monitor)           
                                        <div class="col-md-12">
                                            <div class="card card-danger">
                                                <div class="card-header">
                                                    <h4 class="card-title">
                                                        <i class="fas fa-tv"></i>
                                                        Actualización del Monitor 
                                                    </h4>
                                                </div>
                                                <form method='POST' action='{{route('inventario.posiciones.update.Content',[$posicion->id,'monitor'])}}' id='frommonitor{{$key}}'  class='form-horizontal form-label-left'>
                                                    @csrf
                                                    @method('PUT')                                        
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="monitor{{$key}}">Monitor {{$monitor->id}} <span style="color:red">*</span></label>                                 
                                                                    <div class="input-group">                                       
                                                                        <input type="text" id="monitor{{$key}}" disabled name="monitor{{$key}}" value="{{'MON|'.sprintf("%05d", $monitor->id).'|'.$monitor->marca.'|'.$monitor->pulgadas.'"'}}" autocomplete='off' required class="form-control"  placeholder='XXXXX'>
                                                                    </div> 
                                                                </div>
                                                            </div>  
                                                            <div class="col-md-4 text-center">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="estatus{{$key}}">Estatus del Monitor {{$monitor->id}} <span style="color:red">*</span></label>                                 
                                                                    <div class="input-group justify-content-center">                                       
                                                                        <input type="checkbox" id="estatus{{$key}}" class="swichBoostrap"
                                                                               name="estatus{{$key}}"
                                                                               data-backdrop="static" data-keyboard="false"        
                                                                               onchange="ChangeEstatusComponentequip('monitor',{{$key}});"                                                                                                                                                                     
                                                                               data-href="{{route('inventario.posiciones.update.syncStatus',["monitor",$monitor->id])}}"
                                                                               {{ $monitor->assigned == 'X' ? 'checked' : '' }}
                                                                        data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                                    </div> 
                                                                </div>
                                                            </div> 
                                                             @permission('inventario.remplace.electronics')
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="cambio{{$key}}">¿Cambiar? <span style="color:red"></span></label>                                 
                                                                    <div class="input-group">                                       
                                                                        <select name="cambio{{$key}}" id="cambio{{$key}}" class="form-control select2"  
                                                                                @if($monitor->assigned == 'X') disabled @endif 
                                                                            onchange="DisplayChangeRemplaceComponentes('monitor',{{$key}},'frommonitor{{$key}}');">                                                   
                                                                            <option value="">[SELECCIONE]</option>     
                                                                            @foreach($cambios as $keys => $values)
                                                                            <option value="{{$keys}}">{{$values}}</option>     
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                             @endpermission
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="etiqueta{{$key}}">Estado de la Etiqueta fisica <span style="color:red">*</span></label>                                 
                                                                    <div class="input-group">             
                                                                        @php $idform = "frommonitor".$key ;@endphp
                                                                        <select name='etiqueta{{$key}}' required id='etiqueta{{$key}}' onchange="EtiquetasChange('frommonitor',{{$key}})" class='form-control select2'>
                                                                            @foreach(config('app.EstatusEtiqueta') as $kea => $valu)
                                                                            <option value='{{$kea}}' @if($kea == $monitor->getrawOriginal('etiqueta')) selected @endif>{{$valu}}</option>                                                       
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                </div>                                                    
                                                            </div>
                                                            <div class="col-md-12 text-center"><p>La opcion de la etiqueta que se encuentra selecionada, solo aplica para el Monitor actual.</p></div>
                                                            <div class="col-md-12 display{{$key}}" style='display:none;' >
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="new{{$key}}">Monitores <span style="color:red">*</span></label>                                 
                                                                    <div class="input-group">                                       
                                                                        <select name="new{{$key}}" id="new{{$key}}" class="form-control select2">                                                   
                                                                            <option value="">[SELECCIONE]</option>     
                                                                            @if(count($disponibles) != 0)
                                                                            <option value=''>[SELECCIONE]</option>
                                                                            @foreach($disponibles as $disponible)
                                                                            <option value='{{$disponible->id}}'> MON|{{sprintf("%05d", $disponible->id)}}| {{$disponible->marca}} | {{$disponible->pulgadas}}"</option>
                                                                            @endforeach
                                                                            @else
                                                                            <option value='' selected>NO HAY MONITORES DISPONIBLES PARA CAMBIAR</option>
                                                                            @endif
                                                                        </select>
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type='hidden' name='keyinput' id='keyinput' value='{{$key}}'>                                        
                                                    <div class="card-footer text-center" >
                                                        <button type="submit" class="btn btn-lg btn-primary btn-block"  disabled data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach 
                                    </div>
                                </div>
                                <div class="col-md-1">                                                               
                                    <button type="button" onclick="AddPosicion('monitor');" class="btn btn-block btn-primary" 
                                            data-type="monitor" data-toggle="tooltip" 
                                            data-placement="top" title="Nuevo Monitor"><i class="fas fa-plus-circle"></i></button>   
                                </div>                                                      
                            </div>                        
                        </div> 
                        @endif
                        <!--MODIFICACION DEL EQUIPO-->
                        @if(!$posicion->PosicionEquipos->isEmpty())  
                        <div class="tab-pane fade show active" id="custom-content-above-desktop" role="tabpanel" aria-labelledby="custom-content-above-desktop-tab">           
                            <div class="row">
                                @foreach($posicion->PosicionEquipos as $equ => $equipo)   
                                @php $equ = $equ."e"; @endphp
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fas fa-tv"></i>
                                                Actualización del Equipo  
                                            </h4>
                                        </div> 
                                        <form method='POST' action='{{route('inventario.posiciones.update.Content',[$posicion->id,'equipo'])}}' id='fromequipo{{$equ}}'  class='form-horizontal form-label-left'>
                                            @csrf
                                            @method('PUT')                                        
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">                                                        
                                                            <label class="col-form-label" for="responsable{{$equ}}">Responsable del Equipo: {{$equipo->EquipoAResponsable->Responsable->primer_nombre.' '.$equipo->EquipoAResponsable->Responsable->primer_apellido}} <span style="color:red">*</span></label>                                 
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                                </div>
                                                                <input type=" text" name="responsable{{$equ}}" id="responsable{{$equ}}" value="{{$equipo->EquipoAResponsable->Responsable->cedula_empleado}}" class="form-control">                                                            
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="equipo{{$equ}}">Equipo <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">                                       
                                                                <input type="text" id="equipo{{$equ}}" disabled name="equipo{{$equ}}" value="{{ $equipo->tipo."|".sprintf("%05d", $equipo->id)}}" autocomplete='off' required class="form-control"  placeholder='XXXXX'>
                                                            </div> 
                                                        </div>
                                                    </div>  
                                                    <div class="col-md-4 text-center">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="estatus{{$equ}}">Estatus del Equipo {{$equipo->id}} <span style="color:red">*</span></label>                                 
                                                            <div class="input-group justify-content-center">                                       
                                                                <input type="checkbox" id="estatus{{$equ}}" class="swichBoostrap"
                                                                       name="estatus{{$equ}}"
                                                                       data-backdrop="static" data-keyboard="false"        
                                                                       onchange="ChangeEstatusComponentequip('equipo','{{$equ}}');"                                                                                                                                                                     
                                                                       data-href="{{route('inventario.posiciones.update.syncStatus',["equipo",$equipo->id])}}"
                                                                       {{ $equipo->assigned == 'X' ? 'checked' : '' }}
                                                                data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                            </div> 
                                                        </div>
                                                    </div> 
                                                     @permission('inventario.remplace.electronics')
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="cambio{{$equ}}">¿Cambiar? <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <select name="cambio{{$equ}}" id="cambio{{$equ}}" class="form-control select2"  
                                                                        onchange="DisplayChangeRemplaceComponentes('equipo','{{$equ}}', '{{"fromequipo".$equ}}');"
                                                                        @if($equipo->assigned == 'X') disabled @endif>                                                   
                                                                    <option value="">[SELECCIONE]</option>     
                                                                    @foreach($cambios as $kys => $values)
                                                                    <option value="{{$kys}}">{{$values}}</option>     
                                                                    @endforeach
                                                                </select>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                     @endpermission
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="etiqueta{{$equ}}">Estado de la Etiqueta fisica <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">             
                                                                @php $idform = "fromequipo".$equ ;@endphp
                                                                <select name='etiqueta{{$equ}}' required id='etiqueta{{$equ}}' onchange="EtiquetasChange('fromequipo','{{$equ}}')" class='form-control select2'>
                                                                    @foreach(config('app.EstatusEtiqueta') as $keas => $valu)
                                                                    <option value='{{$keas}}' @if($keas == $equipo->getrawOriginal('etiqueta')) selected @endif>{{$valu}}</option>                                                       
                                                                    @endforeach
                                                                </select>
                                                            </div> 
                                                        </div>                                                    
                                                    </div>
                                                    <div class="col-md-12 text-center"><p>La opcion de la etiqueta que se encuentra selecionada, solo aplica para el Monitor actual.</p></div>

                                                    <div class="col-md-6 display{{$equ}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="stock{{$equ}}">Stock Disponibles <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                                </div>
                                                                <select name="stock{{$equ}}" id="stock{{$equ}}" class="form-control select2" onchange="SelectStockRemplaceComponentes('equipo','{{$equ}}');" >                                                   
                                                                    <option value="">[SELECCIONE]</option>
                                                                    @foreach($sede as $item)
                                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                                    @endforeach                                                
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 display{{$equ}}" style="display:none;">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="new{{$equ}}">Nuevo Equipo <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">                                                                
                                                                <select name="new{{$equ}}" id="new{{$equ}}" required class="form-control select2">                                                   
                                                                    <option value="">[SELECCIONE]</option>                                                                                                                       
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type='hidden' name='keyinput' id='keyinput' value='{{$equ}}'>                                        
                                            <div class="card-footer text-center">
                                                <button type="submit" class="btn btn-lg btn-primary btn-block"  disabled data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                                            </div>
                                        </form>                                       
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if(!$posicion->PosicionPerifericos->isEmpty())  
                        <div class="tab-pane fade" id="custom-content-above-perifericos" role="tabpanel" aria-labelledby="custom-content-above-perifericos-tab">           
                            <div class="row">
                                <div class="col-md-11">
                                    <div class="row mb-3">
                                        @foreach($posicion->PosicionPerifericos()->orderby('tipo','ASC')->get() as $per => $perifericos)
                                        @php $per = $per."per"; @endphp
                                        <div class="col-md-12">
                                            <div class="card card-success">
                                                <div class="card-header">
                                                    <h4 class="card-title">                                                     
                                                        @switch ($perifericos->getrawOriginal('tipo')) 
                                                        @case(1)<i class="fas fa-keyboard"></i> @break
                                                        @case(2)<i class="fas fa-mouse"></i> @break
                                                        @case(3) <i class="fas fa-headset"></i> @break
                                                        @case(4)<i class="fas fa-digital-tachograph"></i>@break
                                                        @case(5)<i class="fas fa-wifi"></i>@break
                                                        @case(6)<i class="fas fa-volume-off"></i>@break
                                                        @endswitch
                                                        Actualización del Periferico {{$perifericos->id}} | {{$perifericos->tipo}} Conexión: {{$perifericos->conexion}}
                                                    </h4>
                                                </div>
                                                <form method='POST' action='{{route('inventario.posiciones.update.Content',[$posicion->id,'perifericos'])}}' id='fromperiferico{{$per}}'  class='form-horizontal form-label-left'>
                                                    @csrf
                                                    @method('PUT')                                        
                                                    <div class="card-body">
                                                        <div class="row mb-3">                                                            
                                                            <div class="col-md-6 text-center">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="estatus{{$per}}">Estatus del {{strtolower($perifericos->tipo)}} <span style="color:red">*</span></label>                                 
                                                                    <div class="input-group justify-content-center">                                       
                                                                        <input type="checkbox" id="estatus{{$per}}" class="swichBoostrap"
                                                                               name="estatus{{$per}}"
                                                                               data-backdrop="static" data-keyboard="false"        
                                                                               onchange="ChangeEstatusComponentequip('periferico','{{$per}}');"                                                                                                                                                                     
                                                                               data-href="{{route('inventario.posiciones.update.syncStatus',["periferico",$perifericos->id])}}"
                                                                               {{ $perifericos->assigned == 'X' ? 'checked' : '' }}
                                                                        data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                                    </div> 
                                                                </div>
                                                            </div> 
                                                            @permission('inventario.remplace.electronics')
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="cambio{{$per}}">¿Cambiar? <span style="color:red"></span></label>                                 
                                                                    <div class="input-group">                                       
                                                                        <select name="cambio{{$per}}" id="cambio{{$per}}" class="form-control select2"  
                                                                                @if($perifericos->assigned == 'X') disabled @endif 
                                                                            onchange="DisplayChangeRemplaceComponentes('periferico','{{$per}}','fromperiferico{{$per}}');">                                                   
                                                                            <option value="">[SELECCIONE]</option>     
                                                                            @foreach($cambios as $keys => $values)
                                                                            <option value="{{$keys}}">{{$values}}</option>     
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                </div>
                                                            </div>     
                                                            @endpermission
                                                            <div class="col-md-12 display{{$per}}" style="display:none">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="stock{{$per}}">Stock Disponibles <span style="color:red">*</span></label>                                 
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                                        </div>
                                                                        <select name="stock{{$per}}" id="stock{{$per}}" class="form-control select2" onchange="SelectStockRemplaceComponentes('periferico','{{$per}}');" >                                                   
                                                                            <option value="">[SELECCIONE]</option>
                                                                            @foreach($sede as $item)
                                                                            <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                                            @endforeach                                                
                                                                        </select>                                             
                                                                    </div> 
                                                                </div>
                                                            </div>                                                           
                                                        </div>
                                                    </div>
                                                    <input type='hidden' name='keyinput' id='keyinput' value='{{$per}}'>                                        
                                                    <input type='hidden' name='Periferico' id='Periferico' value='{{$perifericos->id}}'>    
                                                    <div class="card-footer text-center" >
                                                        <button type="submit" class="btn btn-lg btn-primary btn-block"  disabled data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-1">                                                               
                                    <button type="button" onclick="AddPosicion('periferico');" class="btn btn-block btn-primary" 
                                            data-type="monitor" data-toggle="tooltip" 
                                            data-placement="top" title="Nuevo Periferico"><i class="fas fa-plus-circle"></i></button>   
                                </div> 
                            </div> 
                        </div>
                        @endif                        
                        <div class="tab-pane fade" id="custom-content-above-unlink" role="tabpanel" aria-labelledby="custom-content-above-unlink-tab">
                            <div class="card card-warning">     
                                <div class="card-header">
                                    <h4 class="card-title">                                                              
                                        <i class="fas fa-unlink"></i> Desvinculación/Actualización de la Posición  @switch ($posicion->sede) @case(2) PLA| @break @case(3) SIE| @break @case(4) AUT| @break @case(5) MAC| @break @endswitch{{$posicion->number}}
                                    </h4>
                                </div>   
                                <form action="{{route('inventario.posiciones.update',$posicion->id)}}" method="POST" id="posicion_update" class="form-horizontal  form-label-left">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-body">    
                                        <div class="row mb-3">                                                                
                                            <div class="col-md-4">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                 
                                                    <div class="input-group">                                       
                                                        <select name="area_trabajo" id="area_trabajo" required data-href="{{route('inventario.selectareas')}}" class="form-control select2">                                                   
                                                            <option value="">[SELECCIONE]</option>
                                                            @foreach($areas as $area)
                                                            <option value="{{$area->id}}" @if($onearea !== null && $onearea == $area->id) selected @endif>{{$area->nombre_campana}}</option> 
                                                            @endforeach   
                                                        </select>                                           
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="carteras">Unidad/Campaña <span style="color:red">*</span></label>                                 
                                                    <div class="input-group">                                       
                                                        <select  name='carteras[]' required  id="carteras" data-href="{{route('selector.carteras')}}" multiple="multiple" class="form-control select2">                                                                                                                                                 
                                                            @if(!$posicion->ToCartera->isEmpty()) 
                                                            @foreach($posicion->ToCartera()->get() as $cartera)
                                                            <option value='{{$cartera->id}}' selected>{{$cartera->nombre_cartera}}</option>    
                                                            @endforeach
                                                            @else
                                                            @endif  
                                                        </select>                                             
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="skill[]" id="skill" multiple="multiple" required="required" class="form-control select2">                                                                                                                                                      
                                                            @if(!$posicion->ToSkill->isEmpty()) 
                                                            @foreach($posicion->ToSkill()->get() as $skills)
                                                            <option value='{{$skills->id}}' selected>{{$skills->nombre_skill}}</option>    
                                                            @endforeach                                                        
                                                            @endif
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="gerentecargo">Gerente a Cargo <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="gerentecargo" id="gerentecargo" required="required" class="form-control select2">                                                                                                                                                      
                                                            @if($gerente != null)
                                                            <option value=''>[SELECCIONE]</option>                                                                                                                                                
                                                            <option value='{{$gerente->id}}' selected>{{$gerente->primer_nombre}} {{$gerente->primer_apellido}}</option>                                                                                                                                                                                                                                
                                                            @else
                                                            <option value=''>[SELECCIONE EL AREA]</option>                                                                                                                                                
                                                            @endif
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="cargo">Cargo del Responsable <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="cargo" id="cargo" required="required" class="form-control select2">                                                                                                                                                      
                                                            <option value=''>[SELECCIONE]</option>   
                                                            @if($cargo != null)
                                                            @foreach($cargo as $carg)
                                                            <option value='{{$carg->id}}' @if($carg->id ==  $posicion->cargo_id) selected @endif>{{$carg->nombre_cargo}}</option>                                                                                                                                                                                                                                
                                                            @endforeach
                                                            @else
                                                            <option value=''>[SELECCIONE EL AREA]</option>                                                                                                                                                
                                                            @endif
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="tipo">Posición De tipo <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="tipo" id="tipo" required="required" class="form-control select2">                                                                                                                                                      
                                                            <option value=''>[SELECCIONE]</option>   
                                                            @if($cargo != null)
                                                            @foreach( config('app.tipoposicion') as $postipy => $tipopos)
                                                            <option value='{{$postipy}}' @if($postipy ==  $posicion->tipo) selected @endif>{{$tipopos}}</option>                                                                                                                                                                                                                                
                                                            @endforeach                                                        
                                                            @endif
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="etiquetapos">Estado de la Etiqueta de la Posición <span style="color:red">*</span></label>  
                                                    <div class="input-group">                                        
                                                        <select name="etiquetapos" id="etiquetapos" required="required" class="form-control select2">                                                                                                                                                      
                                                            <option value=''>[SELECCIONE]</option>                                                           
                                                            @foreach( config('app.EstatusEtiqueta') as $etiqpos => $etiq)
                                                            <option value='{{$etiqpos}}' @if($etiqpos ==  $posicion->GetOriginal('etiqueta')) selected @endif>{{$etiq}}</option>                                                                                                                                                                                                                                
                                                            @endforeach                              
                                                        </select>                                              
                                                    </div> 
                                                </div>
                                            </div>
                                        @permission('inventario.unlink.all')
                                            @if(!$posicion->PosicionMonitores->isEmpty())                                                               
                                            @php $clase = ''; $clase = count($posicion->PosicionMonitores) >= 1 ? 'col-md-12' : 'col-md-6';@endphp                                                                
                                            @foreach($posicion->PosicionMonitores as $monitorunlink)
                                            <div class="{{$clase}}">
                                                <div class="form-group text-center">
                                                    <label class="col-form-label" for="unlink{{$monitorunlink->id}}">{{'MON|'.sprintf('%05d', $monitorunlink->id).'|'.$monitor->marca.'|'.$monitor->pulgadas.'"'}} </label>  
                                                    <div class="form-group">                                                                                   
                                                        <input type="checkbox" id="unlinkmon{{$monitorunlink->id}}" class="swichBoostrapOn"
                                                               name="unlinkmon{{$monitorunlink->id}}" checked="checked"
                                                               data-backdrop="static" data-keyboard="false"
                                                               data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                    </div>  
                                                </div>
                                                <input type='hidden' id='valuemon{{$monitorunlink->id}}' name='valuemon{{$monitorunlink->id}}' value="{{$monitorunlink->id}}">
                                            </div>                                                                
                                            @endforeach 
                                            @endif
                                            <!--desvinculacion de equipo-->
                                            @if(!empty($posicion->PosicionEquipos))                                                                                                                                
                                            @foreach($posicion->PosicionEquipos as $unlinkequipo)
                                            <div class="col-md-12">
                                                <div class="form-group text-center">                                                    
                                                    <label class="col-form-label" for="unlink{{$unlinkequipo->id}}">{{$unlinkequipo->tipo.'|'.sprintf("%05d", $unlinkequipo->id)}} </label>  
                                                    <div class="form-group">                                                                                     
                                                        <input type="checkbox" id="unlinkequipo{{$unlinkequipo->id}}" class="swichBoostrapOn"
                                                               name="unlinkequipo{{$unlinkequipo->id}}" checked="checked"
                                                               data-backdrop="static" data-keyboard="false"
                                                               data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                    </div>  
                                                </div>
                                                <input type='hidden' id='valueequip{{$unlinkequipo->id}}' name='valueequip{{$unlinkequipo->id}}' value="{{$unlinkequipo->id}}">
                                            </div>                                                               
                                            @endforeach
                                            @endif  
                                            @if(!$posicion->PosicionPerifericos->isEmpty()) 
                                            @foreach($posicion->PosicionPerifericos()->orderby('tipo','ASC')->get() as $perifericos)
                                            <div class="col-md-12">
                                                <div class="form-group text-center">                                            
                                                      <label class="col-form-label" for="unlink{{$perifericos->id}}">{{ $perifericos->tipo." ".$perifericos->id.' Conexión:'.$perifericos->conexion}} </label>  
                                                    <div class="form-group">                                                                                     
                                                        <input type="checkbox" name="unlink{{strtolower($perifericos->tipo).$perifericos->id}}" id="unlink{{strtolower($perifericos->tipo).$perifericos->id}}" class="swichBoostrapOn"
                                                               checked="checked" data-backdrop="static" data-keyboard="false"
                                                               data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                    </div>  
                                                </div>
                                                <input type='hidden' id="{{strtolower($perifericos->tipo).$perifericos->id}}" name="{{strtolower($perifericos->tipo).$perifericos->id}}" value="{{$perifericos->id}}">
                                            </div>                                                                 
                                            @endforeach
                                            <div class='col-md-12 text-center'>
                                                <p>Los Items con el Swich en <span style="color:red;">ROJO</span>, seran desvinculados de la posición, Si todos los items se desvincula, la posicion quedara Como Disponible.</p>
                                            </div>    
                                            @endif
                                        @endpermission
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button type="submit" class="btn btn-lg btn-primary btn-block"   id="unlink"  data-toggle="tooltip" data-placement="top" title="Procesar"> <i class=" fa fa-save"></i></button>   
                                    </div>
                                </form>
                            </div>                                
                        </div>
                        
                        <div class="tab-pane fade" id="custom-content-above-link" role="tabpanel" aria-labelledby="custom-content-above-link-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        @if($posicion->PosicionEquipos->isEmpty())                                    
                                        <div class="col-md-4 text-center">                                                                                                        
                                            <button type="button" class="btn btn-lg btn-primary btn-block" onclick="AddPosicion('equipo');" data-toggle="tooltip" data-placement="top" title="Nuevo Equipo"><i class="fas fa-plus-circle"></i></button>                                              
                                        </div>
                                        @endif
                                        @if($posicion->PosicionMonitores->isEmpty())                                    
                                        <div class="col-md-4 text-center">                                                                                                        
                                            <button type="button" class="btn btn-lg btn-primary btn-block" onclick="AddPosicion('monitor');" data-toggle="tooltip" data-placement="top" title="Nuevo Monitor"><i class="fas fa-plus-circle"></i></button>                                              
                                        </div>
                                        @endif
                                        @if($posicion->PosicionPerifericos->isEmpty())                                    
                                        <div class="col-md-4 text-center">                                                                                                        
                                            <button type="button" class="btn btn-lg btn-primary btn-block" id="buttonaddperiferico" data-toggle="tooltip" data-placement="top" title="Nuevo Periferico"><i class="fas fa-plus-circle"></i></button>                                              
                                        </div>
                                        @endif
                                    </div>
                                    <br/>
                                    <div class="col-md-12 text-center">
                                        <p>Haga Click En el boton de su preferencia para vincular a la posición</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="new" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header success">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>  
                            <!--formulario de agregar monitor nuevo-->
                            <form action="{{route('inventario.add.perifericos','monitor')}}" id="modalmonitor" style="display:none;" method="POST" class="form-horizontal form-label-left">
                                <input type="hidden" id="modalposition_addperiferic" name="modalposition_addperiferic" value="{{$posicion->id}}">
                                @csrf
                                <div class="modal-body">
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
                                                    @foreach($tablemonitor as $monitors)
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
                                </div><input type="hidden" id="modalposition_addperiferic" name="modalposition_addperiferic" value="{{$posicion->id}}">                            
                                <div class="modal-footer">                            
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary"> Añadir</button>
                                </div>
                            </form>
                            <!--formulario de agregar periferico nuevo-->
                            <form action="{{route('inventario.add.perifericos','periferico')}}" id="modalperiferico" style="display:none;" method="POST" class="form-horizontal form-label-left">
                                <input type="hidden" id="modalposition_addperiferic" name="modalposition_addperiferic" value="{{$posicion->id}}">
                                @csrf
                                <div class="modal-body">
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
                                                @foreach($newperifericos as $peris)
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
                                </div><input type="hidden" id="modalposition_addperiferic" name="modalposition_addperiferic" value="{{$posicion->id}}">                            
                                <div class="modal-footer">                            
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary"> Añadir</button>
                                </div>
                            </form>
                            <!--formulario de agregar equipos nuevo-->
                            <form action="{{route('inventario.add.perifericos','equipo')}}" id="modalequipo" style="display:none;" method="POST" class="form-horizontal form-label-left">
                                <input type="hidden" id="modalposition_addperiferic" name="modalposition_addperiferic" value="{{$posicion->id}}">
                                @csrf
                                <div class="modal-body">
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
                                        <div class="col-md-12 displayequipo" style="display:none;">
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
                                                <label class=" col-form-label" for="equipos">Asignar este Equipo <span style="color:red">*</span></label>                                                                                                                                        
                                                <div class="input-group mb-3">                                                           
                                                    <select id='equipos' name='equipos' required='required' class="select2 form-control" style="width: 100%;">
                                                        <option value=''>[SELECCIONE]</option>                                                                        
                                                    </select> 
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">                                                        
                                                <label class="col-form-label" for="responsable">Responsable del Equipo <span style="color:red">*</span></label>                                 
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                    </div>
                                                    <input type=" text" name="responsable" required id="responsable" class="form-control">                                                            
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <input type="hidden" id="modalposition_addperiferic" name="modalposition_addperiferic" value="{{$posicion->id}}">                            
                                <div class="modal-footer">                            
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary"> Añadir</button>
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
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>

<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/inventario/autocomplete.js')}}"></script>
<script src="{{asset('js/inventario/inventario.js')}}"></script>
@endpush
