<?php

namespace App\Http\Controllers;

use GitLab;
use Illuminate\Support\Facades\Auth;

class TarjetaController extends Controller
{
    public function texto_markdown()
    {
        try {
            $username = Auth::user()->username;
            $proyecto = GitLab::projects()->show($username . '/prog-2019-ud6-colecciones-ejercicio-11');
            $readme = GitLab::repositoryfiles()->getRawFile($proyecto['id'], "README.md", 'master');
            $readme = preg_replace('/(!\[.*\]\()/', '${1}' . 'http://gitlab.ikasgela.test:8989/'
                . $username
                . '/prog-2019-ud6-colecciones-ejercicio-11'
                . '/raw/master/'
                , $readme);
            $readme = preg_replace('/(\s+\[.*\]\()/', '${1}' . 'http://gitlab.ikasgela.test:8989/'
                . $username
                . '/prog-2019-ud6-colecciones-ejercicio-11'
                . '/blob/master/'
                , $readme);
        } catch (\Exception $e) {
            $readme = "# Error\n\nRepositorio inexistente.";
        }

        return view('tarjetas.texto_markdown', compact(['readme']));
    }
}
