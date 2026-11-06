<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\personal\CargoModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\ChartsPersonalModel;



use Carbon\Carbon;

class Store_data_chats_personal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $ejecutivo;

    public function __construct()
    {
        $this->ejecutivo = CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
            ->orwhere('nombre_cargo', 'like', 'Team%')
            ->orwhere('nombre_cargo', 'like', 'Validador%')
            ->orwhere('nombre_cargo', 'like', 'Back%')
            ->orwhere('nombre_cargo', 'like', 'Asesor%')
            ->orwhere('nombre_cargo', 'like', 'Agente%')->pluck('id');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fechaactual = Carbon::now();
        $mes = $fechaactual->copy()->format('m');
        $anio = $fechaactual->copy()->format('Y');

        $carterasACtives = CarteraModel::where('estatus_id', 1)->pluck('id', 'nombre_cartera')->toarray();

        $totalesPorAgentes = PersonalModel::where('area_trabajo_id', '2')
            ->wherenotIn('cartera_id', [53])
            ->whereIn('cargo_id', $this->ejecutivo)
            ->wherenotIn('sede_laboral', ['HomeOffice'])
            ->get()->groupBy('cartera_id')
            ->map(function ($items) use ($mes, $anio)
            {
                // Filtramos los egresados que coincidan exactamente con el mes y año
                $egresadosDelMes = $items->filter(function ($item) use ($mes, $anio)
                {
                    if (!$item->fecha_egreso) return false;
                    $fecha = Carbon::parse($item->fecha_egreso);
                    return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                });

                $ingresosDelmes = $items->filter(function ($item) use ($mes, $anio)
                {
                    if (!$item->fecha_ingreso) return false;
                    $fecha = Carbon::parse($item->fecha_ingreso);
                    return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                });
                return [
                    'total_activos'   => $items->where('estatus_id', 1)->count(),
                    'total_egresados' => $egresadosDelMes->count(),
                    'total_ingresos'  => $ingresosDelmes->count(),
                    // Agrupa y cuenta los motivos de egreso en forma de array asociativo [motivo => total]
                    'motivos_egreso'  => $egresadosDelMes->whereNotNull('motivo_egreso')
                        ->groupBy('motivo_egreso')
                        ->map->count()
                        ->toArray(),
                ];
            })->keyBy(fn($item, $carteraId) => $carteraId);




        $totalesCampania = PersonalModel::where('area_trabajo_id', '2')
            ->wherenotIn('cartera_id', [53])
            //->wherenotIn('sede_laboral', ['HomeOffice'])
            ->get()->groupBy('cartera_id')
            ->map(function ($items) use ($mes, $anio)
            {
                $egresadosDelMes = $items->filter(function ($item) use ($mes, $anio)
                {
                    if (!$item->fecha_egreso) return false;
                    $fecha = Carbon::parse($item->fecha_egreso);
                    return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                });

                $ingresosDelmes = $items->filter(function ($item) use ($mes, $anio)
                {
                    if (!$item->fecha_ingreso) return false;
                    $fecha = Carbon::parse($item->fecha_ingreso);
                    return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                });

                return [
                    'total_activos'   => $items->where('estatus_id', 1)->count(),
                    'total_ingresos'  => $ingresosDelmes->count(),
                    'total_egresados' => $egresadosDelMes->count(),
                    // Agrupa y cuenta los motivos de egreso en forma de array asociativo [motivo => total]
                    'motivos_egreso'  => $egresadosDelMes->whereNotNull('motivo_egreso')
                        ->groupBy('motivo_egreso')
                        ->map->count()
                        ->toArray(),
                ];
            })->keyBy(fn($item, $carteraId) => $carteraId);

        ////validamos inicio de mes
        $personalInitia = $fechaactual->isSameDay($fechaactual->copy()->startOfMonth());

        foreach ($totalesPorAgentes as $key => $chat)
        {
            if (in_array($key, $carterasACtives))
            {
                if ($totalesPorAgentes[$key]['total_activos'] == 0)
                {
                    continue;
                }

                $datos = [
                    'activos'         => $totalesPorAgentes[$key]['total_activos'],
                    'total_egresos'   => $totalesCampania[$key]['total_egresados'],
                    'total_ingresos'  => $totalesCampania[$key]['total_ingresos'],
                    'motivos_egresos' => json_encode($totalesPorAgentes[$key]['motivos_egreso']),
                    'personal_final'  => $totalesCampania[$key]['total_activos']
                ];

                // Solo se actualiza si es inicio de mes
                if ($personalInitia)
                {
                    $datos['personal_inicial'] = $totalesCampania[$key]['total_activos'];
                }

                ChartsPersonalModel::updateOrCreate(
                    [
                        'cartera_id'    => $key,
                        'mes'           => $mes,
                        'anio'          => $anio
                    ],
                    $datos
                );
            }
            else
            {
                continue;
            }
        }

        $validateInit = ChartsPersonalModel::where('mes', $mes)
            ->where('anio', $anio)
            ->whereNull('personal_inicial')
            ->exists();

        if ($validateInit)
        {
            $this->extractReplicasPersonal($mes, $anio, $carterasACtives);
        }
    }

    /// solo se invoca la funcion si y solo si, la consulta inicial 
    public function extractReplicasPersonal($mes, $anio, $carterasACtives = null)
    {
        $filePath = $anio . "/" . $mes . "/" . $anio . "_" . $mes . "_" . '01_ReplicaExpedientesDiarios' . '.csv';

        if (!Storage::disk('replicalp')->exists($filePath))
        {
            Log::warning("[Store_data_chats_personal] No se encontró el archivo de réplica: " . $filePath);
            return;
        }

        if (!$carterasACtives)
        {
            $carterasACtives = CarteraModel::where('estatus_id', 1)->pluck('id', 'nombre_cartera')->toArray();
        }

        $filename = Storage::disk('replicalp')->get($filePath);
        // 1. Convertimos las filas directamente en una colección y quitamos las vacías
        $rows = collect(explode("\n", $filename))->map('trim')->filter();
        if ($rows->isEmpty())
        {
            return;
        }

        // 2. Extraemos los encabezados (primera fila) y eliminamos BOM UTF-8 si existe
        $headers = str_getcsv($rows->shift(), ';');
        if (isset($headers[0]))
        {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        }
        $totalHeaders = count($headers);

        // 3. Procesamos las filas restantes de forma segura
        $datosConHeaders = $rows->map(function ($row) use ($headers, $totalHeaders)
        {
            $rowData = str_getcsv($row, ';');
            // Si por algún motivo la fila está incompleta o tiene columnas de más, la nivelamos
            if (count($rowData) !== $totalHeaders)
            {
                // Opción A: Rellenar o recortar para que coincida con los headers (evita el crash)
                $rowData = array_slice(array_pad($rowData, $totalHeaders, ''), 0, $totalHeaders);
            }
            return array_combine($headers, $rowData);
        });

        $carterasMap = CarteraModel::pluck('id', 'nombre_cartera')
            ->mapWithKeys(fn($id, $name) => [trim(mb_strtolower($name)) => $id])
            ->toArray();

        // 5. Aplicamos los filtros equivalentes a la consulta original:
        // - Cartera != 53 (Distribuido)
        $datosFiltrados = $datosConHeaders->filter(function ($item) use ($carterasMap)
        {
            $carteraNombre = trim(mb_strtolower($item['Unidad/Campaña'] ?? ''));
            $carteraId = $carterasMap[$carteraNombre] ?? null;
            if (!$carteraId || $carteraId == 53)
            {
                return false;
            }
            /*  $sede = trim(mb_strtolower($item['Sede Laboral'] ?? ''));
            if ($sede === 'homeoffice')
            {
                return false;
            } */
            return true;
        });

        // 6. Agrupamos por cartera y calculamos totales desde el archivo
        $totalesReplica = $datosFiltrados->groupBy(function ($item) use ($carterasMap)
        {
            return $carterasMap[trim(mb_strtolower($item['Unidad/Campaña']))];
        })->map(function ($items) use ($mes, $anio)
        {
            $egresadosDelMes = $items->filter(function ($item) use ($mes, $anio)
            {
                $fechaEgreso = trim($item['Fecha de Egreso'] ?? '');
                if (empty($fechaEgreso)) return false;
                try
                {
                    $fecha = Carbon::createFromFormat('d/m/Y', $fechaEgreso);
                    return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                }
                catch (\Exception $e)
                {
                    try
                    {
                        $fecha = Carbon::parse($fechaEgreso);
                        return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                    }
                    catch (\Exception $e)
                    {
                        return false;
                    }
                }
            });

            $ingresosDelMes = $items->filter(function ($item) use ($mes, $anio)
            {
                $fechaIngreso = trim($item['Fecha Ingreso'] ?? '');
                if (empty($fechaIngreso)) return false;
                try
                {
                    $fecha = Carbon::createFromFormat('d/m/Y', $fechaIngreso);
                    return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                }
                catch (\Exception $e)
                {
                    try
                    {
                        $fecha = Carbon::parse($fechaIngreso);
                        return $fecha->month == (int)$mes && $fecha->year == (int)$anio;
                    }
                    catch (\Exception $e)
                    {
                        return false;
                    }
                }
            });

            return [
                'total_activos'   => $items->filter(fn($item) => trim(mb_strtolower($item['Estatus'] ?? '')) === 'activo')->count(),
                'total_egresados' => $egresadosDelMes->count(),
                'total_ingresos'  => $ingresosDelMes->count(),
                'motivos_egreso'  => $egresadosDelMes->filter(fn($item) => !empty(trim($item['Motivo del Egreso'] ?? '')))
                    ->groupBy(fn($item) => trim($item['Motivo del Egreso']))
                    ->map->count()
                    ->toArray(),
            ];
        });

        // 7. Guardamos los valores en BD
        foreach ($totalesReplica as $key => $chat)
        {
            if (in_array($key, $carterasACtives))
            {
                $personalInicial = $totalesReplica[$key]['total_activos'];

                $registro = ChartsPersonalModel::where('cartera_id', $key)
                    ->where('mes', $mes)
                    ->where('anio', $anio)
                    ->first();

                if ($registro)
                {
                    // Si ya existe el registro y personal_inicial está nulo, lo actualizamos con la réplica
                    if (is_null($registro->personal_inicial))
                    {
                        $registro->update([
                            'personal_inicial' => $personalInicial
                        ]);
                    }
                }
            }
        }
    }
}
