<?php

namespace App\Models\cursos;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;

class AsistenciaModel extends Model
{
    protected $table = 'rec_asistencia';     
    
    public function RelationParticipantes() {        
        return $this->belongsTo(ParticipantesModel::class,'rec_cparticipantes_id');
    }
    
    public function RelationCursos() {        
        return $this->belongsTo(CursosModel::class,'rec_cursos_id');
    }
    
    public function RelationPersonal() {        
        return $this->belongsTo(PersonalModel::class,'facilitador_id');
    }
}
