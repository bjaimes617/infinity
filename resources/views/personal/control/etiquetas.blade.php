<html>
    <head>
        <style>
            /** Define the margins of your page **/
            @page {
                margin: 1cm 2cm;
                margin-bottom: 1cm;
            }
            table td{ 
                font-size: 1em;
            } 
        </style>
    </head>
    <body> 
        <main>            
            <table style="border: 0px dotted #000;width: 100%;">
               <?php $i = 0; ?>
                @foreach($objpersonal as $personal)
               
                @if($i%2 == "0")    
                <tr>                   
                    <td>
                        <table style="border: 1px dotted #000;width: 30%;">
                            <tr>
                                &nbsp;
                                <td  style="width:50%; text-align:center;" valign="bottom"><img style="width:150px; height: 70px;" src="{{asset("img/logo directa.png")}}">
                                    <span style="font-size:x-small;"> <b>J-31463317-1</b></span>
                                </td>
                                <td valign="middle" rowspan="2"> <img src="data:image/png;base64,{{base64_encode(\QrCode::format('png')->size(150)->generate(route('control.scanner.check',[$personal->cedula_empleado.'/']))) }}"></td>
                            </tr>                            
                            <tr>
                                <td valign="middle" style="font-size:12px; width: 50%;text-align: center;">
                                    <b>{{strtoupper($personal->primer_nombre)}} {{strtoupper($personal->primer_apellido)}}</b>
                                    <br>
                                    CI: {{strtoupper($personal->cedula_empleado)}}
                                    <br>
                                    <br>
                                    <b>{{strtoupper($personal->nombre_cargo)}}</b>
                                </td>
                            </tr>                           
                        </table>
                    </td>                    
                    @else                    
                    <td>
                        <table style="border: 1px dotted #000;width: 30%;">
                            <tr>
                                &nbsp;
                                <td  style=" width:50%; text-align:center;" valign="bottom"><img style="width:150px; height: 70px;" src="{{asset("img/logo directa.png")}}">
                                    <span style="font-size:x-small;"> <b>J-31463317-1</b></span>
                                </td>
                                <td valign="middle" rowspan="2"> <img src="data:image/png;base64, {{ base64_encode(\QrCode::format('png')->size(150)->generate(route('control.scanner.check',$personal->cedula_empleado))) }}"></td>
                            </tr>                            
                            <tr>
                                <td valign="middle" style=" font-size:12px; width: 50%;text-align: center;">
                                    <b>{{strtoupper($personal->primer_nombre)}} {{strtoupper($personal->primer_apellido)}}</b>
                                    <br>
                                    CI: {{strtoupper($personal->cedula_empleado)}}
                                    <br>
                                    <br>
                                    <b>{{strtoupper($personal->nombre_cargo)}}</b>
                                </td>
                            </tr>
                        </table>
                    </td>     
                </tr>              
                @endif
                 <?php $i++ ; ?>
                @endforeach     
            </table>           
        </main>
    </body>
</html>
