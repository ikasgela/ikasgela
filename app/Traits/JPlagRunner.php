<?php

namespace App\Traits;

use App\Models\JPlag;
use App\Models\Tarea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

trait JPlagRunner
{
    public function run_jplag(Tarea $tarea, string $ruta, string $directorio): void
    {
        // Buscar todas las actividades de compartan plantilla con la actual
        $curso = $tarea->actividad->unidad->curso;
        $actividades = $curso->actividades()->where('plantilla_id', $tarea->actividad->plantilla_id)->get();

        Log::debug('Ejecutando JPlag...', [
            'directorio' => $directorio,
            'actividades' => $actividades?->count(),
            'tarea' => route('profesor.revisar', ['user' => $tarea->user->id, 'tarea' => $tarea->id]),
        ]);

        // Descargar los repositorios
        foreach ($actividades as $actividad) {
            $intellij_projects = $actividad->intellij_projects()->where('open_with', '=', 'idea')->get();

            $primero = true;
            foreach ($intellij_projects as $intellij_project) {
                if ($primero) {
                    $primero = false;

                    $repositorio = $intellij_project->repository_no_cache();
                    $response = Process::path($ruta)
                        ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                            . $repositorio['path_with_namespace'] . '.git '
                            . 'template');
                }

                $repositorio = $intellij_project->repository();

                if ($repositorio['owner'] != '?') {
                    Log::debug('Clonando...', [
                        'repo' => $repositorio,
                    ]);

                    $response = Process::path($ruta)
                        ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                            . $repositorio['path_with_namespace'] . '.git '
                            . $repositorio['owner'] . '@' . $repositorio['name']);

                    if (!$response->successful()) {
                        Log::error('Error al descargar repositorios mediante Git.', [
                            'output' => $response->output(),
                            'tarea' => route('profesor.revisar', ['user' => $tarea->user->id, 'tarea' => $tarea->id]),
                        ]);
                    } else {
                        Log::debug('Descargando repositorios mediante Git.', [
                            'output' => $response->output(),
                            'tarea' => route('profesor.revisar', ['user' => $tarea->user->id, 'tarea' => $tarea->id]),
                        ]);
                    }
                }
            }
        }

        // Ejecutar JPlag
        $response = Process::path($ruta)
            ->run('java -jar /opt/jplag.jar -l java19 -m 1000 -s -r "./__resultados" -bc template .');

        if (!$response->successful()) {
            Log::error('Error al ejecutar JPlag.', [
                'output' => $response->output(),
                'tarea' => route('profesor.revisar', ['user' => $tarea->user->id, 'tarea' => $tarea->id]),
            ]);

            Log::debug('Borrando...', [
                'directorio' => $directorio,
            ]);
            Storage::disk('temp')->deleteDirectory($directorio);
        } else {
            Log::debug('Salida de JPlag.', [
                'output' => $response->output(),
            ]);
        }

        if (Storage::disk('temp')->exists($directorio . '/__resultados/matches_avg.csv')) {

            // Cargar el CSV del resultado
            // https://stackoverflow.com/a/50870196
            $resultados = array_map(fn($v) => str_replace("@", "/", str_getcsv((string)$v, ";", escape: '\\')), file($ruta . '/__resultados/matches_avg.csv'));

            Log::debug('Resultados de JPlag.', [
                'resultados' => $resultados,
                'tarea' => route('profesor.revisar', ['user' => $tarea->user->id, 'tarea' => $tarea->id]),
            ]);

            // Borrar las entradas de envíos anteriores
            if ($tarea->id > 0)
                DB::table('j_plags')->where('tarea_id', '=', $tarea->id)->delete();

            // Recorrer los datos del CSV e insertarlos en la BD
            foreach ($tarea->actividad->intellij_projects()->get() as $intellij_project) {
                $enviado = $intellij_project->repository();
                foreach ($resultados as $resultado) {
                    $resultado = array_filter($resultado, 'strlen');

                    Log::debug('Resultado individual de JPlag.', [
                        'enviado' => $enviado,
                        'resultado' => $resultado,
                    ]);

                    if ($enviado['path_with_namespace'] == $resultado[0]) {
                        // Recorrer todos y añadirlos a la tabla
                        for ($i = 0; $i < intdiv(count($resultado), 3); ++$i) {
                            $repo = $resultado[$i * 3 + 2];
                            $porcentaje = $resultado[$i * 3 + 3];

                            $datos = DB::table('actividad_intellij_project')
                                ->where('fork', '=', $repo)
                                ->first();

                            // Insertar los resultados en la tabla RegistrosJPlag
                            if ($datos != null) {
                                JPlag::create([
                                    'tarea_id' => $tarea->id,
                                    'match_id' => Tarea::where('actividad_id', $datos->actividad_id)->first()->id,
                                    'percent' => $porcentaje,
                                ]);
                            } else {
                                Log::error('Error al ejecutar JPlag, no se han encontrado datos.', [
                                    'enviado' => $enviado,
                                    'resultado' => $resultado,
                                    'tarea' => route('profesor.revisar', ['user' => $tarea->user->id, 'tarea' => $tarea->id]),
                                ]);
                            }
                        }
                    } else {
                        // Recorrer pero solo añadir el primero si aparece el enviado
                        for ($i = 0; $i < intdiv(count($resultado), 3); ++$i) {
                            $repo = $resultado[$i * 3 + 2];
                            $porcentaje = $resultado[$i * 3 + 3];

                            if ($repo == $enviado['path_with_namespace']) {
                                $datos = DB::table('actividad_intellij_project')
                                    ->where('fork', '=', $resultado[0])
                                    ->first();

                                // Insertar los resultados en la tabla RegistrosJPlag
                                if ($datos != null) {
                                    JPlag::create([
                                        'tarea_id' => $tarea->id,
                                        'match_id' => Tarea::where('actividad_id', $datos->actividad_id)->first()->id,
                                        'percent' => $porcentaje,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
