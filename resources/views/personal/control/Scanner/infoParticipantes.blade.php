<div class="containerbody">
    <div class="main_container row">
        <div class=" col-8">
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
                            <td>Desde:</td>
                            <td>Hasta:</td>                               
                        </tr>                       
                        <tr>                        
                            <td>Curso:</td>                           
                            <td><span style="color:#1B3155; font-size: 1.5rem;">{{$empleado->RelationCurso->Relationskill->nombre_skill}}</span></td>                               
                            <td><span style="color:#1B3155; font-size: 1.5rem;">{{date('d/m/Y',strtotime($empleado->RelationCurso->inicio))}}</span></td>                               
                            <td><span style="color:#1B3155; font-size: 1.5rem;">{{date('d/m/Y',strtotime($empleado->RelationCurso->finaliza))}}</span></td>                               
                            
                        </tr>
                        <tr>
                            <td>Estatus: </td> 
                            @if($empleado->estatus_id == 12)
                            <td colspan="3" style=" background:#149524; vertical-align: middle; text-align:center;"><span style="color:#ffffff; font-size: 2rem;"> Participante <br> Activo </span> </td>
                            @else
                            <td colspan="3" style=" background:#cc0000; vertical-align: middle; text-align:center;"><span style="color:#ffffff; font-size: 2rem;"> Participante <br> Inactivo </span> </td>
                            @endif                            
                        </tr>
                    </tbody>
                </table>                    
            </div>
            <p>Autorización de ingreso a las Instalaciones: </p>
            @if($empleado->estatus_id == 12)  
            <p style=" color:#149524; font-size: 3rem;text-align: center;">Acceso Autorizado <i class="fas fa-check-circle"></i></p>
            @else  
            <p style=" color:#cc0000 ; font-size: 2rem; text-align: center;">Acceso no permitido.</p>
            @endif



        </div>
        <div class="col-4" style=" background-color: #1B3155;"> 
            <br/>
            <div class="circular--portrait"><img style="" src="{{ asset('source/storage/A_dir_personal/'.$image) }}" alt="" ></div>
            <br/>                                                   
        </div>
    </div>
</div>