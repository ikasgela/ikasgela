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
            $ruta = '/apuntes';
            $proyecto = GitLab::projects()->show($username . $ruta);
//            $readme = GitLab::repositoryfiles()->getRawFile($proyecto['id'], "/README.md", 'master');
            $readme = GitLab::repositoryfiles()->getRawFile($proyecto['id'], "/subcarpeta/prueba.md", 'master');
            $readme = preg_replace('/(!\[.*\]\((?!http))/', '${1}' . 'http://gitlab.ikasgela.test/'
                . $username
                . $ruta
                . '/raw/master/'
                , $readme);
            $readme = preg_replace('/(\s+\[.*\]\((?!http))/', '${1}' . 'http://gitlab.ikasgela.test/'
                . $username
                . $ruta
                . '/blob/master/'
                , $readme);
        } catch (\Exception $e) {
            $readme = "# Error\n\nRepositorio inexistente.";
        }

        return view('tarjetas.texto_markdown', compact(['readme']));
    }
}
