<?php

namespace App\Console\Commands;

use App\Gitea\GiteaClient;
use GitLab;
use Illuminate\Console\Command;

class GiteaDumpGitLabRepos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitea:dump-gitlab-repos';

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
        if (config('app.env') == 'production') {
            $this->alert('App en producción');
            if ($this->confirm('¿Continuar?')) {

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
                        if (isset($project['owner'])) {
                            $user = GitLab::users()->show($project['owner']['id']);
                            $nombre = $project['path'];
                            $resultado = GiteaClient::dump_gitlab($project, $user['username'], $nombre);
                        } else {
                            $nombre = str_replace('/', '.', $project['path_with_namespace']);
                            $resultado = GiteaClient::dump_gitlab($project, 'root', $nombre);
                        }
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
    }
}
