@extends('layouts.main')
@section('title', 'Personal|Añadir')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('personal.list','active')
@section('content')
<style>
    .cambios {
        color: green;
        font-weight: bold;
        font-size: 14px;
    }
</style>
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
                        <li class="breadcrumb-item active">Historico de Cambios</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-12 col-md-12 col-xl-12">
                    <div class="card">                        
                        <div class="card-header">
                            <h3 class="card-title">Modificaciones realizadas por <b>{{$personal->modificado_por}}</b> en el expediente de Personal </h3>
                            <p class="float-right">                                                              
                                <a href="#" style="position: relative;"  class="btn btn-success disabled" id="view" data-toggle="tooltip" data-placement="top" title="Servicios Vinculados al Expediente" ><i class="fas fa-link"></i></a>                                
                                <!-- AutoServicios,-->
                            </p>
                        </div>                                                      
                        <div class="card-body">                                
                            <div class="bs-stepper">
                                <div class="bs-stepper-header" role="tablist">
                                    <!-- your steps here -->
                                    <div class="step" data-target="#personal-part">
                                        <button type="button" class="step-trigger" role="tab" aria-controls="personal-part" id="personal-part-trigger">
                                            <span class="bs-stepper-circle"><i><i class="fa fa-user"></i></i></span>
                                            <span class="bs-stepper-label">Persona</span>                                            
                                        </button>

                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#contacto-part">
                                        <button type="button" class="step-trigger" role="tab" aria-controls="contacto-part" id="contacto-part-trigger">
                                            <span class="bs-stepper-circle"><i><i class="fa fa-phone"></i></i></span>
                                            <span class="bs-stepper-label">Contacto</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#contrato-part">
                                        <button type="button" class="step-trigger" role="tab" aria-controls="contrato-part" id="contrato-part-trigger">
                                            <span class="bs-stepper-circle"><i class="fas fa-file-contract"></i></span>                                          
                                            <span class="bs-stepper-label">Contractual</span>
                                        </button>                                        
                                    </div>
                                </div>                                
                                <form action="{{route('personal.update',$personal->id)}}" method="POST" id='view_personal_form' class="form-horizontal form-label-left" data-parsley-validate >                                   
                                    <div class="bs-stepper-content">
                                        <!-- /Persona -->
                                        <div id="personal-part" class="content" role="tabpanel" aria-labelledby="personal-part-trigger">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="text-center circular--portrait">
                                                            <label for="image">
                                                                <img id="previewimg"  src="{{$personal->image != null ? \Storage::disk('personal')->url($personal->image) : asset('img/user.png') }}" data-toggle="tooltip" data-placement="top" title="Fotografia de la persona"> 
                                                            </label>
                                                            <br/>
                                                            <input type="file" id="image" name="image" onchange="loadFile(event)" />    
                                                        </div>  
                                                    </div>   
                                                </div>                                                    
                                                <div class='col-md-12'>
                                                    <div class='line'></div>
                                                    <br>
                                                    <p><small ><i class="fa fa-tag"></i> Datos Personales</Small></p>
                                                </div> 
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cedula_empleado">Cedula de Identidad <span style="color:red">*</span></label>                                 
                                                        <div  class="input-group">
                                                            <div class="input-group-prepend">
                                                                <select name="nacionalidad" id="nacionalidad"  required class="form-control">
                                                                    @foreach($nacionalidades as $key => $nacionalidad)
                                                                    <option value='{{$key}}' @if($personal->nacionalidad == $key) selected @endif> {{$nacionalidad}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <input type="text" class="form-control input-number" required name="cedula_empleado" 
                                                                   data-href="{{route('search.exist.personal')}}" 
                                                                   id="cedula_empleado"                                                                   
                                                                   value="{{$personal->cedula_empleado}}" 
                                                                   placeholder="123456789">                                           
                                                        </div>
                                                        @if(isset($cambios->nacionalidad))
                                                        <span class="cambios">Nuevo valor: {{$cambios->nacionalidad}}</span>
                                                        @endif 
                                                        @if(isset($cambios->cedula_empleado))
                                                        <span class="cambios">Nuevo valor: {{$cambios->cedula_empleado}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="nacimiento">Fecha de Nacimiento <span style="color:red">*</span></label>                                 
                                                        <div  class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control dateter" autocomplete="off" name="nacimiento" id="nacimiento"  
                                                                   required value="{{date('d/m/Y',strtotime($personal->fecha_nacimiento))}}" placeholder="01/01/1900"> 
                                                        </div>
                                                        @if(isset($cambios->fecha_nacimiento))
                                                        <span class="cambios">Nuevo valor: {{date('d/m/Y',strtotime($cambios->fecha_nacimiento))}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="nacademico">Grado Academico <span style="color:red">*</span></label>                                                                                    
                                                        <div class="input-group">                                        
                                                            <select name="nacademico" id="nacademico" required class="form-control">
                                                                <option value=''>[SELECCIONE]</option>
                                                                @foreach($especialidades as $key => $nacionalidad)
                                                                <option value='{{$key}}'  @if($personal->getrawOriginal("nivel_academico") == $key) selected @endif> {{$nacionalidad}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if(isset($cambios->nivel_academico))
                                                        <span class="cambios">Nuevo valor: {{$academicos[$cambios->nivel_academico]}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="especialidad">Especialidad <span style="color:red">*</span></label>                                                                                    
                                                        <div class="input-group">  
                                                            <input type="text" class="form-control" name="especialidad"                                                                   
                                                                   id="especialidad" required                                                               
                                                                   value="{{$personal->especialidad}}" 
                                                                   placeholder="Mercadeo/R.R.H.H.">                                                                 
                                                        </div>  
                                                        @if(isset($cambios->especialidad))
                                                        <span class="cambios">Nuevo valor: {{$cambios->especialidad}}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="pnombre">Primer Nombre <span style="color:red">*</span></label>                                                                                                                                      
                                                        <div class="input-group">  
                                                            <input type="text" class="form-control" autocomplete="off" name="pnombre" id="pnombre"  
                                                                   required value="{{$personal->primer_nombre}}" placeholder="Primer Nombre">                                                         
                                                        </div>
                                                        @if(isset($cambios->primer_nombre))
                                                        <span class="cambios">Nuevo valor: {{$cambios->primer_nombre}}</span>
                                                        @endif 
                                                    </div>
                                                </div>       
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="papellido">Primer Apellido <span style="color:red">*</span></label>                                                                                                                                      
                                                        <div class="input-group">  
                                                            <input type="text" class="form-control" autocomplete="off" name="papellido" id="papellido"  
                                                                   required value="{{$personal->primer_apellido}}" placeholder="Primer Apellido">                                                         
                                                        </div>  
                                                        @if(isset($cambios->primer_apellido))
                                                        <span class="cambios">Nuevo valor: {{$cambios->primer_apellido}}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="snombre">Segundo Nombre </label>                                                                                                                                      
                                                        <div class="input-group">  
                                                            <input type="text" class="form-control" autocomplete="off" name="snombre" id="snombre"  
                                                                   required value="{{$personal->segundo_nombre}}" placeholder="Segundo Nombre">                                                         
                                                        </div>
                                                        @if(isset($cambios->segundo_nombre))
                                                        <span class="cambios">Nuevo valor: {{$cambios->segundo_nombre}}</span>
                                                        @endif   
                                                    </div>
                                                </div>     
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="sapellido">Segundo Apellido <span style="color:red">*</span></label>                                                                                                                                      
                                                        <div class="input-group">  
                                                            <input type="text" class="form-control" autocomplete="off" name="sapellido" id="sapellido"  
                                                                   required value="{{$personal->segundo_apellido}}" placeholder="Segundo Apellido">                                                         
                                                        </div>  
                                                        @if(isset($cambios->segundo_apellido))
                                                        <span class="cambios">Nuevo valor: {{$cambios->segundo_apellido}}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="hijos">Cantidad de Hijos <span style="color:red">*</span></label>                                                                                                                                                                                         
                                                        <div class="input-group">                                        
                                                            <select name="hijos" id="hijos" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($hijo as $key => $hijos)
                                                                <option value='{{$key}}' @if($personal->hijos == $key) Selected @endif> {{$hijos}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div> 
                                                        @if(isset($cambios->hijos))
                                                        <span class="cambios">Nuevo valor: {{$cambios->hijos}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="rif">Registro Fiscal <span style="color:red">*</span></label>                                                    
                                                        <div class="input-group">                                        
                                                            <input type="text" class="form-control" autocomplete="off" name="rif" id="rif"  
                                                                   required value="{{$personal->rif}}" 
                                                                   minlength='6'maxlength='10' placeholder="V12345777">                                                         
                                                        </div> 
                                                        @if(isset($cambios->rif))
                                                        <span class="cambios">Nuevo valor: {{$cambios->rif}}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class="col-md-3">                                    
                                                    <div class="form-group text-center">
                                                        <label class="col-form-label" for="sexo">Genero <span style="color:red">*</span></label>                                                        
                                                        <fieldset> 
                                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                <label class="input-group btn bg-primary">
                                                                    <input type="radio" name="sexo" id="sexo" value="F" @if($personal->sexo == "F") checked @endif autocomplete="off"> Femenino
                                                                </label>                  
                                                                <label class="input-group btn bg-primary">
                                                                    <input type="radio" name="sexo" id="sexo" value="M" @if($personal->sexo == "M") checked @endif autocomplete="off"> Masculino
                                                                </label>
                                                            </div>
                                                        </fieldset> 
                                                        @if(isset($cambios->sexo))
                                                        <span class="cambios">Nuevo valor: {{$cambios->sexo}}</span>
                                                        @endif
                                                    </div>
                                                </div>                                                  
                                                <div class='col-md-12'>
                                                    <div class='line'></div>
                                                    <br>
                                                    <p><small ><i class="fa fa-tag"></i> Datos Bancarios</Small></p>
                                                </div>                                               
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="nombretitular">Titular de la Cuenta <span style="color:red">*</span></label>                                                                                                        
                                                        <div class="input-group">                                        
                                                            <input type="text" class="form-control" autocomplete="off" name="nombretitular" id="nombretitular"  
                                                                   required value="{{$personal->nombre_titular}}" placeholder="Nombre y Apellido">                                                                                                                 
                                                        </div>  
                                                    </div>
                                                </div>                                                 
                                                <div class="col-md-6">                                                
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cedulatitular">Cedula Identidad Titular <span style="color:red">*</span></label>                                                                                                        
                                                        <div  class="input-group">
                                                            <div class="input-group-prepend">
                                                                <select name="nacionalidad_cuenta" required id="nacionalidad_cuenta"  class="form-control">
                                                                    @foreach($nacionalidades as $key => $nacionalidad)
                                                                    <option value='{{$key}}' @if($personal->nacionalidad_cuenta == $key) selected @endif > {{$nacionalidad}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <input type="text" class="form-control input-number" name="cedulatitular"                                                                   
                                                                   id="cedulatitular" required
                                                                   minlength='6' 
                                                                   maxlength='13' 
                                                                   value="{{$personal->cedula_titular}}" 
                                                                   placeholder="123456789">                                           
                                                        </div>
                                                        @if(isset($cambios->nacionalidad_cuenta))
                                                        <span class="cambios">Nuevo valor: {{$cambios->nacionalidad_cuenta}}</span>
                                                        @endif 
                                                        @if(isset($cambios->cedula_titular))
                                                        <span class="cambios">Nuevo valor: {{$cambios->cedula_titular}}</span>
                                                        @endif  
                                                    </div>
                                                </div>                                             
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="banco">Banco <span style="color:red">*</span></label>                                                                                                                                                            
                                                        <div class="input-group">
                                                            <select name="banco" id="banco" required class="form-control">
                                                                <option value='' > [SELECCIONE]</option>
                                                                @foreach($banco as $banco)
                                                                <option value='{{$banco->id}}' @if($personal->bancos_id == $banco->id) selected @endif> {{$banco->bancos}}</option>
                                                                @endforeach
                                                            </select>                                                               
                                                        </div> 
                                                        @if(isset($cambios->bancos_id))
                                                        <span class="cambios">Nuevo valor: {{$banco_lp}}</span>
                                                        @endif 
                                                    </div>  
                                                </div>
                                                <div class="col-md-6">                                                
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="ctabancaria">Nro. Cuenta Bancaria <span style="color:red">*</span></label>                                                                                                        
                                                        <div  class="input-group">                                                            
                                                            <input type="text" class="form-control input-number" name="ctabancaria"                                                                   
                                                                   id="ctabancaria" required
                                                                   minlength='20' 
                                                                   maxlength='20' 
                                                                   data-href="{{route('search.exist.cuentas')}}"
                                                                   value="{{$personal->cuenta_bancaria}}" 
                                                                   placeholder="010XXXXXXXXXX">                                           
                                                        </div>
                                                        @if(isset($cambios->cuenta_bancaria))
                                                        <span class="cambios">Nuevo valor: {{$cambios->cuenta_bancaria}}</span>
                                                        @endif 
                                                    </div>
                                                </div>   
                                                <div class="col-md-12">
                                                    <div class="row mb-3">
                                                        <div class="col-md-12 text-center">
                                                            <p><b>Informacion sobre Asignación de Tarjetas TodoTicket</b></p>
                                                        </div>
                                                        <div class="col-md-6 text-center">
                                                            <div class="form-group text-center">
                                                                <label class="col-form-label" for="integral">Asignada TodoTicket Integral <span style="color:red">*</span></label>
                                                                <fieldset>
                                                                    <input type="hidden" name="integral" value="{{$personal->todoticket_integral}}">
                                                                    <input type="checkbox" id="integral" disabled name="integral" value="{{$personal->todoticket_integral}}"
                                                                           @if($personal->todoticket_integral == "1") checked data-state="true" @else data-state="false" @endif 
                                                                           data-bootstrap-switch data-off-color="danger"
                                                                           data-on-color="success">
                                                                </fieldset>
                                                            </div>
                                                            @if(isset($cambios->todoticket_integral))
                                                                <span class="cambios">Nuevo valor: {{$integral_lp}}</span>
                                                            @endif 
                                                        </div>
                                                        <div class="col-md-6 text-center">
                                                            <div class="form-group text-center">
                                                                <label class="col-form-label" for="alimentacion">Asignada TodoTicket Alimentacion <span style="color:red">*</span></label>
                                                                <fieldset>
                                                                    <input type="hidden" name="alimentacion" value="{{$personal->todoticket_alimentacion}}">
                                                                    <input type="checkbox" id="alimentacion" disabled name="alimentacion" value="{{$personal->todoticket_alimentacion}}" 
                                                                            @if($personal->todoticket_alimentacion == "1") checked data-state="true" @else data-state="false" @endif 
                                                                           data-bootstrap-switch 
                                                                           data-off-color="danger" data-on-color="success">
                                                                </fieldset>
                                                            </div>
                                                              @if(isset($cambios->todoticket_alimentacion))
                                                                <span class="cambios">Nuevo valor: {{$alimentacion_lp}}</span>
                                                            @endif  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">                                                 
                                                    <div class="btn-group btn-block float-right">                                                        
                                                        <button type="button" class="btn btn-primary validateNext" onclick="stepper.next()"  data-toggle="tooltip" data-placement="top" title="Siguiente"> <i class=" fa fa-arrow-right"></i></button>                                                                                                           
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="contacto-part" class="content" role="tabpanel" aria-labelledby="contacto-part-trigger">
                                            <div class="row mb-3">                                                 
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="email">Correo Electronico <span style="color:red">*</span></label>                                                                                                        
                                                        <div class="input-group">                                                           
                                                            <input type="email" class="form-control" name="email"                                                                   
                                                                   id="email" required 
                                                                   value="{{$personal->email}}" 
                                                                   placeholder="example@example.com">    
                                                        </div> 
                                                        @if(isset($cambios->email))
                                                        <span class="cambios">Nuevo valor: {{$cambios->email}}</span>
                                                        @endif 
                                                    </div>                                                
                                                </div>
                                                @php if($personal->activetransporte == 1) $style = 'style='; else $style = 'style=display:none;'; @endphp
                                                <div class="col-md-6 ">
                                                    <div class="form-group text-center">
                                                        <label class="col-form-label" for="activetransporte">Utiliza rutas de Transporte <span style="color:red">*</span></label>                                                                                                        
                                                        <fieldset>                                                           
                                                            <input type="checkbox" id="activetransporte" @if ($personal->activetransporte == '1') checked @endif disabled name="activetransporte" data-bootstrap-switch data-off-color="danger" data-on-color="success"> 
                                                        </fieldset>
                                                    </div>
                                                </div>      
                                                <div class="col-md-12 displaytransporte" {{$style}}>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="htransporte">Horario del Transporte <span style="color:red">*</span></label>                                                                                                        
                                                                <div class="input-group ">                                                                                         
                                                                    <select name="htransporte" id="htransporte" class="form-control">
                                                                        <option value=''> [SELECCIONE]</option>
                                                                        @foreach($horariosTransporte as $key => $horario)
                                                                        <option value='{{$key}}' @if($personal->htransporte == $key) selected @endif> {{$horario}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                @if(isset($cambios->htransporte))                                                     
                                                                <span class="cambios">Nuevo valor: {{$horariosTransporte[$cambios->htransporte]}}</span>
                                                                @endif   
                                                            </div>
                                                        </div>     
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="zona_transporte_id">Horario del Transporte <span style="color:red">*</span></label>                                                                                                        
                                                                <div class="input-group">                                                                                         
                                                                    <select name="zona_transporte_id" id="zona_transporte_id" class="form-control">
                                                                        <option value=''> [SELECCIONE]</option>
                                                                        @foreach($zonatransporte as $key => $zonas)
                                                                        <option value='{{$zonas->id}}' @if($personal->zona_transporte_id == $zonas->id) selected @endif> {{$zonas->zonatransporte}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                @if(isset($cambios->zona_transporte_id))
                                                                <span class="cambios">Nuevo valor: {{$zona_lp}}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                                <div class="col-md-6">                                                                                                  
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="tlf1">Telefono Habitación <span style="color:red">*</span></label>                                                                                                        
                                                        <div class="input-group">      
                                                            <input type="text" class="form-control  input-number" name="tlf1"                                                                   
                                                                   id="tlf1" required 
                                                                   maxlength="11"
                                                                   value="{{$personal->telefono_casa}}" 
                                                                   placeholder="2120000000">                                                           
                                                        </div> 
                                                        @if(isset($cambios->telefono_casa))
                                                        <span class="cambios">Nuevo valor: {{$cambios->telefono_casa}}</span>
                                                        @endif  
                                                    </div>                                           
                                                </div>
                                                <div class="col-md-6">                                                                                                  
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="tlf2">Telefono Celular <span style="color:red">*</span></label>                                                                                                        
                                                        <div class="input-group">      
                                                            <input type="text" class="form-control  input-number" name="tlf2"                                                                   
                                                                   id="tlf2" required 
                                                                   maxlength="11"
                                                                   value="{{$personal->telefono_movil}}" 
                                                                   placeholder="4120000000">                                                           
                                                        </div> 
                                                        @if(isset($cambios->telefono_movil))
                                                        <span class="cambios">Nuevo valor: {{$cambios->telefono_movil}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div>  

                                                <div class="col-md-12">
                                                    <label class="col-form-label"> Estado Donde se ubica <span class="required" style="color:red;"> * </span></label>
                                                    <div class="form-group">
                                                        <div class="input-group">                                                      
                                                            <select class="form-control" id="region" required name="region">
                                                                <option value="">[Seleccione]</option>
                                                                @foreach($region as $regiones)
                                                                <option value="{{$regiones->id}}" @if($personal->estado_id == $regiones->id) selected @endif >{{$regiones->nombre_estado}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if(isset($cambios->estado_id))
                                                        <span style="color: green; font-weight: bold; font-size: 14px;">Nuevo valor: {{$region_lp}}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">                                                                                                  
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="sector">Sector del Domicilio <span style="color:red">*</span></label>                                                                                                        
                                                        <div class="input-group">      
                                                            <input type="text" class="form-control" name="sector"                                                                   
                                                                   id="sector" required                                                                    
                                                                   value="{{$personal->sector}}"
                                                                   placeholder="Caracas">                                                           
                                                        </div> 
                                                        @if(isset($cambios->sector))
                                                        <span class="cambios">Nuevo valor: {{$cambios->sector}}</span>
                                                        @endif  
                                                    </div>                                           
                                                </div> 
                                                <div class="col-md-6">                                                                                                  
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="direccion">Dirección de Habitación <span style="color:red">*</span></label>                                                                                                        
                                                        <div class="input-group">      
                                                            <input type="text" class="form-control" name="direccion"                                                                   
                                                                   id="direccion" required                                                                    
                                                                   value="{{$personal->direccion}}" 
                                                                   placeholder="Av./Calle/Carretera xxxxxxx">                                                           
                                                        </div> 
                                                        @if(isset($cambios->direccion))
                                                        <span class="cambios">Nuevo valor: {{$cambios->direccion}}</span>
                                                        @endif  
                                                    </div>                                           
                                                </div> 
                                                <div class="col-md-12">                                                 
                                                    <div class="btn-group btn-block float-right">
                                                        <button type="button" class="btn btn-info" onclick="stepper.previous()" data-toggle="tooltip" data-placement="top" title="Anterior"> <i class=" fa fa-arrow-left"></i></button>                                                                       
                                                        <button type="button" class="btn btn-primary validateNext" onclick="stepper.next()"  data-toggle="tooltip" data-placement="top" title="Siguiente"> <i class=" fa fa-arrow-right"></i></button>                                                                                                           
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                          

                                        <div id="contrato-part" class="content" role="tabpanel" aria-labelledby="contrato-part-trigger">
                                            <div class="row mb-3">
                                                <div class='col-md-12'>                                                                                                  
                                                    <p><small ><i class="fa fa-tag"></i> Información de Ingreso</Small></p>
                                                </div>
                                                <div class="col-md-4">                                                                                                                                                                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="profit">Profit </label>      
                                                        <div class="input-group">                                        
                                                            <input type="text" class="form-control input-number" name="profit"                                                                   
                                                                   id="profit"                                                                    
                                                                   value="{{$personal->cod_profit}}" 
                                                                   placeholder="123456">                                                           
                                                        </div> 
                                                        @if(isset($cambios->cod_profit))
                                                        <span class="cambios">Nuevo valor: {{$cambios->cod_profit}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div>
                                                <div class="col-md-4">                                                   
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="nomina">Tipo de Nomina <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">                                        
                                                            <select name="nomina" id="nomina" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($nomina as $key => $nomina)
                                                                <option value='{{$nomina->id}}' @if($nomina->id == 1) 
                                                                    data-contrato1="{{config('app.digacontrato1')}}" 
                                                                    data-contrato2="{{config('app.digacontrato2')}}" 
                                                                    @else  
                                                                    data-contrato1="{{config('app.dgcontrato1')}}" 
                                                                    data-contrato2="{{config('app.dgcontrato2')}}" 
                                                                    @endif 
                                                                    @if($personal->nomina_id == $nomina->id) selected @endif>{{$nomina->nombre_nomina}}</option>
                                                                @endforeach
                                                            </select>                                                           
                                                        </div> 
                                                        @if(isset($cambios->nomina_id))
                                                        <span class="cambios">Nuevo valor: {{$nomina_lp}}</span>
                                                        @endif   
                                                    </div>                                           
                                                </div>                                                
                                                <div class="col-md-4">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="fingreso">Fecha de Ingreso <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">   
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control dateter"
                                                                   autocomplete="off"
                                                                   name="fingreso"                                                                   
                                                                   id="fingreso" required                                                                    
                                                                   value="{{date('d/m/Y',strtotime($personal->fecha_ingreso))}}" 
                                                                   placeholder="01/01/2023">                                                          
                                                        </div>
                                                        @if(isset($cambios->fecha_ingreso))
                                                        <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->fecha_ingreso))}}</span>
                                                        @endif  
                                                    </div>                                           
                                                </div>
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="hentrada">Hora de Entrada <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control time"
                                                                   autocomplete="off"
                                                                   name="hentrada"                                                                   
                                                                   id="hentrada" required                                                                    
                                                                   value="{{date('H:i a',strtotime($personal->horario_entrada))}}" 
                                                                   placeholder="08:00 am">                                                            
                                                        </div> 
                                                        @if(isset($cambios->horario_salida))
                                                        <span class="cambios">Nuevo valor: {{date("h:i a",strtotime($cambios->horario_salida))}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div>
                                                <div class="col-md-6">                                                                                                                                                                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="hsalida">Hora de Salida <span style="color:red">*</span></label>   
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control time"
                                                                   autocomplete="off"
                                                                   name="hsalida"                                                                   
                                                                   id="hsalida" required                                                                    
                                                                   value="{{date('H:i a',strtotime($personal->horario_salida))}}" 
                                                                   placeholder="05:00 pm">                                                            
                                                        </div>
                                                        @if(isset($cambios->horario_salida))
                                                        <span class="cambios">Nuevo valor: {{date("h:i a",strtotime($cambios->horario_salida))}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div> 
                                                <div class="col-md-4">                                                                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="jornada">Tipo de Jornada Laboral <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">                                                        
                                                            <select name="jornada" id="jornada" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($jornadas as $key => $jornada)
                                                                <option value='{{$key}}' @if($personal->getrawOriginal('jornada') == $key) selected @endif> {{$jornada}}</option>
                                                                @endforeach
                                                            </select>   
                                                        </div> 
                                                        @if(isset($cambios->jornada))
                                                        <span class="cambios">Nuevo valor: {{ $jornadas[$cambios->jornada] }}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div>                                                 
                                                <div class="col-md-4">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="horario">Horario Laboral <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                            </div>
                                                            <select name="horario" id="horario" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($horariolab as $key => $horario)
                                                                <option value='{{$key}}' @if($personal->getrawOriginal('horario_operativo') == $key) selected @endif> {{$horario}}</option>
                                                                @endforeach
                                                            </select> 
                                                        </div>  
                                                        @if(isset($cambios->horario_operativo))
                                                        <span class="cambios">Nuevo valor: {{$horariolab[$cambios->horario_operativo]}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div>                                              
                                                <div class="col-md-4">                                                                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="libranza">Esquema de Libranzas <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">                                                        
                                                            <select name="libranza" id="libranza" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($libranza as $key => $libranzas)
                                                                <option value='{{$libranzas->id}}'@if($personal->libranza_id == $libranzas->id) selected @endif>{{$libranzas->cod_libranza}} | {{$libranzas->libranzas}}</option>
                                                                @endforeach
                                                            </select>   
                                                        </div> 
                                                        @if(isset($cambios->libranza_id))
                                                        <span class="cambios">Nuevo valor: {{$libranza_lp}}</span>
                                                        @endif    
                                                    </div>                                           
                                                </div>                                                
                                                <div class='col-md-12'>
                                                    <div class='line'></div>
                                                    <br>
                                                    <p><small ><i class="fa fa-tag"></i> Información del Contrato</Small></p>
                                                </div>
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="estatuscargo">Estatus del Cargo a ejercer <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">   
                                                            <select name="estatuscargo" id="estatuscargo" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($tipoContrato as $key => $estatuscargo)
                                                                <option value='{{$key}}'  @if($personal->getrawOriginal('estatuscargo')) == $key) selected @endif>{{$estatuscargo}}</option>
                                                                @endforeach
                                                            </select> 
                                                        </div>   
                                                        @if(isset($cambios->estatuscargo))
                                                        <span class="cambios">Nuevo valor: {{ $tipoContrato[$cambios->estatuscargo] }}</span>
                                                        @endif  
                                                    </div>                                           
                                                </div>   
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cargo">Cargo a Ejercer <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">
                                                            <select name="cargo" id="cargo" required class="select2 form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($cargo as $key => $cargos)
                                                                <option value='{{$cargos->id}}' @if($personal->cargo_id == $cargos->id) selected @endif >{{$cargos->nombre_cargo}}</option>
                                                                @endforeach
                                                            </select> 
                                                        </div> 
                                                        @if(isset($cambios->cargo_id))
                                                        <span class="cambios">Nuevo valor: {{$cargo_lp}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div>                                         
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="contrato1">Firma del 1er. Contrato <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control dateter"
                                                                   autocomplete="off"
                                                                   name="contrato1"                                                                   
                                                                   id="contrato1" required                                                                    
                                                                   value="{{date('d/m/Y',strtotime($personal->contrato_1))}}" 
                                                                   placeholder="01/01/2024">                                                            
                                                        </div>
                                                        @if(isset($cambios->contrato_1))
                                                        <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->contrato_1))}}</span>
                                                        @endif 
                                                    </div>                                           
                                                </div> 
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="finalcontrato1">Finaliza el 1er. Contrato <span style="color:red">*</span></label>                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control dateter"
                                                                   autocomplete="off"
                                                                   name="finalcontrato1"                                                                   
                                                                   id="finalcontrato1" required                                                                    
                                                                   value="{{date('d/m/Y',strtotime($personal->finalizacion_1))}}" 
                                                                   placeholder="01/01/2024">                                                            
                                                        </div> 
                                                        @if(isset($cambios->finalizacion_1))
                                                        <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->finalizacion_1))}}</span>
                                                        @endif   
                                                    </div>                                           
                                                </div>                                                                                         
                                                <div class="col-md-6">                                                   
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="contrato2">Firma del 2do. Contrato </label>                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control dateter"
                                                                   autocomplete="off"
                                                                   name="contrato2"                                                                   
                                                                   id="contrato2"                                                                    
                                                                   value="{{$personal->contrato_2 != null ? date('d/m/Y',strtotime($personal->contrato_2)) : null}}" 
                                                                   placeholder="01/01/2024">                                                            
                                                        </div>  
                                                        @if(isset($cambios->contrato_2))
                                                        <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->contrato_2))}}</span>
                                                        @endif    
                                                    </div>                                           
                                                </div> 
                                                <div class="col-md-6">
                                                    <label class="col-form-label" for="finalcontrato2">Finaliza el 2do. Contrato </label>                                                                                                                                                      
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control dateter"
                                                                   autocomplete="off"
                                                                   name="finalcontrato2"                                                                   
                                                                   id="finalcontrato2"                                                                    
                                                                   value="{{$personal->finalizacion_2 != null ? date('d/m/Y',strtotime($personal->finalizacion_2)) : null}}" 
                                                                   placeholder="01/01/2024">                                                            
                                                        </div>
                                                        @if(isset($cambios->finalizacion_2))
                                                        <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->finalizacion_2))}}</span>
                                                        @endif     
                                                    </div>                                           
                                                </div> 
                                                <div class="col-md-12">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="scontrato">Situación Contractual <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                                            </div>
                                                            <select name="scontrato" id="scontrato" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($tipoContrato as $key => $contractual)
                                                                <option value='{{$key}}' @if($personal->getrawOriginal('situacion_contractual') == $key) selected @endif >{{$contractual}}</option>
                                                                @endforeach
                                                            </select>                                                    
                                                        </div> 
                                                        @if(isset($cambios->situacion_contractual))
                                                        <span class="cambios">Nuevo valor: {{$tipoContrato[$cambios->situacion_contractual]}}</span>
                                                        @endif  
                                                    </div>
                                                </div> 
                                                <div class='col-md-12'>                                                                                                     
                                                    <p><small><i class="fas fa-map-marker-alt"></i></i> Ubicación en la Estructura Organizativa</Small></p>
                                                </div>                                                 
                                                <div class="col-md-4">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="areatrabajo">Gerencia <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">                                
                                                            <select name="areatrabajo" id="areatrabajo" required data-href="{{route('check.areas')}}" 
                                                                    class="form-control select2" style="width: 100%;">
                                                                <option value=''> [SELECCIONE]</option>   
                                                                @foreach($area as $gerencias)
                                                                <option value='{{$gerencias->id}}' @if($personal->area_trabajo_id == $gerencias->id) selected @endif>{{$gerencias->nombre_campana}}</option>   
                                                                @endforeach
                                                            </select>
                                                        </div> 
                                                        @if(isset($cambios->area_trabajo_id))
                                                        <span class="cambios">Nuevo valor: {{$area_lp}}</span>
                                                        @endif
                                                    </div>
                                                </div> 
                                                <div class="col-md-4">                                                   
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cartera">Unidad/Campaña <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">                                                           
                                                            <select name="cartera" id="cartera" required data-href="{{route('check.carteras')}}" 
                                                                    class="form-control select2" style="width: 100%;">
                                                                <option value=''> [SELECCIONE GERENCIA]</option>  
                                                                @foreach($cartera as $carteras)
                                                                <option value='{{$carteras->id}}' @if($personal->cartera_id == $carteras->id) selected @endif>{{$carteras->nombre_cartera}}</option>   
                                                                @endforeach
                                                            </select> 
                                                        </div>
                                                        @if(isset($cambios->cartera_id))
                                                        <span class="cambios">Nuevo valor: {{$cartera_lp}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @php if(\Auth::user()->haspermission('personal.cecos.autorice')){
                                                $autoriza = "true"; 
                                                $dis= "style=''"; 
                                                }else {
                                                $autoriza = "false";
                                                $dis="style='display:none;'"; 
                                                }
                                                @endphp
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>  
                                                        <div class="input-group">                                                                                                                   
                                                            <select name="skill" id="skill" required 
                                                                    data-actporcentaje="{{$autoriza}}" 
                                                                    data-href="{{route('selector.centros.costos')}}"
                                                                    class="form-control select2" style="width: 100%;">
                                                                <option value=''> [SELECCIONE GERENCIA]</option>  
                                                                @foreach($skill as $skills)
                                                                <option value='{{$skills->id}}' @if($personal->skill_id == $skills->id) selected @endif>{{$skills->nombre_skill}}</option>   
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if(isset($cambios->skill_id))
                                                        <span class="cambios">Nuevo valor: {{$skill_lp}}</span>
                                                        @endif 
                                                    </div>                                                    
                                                </div>    
                                                <div class="col-md-12" id="usuario_crm_porta_display" @if(in_array($personal->cartera_id,[20,89])) style="" @else style="display:none;" @endif>
                                                        <label class="col-form-label" for="usuario_crm_portabilidad">Usuario CRM Portabilidad <span style="color:red">*</span></label>
                                                        <div class="form-group">
                                                            <div class="input-group ">
                                                                <input type="text"
                                                                        class="form-control"
                                                                        autocomplete="off"
                                                                            name="usuario_crm_portabilidad"
                                                                            id="usuario_crm_portabilidad"
                                                                            value="{{old('usuario_crm_portabilidad', $personal->usuario_crm_portabilidad)}}"
                                                                            >
                                                            </div>
                                                            @if(isset($cambios->usuario_crm_portabilidad))
                                                                <span class="cambios">Nuevo valor: {{$cambios->usuario_crm_portabilidad}}</span>
                                                            @endif
                                                        </div>
                                                    </div>                                                                         
                                                <div class='col-md-12'>                                                                                                     
                                                    <p><small><i class="fas fa-users"></i> Asignación de Gerente, Lideres/Jefes Inmediatos</Small></p>
                                                </div>
                                                <div class="col-md-12">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="gerente">Gerente de Área <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            </div>
                                                            <select name="gerente" id="gerente" class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($gerente as $key => $gerentes)
                                                                <option value='{{$gerentes->id}}' @if($personal->gerente_area_id == $gerentes->id)Selected @endif>{{$gerentes->nombre}}</option>
                                                                @endforeach
                                                            </select>                                                    
                                                        </div> 
                                                        @if(isset($cambios->gerente_area_id))
                                                        <span class="cambios">Nuevo valor: {{$gerente_lp}}</span>
                                                        @endif        
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cjefeinmediato">Cargo 1er. Lider/Jefe Inmediato <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                            </div>
                                                            <select name="cjefeinmediato" id="cjefeinmediato" data-href="{{route('check.jefes')}}" class="select2 form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($cargo as $cargos)
                                                                <option value='{{$cargos->id}}'>{{$cargos->nombre_cargo}}</option>
                                                                @endforeach
                                                            </select>                                                    
                                                        </div>                                         
                                                    </div>
                                                </div>
                                                <div class="col-md-6">                                                   
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="jinmediato">1er. Lider/Jefe Inmediato <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            </div>
                                                            <select name="jinmediato" id="jinmediato"  required class="form-control">
                                                                @if($supervisor1 != null)
                                                                <option value='{{$supervisor1->id}}' selected> {{$supervisor1->nombre}}</option>                                                                
                                                                @else
                                                                  <option value='' selected>[SELECCIONE]</option>   
                                                                @endif
                                                            </select>                                                    
                                                        </div>     
                                                        @if(isset($cambios->jefe_inmediato))
                                                        <span class="cambios">Nuevo valor: {{$jefe_lp}}</span>
                                                        @endif     
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="cjefeinmediato2">Cargo 2do. Lider/Jefe Inmediato <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                            </div>
                                                            <select name="cjefeinmediato2" id="cjefeinmediato2" data-href="{{route('check.jefes')}}" class="select2 form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($cargo as $cargos)
                                                                <option value='{{$cargos->id}}'>{{$cargos->nombre_cargo}}</option>
                                                                @endforeach
                                                            </select>                                                    
                                                        </div>                                         
                                                    </div>
                                                </div>
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="jinmediato2">2do. Lider/Jefe Inmediato <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            </div>
                                                            <select name="jinmediato2" id="jinmediato2" required class="form-control">                                                                
                                                                @if($supervisor2 != null)
                                                                <option value='{{$supervisor2->id}}' selected> {{$supervisor2->nombre}}</option>                                                         
                                                                @else
                                                                <option value='' selected>[SELECCIONE]</option>   
                                                                @endif
                                                            </select>                                                    
                                                        </div>  
                                                        @if(isset($cambios->jefe_inmediato_segundo))
                                                        <span class="cambios">Nuevo valor: {{$jefe2_lp}}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class='col-md-12'>                                                                                                     
                                                    <p><small><i class="fa fa-tag"></i> Información adicional</Small></p>
                                                </div>  
                                                <div class="col-md-6">                                                  
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="sede">Sede Laboral <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                            </div>
                                                            <select name="sede" id="sede" required class="form-control">
                                                                <option value=''> [SELECCIONE]</option>   
                                                                @foreach($sedes as $key => $sede)
                                                                <option value='{{$sede->nombre_sede}}' @if($personal->sede_laboral == $sede->nombre_sede)Selected @endif>{{$sede->real_name}}</option>   
                                                                @endforeach
                                                            </select>                                                    
                                                        </div>   
                                                        @if(isset($cambios->sede_laboral))
                                                        <span class="cambios">Nuevo valor: {{$cambios->sede_laboral}}</span>
                                                        @endif 
                                                    </div>
                                                </div>
                                                <div class="col-md-6">                                                    
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="status">Estatus del Personal <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                            </div>
                                                            <select name="status" id="status" required class="form-control">                                                                
                                                                <option value=''> [SELECCIONE]</option>   
                                                                @foreach($status as $estatus)
                                                                <option value='{{$estatus->id}}'  @if($personal->estatus_id == $estatus->id)Selected @endif>{{$estatus->estatus}}</option>   
                                                                @endforeach
                                                            </select>                                                    
                                                        </div>
                                                        @if(isset($cambios->estatus_id))
                                                        <span class="cambios">Nuevo valor: {{$estatus_lp}}</span>
                                                        @endif       
                                                    </div>
                                                </div> 
                                                <div class="col-md-4 region-estado" @if($personal->sede_laboral != "STV") style="display:none;" @endif>
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="region">Region <span style="color:red">*</span></label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                                            </div>
                                                            <select class="form-control" id="region" required name="region" data-href="{{route('selector.estados')}}">
                                                                <option value="">[Seleccione]</option>     
                                                                @foreach($region as $regiones)
                                                                <option value="{{$regiones->id}}" @if($estado->region_id == $regiones->id) selected @endif>{{$regiones->nombre_region}}</option>     
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if(isset($cambios->estado_id))
                                                        <span style="color: green; font-weight: bold; font-size: 14px;">Nuevo valor: {{$region_lp}}</span>
                                                        @endif   
                                                    </div> 
                                                </div>                                 
                                                <div class="col-md-4 region-estado" @if($personal->sede_laboral != "STV") style="display:none;" @endif>
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="estado">Estado <span style="color:red">*</span></label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                            </div> 
                                                            <select class="form-control" id="estado" required name="estado">
                                                                <option value="{{$estado->id}}">{{$estado->nombre_estado}}</option>                                                           
                                                            </select>
                                                        </div> 
                                                        @if(isset($cambios->estado_id))
                                                        <span style="color: green; font-weight: bold; font-size: 14px;">Nuevo valor: {{$estado_lp}}</span>
                                                        @endif 
                                                    </div> 
                                                </div>
                                                <div class="col-md-4 region-estado" @if($personal->sede_laboral != "STV") style="display:none;" @endif>
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="ciudad">Ciudad <span style="color:red">*</span></label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                            </div> 
                                                            <select class="form-control" id="ciudad"  name="ciudad" @if($personal->sede_laboral != "STV") required @endif>
                                                                <option value="{{$ciudad->id}}">{{$ciudad->ciudad}}</option>                                                           
                                                            </select>
                                                        </div>
                                                        @if(isset($cambios->ciudad_id))
                                                        <span style="color: green; font-weight: bold; font-size: 14px;">Nuevo valor: {{$ciudad_lp}}</span>
                                                        @endif  
                                                    </div>                                    
                                                </div>
                                                @php     
                                               
                                                if(isset($cambios->estatus_id)){
                                                   if($cambios->estatus_id == 8){
                                                    $styleVacaciones="style=''";
                                                  }else{
                                                    $styleVacaciones="style='display:none;'";
                                                  }
                                                    //cambios para el div de egresos
                                                    if($cambios->estatus_id == 2 || $cambios->estatus_id == 6){                                            
                                                        $display="style=''";
                                                    }else{
                                                        $display="style='display:none;'";
                                                    }                                            
                                                } else {
                                                    $styleVacaciones="style='display:none;'";
                                                    $display="style='display:none;'";                                                           
                                                }
                                                @endphp
                                                <div class="col-md-12" id="divegreso" {!!$display!!}>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">                                                            
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="fegreso">Fecha de Egreso <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                                    </div>
                                                                    <input name="fegreso" id="fegreso" value="{{$personal->fecha_egreso != null ? date("d/m/Y",strtotime($personal->fecha_egreso)) : null}}" class="form-control dateter">                                                                                                                
                                                                </div>
                                                                @if(isset($cambios->fecha_egreso))
                                                                <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->fecha_egreso))}}</span>
                                                                @endif 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="col-form-label" for="motivoegreso">Motivo del Egreso <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                            <div class="form-group">
                                                                <div class="input-group">                                                                    
                                                                    <input name="motivoegreso" id="motivoegreso" value="{{$personal->motivo_egreso}}" class="form-control">
                                                                </div>
                                                                @if(isset($cambios->motivo_egreso))
                                                                <span class="cambios">Nuevo valor: {{$cambios->motivo_egreso}}</span>
                                                                @endif 
                                                            </div>
                                                        </div>
                                                        @php if($blacklist == "si") $style = 'style='; else $style = 'style=display:none;'; @endphp
                                                        <div class="col-md-4">                                                            
                                                            <div class="form-group text-center">
                                                                <label class="col-form-label" for="recontratar">¿Personal puede ser Recontratado? <span style="color:red">*</span></label>                                                                                                                               
                                                                <fieldset>                                                                                         
                                                                    <input type="checkbox" id="recontratar" name="recontratar"  
                                                                           @if ($blacklist == "no")
                                                                           checked "data-state='true'
                                                                           @else
                                                                           data-state='false'                                                               
                                                                           @endif
                                                                           disabled
                                                                           data-bootstrap-switch data-off-color="danger" data-on-color="success"> 
                                                                </fieldset>                                                                                                      
                                                            </div>
                                                        </div>   
                                                        <div class="col-md-12"> 
                                                            <label class="col-form-label" for="observaciones_egreso">Observaciones sobre el Egreso <span style="color:red">*</span></label>  
                                                            <div class="form-group"> 
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" data-toggle="tooltip" data-placement="top" title="Ingrese observaciones detalladas sobre el egreso..."><i class="fas fa-question-circle"></i></span>
                                                                    </div> 
                                                                    <textarea class="form-control" id="observaciones_egreso" name="observaciones_egreso" rows="2" placeholder="Ingrese observaciones detalladas sobre el egreso...">{{ old('observaciones_egreso', $personal->observaciones_egreso) }}</textarea>
                                                                </div>
                                                                 @if(isset($cambios->observaciones_egreso))
                                                                <span class="cambios">Nuevo valor: {{$cambios->observaciones_egreso}}</span>
                                                                @endif 
                                                            </div>                                                     
                                                        </div>  
                                                        <div class="col-md-12" id="norecontratardisplay" {!!$style!!}>
                                                            <label class="col-form-label" for="norecontratar">Indique los Motivos por lo que no se debe re contratar a esta persona <span style="color:red">*</span></label>                                                                                                                                                                                                      
                                                            <div class="form-group">
                                                                <div class="input-group">                                                                    
                                                                    <input name="norecontratar" id="norecontratar" 
                                                                           value="{{$originblacklist != null ? $originblacklist->motivos : null}}"
                                                                           maxlength=255" class="form-control">
                                                                </div>                                         
                                                            </div>
                                                        </div>
                                                        @if($blacklist  == "si")
                                                        <p class="col-md-12 text-center"><span style="color:red;">Atenci&oacute;n:</span>Esta persona <B>NO</B> es apta para re contratación. El expediente no puede ser modificado.</p> 
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class='col-md-12' id="displayVacaciones" {!!$styleVacaciones!!}>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="col-form-label" for="vacaciones_init">Fecha Inicio Periodo Vacacional <span style="color:red">*</span></label>                                                                                                                                                      
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                                    </div>
                                                                    <input type="text" 
                                                                            class="form-control vacacionesDateclass"
                                                                            autocomplete="off"
                                                                            name="vacaciones_init"                                                                   
                                                                            id="vacaciones_init"                                                                    
                                                                            value="{{$personal->vacaciones_init !== null ? date('d/m/Y',strtotime($personal->vacaciones_init)) : ''}}" 
                                                                            placeholder="01/01/2024">                                                            
                                                                </div>
                                                                @if(isset($cambios->vacaciones_init))
                                                                <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->vacaciones_init))}}</span>
                                                                @endif   
                                                            </div>                                           
                                                        </div> 
                                                        <div class="col-md-6">
                                                            <label class="col-form-label" for="vacaciones_end">Fecha Final Periodo Vacacional <span style="color:red">*</span></label>                                                                                                                                                      
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                                    </div>
                                                                    <input type="text" 
                                                                            class="form-control vacacionesDateclass"
                                                                            autocomplete="off"
                                                                            name="vacaciones_end"                                                                   
                                                                            id="vacaciones_end"                                                                    
                                                                            value="{{$personal->vacaciones_end !== null ? date('d/m/Y',strtotime($personal->vacaciones_end)) : ''}}" 
                                                                            placeholder="01/01/2024">                                                            
                                                                </div> 
                                                                @if(isset($cambios->vacaciones_end))
                                                                <span class="cambios">Nuevo valor: {{date("d/m/Y",strtotime($cambios->vacaciones_end))}}</span>
                                                                @endif 
                                                            </div>                                           
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">                                                 
                                                    <div class="btn-group btn-block float-right">
                                                        <button type="button" class="btn btn-info" onclick="stepper.previous()" data-toggle="tooltip" data-placement="top" title="Anterior"> <i class=" fa fa-arrow-left"></i></button>                                                                                                                               
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
</div>

@endsection

@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bs-stepper/css/bs-stepper.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-stepper/js/bs-stepper.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/gestion_personal/addpersonal.js')}}" type="text/javascript"></script>
@endpush