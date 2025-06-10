<?php

namespace App\Jobs;

use App\Models\Actividad;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportarUsuarioJob implements ShouldQueue
{
    use Queueable;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user->withRelationshipAutoloading();
    }

    public function handle(): void
    {
        // Crear el directorio temporal
        $directorio = '/' . Str::uuid() . '/';
        Storage::disk('temp')->makeDirectory($directorio);
        //$ruta = Storage::disk('temp')->path($directorio);

        // Obtener las actividades del usuario
        $actividades = $this->user->actividades()->get();

        // Recorrer las actividades
        $actividades->each(function (Actividad $actividad) use ($directorio) {
            $subdirectorio = Str::slug($actividad->full_name);
            Storage::disk('temp')->makeDirectory($directorio . '/' . $subdirectorio);


        });


        // Crear una carpeta para la actividad

        // Recorrer los recursos de la actividad

        // Crear un fichero index.html.temporal
        // Descargar y enlazar los recursos
        // Concatenar la cabecera y el pie al indice principal para crear el index.html
        // Borrar el index.html temporal

        // Crear un fichero index.html.temporal
        // Recorrer el directorio y crear el indice principal
        // Concatenar la cabecera y el pie al indice principal para crear el index.html
        // Borrar el index.html temporal

        // Copiar el bootstrap.min a la carpeta

        // Comprimir la carpeta

        // Subirla a S3

        // Borrar el zip
        // Borrar la carpeta

        // Enviar el email con el enlace al fichero de S3

    }
}

/*        // Curso
        $this->exportarFicheroJSON($ruta, 'curso.json', $curso);

        // Unidad
        $this->exportarFicheroJSON($ruta, 'unidades.json', $curso->unidades()->orderBy('orden')->get());

        // Actividad
        $this->exportarFicheroJSON($ruta, 'actividades.json', $curso->actividades()->plantilla()->orderBy('orden')->get());

        // Qualification
        $this->exportarFicheroJSON($ruta, 'qualifications.json', $curso->qualifications()->plantilla()->get());

        // Skill
        $this->exportarFicheroJSON($ruta, 'skills.json', $curso->skills);

        // Qualification "*" -- "*" Skill
        $datos = DB::table('qualification_skill')
            ->join('qualifications', 'qualification_skill.qualification_id', '=', 'qualifications.id')
            ->where('qualifications.curso_id', '=', $curso->id)
            ->where('qualifications.template', '=', true)
            ->select("qualification_skill.*")
            ->orderBy('orden')
            ->get();
        $this->exportarFicheroJSON($ruta, 'qualification_skill.json', $datos);

        // IntellijProject
        $this->exportarFicheroJSON($ruta, 'intellij_projects.json', $curso->intellij_projects);
        $this->exportarRepositorios($ruta, $curso);

        // Actividad "*" -- "*" IntellijProject
        $this->exportarRelacionJSON($ruta, $curso, 'intellij_project');

        // MarkdownText
        $this->exportarFicheroJSON($ruta, 'markdown_texts.json', $curso->markdown_texts);
        $this->exportarMarkdown($ruta, $curso);

        // Actividad "*" -- "*" MarkdownText
        $this->exportarRelacionJSON($ruta, $curso, 'markdown_text');

        // YoutubeVideo
        $this->exportarFicheroJSON($ruta, 'youtube_videos.json', $curso->youtube_videos);

        // Actividad "*" -- "*" YoutubeVideo
        $this->exportarRelacionJSON($ruta, $curso, 'youtube_video');

        // FileResources
        $this->exportarFicheroJSON($ruta, 'file_resources.json', $curso->file_resources);

        // Files
        $this->exportarFicheroJSON($ruta, 'file_resources_files.json', $curso->file_resources_files->sortBy('orden'));
        $this->exportarFileResources($directorio, $curso);

        // Actividad "*" -- "*" FileResources
        $this->exportarRelacionJSON($ruta, $curso, 'file_resource');

        // LinkCollections
        $this->exportarFicheroJSON($ruta, 'link_collections.json', $curso->link_collections);

        // Links
        $this->exportarFicheroJSON($ruta, 'link_collections_links.json', $curso->link_collections_links->sortBy('orden'));

        // Actividad "*" -- "*" LinkCollections
        $this->exportarRelacionJSON($ruta, $curso, 'link_collection');

        // Cuestionario
        $cuestionarios = $curso->cuestionarios()->plantilla()->get();
        $this->exportarFicheroJSON($ruta, 'cuestionarios.json', $cuestionarios);

        // Pregunta
        $this->exportarFicheroJSON($ruta, 'preguntas.json', $curso->preguntas()->plantilla()->orderBy('orden')->get());

        // Item
        $this->exportarFicheroJSON($ruta, 'items.json', $curso->items()->plantilla()->orderBy('orden')->get());

        // Actividad "*" -- "*" Cuestionario
        $this->exportarRelacionJSON($ruta, $curso, 'cuestionario');

        // FileUpload
        $file_uploads = $curso->file_uploads()->plantilla()->get();
        $this->exportarFicheroJSON($ruta, 'file_uploads.json', $file_uploads);

        // Actividad "*" -- "*" FileUpload
        $this->exportarRelacionJSON($ruta, $curso, 'file_upload');

        // Feedback
        $this->exportarFicheroJSON($ruta, 'feedbacks_curso.json', $curso->feedbacks()->orderBy('orden')->get());

        // Milestone
        $this->exportarFicheroJSON($ruta, 'milestones.json', $curso->milestones()->orderBy('date')->get());

        $datos = new Collection();
        foreach ($curso->actividades()->get() as $actividad) {
            foreach ($actividad->feedbacks()->orderBy('orden')->get() as $feedback) {
                $datos->add($feedback);
            }
        }
        $this->exportarFicheroJSON($ruta, 'feedbacks_actividades.json', $datos);

        // SafeExam
        if (isset($curso->safe_exam)) {
            $this->exportarFicheroJSON($ruta, 'safe_exam.json', $curso->safe_exam);
            $this->exportarFicheroJSON($ruta, 'safe_exam_allowed_apps.json', $curso->safe_exam->allowed_apps);
            $this->exportarFicheroJSON($ruta, 'safe_exam_allowed_urls.json', $curso->safe_exam->allowed_urls);
        }

        // Selector
        $this->exportarFicheroJSON($ruta, 'selectors.json', $curso->selectors);
        $this->exportarFicheroJSON($ruta, 'selectors_rule_groups.json', $curso->rule_groups);
        $this->exportarFicheroJSON($ruta, 'selectors_rules.json', $curso->rules);

        // Actividad "*" -- "*" Selector
        $this->exportarRelacionJSON($ruta, $curso, 'selector');

        // Crear el zip
        $fecha = now()->format('Ymd-His');
        $nombre = Str::slug($curso->full_name);

        dispatch(function () use ($directorio) {
            Log::debug('Borrando...', [
                'directorio' => $directorio,
            ]);
            Storage::disk('temp')->deleteDirectory($directorio);
        })->afterResponse();

        return $this->zipDirectoryWithSubdirs("ikasgela-{$nombre}-{$fecha}.zip", $directorio);
    }

    private function exportarFicheroJSON(string $ruta, string $fichero, $datos): void
    {
        SystemFile::put($ruta . $fichero, $datos->toJson(JSON_PRETTY_PRINT));
    }

    private function exportarRelacionJSON(string $ruta, $curso, $tabla): void
    {
        $datos = DB::table("actividad_{$tabla}")
            ->join('actividades', "actividad_{$tabla}.actividad_id", '=', 'actividades.id')
            ->where('actividades.plantilla', '=', true)
            ->join($tabla . 's', "actividad_{$tabla}.{$tabla}_id", '=', $tabla . 's.id')
            ->where($tabla . 's.curso_id', '=', $curso->id)
            ->select("actividad_{$tabla}.*")
            ->orderBy('orden')
            ->get();
        $this->exportarFicheroJSON($ruta, "actividad_{$tabla}.json", $datos);
    }

        private function exportarRepositorios(string $ruta, Curso $curso)
    {
        $path = $ruta . '/repositorios/';

        foreach ($curso->intellij_projects as $intellij_project) {
            $repositorio = $intellij_project->repository_no_cache();
            $this->clonarRepositorio($path, $repositorio);
        }
    }

    private function exportarMarkdown(string $ruta, Curso $curso)
    {
        $path = $ruta . '/markdown/';

        foreach ($curso->markdown_texts as $markdown_text) {
            $repositorio = GiteaClient::repo($markdown_text->repositorio);
            $this->clonarRepositorio($path, $repositorio);
        }
    }

    private function exportarFileResources(string $directorio, Curso $curso)
    {
        $ruta = $directorio . '/file_resources/';
        Storage::disk('temp')->makeDirectory($ruta);

        foreach ($curso->file_resources as $file_resource) {
            foreach ($file_resource->files as $file) {
                $datos = Storage::disk('s3')->get('documents/' . $file->path);
                if (isset($datos)) {
                    Storage::disk('temp')->put($ruta . '/' . $file->path, $datos);
                }
            }
        }
    }

    private function clonarRepositorio(string $path, array $repositorio): void
    {
        $response = Process::path($path)
            ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                . $repositorio['path_with_namespace'] . '.git '
                . $repositorio['owner'] . '@' . $repositorio['name']);

        if (!$response->successful()) {
            Log::error('Error al descargar repositorios mediante Git.', [
                'output' => $response->output()
            ]);
        } else {
            Process::path($path . '/' . $repositorio['owner'] . '@' . $repositorio['name'])
                ->run('git remote remove origin');
        }
    }

    public function zipDirectoryWithSubdirs(string $zip, string $directory)
    {
        $zip_path = Storage::disk('temp')->path($zip);

        $zip = new ZipArchive();

        if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {

            $files = Storage::disk('temp')->allFiles($directory);
            foreach ($files as $file) {
                $filePath = Storage::disk('temp')->path($file);
                $relative_file = Str::replaceStart($directory, '', '/' . $file);
                $zip->addFile($filePath, $relative_file);
            }

            $zip->close();

            return response()->download($zip_path)->deleteFileAfterSend(true);
        } else {
            return "Failed to create the zip file.";
        }
    }
}
*/
