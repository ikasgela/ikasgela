<?php

namespace App\Traits;

use Ikasgela\Gitea\GiteaClient;
use Illuminate\Support\Facades\Log;

trait ClonarRepoGitea
{
    public function clonar_repositorio($repositorio, $username, $destino, $descripcion = null)
    {
        $n = 2;

        if (empty($username))
            $username = 'root';

        $ruta = $destino;
        $ruta_temp = $ruta;

        $reintentos = 3;

        do {
            $result = GiteaClient::clone($repositorio, $username, $ruta, $descripcion);

            Log::debug('Repositorio clonado.', [
                'result' => $result,
                'repo' => $repositorio,
                'username' => $username,
            ]);

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
