<html>
    <head>
        <title>Recibo de Pago</title>
        <link rel="icon" type="image/png" href="http://{{env('IPSERVER')}}/infinity/img/icon.png">
        <meta name="copyright" content="Powered by Deivis Henriquez and Braian Jaimes. <?php echo date('Y'); ?>">
    </head>    
    <style>
        .footer{
            position: absolute;
            top:  95%;
            right:1%;
            font-size: 12px;
        }

        .uptext{
            font-size: 14px;
        }
        .uptexttotal{
            font-size: 16px;
        }
        .bold {
            font-weight: bold;
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
        }
        table, td {

        }
        .ln_solid {
            border: 0.1px solid black;
            color: #ffffff;
            background-color: black;
            height: 0.1px;
        }
    </style>  
    @php   
    $saltos= count($sqls);
    $i     = 0;
    @endphp
    <body> 
        @foreach($sqls as $periodos)         
        @php     
        $suma  = 0;
        $resta = 0; 
        @endphp
        <table>
            <tr>             
                @if($periodos->tiponomina == 1)            
                <td colspan="2" class="center"><img src="{{ public_path('img/DIGA_centrodellamadas2021.jpg')}}" style="width: 230px; height: 70px;"><br>DIGA Centro de llamadas 2021 C.A</td>
                @else
                <td colspan="2" class="center"><img src="{{ public_path('img/LOGO-DG-RIF-01.png')}}" style="width: 230px; height: 70px;"><br> Corporacion de Mercadeo Emotivo C.A</td>                       
                @endif
                <td colspan="3" class="center uptext">RECIBO DE PAGO<br/> <span class="bold">NRO. CONTROL:</span> {{$datarecibo[$periodos->id]["id"]}}</td>                  
            </tr>            
            <tr>
                <td colspan="5" class=""><span class="bold">DESDE: </span> {{$datarecibo[$periodos->id]["fechainicio"]}} <span class="bold">HASTA:</span> {{$datarecibo[$periodos->id]["fechacorte"]}} </td>
            </tr>
            <tr>
                <td colspan="5"><span class="bold">NRO. EMPLEADO: </span> {{$cedula->id}}</td>                
            </tr>
            <tr>
                <td colspan="2"><span class="bold">NOMBRES Y APELLIDOS:</span> {{strtoupper($cedula->primer_nombre)}} {{strtoupper($cedula->segundo_nombre)}}, {{strtoupper($cedula->primer_apellido)}} {{strtoupper($cedula->segundo_apellido)}}</td>                                
                <td colspan="3" class="center"> </td>                
            </tr>
            @if($directivo == 0)
            <tr>
                <td colspan="5"><span class="bold">CARGO EJERCIDO: </span> {{$cedula->RelationCargos->nombre_cargo}}</td> 
            </tr>
            @else
            <tr>
                <td colspan="5"><span class="bold">CARGO DE DIRECCIÓN EJERCIDO: </span> {{$cedula->RelationCargos->nombre_cargo}}</td> 
            </tr>
            @endif 
            <tr>
                <td colspan="5" class="ln_solid"></td>
            </tr> 
            <tbody class="">
                <tr>
                    <td colspan="2" class="center bold">DESCRIPCIÓN</td> 
                    <td colspan="1" class="center bold">AUXILIAR&nbsp;|</td>
                    <td colspan="1" class="center bold">ASIGNACIÓN&nbsp;|</td> 
                    <td colspan="1" class="center bold">DEDUCCIÓN&nbsp;</td> 
                </tr>
            @foreach($datarecibo[$periodos->id]["asignaciones"] as $asigna)   
            <tr>  
                @if($asigna["tipo"] == 1 )                  
                    <td colspan="2" class=" ">{{$asigna["descripcion"]}}</td>
                    <td colspan="1" class="center">  - </td>
                    <td colspan="1" class="center">Bs. {{number_format($asigna["monto"],2,",",".")}}</td>
                    <td colspan="1" class="center"></td>                   
                    @php $suma = $suma + $asigna["monto"]; @endphp
                    
                @else 
                    <!-- fila de detalles y deducciones -->
                    <td colspan="2" class=" ">{{$asigna["descripcion"]}}</td>
                    <td colspan="1" class="center"> - </td>
                    <td colspan="1" class="center"></td>
                    <td colspan="1" class="center">Bs. -{{number_format($asigna["monto"],2,",",".")}}</td>
                    @php $resta = $resta + $asigna["monto"]; @endphp
                @endif 
            </tr>
            @endforeach
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" class="ln_solid"></td>
            </tr>
            <tr><!--fila unificada de Sub monto en funcion a los items -->
                <td colspan="3" class="center bold">SUB TOTAL</td>                      
                <td colspan="1" class="center">Bs.{{number_format($suma ,2,",",".")}}</td> 
                <td colspan="1" class="center">Bs.-{{number_format($resta,2,",",".")}}</td> 
            </tr> 
        </tbody>
    </table>
    <br />
    <table>
        <tr>
            <!--fila de monto total a cancelar -->
            <td colspan="2" class="rigth bold uptexttotal">MONTO TOTAL A COBRAR</td> 
            @if($activesummonto)
            <td colspan="2" class="center bold uptexttotal">Bs. {{number_format($suma - $resta,2,",",".")}}</td>                                 
            @endif
        </tr> 
    </table>
    <div class="footer"><p>Generado automaticamente por la plataforma {{env('APP_NAME')}} el: {{date('d/m/Y H:i:s a',strtotime(now()))}} </p></div>                      
    @php $i++; @endphp
    @if($i != $saltos)
    <div style="page-break-after:always;"></div>  
    @endif
    @endforeach
</body>
</html>