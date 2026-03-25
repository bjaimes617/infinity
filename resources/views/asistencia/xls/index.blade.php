<html>    
    <tr>
        <th class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">#</th>       
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">id</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Cedula</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Primer Nombre</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Segundo Nombre</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Primer Apellido</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Segundo Apellido</th>  
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Cargo</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Jefe Inmediato</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Gerencia</th>  
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Unidad/Campaña</th>        
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Segmento/Skill</th>  
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">convocado</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Asistente</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Falta Injustificada</th>
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">%Absentismo</th>
        @foreach($rango as $key => $value)
        <th class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$value->format('d/m/Y')}}</th>
        @endforeach
    </tr>       
    @if(isset($asistencia) && count($asistencia) > 0)
    <?php
    $i = 0;    
    foreach ($personal as $rs) {
        $i++;        
    ?>
        <tr>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$i}}</td> 
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$rs["personal"]}}</td> 
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["cedula"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["primer_nombre"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["segundo_nombre"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["primer_apellido"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["segundo_apellido"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["cargo"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["jefeinmeadito"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["nombre_campana"]}}</td>             
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["nombre_cartera"]}}</td>                         
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["nombre_skill"]}}</td>                         
            @php $total = $asistencia[$rs["personal"]]["asistente"] + $asistencia[$rs["personal"]]["faltainjustificada"] @endphp
            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$total}}</td>                         
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["asistente"]}}</td>                         
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["faltainjustificada"]}}</td>  
            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{ $total > 0 ? round(($asistencia[$rs["personal"]]["faltainjustificada"] / $total)*100, 0) ."%"  : "0%"}}</td>                         
            
            @foreach($rango as $key => $value) 
                @if(array_key_exists($value->format('d/m/Y'),$asistencia[$rs["personal"]]["asistencia"]))
                <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$asistencia[$rs["personal"]]["asistencia"][$value->format('d/m/Y')]}}</td> 
                @else
                <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;"></td> 
                @endif
            @endforeach                
        </tr>
    <?php } ?>
    @else
    <tr>
        <td class="desc" colspan="14" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No existen resultados para la fecha ingresada</td>      
    </tr>
    @endif 
</html>