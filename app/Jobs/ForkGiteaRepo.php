<?php

namespace App\Jobs;

use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\User;
use App\Traits\ClonarRepoGitea;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ForkGiteaRepo implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ClonarRepoGitea;

    protected $actividad;
    protected $intellij_project;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Actividad $actividad, IntellijProject $intellij_project, User $user, protected $team_users = [])
    {
        $this->onQueue('high');

        $this->actividad = $actividad;
        $this->intellij_project = $intellij_project;
        $this->user = $user;
    }

    public $uniqueFor = 30;

    public function uniqueId(): string
    {
        return $this->user->id . '-' . $this->actividad->id . '-' . $this->intellij_project->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::throttle('fork')->allow(100)->every(60)->then(function () {

            $username = $this->user->username;// Si la actividad no está asociada a este usuario, no hacer el fork

            $ij = $this->actividad->intellij_projects()->find($this->intellij_project->id);

            if (!$this->actividad->users()->where('username', $username)->exists()) {
                $ij->setForkStatus(3);  // Error
                Log::critical('Intento de clonar un repositorio de otro usuario.', [
                    'repo' => $this->intellij_project->repositorio,
                    'username' => $this->user->username,
                ]);
            } else {
                $fork = null;

                $ruta = $this->actividad->unidad->curso->slug
                    . '-' . $this->actividad->unidad->slug
                    . '-' . $this->actividad->slug;

                $ruta .= '-' . bin2hex(openssl_random_pseudo_bytes(3));

                $proyecto = GiteaClient::repo($this->intellij_project->repositorio);

                if (empty($this->intellij_project->descripcion)) {
                    $descripcion = $proyecto['description'];
                } else {
                    $descripcion = $this->intellij_project->descripcion;
                }

                if (!$proyecto['template']) {
                    GiteaClient::template($proyecto['owner'], $proyecto['name'], true);
                }

                $fork = $this->clonar_repositorio($this->intellij_project->repositorio, $username, Str::slug($ruta), $descripcion);

                if (!is_null($fork) && isset($fork['id'])) {
                    $ij->setForkStatus(2, $fork['path_with_namespace']);  // Ok

                    foreach ($this->team_users as $colaborador) {
                        GiteaClient::add_collaborator($fork['owner'], $fork['name'], $colaborador->username);
                    }

                    Cache::put($ij->cacheKey(), $fork, now()->addDays(config('ikasgela.repo_cache_days')));
                    Log::debug("Repositorio en caché después del fork: ", ['key' => $ij->cacheKey(), 'repo' => $fork['path_with_namespace']]);

                    //Mail::to($this->user->email)->send(new RepositorioClonado());

                } else {
                    $ij->setForkStatus(3);  // Error

                    //Mail::to($this->user->email)->send(new RepositorioClonadoError());
                }
            }
        }, fn() => // Could not obtain lock...
        $this->release(5));
    }
}
