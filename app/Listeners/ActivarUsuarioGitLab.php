<?php

namespace App\Listeners;

use App\Actividad;
use App\Curso;
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
        // Activar el usuario de GitLab
        $desactivados = GitLab::users()->all([
            'search' => $event->user['email']
        ]);

        foreach ($desactivados as $usuario) {
            GitLab::users()->unblock($usuario['id']);
        }

        // Asociamos al usuario el curso más nuevo que haya en la organización
        // TODO: Cuando dispongamos de portada, eliminar esto y matricular al usuario en un curso de ejemplo
        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', organizacion());
        })
            ->latest()->first();

        $event->user->cursos()->attach($curso);
        setting_usuario(['curso_actual' => $curso->id], $event->user);

        // Asignar la tarea de bienvenida
        $actividad = Actividad::whereHas('unidad.curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', organizacion());
        })
            ->where('nombre', 'Tarea de bienvenida')
            ->where('plantilla', true)
            ->first();

        $clon = $actividad->duplicate();
        $clon->save();
        $event->user->actividades()->attach($clon, ['puntuacion' => $actividad->puntuacion]);

        // Log
        activity()
            ->causedBy($event->user)
            ->log('Usuario verificado');
    }
}
