<?php

namespace App\Traits;

use Ikasgela\Gitea\GiteaClient;

trait ClonarRepoGitea
{
    public function clonar_repositorio($repositorio, $username, $destino, $nombre = null)
    {
        $n = 2;

        if (empty($username))
            $username = 'root';

        $ruta = $destino;
        $ruta_temp = $ruta;

        $reintentos = 3;

        do {
            $result = GiteaClient::clone($repositorio, $username, $ruta, $nombre);

            if ($result == 409) { //&& Str::contains($error_message, 'has already been taken')) {
                $ruta = $ruta_temp . "-$n";
                $n += 1;
            } else {
                $reintentos--;
            }

        } while ($result == 409 && $reintentos > 0);

        return $result;
    }
}
