<?php

namespace App\Traits;

use GitLab;
use Illuminate\Support\Str;
use Log;

trait ClonarRepoGitLab
{
    public $test_gitlab = false;

    public function clonar_repositorio($origen, $destino, $ruta, $nombre = null)
    {
        $fork = null;
        $error_code = 0;

        $n = 2;

        $namespace = trim($destino, '/');
        if (empty($namespace))
            $namespace = 'root';

        if (empty($nombre))
            $nombre = $origen['name'];

        if (empty($ruta))
            $ruta = Str::slug($nombre);

        if ($this->test_gitlab) {
            $nombre = Str::uuid();
            $ruta = Str::slug($nombre);
        }

        $ruta_temp = $ruta;
        $nombre_temp = $nombre;
        $reintentos = 3;

        do {

            try {
                // Hacer el fork
                $fork = GitLab::projects()->fork($origen['id'], [
                    'namespace' => $namespace,
                    'name' => $nombre,
                    'path' => Str::slug($ruta)
                ]);

                // Desconectarlo del repositorio original
                GitLab::projects()->removeForkRelation($fork['id']);

                // Convertirlo en privado
                GitLab::projects()->update($fork['id'], [
                    'visibility' => 'private'
                ]);

            } catch (\RuntimeException $e) {
                $error_code = $e->getCode();
                $error_message = $e->getMessage();

                if ($error_code == 409 && Str::contains($error_message, 'has already been taken')) {
                    $ruta = $ruta_temp . "-$n";
                    $nombre = $nombre_temp . " - $n";
                    $n += 1;
                } else {
                    $reintentos--;
                    Log::error($e);
                }
            }

        } while ($fork == null && $error_code == 409 && $reintentos > 0);

        return $fork ?: false;
    }
}
