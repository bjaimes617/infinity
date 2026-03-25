<html>
    <br style="background: #FFF;"/>
    <tr style="text-align: left">
        <td style="text-align: left;font-weight:bold; font-size: 12px;" colspan="4" align="left">REPORTE DE ESTRUCTURA GERENCIAL</td>   
    </tr>
    <br/><br/>
    <tr>
        <th class="" style="background: #366cf3;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Nombre Gerencia</th>
        <th class="" style="background: #366cf3;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Estatus Gerencia</th>
        <th class="" style="background: #366cf3;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Nombre Departamento\Campaña</th>
        <th class="" style="background: #366cf3;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Estatus Departamento\Campaña</th>
        <th class="" style="background: #366cf3;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Nombre Skill\Segmento</th>
        <th class="" style="background: #366cf3;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Estatus Skill\Segmento</th> 
    </tr>
    @if(isset($gerencias) && count($gerencias) > 0)
    <?php
    foreach ($gerencias as $rs) {
        ?>
        <tr>
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$rs['nombre_campana']}}</td>
            @if($rs['estatus_area_trabajo_id'] == "1")
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Activo</td>
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Inactivo</td>
            @endif
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$rs['nombre_cartera']}}</td>
            @if($rs['estatus_cartera_id'] == "1")
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Activo</td>
            @elseif($rs['estatus_cartera_id'] == "2")
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Inactivo</td>
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No usar</td>
            @endif
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$rs['nombre_skill']}}</td>
            @if($rs['skill_active'] == "1")
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Activo</td>
            @else
            <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">Inactivo</td>
            @endif
        </tr>
    <?php } ?>
    @else
    <tr>
        <td class="desc" colspan="20" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No existen resultados que coincidan con sus parametros de busqueda.</td>      
    </tr>
    @endif 
</html>