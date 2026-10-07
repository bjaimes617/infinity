<?php

namespace App\Http\Controllers\cursos;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\CargoModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\EstatusModel;
use App\Models\cursos\HistorialModel;
use App\Models\cursos\ParticipantesModel;
use App\Models\cursos\CursosModel;
use App\Models\personal\SedeModel;

use App\Models\RegionModel;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;

use App\Imports\cursos\Participantesimport;
use App\Models\cursos\SalasCursosModel;
use App\Models\User;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;

use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;

class CursosController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'check.password']);

        $this->nacademicos      = config('app.especialidad')    != null ? config('app.especialidad') : [];
        $this->horariolab       = config('app.horariolaboral')  != null ? config('app.horariolaboral') : [];
        $this->sedes            = SedeModel::where('active', 1)->wherein('id', [1, 2, 3, 5, 9, 13])->get();
        $this->contractual      = config('app.contractual')     != null ? config('app.contractual') : [];
        $this->horariocursos    = config('app.horariocursos')   != null ? config('app.horariocursos') : [];
        $this->convocadocursos  = config('app.convocadocursos') != null ? config('app.convocadocursos') : [];
        $this->estatus          = config('app.EstatusCursos')   != null ? config('app.EstatusCursos') : [];
        $this->regiones         = RegionModel::where('active', 1)->orderBy('nombre_region', 'ASC')->get();

        $facilitadores = CargoModel::where('nombre_cargo', 'like', 'facilitador%')
            ->orwhere('nombre_cargo', 'like', 'formador%')
            ->orwhere('nombre_cargo', 'like', 'Analista de Calidad%')
            ->orwhere('nombre_cargo', 'like', 'Supervisor%')
            ->orwhere('nombre_cargo', 'like', 'Analista%')
            ->orwhere('nombre_cargo', 'like', 'Senior Formacion%')
            ->orwhere('nombre_cargo', 'like', 'Senior de Captación%')
            ->orwhere('nombre_cargo', 'like', 'Coordinador de Operaciones%')
            ->orwhere('nombre_cargo', 'like', 'Gerente de%')->pluck('id');

        $this->facilitadores = $facilitadores;
    }

    public function index()
    {
        $skills = CursosModel::Groupby('skill_id')->get();

        return view('cursos.administracion.index')->with(["skills" => $skills, "estatus" => $this->estatus])->render();
    }

    public function create()
    {

        $carteras = CarteraModel::where('estatus_id', 1)->where('area_trabajo_id', 2)->get();
        $cargos = CargoModel::whereIn('id', $this->facilitadores)->where('estatus_id', 1)->get();

        return view('cursos.administracion.create')->with([
            "sedes" => $this->sedes,
            "horario" => $this->horariocursos,
            "carteras" => $carteras,
            "cargos" => $cargos,
            "convocado" => $this->convocadocursos,
            "regiones" => $this->regiones
        ])->render();
    }

    public function SearchCursos(Request $request)
    {

        $user = ENV('DEV_USER_CHANGE', false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();

        $fechas = explode("-", $request->fecha);
        $init   = carbon::createFromFormat('d/m/Y', trim($fechas[0]));
        $end    = carbon::createFromFormat('d/m/Y', trim($fechas[1]));
        $cursos = array();
        $row    = array();
        $data   = array();
        $sql    = CursosModel::whereBetween('inicio', array($init->copy()->StartofDay()->format('Y-m-d H:i:s'), $end->copy()->EndofDay()->format('Y-m-d H:i:s')));

        if ($request->skill == null && $request->estatus == null) {
            $cursos = $sql->get();
        }
        if ($request->skill != null && $request->estatus == null) {
            $cursos = $sql->where('skill_id', $request->skill)->get();
        }
        if ($request->skill != null && $request->estatus != null) {
            $cursos = $sql->where('skill_id', $request->skill)->where('active', $request->estatus)->get();
        }

        foreach ($cursos as $curso) {
            $block                  = $curso->getRawOriginal('active') == 0 ? 'disabled disbled' : '';
            $check                  = $curso->getRawOriginal('active') == 1 ? 'checked' : '';
            $row["cod"]             = "DGC" . $curso->id;
            $row["inicio"]          = carbon::create($curso->inicio)->format('d/m/Y');
            $row["finaliza"]          = carbon::create($curso->finaliza)->format('d/m/Y');
            $row["requeridos"]      = $curso->requeridos;
            $row["skill"]           = $curso->Relationskill->nombre_skill;
            $facilitador            = $curso->RelationPersonal;
            $row["facilitador"]     = ucfirst($facilitador->primer_nombre) . " " . ucfirst($facilitador->primer_apellido);
            $row["sede"]            = SedeModel::where('nombre_sede', $curso->sede)->first()->real_name;
            $row["sala"]            = $curso->sala_asignada !== null ? $curso->sala_asignada : " N/D";

            $estatus    = '<div class="form-group">                                                                                     
                <input type="checkbox" id="' . $curso->id . '" class="estatuscursos" ' . $block . ' 
                    name="estatuscurso"
                   ' . $check . '
                    data-backdrop="static"  data-href="' . route('cursos.change.status', $curso->id) . '" data-keyboard="false" data-bootstrap-switch  
                    data-off-color="danger" data-on-color="success"> 
            </div> ';
            $row["estatus"] = $estatus;
            $historial = $user->haspermission('cursos.participante.historial')  ? '<input type="hidden" name="_method" value="delete"><input type="hidden" name="_token" value="' . csrf_token() . '">' : null;

            $editHtml  = $user->haspermission('cursos.edit')    ?  '<a href="' . route('cursos.edit', $curso->id) . '" class="btn btn-sm btn-primary" target="_blank" id="editcurso' . $curso->id . '"  data-toggle="tooltip" data-placement="top" title="Editar"><i class=" fa fa-edit"></i></a>' : null;

            $deleteHtml = $user->haspermission('cursos.delete') ?  '<button type="button" class="btn btn-sm btn-danger" id="deleted' . $curso->id . '" onclick="DeleteCursos(' . $curso->id . ');" data-toggle="tooltip"'
                . ' data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>' : null;

            $row["acciones"] = '<form action="' . route('cursos.delete', $curso->id) . '" method="POST" id="deleted' . $curso->id . '">'
                . '<input type="hidden" name="_method" value="DELETE">' . csrf_field() . ''
                . '<div class="btn-group">' . $historial . ' ' . $editHtml . ' ' . $deleteHtml . '</div></form>';

            $data[] = $row;
        }

        return json_encode($data);
    }

    public function SearchSalas(Request $request)
    {

        $sede = SedeModel::where('nombre_sede', $request->sede)->first();
        $salas = $sede->relationSalas;
        if ($salas !== null) {
            return json_encode($salas);
        } else {
            $salas[] = ["id" => "0", "sala" => "SALA NO DISPONIBLE"];
            return json_encode($salas);
        }
    }

    public function SearchSalasValidate(Request $request)
    {
        if (empty($request->periodo) || empty($request->sala) || !strpos($request->periodo, '-')) {
            return json_encode(true);
        }

        try {
            $arrays = explode("-", $request->periodo);
            $init = Carbon::createfromFormat('d/m/Y', trim($arrays[0]));
            $end = Carbon::createfromFormat('d/m/Y', trim($arrays[1]));
            $sala = $request->sala;

            //SI LA SALA ES OPERACIONES HABILITAMOS LA VALIDACION EFECTIVA

            if ($sala == "Operaciones") {
                return json_encode(true);
            }

            $cursos = CursosModel::where('sala_asignada', $sala)
                ->where('inicio', '<=', $end->format('Y-m-d'))
                ->where('finaliza', '>=', $init->format('Y-m-d'));

            if (isset($request->curso_id)) {
                $cursos->where('id', '!=', $request->curso_id);
            }

            $cursos = $cursos->get();

            if ($cursos->isNotEmpty()) {
                return json_encode(false);
            } else {
                return json_encode(true);
            }
        } catch (\Exception $e) {
            return json_encode(true);
        }
    }

    public function store(Request $request)
    {

        try {
            $arrays = explode("-", $request->fechacurso);
            $init = Carbon::createfromFormat('d/m/Y', trim($arrays[0]));
            $end = Carbon::createfromFormat('d/m/Y', trim($arrays[1]));
            $cartera    = SkillModels::find($request->skill)->cartera_id;
            $gerencia   = CarteraModel::find($cartera)->area_trabajo_id;

            $curso = new CursosModel();
            $curso->inicio          = $init->format('Y-m-d');
            $curso->finaliza        = $end->format('Y-m-d');
            $curso->personal_id     = $request->facilitador;
            $curso->skill_id        = $request->skill;
            $curso->cartera_id      = $cartera;
            $curso->area_trabajo_id = $gerencia;
            $curso->horario         = $request->horario;
            $curso->requeridos      = $request->requeridos;
            $curso->sede            = $request->sede;
            $curso->sala_asignada   = $request->sala;
            $curso->estado_id       = in_array($request->sede, ['STV', 'ATENAS']) ? $request->estado : 3;
            $curso->ciudad_id       = in_array($request->sede, ['STV', 'ATENAS']) ? $request->ciudad : 149;
            $curso->convodado       = $request->convocado;
            $curso->estado_id       = $request->estado;
            $curso->active = 2;
            $curso->save();

            \Session::flash('success', 'El curso Ha sido cargado Exitosamente.');
            return redirect(route('cursos.create'));
        } catch (\exception $e) {
            \Session::flash('error', 'Ocurrio un problema, ' . $e->getMessage());
            return back();
        }
    }

    public function asignacionParticipantesIndex()
    {

        $cursos = CursosModel::whereRaw("DATEDIFF(CURDATE(), inicio) <= 1")
            ->whereIn('active', [1, 2])->orderby('inicio', 'ASC')->get();

        return view('cursos.administracion.asignacion')->with(["cursos" => $cursos, "referidos" => $this->convocadocursos]);
    }

    public function asignacionParticipantesStore(Request $request)
    {

        Excel::import(new Participantesimport($request->curso, $request->referido), Request()->file('archivo'));
        return redirect()->route('cursos.asignacion');
    }

    public function edit($id)
    {
        $cursos = CursosModel::find($id);
        $carteras = CarteraModel::where('estatus_id', 1)->where('area_trabajo_id', 2)->get();
        $skill = SkillModels::where('id', $cursos->skill_id)->get();
        $cargos = CargoModel::whereIn('id', $this->facilitadores)->where('estatus_id', 1)->get();
        $facilitador = PersonalModel::find($cursos->personal_id);
        $sede = SedeModel::where('nombre_sede', $cursos->sede)->first();

        if (in_array($cursos->sede, ['STV', 'ATENAS'])) {
            $estado         = EstadosModel::find($cursos->estado_id);
            $regionSelect   = $estado->region_id;
            $ciudad = $cursos->ciudad_id !== null ? CiudadesModel::find($cursos->ciudad_id) : [];
            $salas = array();
        } else {
            $estado = [];
            $ciudad = [];
            $regionSelect = null;
            $salas = $sede->relationSalas;
        }

        if (in_array($cursos->getrawOriginal('active'), [1, 0])) {
            $block = "readonly";
        } else {
            $block = null;
        }

        return view('cursos.administracion.edit')->with([
            "block" => $block,
            "cargos" => $cargos,
            "facilitador" => $facilitador,
            "curso" => $cursos,
            "carteras" => $carteras,
            "sedes" => $this->sedes,
            "horario" => $this->horariocursos,
            "convocado" => $this->convocadocursos,
            "skill" => $skill,
            "regiones" => $this->regiones,
            "estados" => $estado,
            "ciudad" => $ciudad,
            "salas" => $salas,
            "regionselect" => $regionSelect
        ])->render();
    }

    public function update(Request $request, $id)
    {

        try {
            $arrays = explode("-", $request->fechacurso);
            $init = Carbon::createfromFormat('d/m/Y', trim($arrays[0]));
            $end = Carbon::createfromFormat('d/m/Y', trim($arrays[1]));

            $cartera = SkillModels::find($request->skill)->cartera_id;
            $gerencia = CarteraModel::find($cartera)->area_trabajo_id;

            $curso = CursosModel::find($id);
            $curso->inicio          = $init->copy()->format('Y-m-d');
            $curso->finaliza        = $end->copy()->format('Y-m-d');
            $curso->personal_id     = $request->facilitador;
            $curso->skill_id        = $request->skill;
            $curso->cartera_id      = $cartera;
            $curso->area_trabajo_id = $gerencia;
            $curso->horario         = $request->horario;
            $curso->requeridos      = $request->requeridos;
            $curso->sede            = $request->sede;
            $curso->sala_asignada   = $request->sala;
            $curso->estado_id       = in_array($request->sede, ['STV', 'ATENAS']) ? $request->estado : 3;
            $curso->ciudad_id       = in_array($request->sede, ['STV', 'ATENAS']) ? $request->ciudad : 149;
            $curso->convodado       = $request->convocado;
            $curso->save();

            \Session::flash('success', 'El curso ha sido actualizado exitosamente.');
            return redirect(route('cursos.index'));
        } catch (\Throwable $e) {
            \Session::flash('error', $e->getMessage());
            return back()->withErrors([]);
        }
    }

    public function changeEstatusCursos(Request $request)
    {

        try {
            $curs = CursosModel::find($request->id);
            $curs->active = $request->estatus;
            $curs->save();
            switch ($request->estatus) {
                case 1:
                    $response = ["msg" => "El curso DGC" . $request->id . " ha sido habilitado."];
                    break;
                case 2:
                    $response = ["msg" => "El curso DGC" . $request->id . " ha sido Desactivado."];
                    break;
            }
            return json_encode($response);
        } catch (\Exception $e) {
            return abort(500, $e->getMessage());
        }
    }

    public function destroy($id)
    {

        try {
            $curso = CursosModel::find($id);

            if (ParticipantesModel::where('rec_cursos_id', $id)->exists()) {
                $participantes = ParticipantesModel::where('rec_cursos_id', $id)->get();

                $participantes_cursos = json_encode($participantes);

                ActivityLogger::activity("Curso eliminado - DGC" . $curso->id . " - " . $curso->Relationskill->nombre_skill . " - " . $curso->inicio . " - " . $curso->finaliza, $participantes_cursos);
            }

            $curso->delete();

            \Session::flash('success', 'El curso Ha sido eliminado correctamente');

            return back();
        } catch (\Exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function indexParticipantes()
    {
        return view('cursos.administracion.participantes.index')->with(["estatus" => $this->estatus]);
    }

    /// buscar participantes por cursos
    public function ShowParticipantesCursos(Request $request)
    {
        try {

            $user = ENV('DEV_USER_CHANGE', false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();

            $start = Carbon::createFromFormat('m/Y', $request->mes)
                ->firstOfMonth()->startOfDay();

            $end = Carbon::createFromFormat('m/Y', $request->mes)
                ->endOfMonth()->endOfDay();

            $initPersonal = PersonalModel::where('cedula_empleado',  $user->cedula)->first();

            switch ($user->hasRole('cursos.facilitador')) {
                case true:
                    switch ($request->estatus) {
                        case 1:
                            $cursos = CursosModel::withCount('RelationParticipantes as participantes')
                                ->where('personal_id', $initPersonal->id)
                                ->whereBetween('inicio', array($start->copy()->format('Y-m-d'), $end->copy()->format('Y-m-d')))
                                ->where('active', 1)->orderby('inicio', "ASC")->get();
                            break;
                        case 2:
                        case 0:
                            $cursos = HistorialModel::select(DB::raw('COUNT(rec_historial_participante.cedula_participante) as participantes'), 'cur.id', 'cur.inicio', 'cur.finaliza', 'cur.cartera_id', 'cur.skill_id', 'cur.personal_id', 'cur.sede', 'cur.active')
                                ->whereBetween('cur.inicio', array($start->copy()->format('Y-m-d'), $end->copy()->format('Y-m-d')))
                                ->leftJoin('rec_cursos as cur', 'rec_historial_participante.id_curso', '=', 'cur.id')
                                ->where('cur.personal_id', $initPersonal)
                                ->where('cur.active', $request->estatus)
                                ->groupby('rec_historial_participante.id_curso')
                                ->orderby('inicio', "ASC")
                                ->get()->load('RelationParticipante', 'RelationCurso', 'RelationEstatus');
                            break;
                    }
                    break;
                case false:

                    switch ($request->estatus) {
                        case 1:
                            $cursos = CursosModel::withCount('RelationParticipantes as participantes')
                                ->whereBetween('inicio', array($start->copy()->format('Y-m-d'), $end->copy()->format('Y-m-d')))
                                ->where('active', 1)->orderby('inicio', "ASC")->get();
                            break;
                        case 2:
                        case 0:
                            $cursos = HistorialModel::select(DB::raw('COUNT(rec_historial_participante.cedula_participante) as participantes'), 'cur.id', 'cur.inicio', 'cur.finaliza', 'cur.cartera_id', 'cur.skill_id', 'cur.personal_id', 'cur.sede', 'cur.active')
                                ->whereBetween('cur.inicio', array($start->copy()->format('Y-m-d'), $end->copy()->format('Y-m-d')))
                                ->leftJoin('rec_cursos as cur', 'rec_historial_participante.id_curso', '=', 'cur.id')
                                ->where('cur.active', $request->estatus)
                                ->groupby('rec_historial_participante.id_curso')
                                ->orderby('inicio', "ASC")
                                ->get()->load('RelationParticipante', 'RelationCurso', 'RelationEstatus');
                            break;
                    }
                    break;

                    break;
            }

            $row = array();
            $data = array();

            foreach ($cursos as $cur) {
                $row["id"] = "DGC" . $cur->id;
                $row["inicio"] = date('d/m/Y', strtotime($cur->inicio));
                $row["finaliza"] = date('d/m/Y', strtotime($cur->finaliza));
                $row["totalpartic"] = $cur->participantes;
                $row["cartera"] = $cur->RelationCartera->nombre_cartera;
                $row["skill"] = $cur->Relationskill->nombre_skill;
                $row["facilitador"] = ucfirst($cur->RelationPersonal->primer_nombre) . " " . ucfirst($cur->RelationPersonal->primer_apellido);
                $row["sede"] = $cur->sede;
                $row["sala"]            = $cur->sala_asignada !== null ? $cur->sala_asignada : " N/D";
                $viewHtml = '<a href="' . route('participantes.showing', $cur->id) . '" class="btn btn-sm btn-success"'
                    . ' id="participanteshow' . $cur->id . '"  data-toggle="tooltip" target="_blank" data-placement="top" '
                    . 'title="Ver participantes"><i class=" fa fa-eye"></i></a>';
                $row["acciones"] = '<div class="btn-group">' . $viewHtml . '</div>';
                $data[] = $row;
            }

            return json_encode($data);
        } catch (\Throwable $e) {
            return abort(500, "Ocurrio un problema durante la consulta " . $e->getMessage());
        }
    }

    public function showParticipantes($idcurso)
    {

        $participantes = ParticipantesModel::where('rec_cursos_id', $idcurso)->get();

        $cursos = CursosModel::where('id', $idcurso)->get();

        return view('cursos.administracion.participantes.listado')->with(["participantes" => $participantes, "cursos" => $cursos])->render();
    }

    public function buscarParticipante()
    {

        return view('cursos.administracion.participantes.buscar');
    }

    ///buscar parcicipantes por cedulas
    public function SearchParticipante(Request $request)
    {

        //// participante - buscar busca el historico de cada perticipante.
        $cedula_empleado        = $request->cedula;
        $nombre_participante    = $request->nombre_participante;

        if ($cedula_empleado != "" && $nombre_participante == "") {

            if (ParticipantesModel::where('cedula_empleado', $cedula_empleado)->exists()) {
                $participante   = ParticipantesModel::where('cedula_empleado', $cedula_empleado)->get();
                $cantidad       = ParticipantesModel::where('cedula_empleado', $cedula_empleado)->count();
            } else {

                return json_encode(abort(500, "La cedula del participante no existe."));
            }
        } elseif ($cedula_empleado == "" && $nombre_participante != "") {

            if (ParticipantesModel::where('primer_nombre', 'LIKE', '%' . $nombre_participante . '%')->orWhere('primer_apellido', 'LIKE', '%' . $nombre_participante . '%')->orWhere('segundo_nombre', 'LIKE', '%' . $nombre_participante . '%')->orWhere('segundo_apellido', 'LIKE', '%' . $nombre_participante . '%')->exists()) {

                $participante = ParticipantesModel::where('primer_nombre', 'LIKE', '%' . $nombre_participante . '%')->orWhere('primer_apellido', 'LIKE', '%' . $nombre_participante . '%')->orWhere('segundo_nombre', 'LIKE', '%' . $nombre_participante . '%')->orWhere('segundo_apellido', 'LIKE', '%' . $nombre_participante . '%')->get();
                $cantidad   = ParticipantesModel::where('primer_nombre', 'LIKE', '%' . $nombre_participante . '%')->orWhere('primer_apellido', 'LIKE', '%' . $nombre_participante . '%')->orWhere('segundo_nombre', 'LIKE', '%' . $nombre_participante . '%')->orWhere('segundo_apellido', 'LIKE', '%' . $nombre_participante . '%')->count();
            } else {
                return json_encode(abort(500, "No existen coincidencias con el nombre solicitado."));
            }
        } elseif ($cedula_empleado == "" && $nombre_participante == "") {

            return json_encode(abort(500, "Por favor llene los campos."));
        } elseif ($cedula_empleado != "" && $nombre_participante != "") {

            return json_encode(abort(500,  'Por favor utilice solo un filtro de busqueda.'));
        }
        $row = array();
        $data = array();
        $editar = null;
        $viewHTml = null;
        $deletehtml = null;

        foreach ($participante  as $part) {

            $row["cedula"]     = $part->cedula_empleado;
            $row["nombre"]     = ucwords($part->primer_nombre) . " " . ucwords($part->primer_apellido);
            $row["telefono"]   = $part->telefono_movil;
            $row["curso"]      = "DGC" . $part->RelationCurso->id . " - " . $part->RelationCurso->Relationskill->nombre_skill;

            $row["estatus"]    = $part->RelationEstatus->estatus;

            if (\Auth::user()->haspermission('cursos.editar.participantes')) {
                $editar = '<a href="' . route('participantes.edit', $part->id) . '" class="btn btn-sm btn-primary">'
                    . '<i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="Editar Participante"></i></a>';
            }

            if (\Auth::user()->haspermission('cursos.ver.participantes')) {
                $viewHTml = '<button type="button" class="btn btn-sm btn-success" id="historial' . $part->id . '" '
                    . 'Onclick="DisplayHistorial(' . $part->id . ');" '
                    . 'data-href="' . route("participantes.historial", $part->id) . '" '
                    . 'data-toggle="tooltip"  data-placement="top" title="Ver Historial" '
                    . 'data-name="' . ucwords($part->primer_nombre) . '' . ucwords($part->primer_apellido) . '">'
                    . '<i class="fas fa-eye"></i></button>';
            }

            if (\Auth::user()->haspermission('cursos.eliminar.participantes')) {
                $deletehtml = '  <button type="button" class="btn btn-sm btn-danger" Onclick="DeleteParticipantes(' . $part->id . ');" data-toggle="tooltip" data-placement="top" title="Remover">
                    <i class="fa fa-trash"</i>
                </button>';
            }

            $row["acciones"] = '<form action="' . route('participante.destroy', $part->id) . '" method="POST" id="deleted' . $part->id . '">'
                . '<input type="hidden" name="_method" value="DELETE">' . csrf_field() . '<div class="btn-group">' . $viewHTml . ' ' . $editar . ' ' . $deletehtml . '</div>'
                . '</form>';

            $data[] = $row;
        }
        return json_encode($data);
    }

    public function editParticipante($idparticipante)
    {

        $participante = ParticipantesModel::find($idparticipante);

        $estatus = EstatusModel::select('id', 'estatus')->where('group_module', 'C')->get();
        return view('cursos.administracion.participantes.edit', compact('participante', 'estatus'));
    }

    public function updateParticipante(Request $request, $id)
    {
        try {
            $participante = ParticipantesModel::find($id);
            $participante->cedula_empleado  = $request->cedula;
            $participante->primer_nombre    = ucfirst(strtolower($request->primer_nombre));
            $participante->primer_apellido  = ucfirst(strtolower($request->primer_apellido));
            $participante->segundo_nombre   = ucfirst(strtolower($request->segundo_nombre));
            $participante->segundo_apellido = ucfirst(strtolower($request->segundo_apellido));
            $participante->telefono_movil   =  $request->telefono_movil;
            $participante->estatus_id = $request->estatus_id;
            $participante->save();

            \Session::flash('success', 'El participante ha sido modificado exitosamente.');
            return redirect()->route('participante.buscador.index');
        } catch (\Throwable $e) {
            \Session::flash('error', 'Ocurrio un problema durante la consulta ' . $e->getMessage());
            return back();
        }
    }

    public function deleteParticipante($id)
    {

        $participante = ParticipantesModel::findOrFail($id);

        $participante->delete();

        \Session::flash('success', 'El participante se eliminó exitosamente.');

        return redirect()->route('participante.buscador.index');
    }

    public function historial(Request $request, $id)
    {
        try {

            $data = array();
            $participante = ParticipantesModel::find($id);

            $historial = HistorialModel::where('cedula_participante', $participante->cedula_empleado)->get();

            foreach ($historial as $datos) {

                $row["curso"]       = "DGC" . $datos->id_curso . " " . $datos->skill;
                $row["fecha_inicio"] = date('d/m/Y', strtotime($datos->fecha_inicio));
                $row["fecha_fin"] = date('d/m/Y', strtotime($datos->fecha_fin));
                $row["estatus"] = $datos->estatus_final;

                $data[] = $row;
            }

            return json_encode($data);
        } catch (\Throwable $e) {
            return json_encode(abort(500, $e->getMessage()));
        }
    }
}
