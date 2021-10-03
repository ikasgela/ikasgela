<?php

namespace App\Models;

use Cache;
use GrahamCampbell\Markdown\Facades\Markdown;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkdownText extends Model
{
    use HasFactory;

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

                if (config('ikasgela.gitea_enabled')) {
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
