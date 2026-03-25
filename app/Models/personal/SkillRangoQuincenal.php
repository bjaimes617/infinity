<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\AreatrabajoModel;

class SkillRangoQuincenal extends Model
{
    protected $table = "dir_skill_quincenal";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationCartera(){

        return $this->belongsTo(CarteraModel::class, 'campana_id','id');

    }

    public function relationSkill(){

        return $this->belongsTo(SkillModels::class, 'skill_id','id');

    }

    public function relationGerencia(){

        return $this->belongsTo(AreatrabajoModel::class, 'gerencia_id','id');
        
    }
}
