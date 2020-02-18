<?php

namespace App\Console\Commands;

use App\Gitea\GiteaClient;
use GitLab;
use Illuminate\Console\Command;

class GiteaDumpGitLabUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitea:dump-gitlab-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Volcado de usuarios de GitLab a Gitea';

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

        $page = 1;
        $users = GitLab::users()->all([
            'per_page' => 100,
            'page' => $page
        ]);
        $page++;

        while ($users != null) {

            $total = 0;
            foreach ($users as $user) {
                echo '.';
                $creado = GiteaClient::user($user['email'], $user['username'], $user['name']);
                if ($creado)
                    $total++;
            }

            $users = GitLab::users()->all([
                'per_page' => 100,
                'page' => $page
            ]);
            $page++;
        }

        $this->line('');
        $this->warn('Nuevos: ' . $total);

        $this->info('Fin: ' . now());
    }
}
