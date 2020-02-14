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

        $page = 1;
        $users = GitLab::users()->all([
            'per_page' => 100,
            'page' => $page
        ]);
        $page++;

        while ($users != null) {

            $total = 0;
            foreach ($users as $user) {
                $projects = GitLab::users()->usersProjects($user['id'], [
                    'per_page' => 100,
                ]);
                foreach ($projects as $project) {
                    echo '.';
                    $resultado = GiteaClient::dump_gitlab($project['path_with_namespace'], $user['username'], $project['path']);
                    $total++;
                }
            }

            $users = GitLab::users()->all([
                'per_page' => 100,
                'page' => $page
            ]);
            $page++;
        }

        $this->line('');
        $this->warn('Copiados: ' . $total);

        $this->info('Fin: ' . now());
    }
}
