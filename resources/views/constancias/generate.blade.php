<table>
    <thead>
     <tr>
     <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Creado el</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Serial</th> 
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Tipo de Contrato</th>   
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Dirigido a</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Primer Nombre</th> 
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Primer Apellido</th> 
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Segundo Nombre</th> 
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Segundo Apellido</th> 
         <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Cargo</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Fecha Ingreso</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Fecha Egreso</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Vigencia</th>
        <th class="desc" style="text-align: center;font-weight:bold; border: 2px solid #000000; wrap-text: true;">Sueldo</th>  
    </tr>       
    </thead>
    <tbody>
        @foreach($constancias as $const)       
        <tr>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->created_at}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->serial}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->tipo_contrato}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->dirigido_a}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->RelationPersonal->primer_nombre}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->RelationPersonal->primer_apellido}}</td>
            @if($const->RelationPersonal->segundo_nombre != null)
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->RelationPersonal->segundo_nombre}}</td>
            @else
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;"> - </td>
            @endif
            @if($const->RelationPersonal->segundo_apellido != null)
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->RelationPersonal->segundo_apellido}}</td>
            @else
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;"> - </td>
            @endif
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->RelationCargo->nombre_cargo}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{date("d-m-Y", strtotime($const->fecha_ingreso))}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{date("d-m-Y", strtotime($const->fecha_egreso))}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{date("d-m-Y", strtotime($const->vigencia_fin))}}</td>
            <td class="desc" style="text-align: left;border: 2px solid #000000; wrap-text: true;">{{$const->sueldo}}</td>
        </tr>

        @endforeach
    </tbody>
</table>

