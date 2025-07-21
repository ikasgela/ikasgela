<?php

namespace App\Models;

use App\Traits\ClonarRepoGitea;
use Bkwld\Cloner\Cloneable;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;

/**
 * @mixin IdeHelperIntellijProject
 */
class IntellijProject extends Model
{
    use HasFactory;
    use Cloneable;
    use ClonarRepoGitea;

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

    public function repository_no_cache()
    {
        return $this->repository(true);
    }

    public function repository($no_cache = false)
    {
        switch ($this->host) {
            case 'gitea':
                try {
                    if ($no_cache)
                        return GiteaClient::repo($this->repositorio);
                    else {
                        if (!$this->isForked()) {
                            return Cache::remember($this->templateCacheKey(), now()->addDays(config('ikasgela.repo_cache_days')), function () {
                                Log::debug("IntellijProject - repository() - Repositorio plantilla en caché: ", ['key' => $this->templateCacheKey(), 'repo' => $this->repositorio]);
                                return GiteaClient::repo($this->repositorio);
                            });
                        } else {
                            return Cache::remember($this->cacheKey(), now()->addDays(config('ikasgela.repo_cache_days')), function () {
                                Log::debug("IntellijProject - repository() - Repositorio fork en caché: ", ['key' => $this->cacheKey(), 'repo' => $this->pivot->fork]);
                                return GiteaClient::repo($this->pivot->fork);
                            });
                        }
                    }
                } catch (Exception $e) {
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
            'owner' => '?',
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
        return $this->pivot?->archivado;
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

    public function cacheKey(): string
    {
        if (isset($this->pivot))
            $key = $this->host . '_' . $this->pivot->intellij_project_id . '_' . $this->pivot->actividad_id;
        else
            $key = $this->templateCacheKey();

        return $key;
    }

    public function templateCacheKey(): string
    {
        return $this->host . '_' . $this->id;
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
            return "gitkraken://repolink/$sha?url=" . $repository['http_url_to_repo'];
        }

        return null;
    }

    public function intellij_idea_deep_link()
    {
        $repository = $this->repository();

        if ($repository['id'] != '?') {
            if ($this->isSafeExamOnMac())
                return str_replace('https://', "https://" . Auth::user()->username . "@", $repository['http_url_to_repo']);
            else
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

    public function pivote(?Actividad $actividad)
    {
        return $actividad?->intellij_projects()->find($this->id)->pivot ?? null;
    }

    public function isSafeExamOnMac(): bool
    {
        return Agent::platform() == 'OS X' && Str::contains(Agent::getUserAgent(), "SEB/ikasgela");
    }

    public function duplicar(?Curso $curso_destino)
    {
        $clon = $this->duplicate();
        if (is_null($curso_destino)) {
            $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        } else {
            $clon->curso_id = $curso_destino->id;
        }
        $clon->save();

        // Si copiamos a otro curso, Duplicar el repositorio
        if (!is_null($curso_destino)) {
            $proyecto = GiteaClient::repo($this->repositorio);
            $usuario = $curso_destino->gitea_organization;
            $ruta = $proyecto['name'];
            $nombre = $proyecto['description'];

            // Verificar que sea plantilla, si no, convertirlo
            if (!$proyecto['template']) {
                GiteaClient::template($proyecto['owner'], $proyecto['name'], true);
            }

            $clonado = $this->clonar_repositorio($proyecto['path_with_namespace'], $usuario, $ruta, $nombre);

            $clon->repositorio = $clonado['path_with_namespace'];

            $clon->save();
        }

        return $clon;
    }
}
