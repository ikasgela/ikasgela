<?php

namespace App\Models;

use Cache;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Log;

/**
 * @mixin IdeHelperIntellijProject
 */
class IntellijProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'repositorio', 'titulo', 'descripcion', 'host',
        '__import_id', 'curso_id', 'open_with',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps()
            ->withPivot([
                'fork',
                'archivado',
                'fork_status',
            ]);
    }

    public function template()
    {
        return $this->repository(true);
    }

    public function repository($template = false)
    {
        switch ($this->host) {
            case 'gitea':
                try {
                    if ($template)
                        return GiteaClient::repo($this->repositorio);
                    else
                        return Cache::remember($this->cacheKey(), now()->addDays(config('ikasgela.repo_cache_days')), function () {
                            if (!$this->isForked()) {
                                return GiteaClient::repo($this->repositorio);
                            } else {
                                return GiteaClient::repo($this->pivot->fork);
                            }
                        });
                } catch (\Exception $e) {
                    Log::error('Error al recuperar un repositorio.', [
                        'host' => $this->host,
                        'repository' => $this->repositorio,
                        'exception' => $e->getMessage(),
                    ]);
                }
                break;
            default:
                Log::error('Tipo de host de repositorios desconocido.', [
                    'host' => $this->host,
                    'repository' => $this->repositorio,
                ]);
        }
        return $this->fakeRepository();
    }

    public function fakeRepository()
    {
        return [
            'id' => '?',
            'name' => '?',
            'description' => '?',
            'http_url_to_repo' => '',
            'path_with_namespace' => $this->repositorio
        ];
    }

    public function isForked()
    {
        return $this->getForkStatus() == 2;
    }

    public function isArchivado()
    {
        return $this->pivot->archivado;
    }

    public function archive()
    {
        $this->updateArchiveStatus(true);
    }

    public function unarchive()
    {
        $this->updateArchiveStatus(false);
    }

    private function updateArchiveStatus($archived = true)
    {
        switch ($this->host) {
            case 'gitea':
                $repository = $this->repository();

                if ($repository['id'] != '?') {
                    GiteaClient::block_repo($repository['owner'], $repository['name'], $archived);
                }

                $this->pivot->archivado = $archived;
                $this->pivot->save();

                Cache::forget($this->cacheKey());
                break;
            default:
                Log::error('Tipo de host de repositorios desconocido.', [
                    'host' => $this->host,
                    'repository' => $this->repositorio,
                ]);
        }
    }

    public function isForking()
    {
        return $this->getForkStatus() == 1;
    }

    public function getForkStatus()
    {
        if (isset($this->pivot))
            return $this->pivot->fork_status;
        else
            return 0;
    }

    public function setForkStatus($fork_status, $fork = null)
    {
        $this->pivot->fork_status = $fork_status;
        if (!is_null($fork))
            $this->pivot->fork = $fork;
        $this->pivot->save();
    }

    /**
     * @return string
     */
    public function cacheKey(): string
    {
        if (isset($this->pivot))
            $key = $this->host . '_' . $this->pivot->intellij_project_id . '_' . $this->pivot->actividad_id;
        else
            $key = $this->host . '_' . $this->id;

        return $key;
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function gitkraken_deep_link()
    {
        $repository = $this->repository();

        if ($repository['id'] != '?') {
            $sha = GiteaClient::repo_first_sha($repository['owner'], $repository['name']);
            return "gitkraken://repolink/$sha?url=" . str_replace('https://', "https://" . Auth::user()->username . "@", $repository['http_url_to_repo']);
        }

        return null;
    }

    public function intellij_idea_deep_link()
    {
        $repository = $this->repository();

        if ($repository['id'] != '?') {
            return "jetbrains://idea/checkout/git?checkout.repo=" . str_replace('https://', "https://" . Auth::user()->username . "@", $repository['http_url_to_repo']) . "&idea.required.plugins.id=Git4Idea";
        }

        return null;
    }

    public function phpstorm_deep_link()
    {
        $repository = $this->repository();

        if ($repository['id'] != '?') {
            return "jetbrains://php-storm/checkout/git?checkout.repo=" . str_replace('https://', "https://" . Auth::user()->username . "@", $repository['http_url_to_repo']) . "&idea.required.plugins.id=Git4Idea";
        }

        return null;
    }

    public function datagrip_deep_link()
    {
        $repository = $this->repository();

        if ($repository['id'] != '?') {
            return "jetbrains://dbe/checkout/git?checkout.repo=" . str_replace('https://', "https://" . Auth::user()->username . "@", $repository['http_url_to_repo']) . "&idea.required.plugins.id=Git4Idea";
        }

        return null;
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->intellij_projects()->find($this->id)->pivot;
    }
}
