<?php

namespace App\Jobs;

use App\Actividad;
use App\Gitea\GiteaClient;
use App\IntellijProject;
use App\Mail\RepositorioClonado;
use App\Mail\RepositorioClonadoError;
use App\Traits\ClonarRepoGitea;
use App\User;
use Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Log;
use Mail;

class ForkGiteaRepo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ClonarRepoGitea;

    protected $actividad;
    protected $intellij_project;
    protected $user;

    public $tries = 1000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Actividad $actividad, IntellijProject $intellij_project, User $user, $test_gitlab = false)
    {
        $this->actividad = $actividad;
        $this->intellij_project = $intellij_project;
        $this->user = $user;
        $this->test_gitlab = $test_gitlab;
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
            if (!$this->actividad->users()->where('username', $username)->exists())
                abort(403, __('Sorry, you are not authorized to access this page.'));

            try {
                $proyecto = GiteaClient::repo($this->intellij_project->repositorio);
            } catch (\Exception $e) {
                Log::critical($e);
                abort(404, __('Repository not found.'));
            }

            $fork = null;

            $ruta = $this->actividad->unidad->curso->slug
                . '-' . $this->actividad->unidad->slug
                . '-' . $this->actividad->slug
                . '-' . pathinfo(basename($this->intellij_project->repositorio), PATHINFO_EXTENSION);

            $fork = $this->clonar_repositorio($this->intellij_project->repositorio, $username, Str::slug($ruta));

            if ($fork) {
                $this->actividad->intellij_projects()
                    ->updateExistingPivot($this->intellij_project->id, ['fork' => $fork['path_with_namespace'], 'is_forking' => false]);

                $ij = $this->actividad->intellij_projects()->find($this->intellij_project->id);

                Cache::put($ij->cacheKey(), $fork, now()->addDays(1));

                //Mail::to($this->user->email)->send(new RepositorioClonado());

            } else {
                $this->actividad->intellij_projects()->updateExistingPivot($this->intellij_project->id, ['is_forking' => false]);

                //Mail::to($this->user->email)->send(new RepositorioClonadoError());
            }

        }, function () {
            // Could not obtain lock...
            return $this->release(5);
        });
    }
}
