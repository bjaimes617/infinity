<table>
    <thead>
     <tr>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">#</th>       
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Curso</th> 
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Estatus</th>   
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Fecha de Inicio</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Fecha de Finalización</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Facilitador</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Campaña</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Skill</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Horario Curso</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Sede</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Región</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Estado</th>    
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Ciudad</th>    
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Convocado</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Referido por</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Cedula</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Primer Nombre</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Segundo Nombre</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Primer Apellido</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Segundo Apellido</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Tlf. Local</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Tlf. Movil</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Horario</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Cargado el</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Cambio de Campaña</th>
         @foreach($data[2] as $key => $value)
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">{{$value->format('Y-m-d')}}</th>
        @endforeach
        
      
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Estatus</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Observación</th>
    </tr>       
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach($data[1] as $participante)        
        <tr>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$i}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["curso"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["estatuscurso"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["inicio"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["finaliza"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["facilitador"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["campana"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["skill"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["horariocurso"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["sede"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["region"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["estado"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["ciudad"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["referido"]}}</td>            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["convocado"]}}</td>            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["cedula"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["primer_nombre"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["segundo_nombre"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["primer_apellido"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["segundo_apellido"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["local"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["movil"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["horarioparticipante"]}}</td>            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["registrado"]}}</td>            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["cambio"]}}</td>                        
            @foreach($data[2] as $key => $value)
           
                @php
                    $fecha = $value->format('Y-m-d');
                    $asistencia = $data[0][$participante]["asistencia"][$fecha] ?? null;
                   
                @endphp
                <td class="desc" style="text-align: left; border: 2px solid #000000; wrap-text: true;">
                    {{ $asistencia !== null ? $asistencia : '' }}
                </td>
            @endforeach            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["estatusparticipante"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[0][$participante]["observacion"]}}</td>
        </tr>
         @php $i++; @endphp
        @endforeach
    </tbody>
</table>

