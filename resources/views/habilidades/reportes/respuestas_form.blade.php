<html>
    <tr style="vertical-align: middle;">
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; height: 88px; font-size: 8px;">Marca Temporal</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; height: 88px; font-size: 8px;">Nombre y Apellido del Evaluado</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Cargo del Evaluado</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Ubicación</th>
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Fecha de Evaluación</th>

        
        @foreach($preguntas_eval as $pregunta)
        <th class="" style="background: #252850;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; width: 20px; font-size: 8px;">{{$pregunta->nombre_pregunta}}</th>
        @endforeach
        

    </tr>
    <?php
    foreach ($form as $rs) {
        ?>
        <tr>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ date("d/m/Y H:i:s",strtotime($rs['created_at'])) }}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['nombre_evaluado']}} </td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['nombre_cargo']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{$rs['sede_laboral']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px;">{{ date("d/m/Y",strtotime($rs['fecha_evaluacion'])) }}</td>
            
            
            <?php 
              foreach($rs['preguntas'] as $respuesta) {
            ?>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">{{$respuesta}}</td>
        <?php } ?>
        </tr>
    <?php } ?>
</html>