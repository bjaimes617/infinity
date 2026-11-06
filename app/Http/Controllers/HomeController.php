<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\personal\PersonalModel;
use App\Models\personal\SkillModels;
use App\Models\personal\CarteraModel;
use App\Models\personal\ChartsPersonalModel;

use App\Models\asistencia\AsistenciaOperativaModel;

use App\Imports\DashboardPersonalRequerido;

use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
use DivisionByZeroError;
use Exception;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'check.password']);
    }

    public function index()
    {

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        if (!$user->roles()->exists())
        {
            abort(403, "Perfil de Acceso no encontrado, No puede Acceder.");
        }
        else
        {
            return view('home.index')->render();
        }
    }

    public function UpdateDashboardRequired(Request $request)
    {
        try
        {

            Excel::import(new DashboardPersonalRequerido($request->mes), $request->file('archivo'));

            return response()->json([
                'success' => true,
                'message' => 'Dashboard actualizado correctamente',
            ]);
        }
        catch (ValidationException $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ]);
        }
        catch (Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el dashboard',
            ]);
        }
    }

    public function DashboardPersonal(Request $request)
    {

        if ($request->method() == 'GET')
        {
            $inicio = Carbon::createFromFormat('m/Y', '10/2026')->startOfDay();
            $mes = $inicio->month;
            $anio = $inicio->year;
            $response = false;
        }
        else
        {
            $inicio = Carbon::createFromFormat('m/Y', $request->mes)->startOfDay();
            $mes = $inicio->month;
            $anio = $inicio->year;
            $response = true;
        }
        $skills = CarteraModel::all()->pluck('nombre_cartera', 'id');
        #extraemos los calculos de cada campaña previamente almacenados
        $personal = ChartsPersonalModel::where('mes', $mes)->where('anio', $anio)->get();

        if ($personal->count() == 0)
        {
            return response()->json([
                'message' => 'No existen Datos Cargados en el periodo Seleccionado.',
            ], 404);
        }
        $AsistenciaCarteras = $personal->pluck('cartera_id');
        $data = array();
        $totalactivos = 0;
        $totalrequeridos = 0;
        $totaldiferencial = 0;
        $totalegresos = 0;
        $motivosUnificados = array();
        $totalConvocados = 0;
        $totalAsistencia = 0;

        #######CALCULOS DE ABSSENTISMO
        $registrosAsistencias = AsistenciaOperativaModel::selectRaw('count(prefijo) as registros, prefijo, cartera_id')
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio)
            ->wherein('cartera_id', $AsistenciaCarteras)
            ->groupBy('cartera_id', 'prefijo')
            ->get()
            ->groupBy('cartera_id')
            ->map(function ($items)
            {
                $asistentes   = (int) $items->where('prefijo', 'A')->sum('registros');
                $inasistentes = (int) $items->where('prefijo', 'FI')->sum('registros');
                $convocados   = $asistentes + $inasistentes;

                return [
                    'asistentes'   => $asistentes,
                    'convocados'   => $convocados,
                    'inasistentes' => $inasistentes,
                ];
            })->keyBy(fn($item, $carteraId) => $carteraId);

        foreach ($registrosAsistencias as $carteraId => $asistenciaReg)
        {
            $data[$carteraId]["asistentes"] = $asistenciaReg['asistentes'] ?? 0;
            $data[$carteraId]["convocados"] = $asistenciaReg['convocados'] ?? 0;
            $totalAsistencia += $asistenciaReg['asistentes'] ?? 0;
            $totalConvocados += $asistenciaReg['convocados'] ?? 0;
        }

        #######RECORRIDO GENERAL DEL DASHBOARD
        foreach ($personal as $pr)
        {
            ##si la cartera tiene 0 activos y 0 requeridos, no la mostramos
            if ($pr["activos"] == 0 && $pr["requerido"] == 0)
            {
                continue;
            }

            $data[$pr->cartera_id]["id"] = $pr->cartera_id;
            $data[$pr->cartera_id]["activos"] = $pr["activos"];
            $data[$pr->cartera_id]["requerido"] = $pr["requerido"];
            $diferencial = $pr["activos"] - $pr["requerido"];


            $rotaciones = $this->CalcularRotacionesGenerales(
                $pr->personal_inicial,
                $pr->personal_final,
                $pr->total_ingresos,
                $pr->total_egresos,
                $data[$pr->cartera_id]["convocados"] ?? 0,
                $data[$pr->cartera_id]["asistentes"] ?? 0,
            );

            /// insertamos los calculos de rotacion indice y absentismo por campaña
            $data[$pr->cartera_id]["rotacion"]     = $rotaciones["rotacion"];
            $data[$pr->cartera_id]["indice"]       = $rotaciones["indice"];
            $data[$pr->cartera_id]["absentismo"]   = $rotaciones["absentismo"];

            $pr->diferencial = $diferencial;
            ## rojo que falta  verde que sobra 
            switch (true)
            {
                case ($diferencial == 0):
                    $data[$pr->cartera_id]["diferencial"] = "<span class='text-info'><b>" . $diferencial . "</b> </span>";
                    break;
                case ($diferencial > 0):
                    $data[$pr->cartera_id]["diferencial"] = "<span class='text-success'><b>" . $diferencial . "</b> <i class='fas fa-sort-up'></i></span>";
                    break;
                case ($diferencial < 0):
                    $data[$pr->cartera_id]["diferencial"] = "<span class='text-danger'><b>" . $diferencial . "</b> <i class='fas fa-sort-down'></i></span>";
                    /// solo contaremos los que estan faltantes
                    $totaldiferencial += $diferencial;
                    break;
            }
            /// porcentajes de cumplimiento
            try
            {
                $porcentaje =  ($data[$pr->cartera_id]["activos"] / $data[$pr->cartera_id]["requerido"]) * 100;
                if ($pr->cumplimiento == 0)
                {
                    $pr->cumplimiento = $porcentaje / 100;
                }

                else if ($porcentaje !== $pr->cumplimiento && $pr->cumplimiento != 0)
                {
                    $pr->cumplimiento = $porcentaje / 100;
                }

                $data[$pr->cartera_id]["cumplimiento"] = round($porcentaje, 2);
            }
            catch (DivisionByZeroError $e)
            {
                $data[$pr->cartera_id]["cumplimiento"] = 0;
            }

            $pr->save();

            $data[$pr->cartera_id]["cartera"] = $skills[$pr->cartera_id];
            $data[$pr->cartera_id]["clase_bg"] = "";

            $totalactivos       = $totalactivos + $pr["activos"];
            $totalrequeridos    = $totalrequeridos + $pr["requerido"];

            ///APARTADO PARA EGRESOS Y ROTACION
            $totalegresos = $totalegresos + $pr->totalegresos;
            $arrymotivos = json_decode($pr->motivos_egresos, true);

            //extraemos el motivo con el valor mas alto de la campaña
            $maxMotivoNombre = collect($arrymotivos)->search(collect($arrymotivos)->max());
            $maxMotivoValor = collect($arrymotivos)->max();

            ///agregamos el motivo y el valor a la campaña
            $data[$pr->cartera_id]["motivos"] = ($maxMotivoNombre !== false)
                ? [ucfirst($maxMotivoNombre) => $maxMotivoValor]
                : null;
        }

        #######IDENTIFICAR TIPO DE DIFERENCIAL ENTRE ACTIVOS Y REQUERIDOS
        if (($totaldiferencial) > 0)
        {
            $totaldiferencial = "<span class='text-success'><b>" . $totaldiferencial . "</b><i class='fas fa-sort-up'></i></span>";
        }
        else
        {
            $totaldiferencial = "<span class='text-danger'><b>" . $totaldiferencial . "</b><i class='fas fa-sort-down'></i></span>";
        }

        //Encontrar el valor más alto de TODO el array $data
        $maxGlobalValor = -1;
        $maxGlobalCarteraId = null;
        foreach ($data as $carteraId => $item)
        {
            if (!empty($item["motivos"]) && $item["motivos"] !== null)
            {
                // Obtenemos el valor numérico del motivo (el primer elemento del array)
                $valorActual = current($item["motivos"]);

                if ($valorActual > $maxGlobalValor)
                {
                    $maxGlobalValor = $valorActual;
                    $maxGlobalCarteraId = $carteraId;
                }
            }
        }
        //aplicamos clase danger
        if ($maxGlobalCarteraId !== null)
        {
            $data[$maxGlobalCarteraId]["clase_bg"] = "bg-danger text-white";
        }


        // Preparar arrays para el bar chart
        $chartLabels = array_column($data, 'cartera');
        $chartActivos = array_column($data, 'activos');
        $chartRequeridos = array_column($data, 'requerido');
        $chartcumplimiento = array_column($data, 'cumplimiento');
        $chartegresos = array_column($data, 'motivos');
        //unificamos los motivos de egresos en uno solo
        $motivosDeEgreso = $this->MotivosDeEgresos($chartegresos);

        usort($data, function ($a, $b)
        {
            //organizamos array por nobmre de carteras       
            $datos = strcmp($a['cartera'], $b['cartera']);
            return $datos;
        });


        //Hacemos todos los calculos de Rotacion General y por Campaña
        $rotaciones = $this->CalcularRotacionesGenerales(
            $personal->pluck('personal_inicial')->sum(),
            $personal->pluck('personal_final')->sum(),
            $personal->pluck('total_ingresos')->sum(),
            $personal->pluck('total_egresos')->sum(),
            $totalConvocados,
            $totalAsistencia
        );
        // Si es true devuelve json para actualizar las vistas
        if ($response)
        {
            return response()->json([
                "datos" => array_values($data),
                "totalactivos" => $totalactivos,
                "totalrequeridos" => $totalrequeridos,
                "totaldiferencial" => $totaldiferencial,
                "chartLabels" => array_values($chartLabels),
                "chartActivos" => array_values($chartActivos),
                "chartRequeridos" => array_values($chartRequeridos),
                "chartcumplimiento" => array_values($chartcumplimiento),
                "totalegresos" => $totalegresos,
                "chartEgresosLabels" => $motivosDeEgreso["chartEgresosLabels"],
                "chartEgresosData" => $motivosDeEgreso["chartEgresosData"],
                "rotacionIndiceAbsGeneral" => $rotaciones
            ]);
        }
        else
        {
            return view('home.dashboard.personal')->with([
                "datos" => $data,
                "totalactivos" => $totalactivos,
                "totalrequeridos" => $totalrequeridos,
                "totaldiferencial" => $totaldiferencial,
                "chartLabels" => json_encode(array_values($chartLabels)),
                "chartActivos" => json_encode(array_values($chartActivos)),
                "chartRequeridos" => json_encode(array_values($chartRequeridos)),
                "chartcumplimiento" => json_encode(array_values($chartcumplimiento)),
                "totalegresos" => $totalegresos,
                "chartEgresosLabels" => $motivosDeEgreso["chartEgresosLabels"],
                "chartEgresosData" => $motivosDeEgreso["chartEgresosData"],
                "rotacionIndiceAbsGeneral" => $rotaciones
            ]);
        }
    }

    public function MotivosDeEgresos($chartegresos)
    {
        foreach ($chartegresos as $motivoArray)
        {
            // Nos aseguramos de que no esté vacío (ignora los [])
            if (!empty($motivoArray) && is_array($motivoArray))
            {
                // Recorremos la clave (nombre del motivo) y el valor (cantidad)
                foreach ($motivoArray as $motivo => $cantidad)
                {
                    // Si el motivo ya existe en nuestro array unificado, le sumamos la cantidad
                    if (isset($motivosUnificados[$motivo]))
                    {
                        $motivosUnificados[$motivo] += $cantidad;
                    }
                    else
                    {
                        // Si no existe, lo inicializamos con su valor
                        $motivosUnificados[$motivo] = $cantidad;
                    }
                }
            }
        }

        $chartEgresosLabels = array_keys($motivosUnificados);
        $chartEgresosData = array_values($motivosUnificados);

        return [
            "chartEgresosLabels" => json_encode($chartEgresosLabels),
            "chartEgresosData" => json_encode($chartEgresosData),
        ];
    }

    public function CalcularRotacionesGenerales(
        $totalPersonalInicial = 0,
        $totalPersonalFinal = 0,
        $totalIngresos = 0,
        $TotalEgresados = 0,
        $convocados = 0,
        $asistentes = 0,
    )
    {

        $promedioPersonal = ($totalPersonalInicial + $totalPersonalFinal) / 2;
        $sumaIngreyEgres = $totalIngresos + $TotalEgresados;
        #rotacion = a promerio de (total personal inicio + total personal fin de mes / 2) / total bajas
        try
        {
            //indice de rotacion
            $rotacion  = ($sumaIngreyEgres / $promedioPersonal) * 100;
            $rotacion = round($rotacion, 2) . "%";
        }
        catch (DivisionByZeroError)
        {
            $rotacion = 0;
        }
        try
        {
            //INDICE DE ROTACION
            $indice  = ($TotalEgresados / $promedioPersonal) * 100;
            $indice = round($indice, 2) . "%";
        }
        catch (DivisionByZeroError)
        {
            $indice = 0;
        }

        try
        {
            //INDICE DE ROTACION
            $abs  = ($convocados - $asistentes);
            $absentismo = ($abs / $convocados) * 100;
            $absentismo = round($absentismo, 2) . "%";
        }
        catch (DivisionByZeroError)
        {
            $absentismo = 0;
        }

        return ["rotacion" => $rotacion, "indice" => $indice, "absentismo" => $absentismo];
    }
}
