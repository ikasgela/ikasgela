<?php

namespace App\Traits;

use App\Models\JPlag;
use App\Models\Tarea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;

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
                    Process::path($ruta)
                        ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                            . $repositorio['path_with_namespace'] . '.git '
                            . '__template');
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
            ->run('java -jar /opt/jplag.jar --language=java --subdirectory=src --base-code=./__template --min-tokens=7 --result-file=__resultados --csv-export --mode=RUN .');

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

        if (Storage::disk('temp')->exists($directorio . '/__resultados/results.csv')) {

            // Cargar el CSV del resultado
            // https://stackoverflow.com/a/50870196

            $csv = Reader::createFromPath($ruta . '/__resultados/results.csv', 'r');
            $csv->setHeaderOffset(0);

            // Borrar las entradas de envíos anteriores
            if ($tarea->id > 0)
                DB::table('j_plags')->where('tarea_id', '=', $tarea->id)->delete();

            // Recorrer los datos del CSV e insertarlos en la BD
            foreach ($tarea->actividad->intellij_projects()->get() as $intellij_project) {
                $enviado = $intellij_project->repository();

                $resultados = $csv->getRecords();
                foreach ($resultados as $resultado) {

                    $submissionName1 = Str::replace('@', '/', $resultado['submissionName1']);
                    $submissionName2 = Str::replace('@', '/', $resultado['submissionName2']);
                    $porcentaje = $resultado['averageSimilarity'] * 100;

                    Log::debug('Resultado individual de JPlag.', [
                        'enviado' => $enviado['path_with_namespace'],
                        'resultado1' => $submissionName1,
                        'resultado2' => $submissionName2,
                        'porcentaje' => $porcentaje,
                    ]);

                    if ($enviado['path_with_namespace'] == $submissionName1 || $enviado['path_with_namespace'] == $submissionName2) {

                        if ($enviado['path_with_namespace'] == $submissionName1) {
                            $repo = $submissionName2;
                        } else {
                            $repo = $submissionName1;
                        }

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
                }
            }
        }
    }
}
