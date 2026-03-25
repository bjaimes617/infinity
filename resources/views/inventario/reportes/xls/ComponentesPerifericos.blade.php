<html>
    <br style="background: #FFF;"/>
    <tr style="text-align: center">
        <td style="text-align: center;font-weight:bold; font-size: 12px;" colspan="8">Contabilización de Componentes, Equipos, Monitores y Perifericos </td>   
    </tr>
    <!----tabla de componentes--->
    <br/>    
    <tr style="text-align: center">
        <td style="text-align: center;font-weight:bold; font-size: 12px;" colspan="8">Contabilización de Componentes en {{$sede}}</td>   
    </tr>
    <tr style="text-align: center">
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Componente</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Tipo</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Capacidad</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Expresion</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Asignados</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Disponibles</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Malos</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Totalización</th> 
    </tr>
     @if(isset($data[1]) && count($data[1]) > 0)
     @foreach($data[1] as $componentes)
    <tr>   
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->componente}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->tipo}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->capacidad}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->expresion}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->assigned}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->available}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$componentes->bad}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"><b>{{$componentes->assigned + $componentes->available +$componentes->bad}}</b>
        </td>
        @endforeach
    </tr>
    @else
    <tr>
        <td class="desc" colspan="8" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No existen resultados que coincidan con sus parametros de busqueda.</td>      
    </tr>
    @endif    
    <br/> 
    <!----tabla de Perifericos--->
    <br/>    
    <tr style="text-align: center">
        <td style="text-align: center;font-weight:bold; font-size: 12px;" colspan="6">Contabilización Periferícos en {{$sede}}</td>   
    </tr>
    <tr style="text-align: center">        
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Tipo</th>  
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Conexion</th>          
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Asignados</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Disponibles</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Malos</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Totalización</th> 
    </tr>
     @if(isset($data[2]) && count($data[2]) > 0)
     @foreach($data[2] as $periferico)
    <tr>           
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$periferico->tipo}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$periferico->conexion}}</td>        
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$periferico->assigned}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$periferico->available}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$periferico->bad}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"><b>{{$periferico->assigned + $periferico->available +$periferico->bad}}</b> </td>
        @endforeach
    </tr>
    @else
    <tr>
        <td class="desc" colspan="6" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No existen resultados que coincidan con sus parametros de busqueda.</td>      
    </tr>
    @endif
    <!----tabla de equipos--->
    <br>
    <br>
    <tr style="text-align: center">
        <td style="text-align: center;font-weight:bold; font-size: 12px;" colspan="5">Contabilización de equipos en {{$sede}}</td>   
    </tr>
    <tr style="text-align: center">        
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Tipo</th>          
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Asignados</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Disponibles</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Malos</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Totalización</th> 
    </tr>
    @if(isset($data[3]) && count($data[3]) > 0)
    @foreach($data[3] as $monitor)
    <tr>           
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->tipo}}</td>             
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->assigned}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->available}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->bad}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"><b>{{$monitor->assigned + $monitor->available +$monitor->bad}}</b> </td>
        @endforeach
    </tr>
    @else
    <tr>
        <td class="desc" colspan="5" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No existen resultados que coincidan con sus parametros de busqueda.</td>      
    </tr>
    @endif
    
    <!----tabla de monitores--->
    <br>
    <br>
    <tr style="text-align: center">
        <td style="text-align: center;font-weight:bold; font-size: 12px;" colspan="5">Contabilización de Monitores en General</td>   
    </tr>
    <tr style="text-align: center">        
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Marca</th>          
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Asignados</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Disponibles</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Malos</th> 
        <th class="desc" style="background: #1B3155;color:#FFFFFF;font-weight: bold;text-align: center;border: 2px solid #000000; wrap-text: true;">Totalización</th> 
    </tr>
    @if(isset($data[4]) && count($data[4]) > 0)
    @foreach($data[4] as $monitor)
    <tr>           
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->marca}}</td>             
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->assigned}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->available}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;">{{$monitor->bad}}</td>
        <td class="desc" style="text-align: center;border: 2px solid #000000; wrap-text: true;"><b>{{$monitor->assigned + $monitor->available +$monitor->bad}}</b> </td>
        @endforeach
    </tr>
    @else
    <tr>
        <td class="desc" colspan="5" style="text-align: center;border: 2px solid #000000; wrap-text: true;">No existen resultados que coincidan con sus parametros de busqueda.</td>      
    </tr>
    @endif
</html>