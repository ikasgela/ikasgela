<?php

namespace App;

use App\Gitea\GiteaClient;
use Cache;
use GitLab;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;

class MarkdownText extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'repositorio', 'rama', 'archivo',
        '__import_id', 'curso_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function markdown()
    {
        $key = $this->repositorio . '/' . $this->archivo;

        return Cache::remember($key, now()->addDays(config('ikasgela.markdown_cache_days')), function () {

            try {
                $repositorio = $this->repositorio;
                $rama = isset($this->rama) ? $this->rama : 'master';
                $archivo = $this->archivo;

                if (config('ikasgela.gitlab_enabled')) {
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

                } elseif (config('ikasgela.gitea_enabled')) {
                    $servidor = config('app.debug') ? 'https://gitea.ikasgela.test/' : 'https://gitea.ikasgela.com/';

                    $proyecto = GiteaClient::repo($repositorio);
                    $texto = GiteaClient::file($proyecto['owner'], $proyecto['name'], $archivo, $rama);

                    // Link
                    $texto = preg_replace('/(\s+\[.*\]\((?!http))/', '${1}' . $servidor
                        . $repositorio
                        . "/src/branch/$rama/"
                        , $texto);
                }

                // Convertir el Markdown a HTML
                $texto = Markdown::convertToHtml($texto);

                // Añadir target="_blank" a los enlaces
                $texto = preg_replace('/(<a href="[^"]+")>/is', '\\1 target="_blank">', $texto);

            } catch (\Exception $e) {
                $texto = "# " . __('Error') . "\n\n" . __('Repository not found.');
            }

            return $texto;
        });
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
