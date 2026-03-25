<?php

namespace App\Models\Comisiones;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\Comisiones\LoteBancoModel;
use App\Models\personal\AreatrabajoModel;

class BancoModel extends Model
{
    protected $table = "com_banco";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationPersonal(){

        return $this->belongsTo(PersonalModel::class, 'personal_id','id');

    }

    public function relationCampana(){
       
       return $this->belongsTo(CarteraModel::class, 'campana_id','id');

    }

    public function relationSkill(){

        return $this->belongsTo(SkillModels::class, 'skill_id','id');
    }

    public function relationLoteBanco(){

        return $this->belongsTo(LoteBancoModel::class, 'lotebanco_id','id');
    }

    public function relationGerencia(){

        return $this->belongsTo(AreatrabajoModel::class, 'gerencia_id','id');
        
    }


}
