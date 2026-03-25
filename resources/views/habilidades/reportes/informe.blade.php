<html>
    <tr>
        <th></th>
        <th></th>
        <!-- TITULO DE LA EVALUACION -->
        <th class="" style="color:blue;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 10px; height: 50px;">{{$test_name}}</th>
        <th></th>
    </tr>
   <tr>
    <td></td>
    <td class="" style="color:black;font-weight: bold;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Nombre y apellido del evaluado:</td>
    <td class="" style="color:black;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">{{$nombre_completo}}</td>
    <td class="" style="color:black;font-weight: bold;text-align: right;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Sede:</td>
    <td class="" style="color:black;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">{{$sede}}</td>
   </tr>
   <tr>
    <td></td>
    <td class="" style="color:black;font-weight: bold;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Cargo del Evaluado:</td>
    <td class="" style="color:black;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">{{$position_name}}</td>
    <td class="" style="color:black;font-weight: bold;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Fecha de Evaluación:</td>
    <td class="" style="color:black;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 30px;">{{$rango_evaluacion}}</td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
   </tr>
   <tr>
    <td style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 50px; vertical-align: middle;">N°</td>
    <td style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 50px; vertical-align: middle;">Competencia</td>
    <td style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 50px; vertical-align: middle;">Descripción</td>
    <td style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 50px; vertical-align: middle;">Puntaje X Competencias</td>
    <td style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 50px; vertical-align: middle;">Comentarios</td>
   </tr>
   @php $i = 1; @endphp
    <?php
    foreach ($cates as $ct) {
        ?>
        <tr>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 65px; vertical-align: middle;">{{ $i }}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 65px; vertical-align: middle;">{{$ct['nombre_categoria']}} </td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8x; height: 65px; vertical-align: middle;">{{$ct['descripcion']}}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 65px; vertical-align: middle;">{{ number_format($ct['puntaje'],3,",","") }}</td>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px; height: 65px; vertical-align: middle;">{{ $ct['comentario'] }}</td>
        </tr>
        @php $i++; @endphp
    <?php } ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td style="background:#002060;color:white;font-weight: bold;text-align: left;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Nota final</td>
        <td style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">{{ number_format($resultadoPromedio,3,",","") }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td class="desc" style="text-align: left;font-weight: bold;border: none; wrap-text: true; font-size: 8px;">Conclusiones Generales</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td class="desc" style="text-align: left;border: none; wrap-text: true; font-size: 8px; height: 70px; vertical-align: middle;">{{ucfirst($nota_final)}}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <?php
        foreach ($headers_param as $param) {
            ?>
            <td class="" style="background:#002060;color:white;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">Cantidad de {{$param}}</td>
        <?php } ?>
    </tr>
    <tr>
        <td></td>
        <?php
        foreach ($data_com as $counter) {
            ?>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true; font-size: 8px;">{{ $counter }}</td>
        <?php } ?>    
    </tr>
</html>