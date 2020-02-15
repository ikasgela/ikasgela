<?php

namespace App;

use App\Gitea\GiteaClient;
use Cache;
use GitLab;
use Illuminate\Database\Eloquent\Model;
use Log;

class IntellijProject extends Model
{
    protected $fillable = [
        'repositorio', 'titulo', 'descripcion', 'host'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps()
            ->withPivot([
                'fork',
                'archivado',
                'is_forking'
            ]);
    }

    public function repository()
    {
        switch ($this->host) {
            case 'gitlab':
                try {
                    $key = $this->cacheKey();

                    if (!$this->isForked()) {
                        return Cache::remember($key, now()->addDays(config('ikasgela.repo_cache_days')), function () {
                            return GitLab::projects()->show($this->repositorio);
                        });
                    } else {
                        return Cache::remember($key, now()->addDays(config('ikasgela.repo_cache_days')), function () {
                            return GitLab::projects()->show($this->pivot->fork);
                        });
                    }
                } catch (\Exception $e) {
                    Log::critical($e);
                    $fake = [
                        'id' => '?',
                        'name' => '?',
                        'description' => '?',
                        'http_url_to_repo' => '',
                        'path_with_namespace' => $this->repositorio
                    ];
                    return $fake;
                }
                break;
            case 'gitea':
                $key = $this->cacheKey();

                if (!$this->isForked()) {
                    return Cache::remember($key, now()->addDays(config('ikasgela.repo_cache_days')), function () {
                        return GiteaClient::repo($this->repositorio);
                    });
                } else {
                    return Cache::remember($key, now()->addDays(config('ikasgela.repo_cache_days')), function () {
                        return GiteaClient::repo($this->pivot->fork);
                    });
                }
                break;
            default:
                Log::error('Tipo de host de repositorios desconocido.', [
                    'host' => $this->host
                ]);
        }
        return null;
    }

    public function isForked()
    {
        return isset($this->pivot->fork) && strlen($this->pivot->fork) > 2;
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
        $repository = $this->repository();

        if ($archived)
            GitLab::projects()->archive($repository['id']);
        else
            GitLab::projects()->unarchive($repository['id']);

        $this->pivot->archivado = $archived;
        $this->pivot->save();

        Cache::forget($this->cacheKey());
    }

    public function isForking()
    {
        return $this->pivot->is_forking;
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
}
