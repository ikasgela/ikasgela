<?php

namespace App\Jobs;

use App\Models\JPlag;
use App\Models\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TitasGailius\Terminal\Terminal;

class RunJPlag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Tarea $tarea;

    public function __construct(Tarea $tarea)
    {
        $this->onQueue('low');

        $this->tarea = $tarea;
    }

    public function handle()
    {
        // Crear el directorio temporal
        $directorio = '/' . Str::uuid() . '/';
        Storage::disk('temp')->makeDirectory($directorio);
        $ruta = Storage::disk('temp')->path($directorio);

        // Buscar todas las actividades de compartan plantilla con la actual
        $curso = $this->tarea->user->curso_actual();
        $actividades = $curso->actividades()->where('plantilla_id', $this->tarea->actividad->plantilla_id)->get();

        // Descargar los repositorios
        foreach ($actividades as $actividad) {
            $intellij_projects = $actividad->intellij_projects()->get();

            foreach ($intellij_projects as $intellij_project) {
                $repositorio = $intellij_project->repository();
                Terminal::in($ruta)
                    ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                        . $repositorio['path_with_namespace'] . '.git '
                        . $repositorio['owner'] . '@' . $repositorio['name']);
            }
        }

        // Ejecutar JPlag
        Terminal::in($ruta)
            ->run('java -jar /opt/jplag.jar -l java19 -m 1000 -s -r "./__resultados" .');

        if (Storage::disk('temp')->exists($directorio . '/__resultados/matches_avg.csv')) {

            // Cargar el CSV del resultado
            // https://stackoverflow.com/a/50870196
            $resultados = array_map(function ($v) {
                return str_replace("@", "/", str_getcsv($v, ";"));
            }, file($ruta . '/__resultados/matches_avg.csv'));

            // Borrar las entradas de envíos anteriores
            if ($this->tarea->id > 0)
                DB::table('j_plags')->where('tarea_id', '=', $this->tarea->id)->delete();

            // Recorrer los datos del CSV e insertarlos en la BD
            foreach ($this->tarea->actividad->intellij_projects()->get() as $intellij_project) {
                $enviado = $intellij_project->repository();
                foreach ($resultados as $resultado) {
                    $resultado = array_filter($resultado, 'strlen');

                    if ($enviado['path_with_namespace'] == $resultado[0]) {
                        // Recorrer todos y añadirlos a la tabla
                        for ($i = 0; $i < intdiv(count($resultado), 3); ++$i) {
                            $repo = $resultado[$i * 3 + 2];
                            $porcentaje = $resultado[$i * 3 + 3];

                            $datos = DB::table('actividad_intellij_project')
                                ->where('fork', '=', $repo)
                                ->first();

                            // Insertar los resultados en la tabla RegistrosJPlag
                            JPlag::create([
                                'tarea_id' => $this->tarea->id,
                                'match_id' => Tarea::where('actividad_id', $datos->actividad_id)->first()->id,
                                'percent' => $porcentaje,
                            ]);
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
                                JPlag::create([
                                    'tarea_id' => $this->tarea->id,
                                    'match_id' => Tarea::where('actividad_id', $datos->actividad_id)->first()->id,
                                    'percent' => $porcentaje,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Borrar el directorio temporal
        Storage::disk('temp')->deleteDirectory($directorio);
    }
}
