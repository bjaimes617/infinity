<html>

    
    <tr>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Fecha Creación</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Tipo de Evaluacion</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Rango de Evaluacion</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Mes</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Periodo </th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Cedula</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Nombre</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Cargo</th>
        <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Servicio</th>
                                
        @foreach($indicadores as $key =>$indicador)       
        @php $val = $key + 1; @endphp
            <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Medicion {{$val }}</th>
            <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Meta</th>
            <th class="" style="background: #002060;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Resultado</th>
        @endforeach                   
    </tr>
    @foreach($evaluado as $per)
    <tr>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['created_at']}}</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['tipoevaluacion']}}</td>        
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['fecha_evaluacion']}}</td>   
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['mes']}}</td>    
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['periodo']}}</td> 
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['cedula_empleado']}}</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['nombre']}}</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['nombre_cargo']}}</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$per['nombre_skill']}}</td>

        @foreach($per['resultados'] as $indicadores)
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$indicadores['indicador']}}</td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$indicadores['meta']}} </td>
        <td style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$indicadores['puntaje']}}</td>
        @endforeach

    </tr>   
    @endforeach



</html>