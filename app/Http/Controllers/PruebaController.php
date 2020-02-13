<?php

namespace App\Http\Controllers;

use App\Gitea\GiteaClient;
use GitLab;

class PruebaController extends Controller
{
    public function index()
    {
        GiteaClient::borrar();

        $users = GitLab::users()->all();

        foreach ($users as $user) {
            $projects = GitLab::users()->usersProjects($user['id']);
            $total = 0;
            foreach ($projects as $project) {
                $resultado = GiteaClient::dump_gitlab($project['path_with_namespace'], $user['username'], $project['path']);
                $total++;
            }
            dump($total);
        }
    }
}
