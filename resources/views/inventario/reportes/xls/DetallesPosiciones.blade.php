<html>
    <br style="background: #FFF;"/>    
    <!----tabla de componentes--->
    <br/>    
    <tr style="text-align: center">
        <td style="text-align: center;font-weight:bold; font-size: 12px;" colspan="12">Detalles de las posición {{$sede}}</td>   
    </tr>
    <tr style="text-align: center">
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Posición</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Area en Mapa</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Etiqueta Posición</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Asignada</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Gerente</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Responsable</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Gerencia</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Campaña</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">skill</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">CECO</th> 
        @for($e = 1; $data[2]["equipos"] >= $e ; $e++)
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Equipo {{$e}}</th> 
        @endfor
        @for($m = 1; $data[2]["monitores"] >= $m ; $m++)
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Monitor {{$m}}</th> 
        @endfor        
        @foreach(config('app.Perifericos') as $periferico)
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">{{$periferico}}</th>         
        @endforeach
    </tr> 

    @foreach($data[3] as $pos)
    @switch($pos->sede)
    @case(2) 
    @php $ab = "PLA" @endphp
    @break;
    @case(3) 
    @php $ab = "SIE"@endphp
    @break;
    @case(4)  
    @php $ab = "AUT" @endphp
    @break;
    @case(5)  
    @php $ab = "MAC" @endphp
    @break;
    @endswitch
    <tr>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$ab}}|{{$pos->number}}</td>       
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$pos->area}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$pos->etiqueta != null ? config('app.EstatusEtiqueta')[$pos->etiqueta] : config('app.EstatusEtiqueta')[1]}}</td> 
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$pos->assigned != null ? "ASIGNADA" : "S/A"}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[1][$pos->id]["gerente"]}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[1][$pos->id]["responsable"]}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[1][$pos->id]["gerencia"]}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[1][$pos->id]["cartera"]}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[1][$pos->id]["skill"]}}</td>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$data[1][$pos->id]["ceco"]}}</td>   
        @if($data[1][$pos->id]["equipos"] != 'S/A')
        @foreach($data[1][$pos->id]["equipos"] as $equipo)
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$equipo->tipo . "|" . sprintf("%05d", $equipo->id)}}</td>   
        @endforeach
        @else
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">S/A</td>   
        @endif
        <!--se valida si existen monitores asociados-->
        @if($data[1][$pos->id]["monitor"] != 'S/A')       
            @php $mo = 0; @endphp 
            <!--contador en cero para sumar cuantos monitores tiene la posicion y recorremos-->
            @foreach($data[1][$pos->id]["monitor"] as $monitores)
                <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{"MON|" . sprintf("%05d", $monitores->id) . "|" . $monitores->marca . "|" . $monitores->pulgadas . '"'}}</td>   
                @php $mo++; @endphp
            @endforeach
            <!--Comparamos el valor $mo con el $m total si es menor imprimimos n td-->
            @if($mo <= $data[2]["monitores"])
                @for($mo; $mo < $data[2]["monitores"] ; $mo++)
                    <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">-</td>
                @endfor  
            @endif
        @else
            <!--si en el array no hay monitores asignados-->
            @for($mr = 1; $data[2]["monitores"] >= $mr ; $mr++)
                <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">S/A</td>
            @endfor             
        @endif

        @if($data[1][$pos->id]["perifericos"] != 'S/A') 
       
        @foreach($data[1][$pos->id]["perifericos"] as $dataper)        
        @foreach(config('app.Perifericos') as $key => $periferico)            
        @if($dataper->tipo == $periferico)
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$dataper->conteo}}</td>                        
        @endif                
        @endforeach
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">-</td>
        @endforeach          
        @else        
        @foreach(config('app.Perifericos') as $key => $periferico)            
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">-</td>                          
        @endforeach                
        @endif
    </tr>
    @endforeach    
</html>