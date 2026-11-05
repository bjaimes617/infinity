<?php

namespace App\Models\asistencia;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;

class AsistenciaOperativaModel extends Model
{
    protected $table = 'dir_asistencia_operativa';
    public $timestamps = true;

    public function scopeFilter($query, array $filters)
    {
        // Ejemplo de cómo manejar filtros comunes:
        $query->when($filters['mes'] ?? null, function ($q, $mes)
        {
            $q->whereMonth('created_at', $mes);
        });

        $query->when($filters['anio'] ?? null, function ($q, $anio)
        {
            $q->whereYear('created_at', $anio);
        });

        $query->when($filters['prefijo'] ?? null, function ($q, $prefijo)
        {
            $q->where('prefijo', $prefijo);
        });
    }

    public function RelationPersonal()
    {
        return $this->belongsTo(PersonalModel::class, 'personal_id');
    }

    public function RelationAreas()
    {
        return $this->belongsTo(AreatrabajoModel::class, 'area_trabajo_id');
    }

    public function RelationCartera()
    {
        return $this->belongsTo(CarteraModel::class, 'cartera_id');
    }

    public function RelationSkill()
    {
        return $this->belongsTo(SkillModels::class, 'skill_id');
    }

    public function RelationListAsistenciaJefeInmediato()
    {
        return $this->belongsTo(PersonalModel::class, 'supervisor_id');
    }

    public function RelationListAsistenciaJefeInmediatosegundo()
    {
        return $this->belongsTo(PersonalModel::class, 'coordinador_id');
    }

    public function RelationListAsistenciaGerente()
    {
        return $this->belongsTo(PersonalModel::class, 'gerente_id');
    }
}
