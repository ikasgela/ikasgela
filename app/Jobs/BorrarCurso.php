<?php

namespace App\Jobs;

use App\Models\Curso;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BorrarCurso implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(protected Curso $curso)
    {
        $this->onQueue('low');
    }

    public function handle()
    {
        $curso = $this->curso;

        Log::debug('Iniciando borrado de curso...', [
            'curso' => $curso->slug
        ]);

        foreach ($curso->actividades()->get() as $actividad) {
            DB::table('tareas')
                ->where('actividad_id', '=', $actividad->id)
                ->delete();

            DB::table('actividad_team')
                ->where('actividad_id', '=', $actividad->id)
                ->delete();

            $actividad->feedbacks()->delete();
        }

        DB::table('curso_user')
            ->where('curso_id', '=', $curso->id)
            ->delete();

        DB::table('settings')
            ->where('key', '=', 'curso_actual')
            ->where('value', '=', $curso->id)
            ->delete();

        $curso->intellij_projects()->delete();
        GiteaClient::borrar_organizacion($curso->slug);
        $curso->markdown_texts()->delete();
        $curso->youtube_videos()->delete();
        $curso->cuestionarios()->delete();

        foreach ($curso->file_resources_files as $file) {
            $file->delete();
        }
        $curso->file_resources()->delete();

        foreach ($curso->file_uploads_files as $file) {
            $file->delete();
        }
        $curso->file_uploads()->delete();

        $curso->link_collections_links()->delete();
        $curso->link_collections()->delete();

        Schema::disableForeignKeyConstraints();
        $curso->actividades()->forceDelete();
        Schema::enableForeignKeyConstraints();

        $curso->unidades()->delete();

        Schema::disableForeignKeyConstraints();
        foreach ($curso->qualifications()->get() as $qualification) {
            $qualification->skills()->sync([]);
        }
        $curso->qualifications()->delete();
        $curso->skills()->delete();
        Schema::enableForeignKeyConstraints();

        $curso->feedbacks()->delete();

        $curso->milestones()->delete();

        foreach ($curso->hilos()->get() as $hilo) {
            DB::table('messages')
                ->where('thread_id', '=', $hilo->id)
                ->delete();

            DB::table('participants')
                ->where('thread_id', '=', $hilo->id)
                ->delete();
        }

        $curso->hilos()->delete();

        $curso->groups()->detach();

        Schema::disableForeignKeyConstraints();
        $curso->delete();
        Schema::enableForeignKeyConstraints();

        // Borrar la caché
        Cache::flush();

        Log::debug('Curso borrado.', [
            'curso' => $curso->slug
        ]);
    }
}
