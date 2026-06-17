<?php

namespace App\Http\Controllers\cursos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\cursos\CursosModel;
use App\Models\personal\PersonalModel;
use App\Models\cursos\ParticipantesModel;
use App\Models\cursos\AsistenciaModel;
use App\Models\asistencia\AsistenciaOperativaModel;
use App\Models\cursos\ObservacionesModels;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Imports\cursos\AsistenciaImport;

class AsistenciaController extends Controller
{

    public function __construct()
    {
        $this->itemsasitencia = [
            "A" => "Asistente",
            "I" => "Inasistente",
            "E" => "Egreso",
            "NA" => "nunca asistio",
            "AB" => "Abandono"
        ];
    }

    public function index()
    {
        $initPersonal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();

        $today = Carbon::today();

        if ($initPersonal) {
            $cursos = CursosModel::where('personal_id', $initPersonal->id)
                ->whereDate('inicio', '<=', $today)
                //->whereDate('finaliza', '>=', $today)
                ->where('active', 1)->get();
        } else {
            $cursos = array();
        }

        return view('cursos.asistencia.index')->with(["cursos" => $cursos]);
    }

    public function create()
    {

        $initPersonal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();
        if ($initPersonal) {
            $cursos = CursosModel::where('personal_id', $initPersonal->id)->get();
        } else {
            $cursos = array();
        }

        return view('cursos.asistencia.list')->with(["cursos" => $cursos]);
    }

    public function ShowCursosAsistencia(Request $request)
    {
        try {
            $arrayBegin = carbon::createfromformat('d/m/Y', $request->fecha)->startofday();

            $participantes = AsistenciaModel::where('rec_cursos_id', $request->cursoasistencia)
                ->Wheredate('created_at', 'like', $arrayBegin->copy()->format('Y-m-d') . '%')->get();

            $row = array();
            $data = array();
            $participantes->load('RelationParticipantes');

            $fecha1 = Carbon::today();
            $fecha2 = Carbon::parse($arrayBegin);

            foreach ($participantes as $parts) {
                $row["cod"] = "DGC" . $request->cursoasistencia;
                $row["cedula"] = $parts->RelationParticipantes->cedula_empleado;
                $row["nombre"] = $parts->RelationParticipantes->primer_nombre . " " . $parts->RelationParticipantes->primer_apellido;
                //dd($parts->RelationCursos);
                $row["skill"] = $parts->RelationCursos->Relationskill->nombre_skill;
                $row["prefijo"] = $parts->prefijo;
                $row["observacion"] = $parts->observacion;
                $editHtml = ' 
                <a href="' . route('cursos.asistencia.edit', $parts->id) . '">
                <button class="btn btn-sm btn-primary" data-toggle="tooltip" target="_blank" data-placement="top" title="Editar"><i class="fa fa-edit"></i></button>
                </a>
            ';
                $row["acciones"] = $editHtml;
                $data[] = $row;
            }

            return json_encode($data);
        } catch (\Throwable $e) {
            return json_encode(abort(500, 'Ocurrio un problema. ' . $e->getMessage()));
        }
    }

    public function store(Request $request)
    {

        $today = Carbon::today();
        try {
            $begin = $request->fechaasistencia;
            $dia = date('l', strtotime($begin)); ///dia de la semana en letras///   
            $hora = date('H:i:s', strtotime(now()));
            $facilitador = CursosModel::find($request->curso);
            $i = 0;

            //dd($facilitador->finaliza == date('Y-m-d', strtotime($begin)),$begin);
            foreach ($request->operador as $operador) {
                $sistencia = new AsistenciaModel();
                $sistencia->facilitador_id = $facilitador->personal_id;
                $sistencia->rec_cparticipantes_id = $operador;
                $sistencia->rec_cursos_id = $request->curso;
                $sistencia->prefijo = $request->input('listado' . $operador);

                $part = ParticipantesModel::find($operador);
                switch ($request->input('listado' . $operador)) {
                    case "E":
                        $part->estatus_id = 15;
                        $part->rec_observacion_id = $request->input('motivo' . $operador);
                        if ($part->cambiocampana == "SI") {
                            $objPersonal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                            $objPersonal->estatus_id = 1;
                            $objPersonal->save();
                        }
                        break;
                    case "NA":
                        $part->estatus_id = 17;
                        $part->rec_observacion_id = 14;
                        if ($part->cambiocampana == "SI") {
                            $objPersonal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                            $objPersonal->estatus_id = 1;
                            $objPersonal->save();
                        }
                        break;
                    case "AB":
                        $part->estatus_id = 13;
                        $part->rec_observacion_id = $request->input('motivo' . $operador);
                        if ($part->cambiocampana == "SI") {
                            $objPersonal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                            $objPersonal->estatus_id = 1;
                            $objPersonal->save();
                        }
                        break;
                }
                if ($part->cambiocampana == "SI") {
                    $personal = PersonalModel::where('cedula_empleado', $part->cedula_empleado)->first();
                    if (!AsistenciaOperativaModel::where('personal_id', $personal->id)
                        ->where('created_at', 'like', $begin . '%')->exists()) {
                        $sistenciaop = new AsistenciaOperativaModel();
                        $sistenciaop->tipo_asistencia = "CC";
                        $sistenciaop->gerente_id = $personal->gerente_area_id;
                        $sistenciaop->supervisor_id = $personal->jefe_inmediato;
                        $sistenciaop->coordinador_id = $personal->jefe_inmediato_segundo;
                        $sistenciaop->personal_id = $personal->id;
                        $sistenciaop->area_trabajo_id = $personal->area_trabajo_id;
                        $sistenciaop->cartera_id = $personal->cartera_id;
                        $sistenciaop->skill_id = $personal->skill_id;
                        switch ($request->input('listado' . $operador)) {
                            case "A":
                                $val = "CCA";
                                break;
                            case "I":
                                $val = "CCI";
                                break;
                            default:
                                $val = "CC" . $request->input('listado' . $operador);
                                break;
                        }
                        $sistenciaop->prefijo = $val;
                        $sistenciaop->block = 1;
                        $sistenciaop->dia = $dia;
                        $sistenciaop->observacion = $request->input('observaciones' . $operador);
                        $sistenciaop->created_at = $begin . " " . $hora;
                        $sistenciaop->created_at = $begin . " " . $hora;
                        $sistenciaop->save();
                    }
                }
                $sistencia->block = 1;
                $sistencia->dia = $dia;
                $sistencia->observacion = $request->input('observaciones' . $operador);
                $sistencia->created_at = $begin . " " . $hora;
                $sistencia->created_at = $begin . " " . $hora;

                $part->save();
                $sistencia->save();
            }

            /// CIERRE AUTOMATICO DEL CURSO CUANDO LA FECHA BUSCADA SEA LA DEL CIERRE.
            if ($facilitador->finaliza == date('Y-m-d', strtotime($begin))) {
                $facilitador->automaticClose = 1;
                $facilitador->active = 0;
                $facilitador->save();
            }

            \Session::flash('success', 'La asistencia de los participanes ha sido procesada exitosamente.');
            return redirect()->back();
        } catch (\Exception $e) {

            \Session::flash('error', 'Ocurrio un problema. ' . $e->getMessage());
            return back();
        }
    }

    public function show(Request $request, $id)
    {

        $curso = CursosModel::find($id);

        if ($curso->RelationParticipantes->count() == 0) {
            return response()->json("El curso no tiene cuenta con participantes asignados", 500);
        }

        $arrayBegin = carbon::createfromformat('d/m/Y', $request->fecha)->startofday();

        $asistenciatomada = AsistenciaModel::WhereDate('created_at', 'like', $arrayBegin->copy()->format('Y-m-d') . '%')
            ->where('rec_cursos_id', $id)->pluck('rec_cparticipantes_id');

        $participantes = ParticipantesModel::whereNotIn('id', $asistenciatomada)->where('rec_cursos_id', $id)->where('estatus_id', 12)->get();

        ///desincorporado - son egresos
        $egresos = ObservacionesModels::where('estatus_id', 15)->get();
        /// abanono  - abandono
        $abandonos = ObservacionesModels::where('estatus_id', 13)->get();

        if (!$participantes->IsEmpty()) {
            $data = view('cursos.asistencia.select', [
                "personalacargo" => $participantes,
                "egresos" => json_encode($egresos),
                "abandonos" => json_encode($abandonos),
                "curso" => $curso,
                "fechas" => $arrayBegin->copy()->format('Y-m-d'),
                "items" => $this->itemsasitencia
            ])->render();

            return response($data, 200);
        } else {
            return response()->json("Ya ha sido tomada la asistencia de los participantes ese dia.", 500);
        }
    }

    public function edit($id)
    {

        $asistencia = AsistenciaModel::find($id);
        ///desincorporado - son egresos
        $egresos = ObservacionesModels::where('estatus_id', 15)->get();
        /// abanono  - abandono
        $abandonos = ObservacionesModels::where('estatus_id', 13)->get();

        return view('cursos.asistencia.edit')->with([
            "asistencia" => $asistencia,
            "itemsasistencia" => $this->itemsasitencia,
            "egresos" => json_encode($egresos),
            "abandonos" => json_encode($abandonos),
        ]);

        /* $fecha1 = Carbon::today();
          $fecha2 = Carbon::parse($asistencia->created_at);

          if($fecha1->isSameDay($fecha2)){

          }else{
          \Session::flash('error', 'Solo puede editar las asistencias del día.');
          return redirect()->back();
          } */
    }

    public function update(Request $request, $id)
    {
        try {
            $asistencia = AsistenciaModel::find($id);
            $asistencia->observacion = $request->observaciones;
            $asistencia->prefijo = $request->cursoasistencia;

            $part = ParticipantesModel::find($asistencia->rec_cparticipantes_id);
            switch ($request->cursoasistencia) {
                case "E":
                    $part = ParticipantesModel::find($asistencia->rec_cparticipantes_id);
                    $part->estatus_id = 15;
                    $part->rec_observacion_id = $request->input('motivo' . $id);
                    break;
                case "AB":
                    $part->estatus_id = 13;
                    $part->rec_observacion_id = $request->input('motivo' . $id);

                    break;
                case "NA":
                    $part->estatus_id = 17;
                    $part->rec_observacion_id = 14;

                    break;
                case "A":
                case "I":
                    $part->estatus_id = 12;
                    $part->rec_observacion_id = 6;
                    break;
            }
            $asistencia->save();
            $part->save();

            \Session::flash('success', 'La asistencia Del participante fue actualiza con exito.');
            return redirect()->route('cursos.asistencia.create');
        } catch (\Throwable $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function IndexUpload()
    {
        $initPersonal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();

        $today = Carbon::today();

        if (\auth::user()->hasrole('admin')) {

            $cursos = CursosModel::whereDate('inicio', '<=', $today)
                ->whereDate('finaliza', '>=', $today)
                ->where('active', 1)->get();
        } else if ($initPersonal) {
            $cursos = CursosModel::where('personal_id', $initPersonal->id)
                ->whereDate('inicio', '<=', $today)
                //->whereDate('finaliza', '>=', $today)
                ->where('active', 1)->get();
        } else {
            $cursos = array();
        }

        ///desincorporado - son egresos
        $egresos = ObservacionesModels::where('estatus_id', 15)->get();
        /// abanono  - abandono
        $abandonos = ObservacionesModels::where('estatus_id', 13)->get();

        return view("cursos.asistencia.upload.index")->with([
            "cursos" => $cursos,
            "egresos" => $egresos,
            "abandonos" => $abandonos,
        ]);
    }

    public function GenerateUpload(Request $request)
    {
        $arrayBegin = carbon::createfromformat('d/m/Y', $request->fecha)->startofday();

        $asistenciatomada = AsistenciaModel::WhereDate('created_at', 'like', $arrayBegin->copy()->format('Y-m-d') . '%')
            ->where('rec_cursos_id', $request->cursoasistencia)->pluck('rec_cparticipantes_id');

        $participantes = ParticipantesModel::whereNotIn('id', $asistenciatomada)->where('rec_cursos_id', $request->cursoasistencia)->where('estatus_id', 12)->get();

        if (!$participantes->IsEmpty()) {

            $disk = Storage::build([
                'driver' => 'local',
                'root' => storage_path('app/cursos'),
                'url' => env('APP_URL') . '/storage/cursos',
                'visibility' => 'private', // O 'private' si prefieres                
            ]);

            $nombre = "curso_DGC_" . $request->cursoasistencia . '_participantes.csv';
            $cabeceras = "cedula;nombre;apellido;'" . $arrayBegin->copy()->format('Y-m-d') . ";observaciones";
            $disk->put($nombre, $cabeceras);

            foreach ($participantes as $participante) {
                $disk->append($nombre, $participante->cedula_empleado . ';' . $participante->primer_nombre  . ';' . $participante->primer_apellido);
            }
            return response()->download(storage_path('app/cursos/' . $nombre), $nombre)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Ya ha sido tomada la asistencia de los participantes ese dia.');
        }
    }

    public function UploadFilesAsistencia(request $request)
    {

        try {
            $files = $request->file('archivo');
            $headings = (new HeadingRowImport)->toArray(request()->file('archivo'));
            $primerasCabeceras = $headings[0][0];

            if (empty($request->cursoasistencia)) {
                $textnamefile = explode("_", $request->archivo->getClientOriginalName());
                $idcurso = $textnamefile[2];
            } else {
                $idcurso = $request->cursoasistencia;
            }

            if (empty($idcurso) || $idcurso == "") {
                return back()->with('error', 'No se pudo identificar el curso, renombre el Archivo o vuelva a Generar el Archivo de Carga');
            }

            Excel::import(new AsistenciaImport($idcurso, $primerasCabeceras), $files);

            return back()->with('success', 'Asistencia cargada exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ocurrio un problema. ' . $e->getMessage());
        }
    }
}
