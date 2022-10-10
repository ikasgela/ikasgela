<?php

namespace App\Jobs;

use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\User;
use App\Traits\ClonarRepoGitea;
use Cache;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Log;

class ForkGiteaRepo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ClonarRepoGitea;

    protected $actividad;
    protected $intellij_project;
    protected $user;
    protected $team_users;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Actividad $actividad, IntellijProject $intellij_project, User $user, $team_users = [])
    {
        $this->onQueue('high');

        $this->actividad = $actividad;
        $this->intellij_project = $intellij_project;
        $this->user = $user;
        $this->team_users = $team_users;
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

                    Cache::tags([$ij->templateCacheKey()])->flush();
                    Cache::put($ij->cacheKey(), $fork, now()->addDays(config('ikasgela.repo_cache_days')));

                    //Mail::to($this->user->email)->send(new RepositorioClonado());

                } else {
                    $ij->setForkStatus(3);  // Error

                    //Mail::to($this->user->email)->send(new RepositorioClonadoError());
                }
            }
        }, function () {
            // Could not obtain lock...
            return $this->release(5);
        });
    }
}
