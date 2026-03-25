<div class="containerbody">
    <div class="main_container">                                
        <div class="col-xs-12" style=" background-color: #1B3155;"> 
            <br/>
            <p align="center"><img style="border-radius:50%; border: solid #fc020310px; width:auto; height:50%" src="{{ asset('img/incompleto.jpg') }}" alt="" ></p>
            <br/>                                                   
        </div>
        <br>
        <p align="center" style=" font-size: 2rem; font-weight: bold;"> {{$empleado->primer_nombre}} {{$empleado->segundo_nombre}} {{$empleado->primer_apellido}} {{$empleado->segundo_apellido}} </p>  
        <p align="center" style=" font-size: 2rem; font-weight: bold;">Cedula:  {{$empleado->cedula_empleado}} </p>  
        <div class="col-xs-12" style=" font-size: 2rem; color:green;"> 
            <p align="center"> Personal Autorizado para Ingresar a las Instalaciones.</p>            
            <p align="center"> Falta información en su ficha de personal </p>
            <br/> 
        </div>           
    </div> 
</div>  