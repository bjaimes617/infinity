<?php

namespace App\Models\cursos;

use Illuminate\Database\Eloquent\Model;
use App\Models\cursos\ParticipantesModel;
use App\Models\cursos\CursosModel;
use App\Models\personal\EstatusModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;

class HistorialModel extends Model
{
    protected $table = "rec_historial_participante";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function RelationParticipante(){
        return $this->belongsTo(ParticipantesModel::class,'participante_id');
    }
    
     public function RelationPersonal() {        
        return $this->belongsTo(PersonalModel::class,'personal_id');
    }
    
    public function RelationCartera() {        
        return $this->belongsTo(CarteraModel::class,'cartera_id');
    }
    
    public function Relationskill() {        
        return $this->belongsTo(SkillModels::class,'skill_id');
    }

    public function RelationCurso(){

        return $this->belongsTo(CursosModel::class,'curso_id');
    }

    public function RelationEstatus(){

        return $this->belongsTo(EstatusModel::class, 'estatus_id');
    }

}
