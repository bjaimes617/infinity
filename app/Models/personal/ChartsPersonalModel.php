<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CargoModel;
use App\Models\personal\CarteraModel;

class ChartsPersonalModel extends Model
{
    protected $table = 'dir_charts_personal';
    public $timestamps = true;

    protected $fillable = [
        'cartera_id',
        'mes',
        'anio',
        'personal_inicial',
        'personal_final',
        'activos',
        'requerido',
        'diferencial',
        'cumplimiento',
        'rotacion',
        'indice',
        'absentismo',
        'total_ingresos',
        'total_egresos',
        'motivos_egresos'
    ];
}
