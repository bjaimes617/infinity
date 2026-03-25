<html>
    <head>
        <title>Comprobante de Retención (ARC)</title>
    </head>    
    <style>
        .footer{
            position: absolute;
            top:  95%;
            right:1%;
            font-size: 12px;
        }
        .rem_style{

            font-size: 10px;
        }
        .uptext{
            font-size: 14px;
        }
        .uptexttotal{
            font-size: 16px;
        }
        .bold {
            font-weight: bold;
            font-size: 12px;
        }
        .boldh {
            font-weight: bold;
            font-size: 13px;
        }
        .removespace{
            margin: 0 !important;
        }
        .center{
            text-align: center !important;
        }
        .rigth{
            text-align: right !important;
        }
        table{
            padding-left: 3px;
            padding-right: 3px;
            width: 100%;
            border: 1px solid black;
            font-size: 13px;
            border-collapse: collapse;
            border-right: 1px solid black;
        }
        .nobordertable{
            padding-left: 3px;
            padding-right: 3px;
            width: 100%;
            border: 1px solid white;
            font-size: 13px;
            border-collapse: collapse;
            border-right: 1px solid white;
        }
        .noborder-bottom{
            width: 100%;
            border: 1px solid black;
            font-size: 13px;
            border-collapse: collapse;
            border-bottom-style : hidden!important;
        }
        .noborderbottom{
            width: 100%;
            border: 1px solid black;
            font-size: 13px;
            border-collapse: collapse;
        }
        .noborderup{
            width: 100%;
            border: 1px solid black;
            font-size: 13px;
            border-collapse: collapse;
            border-top-style: hidden!important; 
        }
        .ln_solid {

            border: 0.1px solid black;
            color: #ffffff;
            background-color: black;
            height: 0.1px;
        }
        .noborder {
            border-style : hidden!important;
            width: 100%;
        }
        .nobordertop {
            border-bottom-style : hidden!important;
            border-left-style: hidden!important;
            border-right-style: hidden!important;
            border-top-style: solid!important;

        }
        .piepag{
            width: 100%;
            position: fixed;
            padding-top: 920px;
        }
    </style>
    <body> 
        <table class="nobordertable"> 
          <thead>           
            <tr>
                @if($datos->nomina_id == "1")
                <th colspan="1" class="center"><img src="{{ public_path('img/DIGA_centrodellamadas2021.jpg') }}" style="width: 230px; height: 70px;"><br> </th>
                @else
                <th colspan="1" class="center"><img src="{{ public_path('img/LOGO-DG-CON-RIF-01.png') }}" style="width: 230px; height: 70px;"></th>
                @endif
                <th colspan="3" class="center uptext"><b>COMPROBANTE DE RETENCIÓN</b> <br/> <b>Del 01-01-{{$anio}} al 31-12-{{$anio}}</b> </th>
            </tr>
        </thead>
           </table>
           <table class="noborder-bottom">
               <tr class="center">
                   <td>Beneficiario de las Remuneraciones</td>
               </tr><br>
           </table>
           <table class="noborderup">
               <thead>
                   <tr>
                      <td>Trabajador:</td> 
                      <td><b>{{strtoupper($datos->primer_nombre)}} {{strtoupper($datos->segundo_nombre)}} , {{strtoupper($datos->primer_apellido)}} {{strtoupper($datos->segundo_apellido)}}</b></td> 
                      <td></td> 
                      <td></td> 
                   </tr>
                   <tr>
                      <td>Fecha de Ingreso:</td> 
                      <td><b>@php echo date("d/m/Y", strtotime($datos->fecha_ingreso)) @endphp</b></td> 
                      <td></td> 
                      <td></td> 
                   </tr>
                   <tr>
                      <td>Cedula de Identidad:</td> 
                      <td><b>{{$datos->cedula_empleado}}</b></td> 
                      <td></td> 
                      <td></td> 
                   </tr>
               </thead>           
           </table><br>
           <table>
               <thead>
                   <tr>
                      <td>Persona Jurídica:</td>
                      @if($datos->nomina_id == "1") 
                       <td><b>DIGA CENTRO DE LLAMADAS</b></td>  
                      @else
                        <td><b>CORPORACIÓN DE MERCADEO EMOTIVO, C.A.</b></td> 
                      @endif
                   </tr>
                   <tr>
                      <td>Nro de RIF:</td> 
                      @if($datos->nomina_id == "1") 
                      <td><b>R.I.F. J - 29379582-6</b></td> 
                      @else
                      <td><b>R.I.F. J - 31463317-1</b></td>  
                      @endif
                   </tr>
                   <tr>
                      <td>Dirección de Agente de Retención:</td> 
                      @if($datos->nomina_id == "1")  
                      <td><b>Av. San Francisco, EDIF. INDULAC, Piso 4, Ofic. 4, Urb. Colinas de la California. Caracas (Petare). Estado Miranda - Zona Postal 1071.</b></td> 
                      @else
                      <td><b>Av. La Salle con Calle Lima, Torre Phelps, Piso 4, Plaza Venezuela.</b></td>
                      @endif
                   </tr>
               </thead>           
           </table><br><br>
        <table border="1">          
            <thead class="">
                <tr class="center">
                    <td class="center boldh separar"><br>Mes<br><br></td> 
                    <td class="center boldh separar">Remuneraciones Pagadas </td> 
                    <td class="center boldh separar">Porcentaje de Retención</td> 
                    <td class="center boldh separar">Impuesto Retenido</td>
                    <td class="center boldh separar">Remuneraciones Pagadas Acumuladas</td> 
                    <td class="center boldh separar">Impuesto Retenido Acumulado</td> 
                </tr>               
            </thead>
            <tbody>
                @foreach($nom as $nomina)
               <tr class="center">
                   <td>{{trans('meses.'.$nomina['mes'])}}</td>
                   <td>{{ number_format($nomina['remuneracion'], 2, ',', ' ')}}</td>
                   @if(!isset($nomina['porcentaje']))
                   <td>0,00%</td>
                   @else
                   <td>{{number_format($nomina['porcentaje'], 2, ',', ' ')."%"}}</td>
                   @endif
                   @if(!isset($nomina['impuesto_retenido']))
                   <td>0,00</td>
                   @else
                   <td>{{number_format($nomina['impuesto_retenido'], 2, ',', ' ')}}</td>
                   @endif
                   <td>{{number_format($nomina['suma_pagada'], 2, ',', ' ')}}</td>
                   <td>{{number_format($nomina['suma_impuesto'], 2, ',', ' ')}}</td>
               </tr>
             @endforeach
                <tr class="center">
                    <td><b>TOTALES</b></td>
                    <td><b>{{number_format($remun, 2, ',', ' ')}}</b></td>
                    <td><b></b></td>
                    <td><b>{{number_format($impuestos_totales, 2, ',', ' ')}}</b></td>
                    <td><b>{{number_format($suma_pagada, 2, ',', ' ')}}</b></td>
                    <td></td>
                </tr>
            </tbody>          

        </table> 
        <br />
        <table class="noborder">
            <thead>
                <tr class="center">
                    <td></td>
                    @if($datos->nomina_id == "1") 
                    <td><b>DIGA CENTRO DE LLAMADAS<br>R.I.F. J - 29379582-6</b></td>
                    @else
                    <td><b>CORPORACIÓN DE MERCADEO EMOTIVO, C.A.<br>R.I.F. J - 31463317-1</b></td>
                    @endif
                </tr>
            </thead>
        </table>
        <table class="noborder">
            <thead>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;"><b>Agente de Retención&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                </tr>
            </thead>
        </table>

        <div class="footer"> 
            @if($datos->nomina_id == 1)
            <img src="{{ public_path('img/PIE PAGINA DIGA-01.png') }}" class="piepag">
            @else
            <img src="{{ public_path('/img/PIE_MEMBRETE-01.png') }}" class="piepag">
            @endif    
        </div>
    </body>
</html>