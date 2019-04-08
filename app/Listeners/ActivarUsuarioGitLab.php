<?php

namespace App\Listeners;

use App\Actividad;
use Exception;
use GitLab;
use Illuminate\Auth\Events\Verified;

class ActivarUsuarioGitLab
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param Verified $event
     * @return void
     */
    public function handle(Verified $event)
    {
        try {

            $desactivados = GitLab::users()->all([
                'search' => $event->user['email']
            ]);

            foreach ($desactivados as $usuario) {
                GitLab::users()->unblock($usuario['id']);
            }

            $actividad = Actividad::where('nombre', 'Tarea de bienvenida')->first();
            $clon = $actividad->duplicate();
            $clon->plantilla = false;
            $clon->save();
            $event->user->actividades()->attach($clon, ['puntuacion' => $actividad->puntuacion]);

            activity()
                ->causedBy($event->user)
                ->log('Usuario verificado');

        } catch (Exception $e) {
        }
    }
}
