<?php

namespace App\Jobs;

use App\Actividad;
use App\IntellijProject;
use App\Mail\RepositorioClonado;
use App\User;
use Cache;
use GitLab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Log;
use Mail;

class ForkGitLabRepo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $actividad;
    protected $intellij_project;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Actividad $actividad, IntellijProject $intellij_project, User $user)
    {
        $this->actividad = $actividad;
        $this->intellij_project = $intellij_project;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = $this->user->username;

        // Si la actividad no está asociada a este usuario, no hacer el fork
        if (!$this->actividad->users()->where('username', $username)->exists())
            abort(403, __('Sorry, you are not authorized to access this page.'));

        $proyecto = GitLab::projects()->show($this->intellij_project->repositorio);

        $fork = null;
        if (isset($proyecto['path'])) {
            $ruta = $this->actividad->unidad->curso->slug
                . '-' . $this->actividad->unidad->slug
                . '-' . $this->actividad->slug
                . '-' . $proyecto['path'];

            //$this->actividad->intellij_projects()->updateExistingPivot($this->intellij_project->id, ['is_forking' => true]);

            $fork = $this->clonar_repositorio($proyecto, $username, $ruta);
        }

        if ($fork) {
            $this->actividad->intellij_projects()
                ->updateExistingPivot($this->intellij_project->id, ['fork' => $fork['path_with_namespace'], 'is_forking' => false]);

            $ij = $this->actividad->intellij_projects()->find($this->intellij_project->id);
            $key = 'gitlab_' . $ij->pivot->intellij_project_id . '_' . $ij->pivot->actividad_id;

            Cache::put($key, $fork, now()->addDays(1));

            Mail::to($this->user->email)->send(new RepositorioClonado());

        } else {
            $this->actividad->intellij_projects()->updateExistingPivot($this->intellij_project->id, ['is_forking' => false]);
        }
    }

    private function clonar_repositorio($origen, $destino, $ruta, $nombre = null)
    {
        try {

            $fork = null;
            $error_code = 0;

            $n = 2;

            $namespace = trim($destino, '/');
            if (empty($namespace))
                $namespace = 'root';

            if (empty($nombre))
                $nombre = $origen['name'];

            if (empty($ruta))
                $ruta = Str::slug($nombre);

            $ruta_temp = $ruta;
            $nombre_temp = $nombre;

            do {

                try {
                    // Hacer el fork
                    $fork = GitLab::projects()->fork($origen['id'], [
                        'namespace' => $namespace,
                        'name' => $nombre,
                        'path' => Str::slug($ruta)
                    ]);

                    // Desconectarlo del repositorio original
                    GitLab::projects()->removeForkRelation($fork['id']);

                    // Convertirlo en privado
                    GitLab::projects()->update($fork['id'], [
                        'visibility' => 'private'
                    ]);

                } catch (\RuntimeException $e) {
                    $error_code = $e->getCode();

                    if ($error_code == 409) {
                        $ruta = $ruta_temp . "-$n";
                        $nombre = $nombre_temp . " - $n";
                        $n += 1;
                    } else {
                        Log::error($e);
                    }
                }

            } while ($fork == null && $error_code == 409);

            return $fork;

        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

}
