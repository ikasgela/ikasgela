<?php

namespace App\Console\Commands;

use App\Gitea\GiteaClient;
use GitLab;
use Illuminate\Console\Command;

class GitLabDumpUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitlab:dump-users';

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
        $users = GitLab::users()->all();

        $total = 0;
        foreach ($users as $user) {
            echo '.';
            GiteaClient::user($user['email'], $user['username'], $user['name']);
            $total++;
        }
        $this->info('');
        $this->info('Copiados: ' . $total);
    }
}
