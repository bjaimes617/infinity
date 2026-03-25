@php     
    $celdascabeceras = 'text-align: center; border: 2px solid #000000; background:#1B3155; color:#ffffff; wrap-text: true;'; 
    $espacio_celdas ='width:10px;';
    $totalconvocados = 0;
    $asistencia = 0;
    $faltantes = 0;
    $ingresos = 0;
    $egresos = 0;
    $reposos = 0;
    $cambios = 0;
    $nl =0;
    $vacaciones =0;    
    @endphp

<html> 
    <tr>
        <td></td>
        <td style="text-align: center; background:#1B3155; color:#ffffff" colspan='5'>Indicadores Operativos de la Campaña {{$name}}</td>       
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}">Rotación</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data["rotacion"]}}%</td>
       
    </tr>
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}">Ausentismo</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data["ausentismo"]}}%</td>
    </tr>
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}">Indice de Rotación</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data["indicerot"]}}%</td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
 
    <tr>
        <th class="celdas" style=" width:30px;"> </th>
        <th rowspan="2" class="desc" style="text-align:center; border: 2px solid #000000;" valign="middle">#</th>
        @foreach($rango as $key => $value)
        <th class="desc" style="text-align: center;border: 2px solid #1B3155; background:#1B3155; color:#ffffff;  wrap-text: true;">{{trans('semanas.'.$value->format('l'))}}</th>        
        @endforeach
        <th rowspan="2" style="{{$celdascabeceras}} text-align:center;" valign="middle">Totales</th>
    </tr>
    <tr>
        <th class="celdas"></th>       
        @foreach($rango as $key => $value)
        <th class="desc" style="text-align: center;border: 2px solid #BE3A8D; background:#BE3A8D; color:#ffffff;  wrap-text: true;">{{$value->format('d/m/Y')}}</th>        
        @endforeach        
    </tr>
     
    <tr>
        <td style="{{$espacio_celdas}}"></td>
        <td style="{{$celdascabeceras}}">Ingresos</td>   
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["ingresos"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["ingresos"]}}</td>
            @php  $ingresos = $ingresos + $data[$value->format('Y-m-d')]["ingresos"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$ingresos}}</b></td>
    </tr> 
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">Convocados</td>
        @foreach($rango as $key => $value)              
        @if(!empty($data[$value->format('Y-m-d')]["convocados"]))
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["convocados"]}}</td>
        @php  $totalconvocados = $totalconvocados + $data[$value->format('Y-m-d')]["convocados"];  @endphp
        @else
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
        @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$totalconvocados}}</b></td>
    </tr> 
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">Asistencia</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["asistente"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["asistente"]}}</td>
            @php  $asistencia = $asistencia + $data[$value->format('Y-m-d')]["asistente"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$asistencia}}</b></td>
    </tr> 
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">No Laborable</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["nolaboral"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["nolaboral"]}}</td>
            @php  $nl = $nl + $data[$value->format('Y-m-d')]["nolaboral"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$nl}}</b></td>
    </tr> 
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">Falta Injustificada</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["faltantes"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["faltantes"]}}</td>
            @php  $faltantes = $faltantes + $data[$value->format('Y-m-d')]["faltantes"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$faltantes}}</b></td>
    </tr> 
    <tr>
        <td ></td>
        <td style="{{$celdascabeceras}}}">Cambios de Campañas</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["cambios"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["cambios"]}}</td>
            @php  $cambios = $cambios + $data[$value->format('Y-m-d')]["cambios"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$cambios}}</b></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">Vacaciones</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["vacaciones"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["vacaciones"]}}</td>
            @php  $vacaciones = $vacaciones + $data[$value->format('Y-m-d')]["vacaciones"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$vacaciones}}</b></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">Reposos</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["reposos"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["reposos"]}}</td>
            @php  $reposos = $reposos + $data[$value->format('Y-m-d')]["reposos"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$reposos}}</b></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{$celdascabeceras}}}">Egresados</td>
        @foreach($rango as $key => $value)              
            @if(!empty($data[$value->format('Y-m-d')]["egresos"]))
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[$value->format('Y-m-d')]["egresos"]}}</td>
            @php  $egresos = $egresos + $data[$value->format('Y-m-d')]["egresos"];  @endphp
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"></td>
            @endif
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;" ><b>{{$egresos}}</b></td>
    </tr>
</html>