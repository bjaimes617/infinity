<html>
    <tr style="vertical-align: middle;">
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; height: 810px; font-size: 8px;">Marca Temporal</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; height: 810px; font-size: 8px;">Nombre y Apellido del Evaluado</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Cargo del Evaluado</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Ubicación</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Fecha de Evaluación</th>

        <!-- INICIALIZAMOS UN CONTADOR DE PREGUNTAS POR CATEGORIA -->
        @php $trigger_count = 1; @endphp
        @foreach($preguntas_eval_rf as $pregunta)
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; width: 20px; font-size: 8px;">{{$pregunta->nombre_pregunta}}</th>
        <!-- SI EL CONTADOR LLEGA EL MAXIMO DE PREGUNTAS POR CATEGORIA IMPRIMIMOS LA CATEGORIA Y REINICILIZAMOS EL CONTADOR EN 1 -->
        @if($trigger_count == $pregunta->relationCategoria->cantidad_preguntas)
        <th class="" style="background: #C0C0C0;color:blue;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; width: 20px; font-size: 8px;">Total {{$pregunta->relationCategoria->nombre_categoria}}</th>
        @php $trigger_count = 1; @endphp
        @else
        <!-- SI NO AUMENTAMOS EL CONTADOR HASTA QUE LLEGUE AL TOPE-->
        @php $trigger_count++; @endphp
        @endif
        @endforeach
        <th></th>
        <th class="" style="background: #008080;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; width: 20px; font-size: 8px;">TOTAL GENERAL</th>        
    </tr>
    @if($cantidad > 1)
    <?php
    foreach ($datas as $rs) {
        ?>
        <tr>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ date("d/m/Y H:i:s",strtotime($rs['created_at'])) }}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['nombre_evaluado']}} </td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['nombre_cargo']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['sede_laboral']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ date("d/m/Y",strtotime($rs['fecha_evaluacion'])) }}</td>
            @foreach ($rs['preguntas'] as $categoria => $valores)
                @foreach ($valores['valores'] as $index => $valor)
                @if($index === count($valores['valores']) - 1)            
                <td class="desc" style=" background: #C0C0C0;color:blue;  text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">
                    @else
                <td class="desc" style=" text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">
                    @endif            
                    {{ $valor }}
                </td>
                @endforeach
            @endforeach
            <td></td>
            <td class="desc" style="background: #008080; color:#FFFFFF; font-weight: bold; text-align: center; border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ $rs['resultado_total'] }}</td>
        </tr>
    <?php } ?>
    @else
    <?php
    foreach ($ex as $rs) {
        ?>
        <tr>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ date("d/m/Y H:i:s",strtotime($rs['created_at'])) }}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['nombre_evaluado']}} </td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['nombre_cargo']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['sede_laboral']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ date("d/m/Y",strtotime($rs['fecha_evaluacion'])) }}</td>
            @foreach ($rs['preguntas'] as $categoria => $valores)
                @foreach ($valores['valores'] as $index => $valor)            
                @if($index === count($valores['valores']) - 1) 
                <td class="desc" style="background: #C0C0C0; color:blue; text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">
                    @else 
                <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">
                    @endif            
                    {{ $valor }}
                </td>
                @endforeach
            @endforeach
            <td></td>
            <td class="desc" style="background: #008080;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ $rs['resultado_total'] }}</td>
        </tr>
    <?php } ?>
    @endif
    <?php
    foreach ($result as $prom) {
        ?>
        <tr>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;"></td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;"> </td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;"></td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;"></td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;"></td>
            @foreach ($prom['preguntas'] as $categoria => $valores)
                @foreach ($valores['valores'] as $index => $valor)
                @if($index === count($valores['valores']) - 1)            
                <td class="desc" style=" background: #C0C0C0;color:blue;  text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 12px; font-weight: bold; height: 50px; vertical-align: middle;">
                    @else
                <td class="desc" style=" text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 12px; font-weight: bold; height: 50px; vertical-align: middle;">
                    @endif           
                @if($valor != "")
                    @php echo number_format($valor,3,",",""); @endphp
                @else
                    @php echo ""; @endphp
                @endif
                </td>
                @endforeach
            @endforeach
            <td></td>
            <td class="desc" style="background: #008080;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ number_format($resultadoPromedio,3,",","") }}</td>
        </tr>
    <?php } ?>
</html>