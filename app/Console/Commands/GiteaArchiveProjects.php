<?php

namespace App\Console\Commands;

use App\Models\Actividad;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GiteaArchiveProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitea:archive-intellij-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archivar todos los proyectos de Gitea';

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

                $actividades = Actividad::all();

                $total = 0;
                foreach ($actividades as $actividad) {
                    $proyectos = $actividad->intellij_projects()->get();
                    foreach ($proyectos as $proyecto) {
                        if ($proyecto->isForked() && $proyecto->isArchivado()) {
                            try {
                                $proyecto->archive();
                                echo '.';
                                $total++;
                            } catch (Exception $e) {
                                echo 'E';
                            }
                        }

                        Cache::forget($proyecto->cacheKey());
                    }
                }
                $this->line('');
                $this->warn('Total: ' . $total);

                $this->info('Fin: ' . now());
            }
        }
    }
}
