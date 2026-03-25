<?php

namespace App\Imports\inventario;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use App\Models\inventario\EquipoModel;
use App\Models\inventario\ComponentesModel;
use App\Models\inventario\StockModel;
use App\Models\inventario\RelationEquipoComponentesModel;
use App\Models\inventario\RelationComponentesStockModel;
use App\Models\inventario\RelationEquiposStockModel;

class EquiposUpload implements ToCollection, WithHeadingRow, WithGroupedHeadingRow, WithValidation {

    use Importable;

    private $data;

    public function __construct() {
        
    }

    public function collection(Collection $data) {
       try {
            foreach ($data as $row) {
               //dd(count(explode("|", $row["cpu"])) != 3,$row["cpu"]);
                $stocks = StockModel::where('tipo', strtoupper(trim($row["stock"])))->first();
                //dd($stocks);
                $equip = new EquipoModel();
                $equip->id = $row["identificador"];
                switch ($row["tipo"]) {
                    case "LAPTOP": $val = 1;
                        break;
                    case "PC": $val = 2;
                        break;
                    case "SRV": $val = 3;
                        break;
                }
                $equip->tipo = $val;
                $equip->marca_pc = strtoupper($row["marca"]);
                $equip->estado_fuente = array_search($row["fuente"], config('app.EstatusDisks'));
                $equip->serial_equipo = $row["serial"];
                $equip->slot = $row["slot_rams"];
                $equip->ram_assigned = $row["asignadas"];
                $equip->disks = $row["discos"];
                $equip->lan_mac_address = $row["macadress"];
                $equip->ip = $row["iplan"];
                $equip->wlan_mac_address = $row["wmaccadresss"];
                $equip->wip = $row["wlan"];
                $equip->available = "X";
                $equip->etiqueta = array_search($row["etiqueta"], config('app.EstatusEtiqueta'));
                $equip->save();

                $stockequipo = new RelationEquiposStockModel();
                $stockequipo->inv_equipos_id = $equip->id;
                $stockequipo->inv_stocks_id = $stocks->id;
                $stockequipo->save();

                $this->OneCPU($equip, $row["cpu"], $stocks);

                if (is_array($row["disco"])) {
                    $this->ArrayDiscos($equip, $row["disco"], $stocks);
                } else {
                    $this->OneDiscos($equip, $row["disco"], $stocks);
                }

                if (is_array($row["ram"])) {
                    $this->ArrayRam($equip, $row["ram"], $stocks);
                } else {
                    $this->OneRam($equip, $row["ram"], $stocks);
                }
            }
            \Session::flash('success', __('success.upload.equipo.masive'));
            return true;
        } catch (\Throwable $e) {
           // dd($row);
           \Session::flash('error', __('ErrorCode') . ' ' . $e->getMessage()).' en Linea '.$e->getLine();
           return false;
        }
    }

    public function ArrayDiscos($equip, $row, $stock) {

        foreach ($row as $disco) {
            if ($disco != null) {
                $arrayvalues = explode("|", $disco);
                if (ComponentesModel::where('componente', "DISCO")->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->exists()) {
                    ///ubicamos
                    $componente = ComponentesModel::where('componente', "DISCO")
                                    ->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->first();
                    //actualizamos
                    $componente->assigned = "X";
                    $componente->available = null;
                    $componente->estatus_disk = 1;
                    $componente->save();
                    /// relacionamos
                    $relation = new RelationEquipoComponentesModel();
                    $relation->inv_equipos_id = $equip->id;
                    $relation->inv_componentes_id = $componente->id;
                    $relation->save();

                    ///cambiamos de stock
                    $changestock = RelationComponentesStockModel::where('inv_componentes_id', $componente->id)->first();
                    $changestock->inv_stocks_id = $stock->id;
                    $changestock->save();
                } else {
                    ///si el componente indicado no existe se crea
                    $componente = new ComponentesModel();
                    $componente->componente = "DISCO";
                    $componente->tipo = strtoupper($arrayvalues[0]);
                    $componente->capacidad = $arrayvalues[1];
                    $componente->expresion = "GB";
                    $componente->generacion = null;
                    $componente->segmento = "STO";
                    $componente->estatus_disk = 1;
                    $componente->assigned = "X";
                    $componente->available = null;
                    $componente->save();
                    ///asociados el componente al equipo

                    $relation = new RelationEquipoComponentesModel();
                    $relation->inv_equipos_id = $equip->id;
                    $relation->inv_componentes_id = $componente->id;
                    $relation->save();

                    ///asignamos al stock a donde va el equipo
                    $changestock = new RelationComponentesStockModel();
                    $changestock->inv_stocks_id = $stock->id;
                    $changestock->inv_componentes_id = $componente->id;
                    $changestock->save();
                }
            }
        }

        return true;
    }

    public function OneDiscos($equip, $row, $stock) {

        $arrayvalues = explode("|", $row);
        //$arrayvalues[0] = SSD/HDD ---- $arrayvalues[1]=CAPACIDAD
        //SE VALIDA SI EL COMPONENTE EXISTE EN EL INVENTARIO
        if (ComponentesModel::where('componente', "DISCO")->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->exists()) {
            ///ubicamos
            $componente = ComponentesModel::where('componente', "DISCO")
                            ->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->first();
            //actualizamos
            $componente->assigned = "X";
            $componente->available = null;
            $componente->estatus_disk = 1;
            $componente->save();
            /// relacionamos
            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $equip->id;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            ///cambiamos de stock
            $changestock = RelationComponentesStockModel::where('inv_componentes_id', $componente->id)->first();
            $changestock->inv_stocks_id = $stock->id;
            $changestock->save();
        } else {
            ///si el componente indicado no existe se crea
            $componente = new ComponentesModel();
            $componente->componente = "DISCO";
            $componente->tipo = strtoupper($arrayvalues[0]);
            $componente->capacidad = $arrayvalues[1];
            $componente->expresion = "GB";
            $componente->generacion = null;
            $componente->segmento = "STO";
            $componente->estatus_disk = 1;
            $componente->assigned = "X";
            $componente->available = null;
            $componente->save();
            ///asociados el componente al equipo

            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $equip->id;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            ///asignamos al stock a donde va el equipo
            $changestock = new RelationComponentesStockModel();
            $changestock->inv_stocks_id = $stock->id;
            $changestock->inv_componentes_id = $componente->id;
            $changestock->save();
        }

        return true;
    }

    public function OneCPU($equip, $row, $stock) {

        $arrayvalues = explode("|", $row);
        //$arrayvalues[0] = SSD/HDD ---- $arrayvalues[1]=CAPACIDAD
        //SE VALIDA SI EL COMPONENTE EXISTE EN EL INVENTARIO
        if (ComponentesModel::where('componente', "CPU")->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->wherenotnull('available')->exists()) {

            $componente = ComponentesModel::where('componente', "CPU")->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->first();
            $componente->assigned = "X";
            $componente->available = null;
            $componente->save();
            /// relacionamos
            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $equip->id;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            ///cambiamos de stock
            $changestock = RelationComponentesStockModel::where('inv_componentes_id', $componente->id)->first();
            $changestock->inv_stocks_id = $stock->id;
            $changestock->save();
        } else {

            $componente = new ComponentesModel();
            $componente->componente = "CPU";
            $componente->tipo = strtoupper($arrayvalues[0]);
            $componente->capacidad = $arrayvalues[1];
            $componente->expresion = "MHz";
            $componente->generacion = $arrayvalues[2];
            $componente->segmento = "PRO";
            $componente->estatus_disk = null;
            $componente->assigned = "X";
            $componente->available = null;
            $componente->save();

            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $equip->id;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            ///asignamos al stock a donde va el equipo
            $changestock = new RelationComponentesStockModel();
            $changestock->inv_stocks_id = $stock->id;
            $changestock->inv_componentes_id = $componente->id;
            $changestock->save();
        }
    }

    public function ArrayRam($equip, $row, $stock) {

        foreach ($row as $ram) {
            if ($ram != null) {
                $arrayvalues = explode("|", $ram);
                //dd($arrayvalues,$row);
                if (ComponentesModel::where('componente', "RAM")->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->exists()) {
                    ///ubicamos
                    $componente = ComponentesModel::where('componente', "RAM")
                            ->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->first();
                    //actualizamos
                    $componente->assigned = "X";
                    $componente->available = null;
                    $componente->save();
                    /// relacionamos
                    $relation = new RelationEquipoComponentesModel();
                    $relation->inv_equipos_id = $equip->id;
                    $relation->inv_componentes_id = $componente->id;
                    $relation->save();

                    ///cambiamos de stock
                    $changestock = RelationComponentesStockModel::where('inv_componentes_id', $componente->id)->first();
                    $changestock->inv_stocks_id = $stock->id;
                    $changestock->save();
                } else {
                    ///si el componente indicado no existe se crea
                    $componente = new ComponentesModel();
                    $componente->componente = "RAM";
                    $componente->tipo = strtoupper($arrayvalues[0]);
                    $componente->capacidad = $arrayvalues[1];
                    $componente->expresion = "GB";
                    $componente->generacion = null;
                    $componente->segmento = "PRO";
                    $componente->estatus_disk = null;
                    $componente->assigned = "X";
                    $componente->available = null;
                    $componente->save();
                    ///asociados el componente al equipo

                    $relation = new RelationEquipoComponentesModel();
                    $relation->inv_equipos_id = $equip->id;
                    $relation->inv_componentes_id = $componente->id;
                    $relation->save();

                    ///asignamos al stock a donde va el equipo
                    $changestock = new RelationComponentesStockModel();
                    $changestock->inv_stocks_id = $stock->id;
                    $changestock->inv_componentes_id = $componente->id;
                    $changestock->save();
                }
            }
        }

        return true;
    }

    public function OneRam($equip, $row, $stock) {

        $arrayvalues = explode("|", $row);
        //$arrayvalues[0] = SSD/HDD ---- $arrayvalues[1]=CAPACIDAD
        //SE VALIDA SI EL COMPONENTE EXISTE EN EL INVENTARIO
        if (ComponentesModel::where('componente', "RAM")->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->exists()) {
            ///ubicamos
            $componente = ComponentesModel::where('componente', "RAM")
                            ->where('tipo', $arrayvalues[0])->where('capacidad', $arrayvalues[1])->wherenotnull('available')->first();
            //actualizamos
            $componente->assigned = "X";
            $componente->available = null;
            $componente->save();
            /// relacionamos
            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $equip->id;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            ///cambiamos de stock
            $changestock = RelationComponentesStockModel::where('inv_componentes_id', $componente->id)->first();
            $changestock->inv_stocks_id = $stock->id;
            $changestock->save();
        } else {
            ///si el componente indicado no existe se crea
            $componente = new ComponentesModel();
            $componente->componente = "RAM";
            $componente->tipo = strtoupper($arrayvalues[0]);
            $componente->capacidad = $arrayvalues[1];
            $componente->expresion = "GB";
            $componente->generacion = null;
            $componente->segmento = "PRO";
            $componente->estatus_disk = null;
            $componente->assigned = "X";
            $componente->available = null;
            $componente->save();
            ///asociados el componente al equipo

            $relation = new RelationEquipoComponentesModel();
            $relation->inv_equipos_id = $equip->id;
            $relation->inv_componentes_id = $componente->id;
            $relation->save();

            ///asignamos al stock a donde va el equipo
            $changestock = new RelationComponentesStockModel();
            $changestock->inv_stocks_id = $stock->id;
            $changestock->inv_componentes_id = $componente->id;
            $changestock->save();
        }

        return true;
    }

    public function rules(): array {
        return [
            "stock" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Stock es Obligatorio.');
                } else {
                    if (!StockModel::where('tipo', trim($value))->exists()) {
                        $onFailure('El Stock suministrado no Existe.');
                    }
                }
            },
            "identificador" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Identificador del Equipo es Obligatorio.');
                } else {
                    if (EquipoModel::where('id', trim($value))->exists()) {
                        $onFailure('El Identificador ya esta en uso.');
                    }
                }
            },
            "tipo" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El Tipo de Equipo es Obligatorio.');
                } else {
                    switch (trim($value)) {
                        case "LAPTOP": $val = 1;
                            break;
                        case  "PC": $val = 2;
                            break;
                        case "SRV": $val = 3;
                            break;
                        default:
                            $val = '';
                            break;
                    }
                    //dd($val == '',$val);
                    if ($val == '') {
                        $onFailure('El Tipo de Equipo no existe.');
                    }
                }
            },
            "marca" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La marca del Equipo es Obligatorio.');
                }
            },
            "serial" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El serial del Equipo es Obligatorio.');
                }
            },
            "fuente" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Fuente del Equipo es Obligatorio');
                } else if (!array_search($value, config('app.EstatusDisks'))) {
                    $onFailure('El Valor de la fuente no existe.');
                }
            },
            "discos" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Cantidad de Discos Asignados es Obligatorio.');
                } 
            },
            "slot_rams" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Cantidad de Slots de memoria Ram es Obligatorio.');
                } 
            },
            "asignadas" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cantidad de ram Asignada es Obligatorio.');
                } 
            },
            "discos" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cantidad de discos Asignados es Obligatorio.');
                } 
            },
            "macadress" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Mac Adress del equipo es Obligatorio.');
                } 
            },
            "macadress" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La Mac Adress del equipo es Obligatorio.');
                } 
            },
            "etiqueta" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El valor de la etiqueta es Obligatorio');
                } else if (!array_search($value, config('app.EstatusEtiqueta'))) {
                    $onFailure('El Valor de la etiqueta no existe.');
                }
            },
            "cpu" => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('El valor del CPU es Obligatorio');
                } else if (count(explode("|", $value)) != 3) {
                    $onFailure('El formato del CPU es incorrecto');
                }
            },
            "disco" => function ($attribute, $value, $onFailure) {
                if (is_array($value)) {                    
                    foreach ($value as $val) {
                        if ($val != null) {
                            if (count(explode("|", $val)) != 2) {
                                $onFailure('El formato de los DISCOS Asignados no es Correcto');
                            }
                        }
                    }
                } else if ($value != null) {
                    if (count(explode("|", $value)) != 2) {
                        $onFailure('El formato del Disco Asignado no es Correcto');
                    }
                }
            },
            "ram" => function ($attribute, $value, $onFailure) {
                if (is_array($value)) {                    
                    foreach ($value as $val) {
                        if ($val != null) {
                            if (count(explode("|", $val)) != 2) {
                                $onFailure('El formato de la RAM Asignada no es Correcto');
                            }
                        }
                    }
                } else if ($value != null) {
                    if (count(explode("|", $value)) != 2) {
                        $onFailure('El formato de la RAM Asignada no es Correcto');
                    }
                }
            },
        ];
    }
}
