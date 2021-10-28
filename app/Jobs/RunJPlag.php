<?php

namespace App\Jobs;

use App\Models\Tarea;
use App\Traits\JPlagRunner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RunJPlag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use JPlagRunner;

    protected Tarea $tarea;

    public function __construct(Tarea $tarea)
    {
        $this->onQueue('low');

        $this->tarea = $tarea;
    }

    public function handle()
    {
        $directorio = '/' . Str::uuid() . '/';

        try {
            // Crear el directorio temporal
            Storage::disk('temp')->makeDirectory($directorio);
            $ruta = Storage::disk('temp')->path($directorio);

            $this->run_jplag($this->tarea, $ruta, $directorio);

        } catch (\Exception $e) {
            Log::error('Error al ejecutar JPlag.', [
                'exception' => $e->getMessage(),
                'tarea' => route('profesor.revisar', ['user' => $this->tarea->user->id, 'tarea' => $this->tarea->id]),
            ]);
        } finally {
            // Borrar el directorio temporal
            if (config('ikasgela.jplag_delete_temp')) {
                Storage::disk('temp')->deleteDirectory($directorio);
            }
        }
    }
}
