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
    protected $signature = 'gitlab:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Volcado de GitLab a Gitea';

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
        $borrados = GiteaClient::borrar();

        $this->info('');
        $this->info('Borrados: ' . $borrados);

        $users = GitLab::users()->all();

        $total = 0;
        foreach ($users as $user) {
            $projects = GitLab::users()->usersProjects($user['id']);
            foreach ($projects as $project) {
                echo '.';
                $resultado = GiteaClient::dump_gitlab($project['path_with_namespace'], $user['username'], $project['path']);
                $total++;
            }
        }
        $this->info('');
        $this->info('Copiados: ' . $total);
    }
}
