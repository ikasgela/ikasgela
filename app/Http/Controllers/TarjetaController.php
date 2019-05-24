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
            $repositorio = '/apuntes';
            $rama = 'master';
            $archivo = '/subcarpeta/prueba.md';
            $servidor = 'http://gitlab.ikasgela.test/';

            $proyecto = GitLab::projects()->show($username . $repositorio);

            $readme = GitLab::repositoryfiles()->getRawFile($proyecto['id'], $archivo, $rama);

            // Imagen
            $readme = preg_replace('/(!\[.*\]\((?!http))/', '${1}' . $servidor
                . $username
                . $repositorio
                . "/raw/$rama//"
                , $readme);

            // Link
            $readme = preg_replace('/(\s+\[.*\]\((?!http))/', '${1}' . $servidor
                . $username
                . $repositorio
                . "/blob/$rama//"
                , $readme);

        } catch (\Exception $e) {
            $readme = "# Error\n\nRepositorio inexistente.";
        }

        return view('tarjetas.texto_markdown', compact(['readme']));
    }
}
