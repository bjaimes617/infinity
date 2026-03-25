<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <title>Planilla de Pre-empleo</title>
        <meta name="generator" content="LibreOffice 24.8.7.2 (Linux)"/>
    </head>
    <style>
        body {
            margin-top: -40px;
            margin-left: -15px;
            width: 100%;
            font-size: 0.7rem !important;

        }
        table {
            width: 105%;
        }
        table tr{
            height: auto;
            line-height: 1; /* Esto ayuda a reducir el espacio vertical */
            padding: 2px 5px; /* Ajusta el padding para controlar espacio interno */
        }
        p {
            margin-top: 0.5em; /* Ajusta según sea necesario */
            margin-bottom: 0.5em; /* Ajusta según sea necesario */
        }
        p font{
            font-style: bold;
        }
    </style>
    <body lang="es-ES" dir="ltr">
        <br>
        <table cellpadding="2" cellspacing="0">
            <col width="69"/>
            <col width="35"/>
            <col width="46"/>
            <col width="53"/>
            <col width="79"/>
            <col width="54"/>
            <col width="54"/>
            <col width="54"/>
            <col width="54"/>
            <col width="54"/>
            <col width="54"/>
            <col width="25"/>
            <col width="29"/>
            <col width="28"/>
            <col width="54"/>
            <col width="18"/>
            <col width="54"/>
            <col width="54"/>
            <col width="22"/>
            <col width="33"/>
            <col width="52"/>
            <col width="107"/>
            <col width="134"/>
            <col width="134"/>
            <tbody>
                <tr>
                    <td colspan="5" height="30" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <br/>
                    </td>
                    <td colspan="14" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><font size="5" style="font-size: 14pt;">
                            <b>SOLICITUD DE EMPLEO</b></font></font>
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Fecha: </font>                            
                        </p>                   
                        <span class="container">{{date('d/m/Y',strtotime(now()))}}</span>
                    </td>
                    <td rowspan="2" bgcolor="#ffffff" width="100" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Fotografía</font></p>
                    </td>
                </tr>

            </tbody>
            <tbody>
                <tr>
                    <td colspan="23" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">
                            <font size="4" style="font-size: 14pt">
                            <b>I.- Datos Personales</b></font></font>
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="12" height="" style="border: 1px solid #000000;"><p align="left">
                            <font face="Times New Roman">Apellidos:</font> &nbsp; {{$prospecto->apellidos}} </p>
                    </td>
                    <td colspan="12" style="border: 1px solid #000000;"><p align="left">
                            <font face="Times New Roman">Nombres:</font> &nbsp; {{$prospecto->nombres}}</p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="6" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Cédula de Identidad N°: </font>
                            {{$prospecto->nacionalidad != "P" ? $prospecto->cedula : ""}}
                        </p>
                    </td>
                    <td colspan="6" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Nacionalidad:</font> &nbsp; {{$prospecto->nacionalidad}}
                        </p>
                    </td>
                    <td colspan="6" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Pasaporte:</font> &nbsp; {{$prospecto->nacionalidad == "P" ? $prospecto->cedula : "N/A"}}</p>
                    </td>
                    <td colspan="6" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Fecha de Nacimiento: </font> &nbsp; {{date('d/m/Y',strtotime($prospecto->fecha_nacimiento))}} 
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="4" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Edad:</font> &nbsp; {{$prospecto->edad}}
                        </p>
                    </td>
                    <td colspan="12" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Lugar de Nacimiento: </font> &nbsp; {{$prospecto->lugarnacimiento}}</p>
                    </td>
                    <td colspan="6" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">País: </font> &nbsp;  {{$prospecto->pais}}</p>
                    </td> 
                    <td colspan="2" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Romanó">Sexo: </font> &nbsp;  {{$prospecto->sexo}}</p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">¿Personas que viven con usted?</font> &nbsp; {{$prospecto->personasviven}}</p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">¿Cuántos a su cargo?</font> &nbsp;{{$prospecto->personasacargo}}
                        </p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000; ">
                        <p align="left">
                            <font face="Times New Roman"> Edad(es): </font> &nbsp; {{$prospecto->edadesacargo}}</p>                    
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="8" style="border-top: 1px solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: 1px solid #000000; padding-top: 0.05cm; padding-bottom: 0cm; padding-left: 0.05cm; padding-right: 0.05cm">
                        <p align="left">
                            <font face="Times New Roman">Estado Civil:</font>
                            &nbsp; @foreach($edocivil as $key => $civil) @if($key == $prospecto->edocivil){{$civil}}@endif @endforeach
                        </p>
                    </td>
                    <td colspan="16" style="border: 1px solid #000000; padding: 0.05cm">
                        <p align="left">
                            <font face="Times New Roman">Apellidos y Nombres del Conyugue:</font> &nbsp; {{$prospecto->conyuge}}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="8" height="17" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">N° de Hijos:</font> &nbsp; {{$prospecto->hijos}}
                        </p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Edad(es):</font> &nbsp; {{$prospecto->edadhijos}}
                        </p>
                    </td>  
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Sexo Hijo(s):</font>  &nbsp; {{$prospecto->sexohijos == null ? "N/A" : $prospecto->sexohijos}} 
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24" style="border-top: 1px solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: 1px solid #000000; padding-top: 0.05cm; padding-bottom: 0cm; padding-left: 0.05cm; padding-right: 0.05cm"><p align="left">
                            <font face="Times New Roman">Dirección de Habitación:</font>  &nbsp;  {{$prospecto->direccion}}</p>
                    </td>
                </tr>		
            </tbody>
            <tbody>
                <tr>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Ciudad:</font>  &nbsp;  {{$prospecto->ciudad}} 
                        </p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Parroquia:</font>  &nbsp;  {{$prospecto->parroquia}}
                        </p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Estado:</font>  &nbsp;  {{$prospecto->estado}}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="8" height="17" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Teléfono(s):</font>   &nbsp;  {{$prospecto->telefono_casa}}
                        </p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Celular:</font>  &nbsp;  {{$prospecto->telefono_celular}}
                        </p>
                    </td>
                    <td colspan="8" style="border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Correo electrónico:</font> &nbsp;  {{$prospecto->email}}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Cargo Solicitado:</font>
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>                   
                    <td colspan="8" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Talla de Camisa: </font> &nbsp; {{$prospecto->camisa}}
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Talla de  Pantalon: </font> &nbsp; {{$prospecto->pantalon}}
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Talla de Zapato: </font> &nbsp; {{$prospecto->zapatos}}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24" bgcolor="#ffffff" style="background: #ffffff; border-top: 1px solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: 1px solid #000000; padding-top: 0.05cm; padding-bottom: 0cm;">
                        <p align="left">
                            <font face="Times New Roman">Donde viste nuestra oferta de empleo:</font> &nbsp; {{$prospecto->redes}} 
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="12" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> ¿Trabajó antes en esta empresa?: </font> &nbsp; @if($prospecto->trabajoantes == "S") SI @else NO @endif
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">¿En qué fecha? </font> &nbsp; @if($prospecto->fechantes !== null){{date('d/m/Y',strtotime($prospecto->fechantes))}} @else "N/A" @endif
                        </p>
                    </td>
                </tr>            
                <tr>
                    <td colspan="12" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">  ¿Tiene parientes en esta empresa?: </font>  &nbsp; @if($prospecto->parientes == "S") SI @else NO @endif
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Menciónelos:</font>  &nbsp; {{$prospecto->mencionelos}}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">
                            <font size="4" style="font-size: 14pt">
                            <b>II.-Educación</b></font></font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" rowspan="2" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Estudios</font>
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000; ">
                        <p align="center">
                            <font face="Times New Roman"><b>Fecha</b></font></p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>A. Cursados</b></font>
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>Título Obtenido</b></font></p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>Uni.o Inst.</b></font></p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>Lugar</b></font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Comienzo </font>
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Culminación </font>
                        </p>
                    </td>
                    <td colspan="15" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"></font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>BACHILLER</b></font>
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->initbachiller}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->endbachiller}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->cursadobachiller}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->titulobachiller}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->instbachiller}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->lugarbachiller}}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>TSU</b></font>
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->inittsu}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->endtsu}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->cursadotsu}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->titulotsu}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->insttsu}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->lugartsu}}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>Especializacion</b></font>
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->initespecializacion}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->endespecializacion}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->cursadoespecializacion}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->tituloespecializacion}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->instespecializacion}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->lugarespecializacion}}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman"><b>Posgrado</b></font>
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->initposgrado}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->endposgrado}}
                        </p>
                    </td>
                    <td colspan="3" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->cursadoposgrado}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->tituloposgrado}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->instposgrado}}
                        </p>
                    </td>
                    <td colspan="4" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->lugarposgrado}}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">¿Qué Estudio o Curso sigue actualmente?: </font> &nbsp; {{$prospecto->estudiosactuales}}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#ffffff" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Otros: </font> &nbsp; {{$prospecto->otrosestudios}}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">
                            <font size="4" style="font-size: 14pt">
                            <b>Referencias Personales (no familiares)</b></font></font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Nombre</font>
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Telefono </font>
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Ocupacion</font>
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Tiempo Conociendose </font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->referencia1}}
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->telefono1}}
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->ocuacion1}}
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->refanios1}}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->referencia2}}
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->telefono2}}
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->ocuacion2}}
                        </p>
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000; border-bottom: 1px solid #000000;">
                        <p align="center">
                            {{$prospecto->refanios2}}
                        </p>
                    </td>
                </tr>                
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24">
                        <br/>
                        <br/>
                         <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/> F-TH-001B-06/2015 </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24">
                        <p align="center">
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">
                            <font size="4" style="font-size: 14pt">
                            <b>III. -Trabajos Anteriores</b></font></font>
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Nombre de la Empresa:</font> &nbsp; {{$prospecto->nombreempresa1}}
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Dirección: </font> &nbsp; {{$prospecto->direccionempresa1}}
                        </p>
                    </td>                    
                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Ciudad: </font> &nbsp; {{$prospecto->ciudadempresa1}} 
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Telefono: </font> &nbsp; {{$prospecto->telefonoempresa1}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Nombre del Jefe Inmediato y Cargo: </font>  &nbsp; {{$prospecto->jefeinmediato1}} 
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Cargo Desempeñado: </font>  &nbsp; {{$prospecto->cargo1}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Personas a su Cargo: </font> &nbsp; {{$prospecto->acargo1}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Fecha de Ingreso: </font>  &nbsp; {{$prospecto->fingreso1 !== null ? date('d/m/Y',strtotime($prospecto->fingreso1)) : null}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Fecha de Retiro: </font> &nbsp; {{$prospecto->fegreso1 !== null ? date('d/m/Y',strtotime($prospecto->fegreso1)) : null}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Tiempo de Servicio: </font> &nbsp; {{$prospecto->tiempo1}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Sueldo Inicial: </font>  &nbsp; {{$prospecto->sueldo11}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Sueldo Final: </font>  &nbsp; {{$prospecto->sueldo12}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Motivo del Retiro: </font>  &nbsp; {{$prospecto->motivo1}} 
                        </p>
                    </td>                    
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Observaciones: </font>  &nbsp; {{$prospecto->observaciones1}} 
                        </p>
                    </td>                    
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="24">
                        <p align="center">
                            <br/>                            
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Nombre de la Empresa:</font> &nbsp; {{$prospecto->nombreempresa2}}
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Dirección: </font> &nbsp; {{$prospecto->direccionempresa2}}
                        </p>
                    </td>                    
                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Ciudad: </font> &nbsp; {{$prospecto->ciudadempresa2}} 
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Telefono: </font> &nbsp; {{$prospecto->telefonoempresa2}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Nombre del Jefe Inmediato y Cargo: </font>  &nbsp; {{$prospecto->jefeinmediato2}} 
                        </p>
                    </td>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Cargo Desempeñado: </font>  &nbsp; {{$prospecto->cargo2}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Personas a su Cargo: </font> &nbsp; {{$prospecto->acargo2}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Fecha de Ingreso: </font>  &nbsp; {{$prospecto->fingreso2 !== null ? date('d/m/Y',strtotime($prospecto->fingreso2)) : null}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Fecha de Retiro: </font> &nbsp; {{$prospecto->fegreso2 !== null ? date('d/m/Y',strtotime($prospecto->fegreso2)) : null}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Tiempo de Servicio: </font> &nbsp; {{$prospecto->tiempo2}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Sueldo Inicial: </font>  &nbsp; {{$prospecto->sueldo12}} 
                        </p>
                    </td>
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Sueldo Final: </font>  &nbsp; {{$prospecto->sueldo22}} 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Motivo del Retiro: </font>  &nbsp; {{$prospecto->motivo2}} 
                        </p>
                    </td>                    
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Observaciones: </font>  &nbsp; {{$prospecto->observaciones2}} 
                        </p>
                    </td>                    
                </tr>
            </tbody>
            <tbody>               
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">
                            <font size="4" style="font-size: 14pt">
                            <b>IV. -Certificación</b></font></font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">Para todos los efectos legales certifico que todas las respuestas e informaciones anotadas por mi son veraces. </font>
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                        </p>
                        <p  align="center">Firma del solicitante</p>
                    </td>                     
                </tr>                
            </tbody>
            <tbody>               
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #d9d9d9; border: 1px solid #000000;">
                        <p align="center">
                            <font face="Times New Roman">
                            <font size="4" style="font-size: 14pt">
                            <b>V. -Solo para uso de la Dirección de Talento Humano</b></font></font>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Cargo: </font>
                        </p>                       
                    </td>      
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Unidad de Adscripción/Sede: </font>
                        </p>                        
                    </td>      
                </tr>                
                <tr>
                    <td colspan="4" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Turno </font>
                        </p>                       
                    </td>      
                    <td colspan="3" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Mañana </font>
                        </p>                        
                    </td>  
                    <td colspan="3" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Tarde </font>
                        </p>                        
                    </td>   
                    <td colspan="8" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Fecha de Ingreso </font>
                        </p>                        
                    </td>
                    <td colspan="6" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman">Salario </font>
                        </p>                        
                    </td>
                </tr> 
                <tr>
                    <td colspan="24" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Observaciones </font>
                            <br>
                            <br>
                            <br>
                            <br>
                        </p> 
                    </td>      

                </tr>
                <tr>
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Aprobado Por: </font>
                            <br>
                            <br>
                            <br>                         
                        </p> 
                        <p align="center">Firma</p>
                    </td>   
                    <td colspan="12" bgcolor="#d9d9d9" style="background: #ffffff; border: 1px solid #000000;">
                        <p align="left">
                            <font face="Times New Roman"> Fecha: </font>
                            <br>
                            <br>
                            <br>
                            <br>                            
                        </p> 

                    </td>
                </tr>
                <tr>
                    <td colspan="24">F-TH-001B-06/2015</td>
                </tr>
            </tbody>
        </table>       
    </body>
</html>
