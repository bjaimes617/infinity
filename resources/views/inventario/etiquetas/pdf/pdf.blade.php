<html>    
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
    <meta name="author" content="Deivis Henriquez deivis(at)vozip.net and Braian Jaimes braian.jaimes(at)directagroup.net"/>
    <meta name="copyright" content="Powered by Deivis Henriquez and Braian Jaimes. <?php echo date('Y'); ?>">
    <title>{{env('APP_NAME')}} - Platform | Inventario | Etiquetas</title>    
        <style>
            /** Define the margins of your page **/
            @page {
                margin: 1cm 2cm 1cm 2cm;
                
            }
            table td{
                font-size: 1em;
                padding: 1px;
            }
            table td p{
                margin-top:0;
                margin-bottom:1px;
            }
        </style>
    </head>
    <body> 
        <main>            
            <table style="border: 0px dotted #000; width: 100%;">
                <?php $i = 0; ?>
                @foreach($data as $data)
                @if($i%2 == "0")    
                <tr>                  
                    <td>
                        <table style="border: 1px dotted #000; width: 100%;">
                            <tr>
                                <td style="width:100%; text-align:center; margin-bottom:0px; " colspan="2">
                                    <img style="width:190px; height: 50px;" src="http://{{env('IPSERVER')}}/infinity/img/LOGO DG AZULYMAGENTA.png">  
                                    <hr>
                                </td>                                
                            </tr> 
                            <tr>
                                <td  style="width:30%; text-align:center; ">
                                    <img src="data:image/png;base64,{{base64_encode(\QrCode::format('png')->size(100)->generate("a")) }}">
                                </td>
                                <td  style="width:50%; text-align:center;">
                                    <p><b>{{$data["identificador"]}}</b></p>
                                    <p>{{isset($data["marca"]) ? $data["marca"] : ""}}</p>                                    
                                </td>
                            </tr>                  
                        </table>
                    </td>                    
                    @else                    
                    <td>
                        <table style="border: 1px dotted #000; width: 100%;">
                            <tr>
                                <td style="width:100%; text-align:center; margin-bottom:0px; " colspan="2">
                                    <img style="width:190px; height: 50px;" src="http://{{env('IPSERVER')}}/infinity/img/LOGO DG AZULYMAGENTA.png"> 
                                    <hr>
                                </td>                                
                            </tr> 
                            <tr>
                                <td  style="width:30%; text-align:center;">
                                    <img src="data:image/png;base64,{{base64_encode(\QrCode::format('png')->size(100)->generate("a")) }}">
                                </td>
                                <td  style="width:50%; text-align:center;">
                                    <p><b>{{$data["identificador"]}}</b></p>
                                    <p>{{isset($data["marca"]) ? $data["marca"] : ""}}</p>                                   
                                </td>
                            </tr>                  
                        </table>
                    </td> 
                </tr>              
                @endif
                <?php $i++; ?>
                @endforeach     
            </table>           
        </main>
    </body>
</html>
