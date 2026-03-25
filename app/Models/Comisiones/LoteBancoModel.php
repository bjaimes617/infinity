<?php

namespace App\Models\Comisiones;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comisiones\LoteModel;
use App\Models\Comisiones\BancoModel;

class LoteBancoModel extends Model
{
    protected $table = "com_lote_banco";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function lotes(){

           return $this->hasMany(LoteModel::class);
    }

    public function bancos(){

        return $this->hasMany(BancoModel::class);
        
    }
}
