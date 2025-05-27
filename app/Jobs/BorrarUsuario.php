<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BorrarUsuario implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected User $user)
    {
        $this->onQueue('low');
    }

    public function handle()
    {
        $user = $this->user;

        Log::debug('Iniciando borrado de usuario...', [
            'user' => $user->username
        ]);

        // Borrar el usuario de Gitea
        if (config('ikasgela.gitea_enabled')) {
            try {
                GiteaClient::borrar_usuario($user->username);
            } catch (Exception $e) {
                Log::error('Gitea: Error al borrar el usuario.', [
                    'username' => $user->username,
                    'exception' => $e->getMessage()
                ]);
            }
        }

        DB::table('settings')
            ->where('user_id', '=', $user->id)
            ->delete();

        // Recorrer las actividades y borrarlas
        foreach ($user->actividades()->get() as $actividad) {
            $actividad->forceDelete();
        }

        foreach ($user->files as $file) {
            $file->delete();
        }

        $user->delete();

        // Borrar la caché
        Cache::flush();

        Log::debug('Usuario borrado.', [
            'user' => $user->username
        ]);
    }
}
