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

    public function gitlab()
    {
        if ($this->host == 'gitlab') {
            try {
                $key = $this->cacheKey();

                if (!$this->isForked()) {
                    return Cache::remember($key, now()->addDays(1), function () {
                        return GitLab::projects()->show($this->repositorio);
                    });
                } else {
                    return Cache::remember($key, now()->addDays(1), function () {
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
        } else {
            if (!$this->isForked()) {
                return GiteaClient::repo($this->repositorio);
            } else {
                return GiteaClient::repo($this->pivot->fork);
            }

        }
    }

    public function isForked()
    {
        return isset($this->pivot->fork) && strlen($this->pivot->fork) > 2;
    }

    public function isArchivado()
    {
        return $this->pivot->archivado;
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
