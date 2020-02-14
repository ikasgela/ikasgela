<?php

namespace App\Console\Commands;

use App\Gitea\GiteaClient;
use GitLab;
use Illuminate\Console\Command;

class CopiaGitLabGitea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitlab:dump-repos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Volcado de repositorios GitLab a Gitea';

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

        $last = GitLab::projects()->all([
            'order_by' => 'id',
            'sort' => 'desc',
            'per_page' => 1
        ])[0]['id'];

        $first = GitLab::projects()->all([
            'order_by' => 'id',
            'sort' => 'asc',
            'per_page' => 1
        ])[0]['id'];

        $this->line('');
        $this->info('Copiando: ' . $first . ' -> ' . $last);

        $total = 0;
        $error = 0;
        for ($i = $first; $i <= $last; $i++) {
            try {
                $project = GitLab::projects()->show($i);
                $user = GitLab::users()->show($project['owner']['id']);
                $resultado = GiteaClient::dump_gitlab($project, $user['username'], $project['path']);
                echo '.';
                if ($resultado)
                    $total++;
            } catch (\Exception $e) {
                echo 'E';
                $error++;
            }
        }

        $this->line('');
        $this->warn('Copiados: ' . $total);
        $this->warn('Error: ' . $error);

        $this->info('Fin: ' . now());
    }
}
