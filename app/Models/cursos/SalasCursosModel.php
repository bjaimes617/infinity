<?php

namespace App\Models\cursos;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;

class SalasCursosModel extends Model
{
    protected $table = 'rec_cursos_salas';
}
