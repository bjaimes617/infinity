<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class AsistenciaModel extends Model
{
    protected $table = 'dir_asistencia';
    protected $fillable = ["id","isp_ip","personal_id","created_at","updated_at"];
    public $timestamps = true;
}
