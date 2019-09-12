<?php

namespace App;

use GitLab;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;

class MarkdownText extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'repositorio', 'rama', 'archivo'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function markdown()
    {
        try {
            $repositorio = $this->repositorio;
            $rama = isset($this->rama) ? $this->rama : 'master';
            $archivo = $this->archivo;
            $servidor = config('app.debug') ? 'https://gitlab.ikasgela.test/' : 'https://gitlab.ikasgela.com/';

            $proyecto = GitLab::projects()->show($repositorio);

            $texto = GitLab::repositoryfiles()->getRawFile($proyecto['id'], $archivo, $rama);

            // Imagen
            $texto = preg_replace('/(!\[.*\]\((?!http))/', '${1}' . $servidor
                . $repositorio
                . "/raw/$rama/"
                , $texto);

            // Link
            $texto = preg_replace('/(\s+\[.*\]\((?!http))/', '${1}' . $servidor
                . $repositorio
                . "/blob/$rama/"
                , $texto);

            // Convertir el Markdown a HTML
            $texto = Markdown::convertToHtml($texto);

            // Añadir target="_blank" a los enlaces
            $texto = preg_replace('/(<a href="[^"]+")>/is', '\\1 target="_blank">', $texto);

        } catch (\Exception $e) {
            $texto = "# " . __('Error') . "\n\n" . __('Repository not found.');
        }

        return $texto;
    }
}
