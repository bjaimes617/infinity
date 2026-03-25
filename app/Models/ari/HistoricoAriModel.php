<?php

namespace App\Models\ari;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;

class HistoricoAriModel extends Model
{
    protected $table = "ari_historico";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationpersonal(){

        return $this->belongsTo(PersonalModel::class, 'personal_id','id');
    }

}
