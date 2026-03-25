<?php

namespace App\Models\Comisiones;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\personal\NominaModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\Comisiones\LoteModel;

class OperacionesComisiones extends Model {

    protected $table = "com_operaciones";
    protected $fillable = ['id', 'cedula', 'personal_id', 'gerencia_id', 'campana_id', 'skill_id', 'tipo_periodo', 'comision_usd', 'variable_usd', 'bonificacion_usd', 'bono_noc_usd', 'he_usd', 'jornada_fin_usd', 'descuentos_usd', 'total_usd', 'cantidad_ventas', 'periodo', 'anio', 'mes', 'atlas', 'activaciones', 'cb', 'total_atlas', 'pago_marketing', 'nomina_id', 'ceco', 'lote_id', 'observaciones', 'incidencia', 'descripcion_incidencia', 'created_at', 'updated_at'];
    
    public function relationPersonal() {

        return $this->belongsTo(PersonalModel::class, 'personal_id', 'id');
    }

    public function relationCartera() {

        return $this->belongsTo(CarteraModel::class, 'campana_id', 'id');
    }

    public function relationSkill() {

        return $this->belongsTo(SkillModels::class, 'skill_id', 'id');
    }

    public function relationNomina() {

        return $this->belongsTo(NominaModel::class, 'nomina_id', 'id');
    }

    public function relationLote() {

        return $this->belongsTo(LoteModel::class, 'lote_id', 'id');
    }

    public function relationGerencia() {

        return $this->belongsTo(AreatrabajoModel::class, 'gerencia_id', 'id');
    }
}
