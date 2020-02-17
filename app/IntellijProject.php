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
                'fork_status',
            ]);
    }

    public function repository()
    {
        switch ($this->host) {
            case 'gitlab':
                try {
                    return Cache::remember($this->cacheKey(), now()->addDays(config('ikasgela.repo_cache_days')), function () {
                        if (!$this->isForked()) {
                            return GitLab::projects()->show($this->repositorio);
                        } else {
                            return GitLab::projects()->show($this->pivot->fork);
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
            case 'gitea':
                try {
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
        switch ($this->host) {
            case 'gitlab':
                $repository = $this->repository();

                if ($archived)
                    GitLab::projects()->archive($repository['id']);
                else
                    GitLab::projects()->unarchive($repository['id']);

                $this->pivot->archivado = $archived;
                $this->pivot->save();

                Cache::forget($this->cacheKey());
                break;
            case 'gitea':
                $repository = $this->repository();

                GiteaClient::block_repo($repository['owner'], $repository['name'], $archived);

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
        return $this->pivot->fork_status == 1;
    }

    public function getForkStatus()
    {
        return $this->pivot->fork_status;
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
}
