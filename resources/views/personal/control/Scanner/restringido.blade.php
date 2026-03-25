<div class="containerbody">
    <div class="main_container">                                
        <div class="col-xs-12" style=" background-color: #1B3155;"> 
            <br/>
            <p align="center"><img style="border-radius:50%; border: solid #fc020310px; width:auto; height:50%" src="{{ asset('img/restringido.jpg') }}" alt="" ></p>
            <br/>                                                   
        </div>
        <div class="col-xs-12" style=" font-size: 2rem; color:red;"> 
            <br/>
            <p align="center"> Personal No Autorizado para Ingresar a las Instalaciones.</p>
            @if($empleado->estatus_id != 1)
            <p align="center"> Motivo: Personal {{ $empleado->RelationEstatus->estatus }} </p>
            @else
            <p align="center"> Motivo: Fuera de Horarios Laborales</p>
            <p align="center" style="color:#BE3A8D;">Horario de Entrada: {{date('h:i a',strtotime($empleado->horario_entrada))}} - Horario Salida: {{date('h:i a',strtotime($empleado->horario_salida))}}</p>
            @endif
            <br/> 
        </div>           
    </div> 
</div>  