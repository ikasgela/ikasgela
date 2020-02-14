<?php

namespace App\Traits;

use App\Gitea\GiteaClient;

trait ClonarRepoGitea
{
    public $test_gitlab = false;

    public function clonar_repositorio($repositorio, $username, $destino)
    {
        $n = 2;

        if (empty($username))
            $username = 'root';

        $ruta = $destino;
        $ruta_temp = $ruta;

        $reintentos = 3;

        do {
            $error = GiteaClient::clone($repositorio['path_with_namespace'], $username, $ruta);

            if ($error == 409) { //&& Str::contains($error_message, 'has already been taken')) {
                $ruta = $ruta_temp . "-$n";
                $n += 1;
            } else {
                $reintentos--;
            }

        } while ($error == 409 && $reintentos > 0);

        return $error;
    }
}
