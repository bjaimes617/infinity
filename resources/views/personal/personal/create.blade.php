@extends('layouts.main')
@section('title', 'Personal|Añadir')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('personal.add','active')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Gestión de Personal</li>
                        <li class="breadcrumb-item active">Añadir</li>
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
                    <h3 class="card-title">Agregar Nuevo Expediente de Personal </h3>
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
                        <form action="{{route('personal.store')}}" method="POST" id='personal-form' class="form-horizontal form-label-left" data-parsley-validate enctype='multipart/form-data'>
                            @csrf
                            <div class="bs-stepper-content">
                                <!-- /Persona -->
                                <div id="personal-part" class="content" role="tabpanel" aria-labelledby="personal-part-trigger">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="text-center circular--portrait">
                                                    <label for="image">
                                                        <img id="previewimg" src="{{asset('img/user.png')}}" data-toggle="tooltip" data-placement="top" title="Fotografia de la persona">
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
                                                <div  class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <select name="nacionalidad" id="nacionalidad"  required class="form-control">
                                                            @foreach($nacionalidades as $key => $nacionalidad)
                                                            <option value='{{$key}}' > {{$nacionalidad}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control input-number" required name="cedula_empleado"
                                                           data-href="{{route('search.exist.personal')}}"
                                                           id="cedula_empleado"
                                                           value="{{old('cedula_empleado')}}"
                                                           placeholder="123456789">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-form-label" for="nacimiento">Fecha de Nacimiento <span style="color:red">*</span></label>
                                                <div  class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control dateter" autocomplete="off" name="nacimiento" id="nacimiento"
                                                           required value="{{old('nacimiento')}}" placeholder="01/01/1900">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-form-label" for="nacademico">Grado Academico <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <select name="nacademico" id="nacademico" required class="form-control">
                                                        <option value=''>[SELECCIONE]</option>
                                                        @foreach($especialidades as $key => $nacionalidad)
                                                        <option value='{{$key}}'> {{$nacionalidad}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-form-label" for="especialidad">Especialidad <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" name="especialidad"
                                                           id="especialidad" required
                                                           value="{{old('cedula_empleado')}}"
                                                           placeholder="Mercadeo/R.R.H.H.">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="pnombre">Primer Nombre <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" autocomplete="off" name="pnombre" id="pnombre"
                                                           required value="{{old('pnombre')}}" placeholder="Primer Nombre">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="papellido">Primer Apellido <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" autocomplete="off" name="papellido" id="papellido"
                                                           required value="{{old('papellido')}}" placeholder="Primer Apellido">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="snombre">Segundo Nombre </label>
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" autocomplete="off" name="snombre" id="snombre"
                                                           required value="{{old('snombre')}}" placeholder="Segundo Nombre">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="sapellido">Segundo Apellido <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" autocomplete="off" name="sapellido" id="sapellido"
                                                           required value="{{old('sapellido')}}" placeholder="Segundo Apellido">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="hijos">Cantidad de Hijos <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <select name="hijos" id="hijos" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($hijo as $key => $hijos)
                                                        <option value='{{$key}}'> {{$hijos}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-form-label" for="rif">Registro Fiscal <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" autocomplete="off" name="rif" id="rif"
                                                           required value="{{old('rif')}}" minlength='6'maxlength='10' placeholder="V12345777">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group text-center">
                                                <label class="col-form-label" for="sexo">Genero <span style="color:red">*</span></label>
                                                <fieldset>
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="input-group btn bg-primary">
                                                            <input type="radio" name="sexo" id="sexo" value="F" autocomplete="off"> Femenino
                                                        </label>
                                                        <label class="input-group btn bg-primary">
                                                            <input type="radio" name="sexo" id="sexo" value="M" autocomplete="off"> Masculino
                                                        </label>
                                                    </div>
                                                </fieldset>
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
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" autocomplete="off" name="nombretitular" id="nombretitular"
                                                           required value="{{old('nombretitular')}}" placeholder="Nombre y Apellido">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="cedulatitular">Cedula Identidad Titular <span style="color:red">*</span></label>
                                                <div  class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <select name="nacionalidad_cuenta" required id="nacionalidad_cuenta"  class="form-control">
                                                            @foreach($nacionalidades as $key => $nacionalidad)
                                                            <option value='{{$key}}' > {{$nacionalidad}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control input-number" name="cedulatitular"
                                                           id="cedulatitular" required
                                                           minlength='6'
                                                           maxlength='13'
                                                           value="{{old('cedulatitular')}}"
                                                           placeholder="123456789">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="banco">Banco <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <select name="banco" id="banco" required class="form-control">
                                                        <option value='' > [SELECCIONE]</option>
                                                        @foreach($banco as $banco)
                                                        <option value='{{$banco->id}}' > {{$banco->bancos}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="ctabancaria">Nro. Cuenta Bancaria <span style="color:red">*</span></label>
                                                <div  class="input-group ">
                                                    <input type="text" class="form-control input-number" name="ctabancaria"
                                                           id="ctabancaria" required
                                                           minlength='20'
                                                           maxlength='20'
                                                           data-href="{{route('search.exist.cuentas')}}"
                                                           value="{{old('ctabancaria')}}"
                                                           placeholder="010XXXXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row mb-3">
                                                <div class="col-md-12 text-center">
                                                    <p><b>Informacion sobre Asignación de Tarjetas TodoTicket</b></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group text-center">
                                                        <label class="col-form-label" for="integral">Asignada TodoTicket Integral <span style="color:red">*</span></label>
                                                        <fieldset>
                                                            <input type="hidden" name="integral" value="0">
                                                            <input type="checkbox" id="integral" name="integral" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group text-center">
                                                        <label class="col-form-label" for="alimentacion">Asignada TodoTicket Alimentacion <span style="color:red">*</span></label>
                                                        <fieldset>
                                                            <input type="hidden" name="alimentacion" value="0">
                                                            <input type="checkbox" id="alimentacion" name="alimentacion" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="btn-group  btn-block float-right">
                                                <button type="button"class="btn btn-primary validateNext" onclick="stepper.next()"  data-toggle="tooltip" data-placement="top" title="Siguiente"> <i class="fa fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="contacto-part" class="content" role="tabpanel" aria-labelledby="contacto-part-trigger">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="email">Correo Electronico <span style="color:red">*</span></label>
                                                <div class="input-group ">
                                                    <input type="email" class="form-control" name="email"
                                                           id="email" required
                                                           value="{{old('email')}}"
                                                           placeholder="example@example.com">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ">
                                            <div class="form-group text-center">
                                                <label class="col-form-label" for="activetransporte">Utiliza rutas de Transporte <span style="color:red">*</span></label>
                                                <fieldset>
                                                    <input type="checkbox" id="activetransporte" name="activetransporte" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="col-md-12 displaytransporte" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="htransporte">Horario del Transporte <span style="color:red">*</span></label>
                                                        <div class="input-group ">
                                                            <select name="htransporte" id="htransporte" class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($horariosTransporte as $key => $horario)
                                                                <option value='{{$key}}'> {{$horario}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label" for="zona_transporte_id">Horario del Transporte <span style="color:red">*</span></label>
                                                        <div class="input-group ">
                                                            <select name="zona_transporte_id" id="zona_transporte_id" class="form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($zonatransporte as $key => $zonas)
                                                                <option value='{{$zonas->id}}'> {{$zonas->zonatransporte}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="tlf1">Telefono Habitación <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control  input-number" name="tlf1"
                                                           id="tlf1" required
                                                           maxlength="11"
                                                           value="{{old('tlf1')}}"
                                                           placeholder="2120000000">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="tlf2">Telefono Celular <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control  input-number" name="tlf2"
                                                           id="tlf2" required
                                                           maxlength="11"
                                                           value="{{old('tlf2')}}"
                                                           placeholder="4120000000">
                                                </div>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="sector">Sector del Domicilio <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" name="sector"
                                                           id="sector" required
                                                           value="{{old('direccion')}}"
                                                           placeholder="Caracas">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="direccion">Dirección de Habitación <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" name="direccion"
                                                           id="direccion" required
                                                           value="{{old('sector')}}"
                                                           placeholder="Av./Calle/Carretera xxxxxxx">
                                                </div>
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
                                            <label class="col-form-label" for="profit">Profit </label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control input-number" name="profit"
                                                           id="profit"
                                                           value="{{old('profit')}}"
                                                           placeholder="123456">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="nomina">Tipo de Nomina <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="nomina" id="nomina" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($nomina as $key => $nomina)
                                                        <option value='{{$nomina->id}}' @if($nomina->id == 1)
                                                            data-contrato1="{{config('app.digacontrato1')}}"
                                                            data-contrato2="{{config('app.digacontrato2')}}"
                                                            @else
                                                            data-contrato1="{{config('app.dgcontrato1')}}"
                                                            data-contrato2="{{config('app.dgcontrato2')}}"
                                                            @endif >{{$nomina->nombre_nomina}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="fingreso">Fecha de Ingreso <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control dateter"
                                                           autocomplete="off"
                                                           name="fingreso"
                                                           id="fingreso" required
                                                           value="{{old('fingreso')}}"
                                                           placeholder="01/01/2023">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="hentrada">Hora de Entrada <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control time"
                                                           autocomplete="off"
                                                           name="hentrada"
                                                           id="hentrada" required
                                                           value="{{old('hentrada')}}"
                                                           placeholder="08:00 am">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="hsalida">Hora de Salida <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control time"
                                                           autocomplete="off"
                                                           name="hsalida"
                                                           id="hsalida" required
                                                           value="{{old('hsalida')}}"
                                                           placeholder="05:00 pm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="jornada">Tipo de Jornada Laboral <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="jornada" id="jornada" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($jornadas as $key => $jornada)
                                                        <option value='{{$key}}'> {{$jornada}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="horario">Horario Laboral <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user-clock"></i></span>
                                                    </div>
                                                    <select name="horario" id="horario" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($horariolab as $key => $horario)
                                                        <option value='{{$key}}'> {{$horario}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="libranza">Esquema de Libranzas <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="libranza" id="libranza" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($libranza as $key => $libranzas)
                                                        <option value='{{$libranzas->id}}'>{{$libranzas->cod_libranza}} | {{$libranzas->libranzas}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-12'>
                                            <div class='line'></div>
                                            <br>
                                            <p><small ><i class="fa fa-tag"></i> Información del Contrato</Small></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="estatuscargo">Estatus del Cargo a ejercer <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <select name="estatuscargo" id="estatuscargo" required class="form-control">
                                                    <option value=''> [SELECCIONE]</option>
                                                    @foreach($tipoContrato as $key => $estatuscargo)
                                                    <option value='{{$key}}'>{{$estatuscargo}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="cargo">Cargo a Ejercer <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <select name="cargo" id="cargo" required class="select2 form-control">
                                                    <option value=''> [SELECCIONE]</option>
                                                    @foreach($cargo as $key => $cargos)
                                                    <option value='{{$cargos->id}}'>{{$cargos->nombre_cargo}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="contrato1">Firma del 1er. Contrato <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control dateter"
                                                           autocomplete="off"
                                                           name="contrato1"
                                                           id="contrato1" required
                                                           value="{{old('contrato1')}}"
                                                           placeholder="01/01/2024">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="maxmdayscontrato1" id="maxmdayscontrato1" value="{{config('app.contrato1')}}">
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="finalcontrato1">Finaliza el 1er. Contrato <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control dateter"
                                                           autocomplete="off"
                                                           name="finalcontrato1"
                                                           disabled
                                                           id="finalcontrato1" required
                                                           value="{{old('finalcontrato1')}}"
                                                           placeholder="01/01/2024">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="contrato2">Firma del 2do. Contrato </label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control dateter"
                                                           autocomplete="off"
                                                           name="contrato2"
                                                           id="contrato2"
                                                           value="{{old('contrato2')}}"
                                                           placeholder="01/01/2024">
                                                     <div class="input-group-prepend">
                                                        <select class="form-control" id="maxmonthcontrato2">
                                                        <option value="">[Seleccione]</option>
                                                        @foreach($tiemposContrato as $key => $value)
                                                            <option value="{{ $key }}">+{{ $value }} Días</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                </div>
                                               
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
                                                           disabled
                                                           name="finalcontrato2"
                                                           id="finalcontrato2"
                                                           value="{{old('finalcontrato2')}}"
                                                           placeholder="01/01/2024">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="col-form-label" for="scontrato">Situación Contractual <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="scontrato" id="scontrato" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($contractual as $key => $contractual)
                                                        <option value='{{$key}}'>{{$contractual}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-12'>
                                            <p><small><i class="fas fa-map-marker-alt"></i></i> Ubicación en la Estructura Organizativa</Small></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="areatrabajo">Gerencia <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="areatrabajo" id="areatrabajo" required
                                                            data-href="{{route('check.areas')}}" class="form-control select2"  style="width: 100%;">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($area as $gerencias)
                                                        <option value='{{$gerencias->id}}'>{{$gerencias->nombre_campana}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="cartera">Unidad/Campaña <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="cartera" id="cartera" required
                                                            data-href="{{route('check.carteras')}}" class="form-control select2"  style="width: 100%;">
                                                        <option value=''> [SELECCIONE GERENCIA]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @php if(\Auth::user()->haspermission('personal.cecos.autorice')) $autoriza = "true"; else $autoriza = "false"; @endphp
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="skill" id="skill" required
                                                            data-actporcentaje="{{$autoriza}}"
                                                            data-href="{{route('selector.centros.costos')}}"
                                                            class="form-control select2"  style="width: 100%;">
                                                        <option value=''> [SELECCIONE GERENCIA]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="col-form-label" for="ceco">Centro de Costos (C.E.C.O) <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <select class="form-control select2" multiple="multiple" disabled required="required" id="ceco" name="ceco[]">
                                                                <option value=''> [SELECCIONE SKILL]</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 porcentajececo" style="display:none;">
                                                    <label class="col-form-label" for="porcentaje">Porcentaje Representativo del (C.E.C.O) <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <input type="text"
                                                                   class="form-control"
                                                                   autocomplete="off"
                                                                   name="porcentaje"
                                                                   id="porcentaje" required
                                                                   value="{{old('porcentaje')}}"
                                                                   >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-center porcentajececo" style="display:none;"><span style="color:red;">Atenci&oacute;n:</span> Coloque en números enteros el porcentaje del CECO y  en el orden que fue seleccionado.</p>
                                        </div>
                                        <div class='col-md-12'>
                                            <p><small><i class="fas fa-users"></i> Asignación de Gerente, Lideres/Jefes Inmediatos</Small></p>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="col-form-label" for="gerente">Gerente de Área <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <select name="gerente" id="gerente" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($gerente as $key => $gerentes)
                                                        <option value='{{$gerentes->id}}'>{{$gerentes->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="cjefeinmediato">Cargo 1er. Lider/Jefe Inmediato <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                    </div>
                                                    <select name="cjefeinmediato" id="cjefeinmediato" required data-href="{{route('check.jefes')}}" class="select2 form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($cargo as $cargos)
                                                        <option value='{{$cargos->id}}'>{{$cargos->nombre_cargo}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="jinmediato">1er. Lider/Jefe Inmediato <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <select name="jinmediato" id="jinmediato"  required class="form-control">
                                                        <option value=''> [SELECCIONE EL CARGO]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="cjefeinmediato2">Cargo 2do. Lider/Jefe Inmediato <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                    </div>
                                                    <select name="cjefeinmediato2" id="cjefeinmediato2" required data-href="{{route('check.jefes')}}" class="select2 form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($cargo as $cargos)
                                                        <option value='{{$cargos->id}}'>{{$cargos->nombre_cargo}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="jinmediato2">2do. Lider/Jefe Inmediato <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <select name="jinmediato2" id="jinmediato2" required class="form-control">
                                                        <option value=''> [SELECCIONE 2DO. CARGO ]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-12'>
                                            <p><small><i class="fa fa-tag"></i> Información adicional</Small></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="sede">Sede Laboral <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                    </div>
                                                    <select name="sede" id="sede" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($sedes as $key => $sede)
                                                        <option value='{{$sede->nombre_sede}}'>{{$sede->real_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="status">Estatus del Personal <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                    </div>
                                                    <select name="status" id="status" required class="form-control">
                                                        <option value=''> [SELECCIONE]</option>
                                                        @foreach($status as $estatus)
                                                        <option value='{{$estatus->id}}'>{{$estatus->estatus}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 region-estado" style="display:none;"> 
                                            <div class="form-group">
                                                 <label class="col-form-label" for="region">Region <span style="color:red">*</span></label>  
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                                    </div>
                                                    <select class="form-control" id="region" name="region" data-href="{{route('selector.estados')}}">
                                                        <option value="">[Seleccione]</option>     
                                                        @foreach($region as $regiones)
                                                        <option value="{{$regiones->id}}">{{$regiones->nombre_region}}</option>     
                                                        @endforeach
                                                    </select>
                                                </div> 
                                            </div>                                    
                                        </div>                                                         
                                        <div class="col-md-4 region-estado"  style="display:none;">
                                            <div class="form-group">
                                                <label class="col-form-label" for="estado">Estado <span style="color:red">*</span></label>  
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                    </div> 
                                                    <select class="form-control" id="estado" name="estado"  data-href="{{route('selector.ciudades')}}">
                                                        <option value="">[SELECCIONE]</option>                                                           
                                                    </select>
                                                </div> 
                                            </div>                                    
                                        </div>
                                        <div class="col-md-4 region-estado" style="display:none;">
                                            <div class="form-group">
                                                <label class="col-form-label" for="ciudad">Ciudad <span style="color:red">*</span></label>                                                    
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                    </div> 
                                                    <select class="form-control" id="ciudad" name="ciudad">
                                                        <option value="">[Seleccione]</option>                                                               
                                                    </select>
                                                </div> 
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12" id="divegreso" style="display:none;">
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label class="col-form-label" for="fegreso">Fecha de Egreso <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                            </div>
                                                            <input name="fegreso" id="fegreso" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="col-form-label" for="motivoegreso">Motivo del Egreso <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <input name="motivoegreso" id="motivoegreso" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group text-center">
                                                        <label class="col-form-label" for="recontratar">¿Personal puede ser Recontratado? <span style="color:red">*</span></label>
                                                        <fieldset>
                                                            <input type="checkbox" id="recontratar" name="recontratar" data-state='true' data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" id="norecontratardisplay" style="display:none;">
                                                    <label class="col-form-label" for="norecontratar">Indique los Motivos por lo que no se debe re contratar a esta persona <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <input name="norecontratar" id="norecontratar" maxlength=255" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-12'>
                                            <p><small><i class="fa fa-tag"></i>Usuario Para Tomar Asistencia</Small></p>
                                        </div>
                                        <div class='col-md-12'>
                                            <div class="form-group text-center">
                                                <label class="col-form-label" for="useractive">¿Este Personal requiere usuario par la Plataforma? <span style="color:red">*</span></label>
                                                <fieldset>
                                                    <input type="checkbox" id="useractive" name="useractive" data-state='true' data-bootstrap-switch  data-off-color="danger" data-on-color="success">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class='col-md-12' id="displayusers" style='display:none;'>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label class="col-form-label" for="username">Usuario <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <input name="username" id="username" disabled class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="col-form-label" for="useremail">Email <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <input name="useremail" id="useremail" disabled class="form-control" placeholder='xxxxxx@directagroup.net'>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="col-form-label" for="sede">Perfil del Usuario <span style="color:red">*</span></label>
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                            </div>
                                                            <select name="roles" id="roles" class="select2 form-control">
                                                                <option value=''> [SELECCIONE]</option>
                                                                @foreach($roles as $key => $rol)
                                                                <option value='{{$rol->id}}'>{{$rol->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <p><span style="color:red;">Atenci&oacute;n:</span> El Usuario Generado por esta vía solo tendra disponible el Modulo de asistencia Operativa O Comisiones y Variables.
                                                        Recuerda Seleccionar el Rol más acorde al Cargo Asignado.
                                                        La contraseña Por defecto es <b>directagroup</b>, y el usuario debera cambiarla una vez Inicie Sesión.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="btn-group btn-block float-right">
                                                <button type="button" class="btn btn-info" onclick="stepper.previous()" data-toggle="tooltip" data-placement="top" title="Anterior"> <i class=" fa fa-arrow-left"></i></button>
                                                <button type="submit" class="btn btn-primary validateNext" data-toggle="tooltip" data-placement="top" title="Procesar"> <i class=" fa fa-save"></i></button>
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

        <!-- /.container-fluid -->
    </section>
</div>
@endsection

@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bs-stepper/css/bs-stepper.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-stepper/js/bs-stepper.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/gestion_personal/addpersonal.js')}}" type="text/javascript"></script>
@endpush
