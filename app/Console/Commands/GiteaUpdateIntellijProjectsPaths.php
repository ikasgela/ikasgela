<?php

namespace App\Console\Commands;

use App\IntellijProject;
use Cache;
use Illuminate\Console\Command;

class GiteaUpdateIntellijProjectsPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitea:update-intellij-projects-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar las URLs de los repositorios que empiezan Borra todos los repositorios de Gitea';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (config('app.env') == 'production') {
            $this->alert('App en producción');
            if ($this->confirm('¿Continuar?')) {

                $this->info('Inicio: ' . now());

                $proyectos = IntellijProject::where('host', 'gitlab')
                    ->where('repositorio', 'like', 'programacion/%')->get();

                foreach ($proyectos as $proyecto) {
                    $nombre = 'root/' . str_replace('/', '.', $proyecto->repositorio);

                    $this->line($nombre);

                    $proyecto->repositorio = $nombre;
                    $proyecto->host = 'gitea';

                    $proyecto->save();

                    Cache::forget($proyecto->cacheKey());
                }

                $this->line('');
                $this->warn('Total: ' . $proyectos->count());

                $this->info('Fin: ' . now());
            }
        }
    }
}
