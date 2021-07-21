<?php

namespace App\Listeners;

use App\Models\Actividad;
use App\Models\Curso;
use App\Gitea\GiteaClient;
use App\Models\Organization;
use Illuminate\Auth\Events\Verified;

class ActivarUsuario
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
        // Activar el usuario de Gitea
        if (config('ikasgela.gitea_enabled')) {
            GiteaClient::unblock($event->user['email'], $event->user['username']);
            $nombre_completo = $event->user['name'] . ' ' . $event->user['surname'];
            GiteaClient::full_name($event->user['email'], $event->user['username'], $nombre_completo);
        }

        // Asociamos al usuario el curso más nuevo que haya en la organización
        // TODO: Cuando dispongamos de portada, eliminar esto y matricular al usuario en un curso de ejemplo
        $organization = Organization::where('slug', organizacion())->first();

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', organizacion());
        })->whereHas('category.period', function ($query) use ($organization) {
            $query->where('periods.id', $organization->current_period_id);
        })->latest()->first();

        $event->user->cursos()->attach($curso);
        setting_usuario(['curso_actual' => $curso->id], $event->user);

        // Asignar la tarea de bienvenida
        $actividad = Actividad::whereHas('unidad.curso', function ($query) use ($curso) {
            $query->where('id', $curso->id);
        })->where('slug', 'tarea-de-bienvenida')
            ->where('plantilla', true)
            ->first();

        if (isset($actividad)) {
            $clon = $actividad->duplicate();
            $clon->plantilla_id = $actividad->id;
            $clon->save();
            $event->user->actividades()->attach($clon, ['puntuacion' => $actividad->puntuacion]);
        }

        // Activar todas las notificaciones
        setting_usuario(['notificacion_mensaje_recibido' => true]);
        setting_usuario(['notificacion_feedback_recibido' => true]);
        setting_usuario(['notificacion_actividad_asignada' => true]);
        setting_usuario(['notificacion_tarea_enviada' => true]);

        // Log
        activity()
            ->causedBy($event->user)
            ->log('Usuario verificado');
    }
}
