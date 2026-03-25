<div class="containerbody"  s>
    <div class="main_container row">
        <div class=" col-7">
            <div class="x_panel">
                <div class="x_title">                
                    <h2><b>{{strtoupper($empleado->primer_nombre)}} {{strtoupper($empleado->primer_apellido)}}</b></h2>                    
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="x_content">

                <table border="0" class="table table-striped">                      
                    <tbody>
                        <tr>                        
                            <td>Cédula:</td> 
                            <td><span style="color:#1B3155; font-size: 1.5rem;"> {{$empleado->cedula_empleado}}</span></td> 
                            <td align="center">Sede: </td> 
                        </tr>
                        <tr>                        
                            <td>Gerencia: </td>                        
                            <td><span style="color:#1B3155; font-size: 1.5rem;"> {{$empleado->RelationAreas->nombre_campana}}</span></td> 
                            <td align="center" style="color:#1B3155; font-size: 1.5rem;">{{$empleado->sede_laboral}}</td> 
                        </tr>
                        <tr>                        
                            <td>Unidad/Campaña:</td>
                            @if($empleado->RelationCartera != null)
                            <td><span colspam="2" style="color:#1B3155; font-size: 1.5rem;">  {{$empleado->RelationCartera->nombre_cartera}}</span></td>
                            @else
                             <td><span style="color:#1B3155; font-size: 1.5rem;">  </span></td>
                            @endif
                            <td></td>                        
                        </tr>
                        <tr>                        
                            <td>Segmento/Skill:</td>
                            @if($empleado->Relationskill != null)
                            <td><span style="color:#1B3155; font-size: 1.5rem;">  {{$empleado->Relationskill->nombre_skill}}</span></td>
                            @else
                             <td><span style="color:#1B3155; font-size: 1.5rem;">  </span></td>
                            @endif
                            <td>Estatus: </td>                        
                        </tr>
                        <tr>
                            <td> Cargo:</td>
                            <td><span style="color:#1B3155; font-size: 1.5rem;">{{$empleado->RelationCargos != null ? $empleado->RelationCargos->nombre_cargo : "NO DISPONIBLE"}} </span></td>                        
                            <td rowspan="3" style=" background:#149524; vertical-align: middle; text-align:center;"><span style="color:#ffffff; font-size: 2rem;">{{$empleado->RelationEstatus->estatus}} </span></td>                        
                        </tr>
                        <tr >                        
                            <td>Horario Operativo:</td>                           
                            <td><span style="color:#1B3155; font-size: 1.5rem;">{{$empleado->horario_operativo}} </span></td>                        
                            
                        </tr>
                        <tr>                        
                            <td>Días de Acceso Restringido:</td>                        
                            <td><span style="color:#cc0000; font-size: 1.5rem;"> {{$empleado->RelationLibranzas->libranzas}}</span></td>                                                
                        </tr>
                    </tbody>
                </table>                    
            </div>
            <p>Autorización de ingreso a las Instalaciones: </p>            
            @if($Mensaje == '1')           
                @if($empleado->estatus_id != 1 || date('H:i:s',strtotime(now())) <= $horaentrada || date('H:i:s',strtotime(now())) >= $empleado->horario_salida)                
                    @if($empleado->estatus_id != 1)
                    <p align="center"> Motivo: Personal {{ $empleado->RelationEstatus->estatus }} </p>
                    @else
                    <p style=" color:#cc0000 ; font-size: 2rem; text-align: center;">Fuera de Horarios Laborales</p>
                    <p align="center" style="color:#BE3A8D;">Horario de Entrada: {{date('h:i a',strtotime($empleado->horario_entrada))}} - Horario Salida: {{date('h:i a',strtotime($empleado->horario_salida))}}</p>
                    @endif 
                @else
                <p style=" color:#149524; font-size: 3rem;text-align: center;">Acceso Autorizado <i class="fas fa-check-circle"></i></p>
                @endif
            @else
            <p style=" color:#cc0000 ; font-size: 2rem; text-align: center;">Acceso no permitido. Personal en Día libres</p>
            @endif                
        </div>
        <div class="col-5" style=" background-color: #1B3155;"> 
            <br/>
                <div class="circular--portrait"><img style="" src="{{$image != null ? \Storage::disk('personal')->url($image) : asset('img/user.png') }}" alt="" ></div>
            <br/>                                                   
        </div>
    </div>
</div>