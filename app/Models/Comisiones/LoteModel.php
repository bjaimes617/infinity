<?php

namespace App\Models\Comisiones;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comisiones\OperacionesComisiones;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\Comisiones\LoteBancoModel;
use App\Models\personal\AreatrabajoModel;

class LoteModel extends Model
{
    protected $table = "com_lotes";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationOperaciones(){

        return $this->hasMany(OperacionesComisiones::class,'lote_id','id');
    }

    public function relationCampana(){
       
       return $this->belongsTo(CarteraModel::class, 'campana_id','id');

    }

    public function relationSkill(){

        return $this->belongsTo(SkillModels::class, 'skill_id','id');
    }

    public function relationLoteBanco(){

        return $this->belongsTo(LoteBancoModel::class,'lotebanco_id','id');
        
    }

    public function relationGerencia(){

        return $this->belongsTo(AreatrabajoModel::class, 'gerencia_id','id');
        
    }

}
