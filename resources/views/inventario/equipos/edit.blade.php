@extends('layouts.main')
@section('title', 'Registro de Equipos en Stock')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_equipo','menu-is-opening menu-open')
@section('inventario.equipo','active')
@section('inventario','active')
@section('inventario.index.equipo','active')
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
                        <li class="breadcrumb-item active">Registrar</li>
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
                        <i class="fas fa-task"></i>
                        Modificación Caracteristicas del Equipo: {{$equipo->tipo."|".sprintf("%05d", $equipo->id)}}
                    </h3>
                </div>                        
                <input type="hidden" id="urlautocomplete" value="{{ asset("js/inventario/autocomplete.js") }}">

                <!--ruta para remplazar componentes busqueda del componente por stock para cargar el selector del nuevo componente-->                                            
                <input type="hidden" id="rutaAsignarPerifericos" value="{{route('inventario.autocompletes')}}"> 
                <div class="card-body">                       
                    <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-content-above-home-tab" data-toggle="pill" href="#custom-content-above-equip" role="tab" aria-controls="custom-content-above-equip" aria-selected="true"><i class="fas fa-desktop"></i> Equipo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-content-above-components-tab" data-toggle="pill" href="#custom-content-above-components" role="tab" aria-controls="custom-content-above-components" aria-selected="false"><i class="fas fa-project-diagram"></i> Modificar</a>
                        </li>                                        
                        <li class="nav-item">
                            <a class="nav-link" id="custom-content-above-add-componente-tab" data-toggle="pill" href="#custom-content-above-add-componente" role="tab" aria-controls="custom-content-above-periferics" aria-selected="false"><i class="fas fa-plus-circle"></i> Adicionar</a>
                        </li>              
                        <li class="nav-item">
                            <a class="nav-link" id="custom-content-above-unlink-tab" data-toggle="pill" href="#custom-content-above-unlink" role="tab" aria-controls="custom-content-above-desvincular" aria-selected="false"><i class="fas fa-unlink"></i> Desvicular</a>
                        </li>
                    </ul>                             
                    <div class="tab-content" id="custom-content-above-tabContent">
                        <div class="tab-pane fade show active" id="custom-content-above-equip" role="tabpanel" aria-labelledby="custom-content-above-home-tab">
                            <form method="POST" action="{{route('inventario.update.equipo',encrypt($equipo->id))}}" id="FromEditequipos" class='form-horizontal form-label-left'>
                                @method('PUT')
                                @csrf
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
                                                    <option value="{{$key}}" @if($key == $equipo->getrawOriginal('tipo')) selected @endif>{{$item}}</option>   
                                                    @endforeach                                                
                                                </select>                                             
                                            </div> 
                                        </div> 
                                    </div>                                   
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="marca">Marca <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="marca" value="{{$equipo->marca_pc}}" name="marca" autocomplete='off' required class="autocomplete form-control"  placeholder='XXXXX'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="serial">Serial del Equipo <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="serial" name="serial"  value="{{$equipo->serial_equipo,}}" required class="form-control" placeholder='XXXXX'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="fuente">Fuente de Poder <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <select name="fuente" id="fuente" required class="form-control select2">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach(config('app.EstatusDisks') as $key => $item)
                                                    <option value="{{$key}}" @if($key == $equipo->getrawOriginal('estado_fuente')) selected @endif>{{$item}}</option>   
                                                    @endforeach                                                
                                                </select>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="slot">Slots de Memoria <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="number" id="slot" name="slot" value="{{$equipo->slot}}" required min="1" max="5" class="form-control" placeholder='1'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="assignedram">RAM Asignada <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="number" id="assignedram" name="assignedram" value="{{$equipo->ram_assigned}}"  required min="0" max="4" class="form-control" placeholder='1'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="disks">Discos Asignados <span style="color:red">*</span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="number" id="disks" name="disks" value="{{$equipo->disks}}" required min="1" max="3" class="form-control" placeholder='1'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="macadress">LAN MacAdress <span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="macadress" name="macadress" value="{{$equipo->lan_mac_address}}" class="form-control" placeholder='00:00:00:00:00:00'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="ip">IP LAN Asignada de Sede<span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="ip" name="ip" value="{{$equipo->ip}}" class="form-control" placeholder='192.xxx.xxx.xxx'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="wlanmacadress">Wireless MacAdress <span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="wlanmacadress" name="wlanmacadress" value="{{$equipo->wlan_mac_address}}" class="form-control" placeholder='00:00:00:00:00:00'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="wip">IP WLAN Asignada de Sede<span style="color:red"></span></label>                                 
                                            <div class="input-group">                                       
                                                <input type="text" id="wip" name="wip" value="{{$equipo->wip}}" class="form-control" placeholder='192.xxx.xxx.xxx'>                                                                                               
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-center"><span style="color:red">Atención:</span> Antes de Asignar los Componentes, debe indicar la Cantidad de Discos y memorias asignadas al Equipo</p>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block" data-equip="{{$equipo->tipo."|".sprintf("%05d", $equipo->id)}}" data-toggle="tooltip" data-placement="top" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
                                    </div>
                                </div>    
                            </form>
                        </div>
                        </div> 
                        <div class="tab-pane fade" id="custom-content-above-components" role="tabpanel" aria-labelledby="custom-content-above-components-tab">                               
                            <div class="row mb-3">
                                @php $ram = 1;@endphp
                                @foreach($equipo->EquiposAComponentes()->orderby('componente','ASC')->get() as $key => $componentes)                                                        
                                @switch($componentes->componente)
                                @case("CPU")
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fas fa-microchip"></i>
                                                Modificacion de CPU
                                            </h4>
                                        </div>                                          
                                        <form method='POST' action='{{route('inventario.equipo.componente.update')}}' id='formCpu'>
                                            @csrf
                                            @method('PUT')
                                            <input type='hidden' name='KeyComponente' id='KeyComponente' value='{{$key}}'>                                                
                                            <input type='hidden' name='idequipo' id='idequipo' value='{{$equipo->id}}'>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="componente{{$key}}">CPU <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">                                       
                                                                <input name="componente{{$key}}" id="componente{{$key}}" class="form-control" disabled value="{{$componentes->id . " | " . strtoupper($componentes->tipo) ." de ". $componentes->generacion." Generacion | " . $componentes->capacidad . " " . $componentes->expresion}}">
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="estatus{{$key}}">Estatus CPU Actual <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <div class="form-group">                                                                                     
                                                                    <input type="checkbox" id="estatus{{$key}}" class="swichBoostrap"
                                                                           name="estatus{{$key}}" onchange="ChangeEstatusComponentequip('cpu',{{$key}})"                                                            
                                                                           data-backdrop="static" data-keyboard="false"                                                                                            
                                                                           data-href="{{route('inventario.equipo.update.componente',$componentes->id)}}"                                                                                           
                                                                           {{$componentes->assigned == 'X' ? 'checked' : ''}}
                                                                    data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                                </div>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="cambio{{$key}}">¿Cambiar? <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <select name="cambio{{$key}}" id="cambio{{$key}}" class="form-control select2"  
                                                                        @if($componentes->assigned == 'X') disabled @endif 
                                                                    onchange="DisplayChangeRemplaceComponentes('cpu',{{$key}},'formCpu');">                                                   
                                                                    <option value="">[SELECCIONE]</option>     
                                                                    @foreach($cambios as $keys => $values)
                                                                    <option value="{{$keys}}">{{$values}}</option>     
                                                                    @endforeach
                                                                </select>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="stock{{$key}}">Stock Disponibles <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                                </div>
                                                                <select name="stock{{$key}}" id="stock{{$key}}" class="form-control select2"
                                                                        onchange="SelectStockRemplaceComponentes('cpu',{{$key}});">                                                   
                                                                    <option value="">[SELECCIONE]</option>
                                                                    @foreach($sede as $item)
                                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                                    @endforeach                                                
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="new{{$key}}">Nuevo CPU <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-ship"></i></span>
                                                                </div>
                                                                <select name="new{{$key}}" id="new{{$key}}" required class="form-control select2">                                                   
                                                                    <option value="">[SELECCIONE]</option>                                                                                                                       
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>

                                                </div>                                                    
                                            </div>
                                            <div class="card-footer text-center display{{$key}}" style="display:none" >
                                                <button type="submit" class="btn btn-lg btn-secondary btn-block" data-toggle="tooltip" data-placement="top" title="Remplazar"> <i class=" fa fa-save"></i></button>   
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @php
                                $atribute = true;
                                @endphp
                                @break
                                @case("RAM")
                                <div class="col-md-12">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fas fa-memory"></i>
                                                Modificacion de RAM {{$ram}}
                                            </h4>
                                        </div>                                          
                                        <form method='POST' action='{{route('inventario.equipo.componente.update')}}' id='formram{{$key}}'>
                                            @csrf
                                            @method('PUT')
                                            <input type='hidden' name='KeyComponente' id='KeyComponente' value='{{$key}}'>   
                                            <input type='hidden' name='idequipo' id='idequipo' value='{{$equipo->id}}'>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="componente{{$key}}">RAM <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">                                       
                                                                <input name="componente{{$key}}" id="componente{{$key}}" class="form-control" disabled value="{{ $componentes->id . " | Tipo " . strtoupper($componentes->tipo) . " | de " . $componentes->capacidad . " " . $componentes->expresion}}">
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="estatus{{$key}}">Estatus RAM Actual <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <div class="form-group">                                                                                     
                                                                    <input type="checkbox" id="estatus{{$key}}" class="swichBoostrap"
                                                                           name="estatus{{$key}}" onchange="ChangeEstatusComponentequip('ram',{{$key}})"                                                            
                                                                           data-backdrop="static" data-keyboard="false"                                                                                            
                                                                           data-href="{{route('inventario.equipo.update.componente',$componentes->id)}}"                                                                                           
                                                                           {{$componentes->assigned == 'X' ? 'checked' : ''}}
                                                                    data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                                </div>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="cambio{{$key}}">¿Cambiar? <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <select name="cambio{{$key}}" id="cambio{{$key}}" class="form-control select2"  
                                                                        @if($componentes->assigned == 'X') disabled @endif 
                                                                    onchange="DisplayChangeRemplaceComponentes('ram',{{$key}},'formram{{$key}}');">                                                   
                                                                    <option value="">[SELECCIONE]</option>     
                                                                    @foreach($cambios as $keys => $values)
                                                                    <option value="{{$keys}}">{{$values}}</option>     
                                                                    @endforeach
                                                                </select>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="stock{{$key}}">Stock Disponibles <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                                </div>
                                                                <select name="stock{{$key}}" id="stock{{$key}}" class="form-control select2"
                                                                        onchange="SelectStockRemplaceComponentes('ram',{{$key}});">                                                   
                                                                    <option value="">[SELECCIONE]</option>
                                                                    @foreach($sede as $item)
                                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                                    @endforeach                                                
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="new{{$key}}">Nueva RAM <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-ship"></i></span>
                                                                </div>
                                                                <select name="new{{$key}}" id="new{{$key}}" required class="form-control select2">                                                   
                                                                    <option value="">[SELECCIONE]</option>                                                                                                                       
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>

                                                </div>                                                    
                                            </div>
                                            <div class="card-footer text-center display{{$key}}" style="display:none" >
                                                <button type="submit" class="btn btn-lg btn-secondary btn-block" data-toggle="tooltip" data-placement="top" title="Remplazar"> <i class=" fa fa-save"></i></button>   
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @php $ram++; @endphp
                                @break
                                @case("DISCO")
                                <div class="col-md-12">
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fas fa-hhd"></i>
                                                Modificacion de DISCO
                                            </h4>
                                        </div>                                          
                                        <form method='POST' action='{{route('inventario.equipo.componente.update')}}' id='formdisco{{$key}}'>
                                            @csrf
                                            @method('PUT')
                                            <input type='hidden' name='KeyComponente' id='KeyComponente' value='{{$key}}'>   
                                            <input type='hidden' name='idequipo' id='idequipo' value='{{$equipo->id}}'>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="componente{{$key}}">RAM <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">                                       
                                                                <input name="componente{{$key}}" id="componente{{$key}}" class="form-control" disabled value="{{$componentes->id . " | Tipo " . strtoupper($componentes->tipo) . " | de " . $componentes->capacidad . " " . $componentes->expresion}}">
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="estatus{{$key}}">Estatus Disco Actual <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <div class="form-group">                                                                                     
                                                                    <input type="checkbox" id="estatus{{$key}}" class="swichBoostrap"
                                                                           name="estatus{{$key}}" onchange="ChangeEstatusComponentequip('disco',{{$key}})"                                                            
                                                                           data-backdrop="static" data-keyboard="false"                                                                                            
                                                                           data-href="{{route('inventario.equipo.update.componente',$componentes->id)}}"                                                                                           
                                                                           {{$componentes->assigned == 'X' ? 'checked' : ''}}
                                                                    data-bootstrap-switch  data-off-color="danger" data-on-color="success"> 
                                                                </div>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="cambio{{$key}}">¿Cambiar? <span style="color:red"></span></label>                                 
                                                            <div class="input-group">                                       
                                                                <select name="cambio{{$key}}" id="cambio{{$key}}" class="form-control select2"  
                                                                        @if($componentes->assigned == 'X') disabled @endif 
                                                                    onchange="DisplayChangeRemplaceComponentes('disco',{{$key}},'formdisco{{$key}}');">                                                   
                                                                    <option value="">[SELECCIONE]</option>     
                                                                    @foreach($cambios as $keys => $values)
                                                                    <option value="{{$keys}}">{{$values}}</option>     
                                                                    @endforeach
                                                                </select>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="tipoestatus{{$key}}">Estado real del Disco <span style="color:red">*</span></label>                                                                       
                                                            <div class="input-group">
                                                                <select name="tipoestatus{{$key}}" id="tipoestatus{{$key}}" class="form-control select2">                                                   
                                                                    <option value="">[SELECCIONE]</option>
                                                                    @foreach(config('app.EstatusDisks') as $ky => $item)
                                                                    <option value="{{$ky}}" @if($componentes->estatus_disk == $ky) selected @endif>{{$item}}</option>   
                                                                    @endforeach                                                
                                                                </select>                                                                 
                                                            </div>
                                                        </div>
                                                        <p><span style="color:red;"><b>Atención:</b></span> Ajuste manualmente el <b>Estado Real del Disco</b> para notificar si el disco esta Critico.</p>
                                                    </div>
                                                    <div class="col-md-6 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="stock{{$key}}">Stock Disponibles <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                                </div>
                                                                <select name="stock{{$key}}" id="stock{{$key}}" class="form-control select2"
                                                                        onchange="SelectStockRemplaceComponentes('disco',{{$key}});">                                                   
                                                                    <option value="">[SELECCIONE]</option>
                                                                    @foreach($sede as $item)
                                                                    <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                                    @endforeach                                                
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 display{{$key}}" style="display:none">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="new{{$key}}">Nuevo Disco <span style="color:red">*</span></label>                                 
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-ship"></i></span>
                                                                </div>
                                                                <select name="new{{$key}}" id="new{{$key}}" required class="form-control select2">                                                   
                                                                    <option value="">[SELECCIONE]</option>                                                                                                                       
                                                                </select>                                             
                                                            </div> 
                                                        </div>
                                                    </div>

                                                </div>                                                    
                                            </div>
                                            <div class="card-footer text-center display{{$key}}" style="display:none" >
                                                <button type="submit" class="btn btn-lg btn-secondary btn-block" data-toggle="tooltip" data-placement="top" title="Remplazar"> <i class=" fa fa-save"></i></button>   
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @break
                                @endswitch
                                @endforeach 
                            </div>  
                        </div>
                        <div class="tab-pane fade" id="custom-content-above-add-componente" role="tabpanel" aria-labelledby="custom-content-above-add-componente-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-center">
                                            <button type="button" class="btn btn-lg btn-primary btn-block addcomponente" data-type="ram" data-href="{{route('inventario.add.componente.equipo','ram')}}" data-toggle="tooltip" data-placement="top" title="Nueva Ram"><i class="fas fa-plus-circle"></i></button>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <button type="button"  class="btn btn-lg btn-primary btn-block addcomponente" data-type="disco"data-href="{{route('inventario.add.componente.equipo','disco')}}" data-toggle="tooltip" data-placement="top" title="Nuevo Disco"><i class="fas fa-plus-circle"></i></button>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <button type="button" disabled="{{$atribute}}" class="btn btn-lg btn-primary btn-block addcomponente" data-type="cpu" data-href="{{route('inventario.add.componente.equipo','cpu')}}" data-toggle="tooltip" data-placement="top" title="Nueva CPU"><i class="fas fa-plus-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-content-above-unlink" role="tabpanel" aria-labelledby="custom-content-above-unlink-tab">
                            <br>
                            <form method="POST" action="null" id="FromUnlinkComponentes" class='form-horizontal form-label-left'>
                                <div class="row mb-3 text-center">
                                    <div class="col-md-12">
                                        <p> Cambie a OFF los componentes a desnvicular, se mantendran el stock y estatus actual del componente desvinculado.</p>
                                    </div>                                    
                                    @foreach($equipo->EquiposAComponentes()->orderby('componente','ASC')->get() as $key => $componentes)                                                        
                                    @switch($componentes->componente)
                                    @case("CPU")
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="unlikcpu">Desvincular CPU <span style="color:red"></span></label>                                 
                                            <div class="input-group justify-content-center"> 
                                                <input type="checkbox" id="unlikcpu" class="swichBoostrapOn"
                                                       name="unlikcpu"
                                                       data-backdrop="static" checked data-keyboard="false"
                                                       data-bootstrap-switch  data-off-color="danger" data-on-color="success">
                                            </div>
                                        </div>
                                    </div>  
                                    @break
                                    @case("RAM")
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="unlikram{{$componentes->id}}">Desvincular RAM {{$componentes->id}} <span style="color:red"></span></label>                                 
                                            <div class="input-group justify-content-center"> 
                                                <input type="checkbox" id="unlikram{{$componentes->id}}" class="swichBoostrapOn"
                                                       name="unlikram{{$componentes->id}}"
                                                       data-backdrop="static" checked data-keyboard="false"
                                                       data-bootstrap-switch  data-off-color="danger" data-on-color="success">
                                            </div>
                                        </div>
                                    </div>  
                                    @break
                                    @case("DISCO")
                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label class="col-form-label" for="unlikdisk{{$componentes->id}}">Desvincular Disco {{$componentes->id}} <span style="color:red"></span></label>                                 
                                            <div class="input-group justify-content-center"> 
                                                <input type="checkbox" id="unlikdisk{{$componentes->id}}" class="swichBoostrapOn"
                                                       name="unlikdisk{{$componentes->id}}"
                                                       data-backdrop="static" checked data-keyboard="false"
                                                       data-bootstrap-switch  data-off-color="danger" data-on-color="success">
                                            </div>
                                        </div>
                                    </div>  
                                    @break
                                    @endswitch
                                    @endforeach                                    
                                </div>
                            </form>
                        </div>                   
                    </div>                    
                </div>
            </div>
        </div>
        <div class="modal fade" id="newcomponente" tabindex="-1" role="dialog" aria-labelledby="newcomponente" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header sussces">
                        <h5 class="modal-title"> AÑADIR NUEVO COMPONENTE </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <form action="" method="POST" id="addNewcomponentes" class='form-horizontal form-label-left'>
                        @csrf
                        <input type='hidden' name='equipo' id='equipo' value='{{$equipo->id}}'>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label" for="addcomponente">Stock Disponibles <span style="color:red">*</span></label>                                 
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-building"></i></span>
                                            </div>
                                            <select name="addcomponente" id="addcomponente" data-href='{{route("inventario.search.add.componente.equipo")}}' class="form-control select2">                                                   
                                                <option value="">[SELECCIONE]</option>
                                                @foreach($sede as $item)
                                                <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                                @endforeach                                                
                                            </select>                                             
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-md-12" style="display:none;" id="tablelistado">  
                                    <h6> Estos datos son extraidos de forma integra desde la Base de Datos.</h6> 
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
                                    <div class="form-group">
                                        <label class="col-form-label" for="selectNewComponente">Nuevo Componente <span style="color:red">*</span></label>                                 
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-building"></i></span>
                                            </div>
                                            <select name="selectNewComponente" id="selectNewComponente" data-type="" class="form-control select2">                                                   
                                                <option value="">[SELECCIONE]</option>                                                                                                   
                                            </select>                                             
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"> Cancelar</button>
                            <button type="submit" class="btn btn-primary"> Añadir</button>
                        </div>
                    </form>
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
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
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