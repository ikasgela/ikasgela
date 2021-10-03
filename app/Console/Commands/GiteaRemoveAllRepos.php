<?php

namespace App\Console\Commands;

use Ikasgela\Gitea\GiteaClient;
use Illuminate\Console\Command;

class GiteaRemoveAllRepos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitea:remove-all-repos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra todos los repositorios de Gitea';

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

                $borrados = GiteaClient::borrar();

                $this->line('');
                $this->warn('Borrados: ' . $borrados);

                $this->info('Fin: ' . now());
            }
        }
    }
}
