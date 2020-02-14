<?php

namespace App\Console\Commands;

use App\Gitea\GiteaClient;
use GitLab;
use Illuminate\Console\Command;

class BorrarGitea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitlab:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra los repositorios de Gitea';

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
        $this->info('Inicio: ' . now());

        $borrados = GiteaClient::borrar();

        $this->line('');
        $this->warn('Borrados: ' . $borrados);

        $this->info('Fin: ' . now());
    }
}
