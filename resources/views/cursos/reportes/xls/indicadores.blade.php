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
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Region</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Estado</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Convocado por</th>    
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Requeridos</th> 
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Total Convocados</th>            
        @foreach($data[2] as $key => $value)
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">{{$value->format('d/m/Y')}}</th>        
        @endforeach
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Abandonos</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Desincorporados</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Reprobados</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Aprobados</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Nunca Asistio</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Asistentes Totales</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Efectividad</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Cumplimiento</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Retencion</th>
    </tr>       
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach($data[0] as $cursos)        
        <tr>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$i}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["curso"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["estatuscurso"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["inicio"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["finaliza"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["facilitador"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["campana"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["skill"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["horariocurso"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["region"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["estado"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["sede"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["convocado"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["requeridos"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["totalconvocado"]}}</td>                      
            @foreach($data[2] as $key => $value) 
                @if(array_key_exists($value->format('d/m/Y'), $data[1][$cursos]["asistente"]))
                <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["asistente"][$value->format('d/m/Y')]}}</td>
                @else
                <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">0</td>
                @endif
            @endforeach
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["abandono"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["desincorporado"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["reprobados"]}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["aprobados"]}}</td> 
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["noasistio"]}}</td> 
            
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["totalasistencia"]}}</td> 
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["efectividad"]}}%</td> 
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["cumplimiento"]}}%</td> 
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$data[1][$cursos]["retencion"]}}%</td>             
        </tr>
         @php $i++; @endphp
        @endforeach
    </tbody>
</table>

