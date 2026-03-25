<?php

namespace App\Exports\Inventario;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Exports\Sheets\InventarioGlobalSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class InventarioGlobalExport implements ShouldAutoSize,WithMultipleSheets,WithTitle{

    private $data;
    private $sede;

    public function __construct($data,$sede) {
        
        $this->data = $data;
        $this->sede = $sede;
        $this->heading = $this->cabeceras();
    }

   public function sheets(): array
    {
        $sheets = [];
        foreach ($this->data as $key => $array) {
            $sheets[] = new InventarioGlobalSheet($array, $key,$this->heading[$key]);
        }
        
        return $sheets;
    }
    
    public function title(): string
    {
        
        return "Inventario Global";
    }
    
    public function cabeceras() {
        
        $monitor    = ["Posición","Identificador", "marca", "pulgadas", "Serial", "Tipo de Alimentador", "etiqueta","Estado Actual"];
        $equipo     = ["Posición","Identificador", "Tipo", "Marca", "Serial", "Estado Fuente", "Slots Máximos", "Ram Asignada", "Discos Asignados", "Mac_Address LAN", "IP-LAN", "Wlan_Mac_Address", "IP-WLAN","Fecha Mantenimiento","Etiqueta Identificativa", "Estado Actual", "CPU","Generación","RAMS Instaladas","Total RAM" ,"Discos Instalados"];
        $periferico = config('app.ReportePerifericos');
        
        return ["Equipos" => $equipo, "Monitores"=>$monitor, "Perifericos"=>$periferico];
    }
}
