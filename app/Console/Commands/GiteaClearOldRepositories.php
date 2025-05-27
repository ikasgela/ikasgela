<?php

namespace App\Console\Commands;

use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GiteaClearOldRepositories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitea:clear-old-repositories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar repositorios antiguos';

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

                $result = DB::table('actividad_intellij_project')->where('fork_status', '>', 0)->get();

                $total = 0;

                foreach ($result->values() as $proyecto) {

                    $repo_id = null;
                    try {
                        $repo_id = GiteaClient::repo($proyecto->fork)['id'];
                    } catch (Exception) {
                    }

                    if ($repo_id == null) {
                        $total++;
                        $this->info("Repositorio huerfano: " . $proyecto->fork);
                        DB::table('actividad_intellij_project')->where('id', '=', $proyecto->id)->delete();
                    }
                }

                $this->line('');
                $this->warn('Total: ' . $total);

                $this->info('Fin: ' . now());
            }
        }
    }
}
