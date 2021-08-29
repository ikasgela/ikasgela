<?php

namespace App\Listeners;

use App\Gitea\GiteaClient;
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
