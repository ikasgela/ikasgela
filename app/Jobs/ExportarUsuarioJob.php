<?php

namespace App\Jobs;

use App\Models\User;
use Ikasgela\Gitea\GiteaClient;
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

        // Cargar la cabecera y el pie
        $cabecera = Storage::disk('templates')->get('header.html');
        $pie = Storage::disk('templates')->get('footer.html');

        // Crear un fichero index.html.temporal
        $html = "";

        // Recorrer las actividades del usuario
        $total = 0;
        foreach ($this->user->actividades as $actividad) {

            // Añadir el nombre del curso
            if ($total == 0) {
                $html .= '<h2>' . $actividad->unidad->curso->nombre . ' ' . $actividad->unidad->curso->category->period->name . '</h2>';
                $html .= '<ul class="mb-4">';
            }

            $total += 1;

            // Crear una carpeta para la actividad
            $subdirectorio = Str::slug($actividad->full_name);
            $ruta = $directorio . '/' . $subdirectorio;
            Storage::disk('temp')->makeDirectory($ruta);

            // Crear un fichero index.html.temporal
            $html_actividad = '<h2>' . $actividad->full_name . '</h2>';

            // Recorrer los recursos de la actividad
            $html_actividad .= '<ul class="mb-4">';

            $total_recursos = 0;

            // Texto Markdown

            // Vídeos de YouTube
            foreach ($actividad->youtube_videos as $youtube_video) {
                $total_recursos += 1;

                // Enlazarlo en el HTML
                $html_actividad .= '<li>';
                $html_actividad .= 'Vídeo: <a target="_blank" href="' . $youtube_video->codigo . '">' . $youtube_video->titulo . '</a>';
                $html_actividad .= '</li>';
            }

            // Enlaces
            foreach ($actividad->link_collections as $link_collection) {
                $total_recursos += 1;

                foreach ($link_collection->links as $link) {
                    // Enlazarlo en el HTML
                    $html_actividad .= '<li>';
                    $html_actividad .= 'Enlace: <a target="_blank" href="' . $link->url . '">' . ($link->descripcion ?: $link->url) . '</a>';
                    $html_actividad .= '</li>';
                }
            }

            // Archivos
            foreach ($actividad->file_resources as $file_resource) {
                $total_recursos += 1;

                foreach ($file_resource->files as $file) {
                    // Descargar el fichero
                    $nombre_fichero = Str::replace('/', '-', $file->path);
                    Storage::disk('temp')->put(
                        $ruta . '/' . $nombre_fichero,
                        Storage::disk('s3')->get('documents/' . $file->path)
                    );

                    // Enlazarlo en el HTML
                    $html_actividad .= '<li>';
                    $html_actividad .= 'Archivo: <a target="_blank" href="' . $nombre_fichero . '">' . ($file->description ?: $file->title) . '</a>';
                    $html_actividad .= '</li>';
                }
            }

            // Cuestionarios

            // Subidas de imágenes

            // Proyectos de IntelliJ
            foreach ($actividad->intellij_projects as $intellij_project) {
                $total_recursos += 1;

                // Descargar el repositorio
                $repositorio = $intellij_project->repository(true);
                $descarga = GiteaClient::download($repositorio['owner'], $repositorio['name'], 'master.zip');
                $nombre_fichero = Str::slug($repositorio['name']) . '.zip';
                Storage::disk('temp')->put($ruta . '/' . $nombre_fichero, $descarga);

                // Enlazarlo en el HTML
                $html_actividad .= '<li>';
                $html_actividad .= 'Proyecto: <a href="' . $nombre_fichero . '">' . $repositorio['name'] . '</a>';
                $html_actividad .= '</li>';
            }

            $html_actividad .= '</ul>';

            if ($total_recursos == 0) {
                $html_actividad .= '<p>No hay ningún recurso disponible en esta actividad.</p>';
            }

            // Añadir el enlace de retorno
            $html_actividad .= '<a href="../index.html">Volver</a>';

            // Concatenar la cabecera y el pie para crear el index.html
            $html_actividad = $cabecera . $html_actividad . $pie;

            // Escribir el index.html de la actividad
            Storage::disk('temp')->put($ruta . '/index.html', $html_actividad);

            // Enlazar la actividad en el HTML general
            $html .= '<li>';
            $html .= '<a href="' . $subdirectorio . '/index.html">' . $actividad->full_name . '</a>';
            $html .= '</li>';
        }

        if ($total > 0) {
            $html .= '</ul>';
        } else {
            $html .= '<p>No hay actividades.</p>';
        }

        // Crear un fichero index.html.temporal
        // Recorrer el directorio y crear el indice principal
        // Concatenar la cabecera y el pie al indice principal para crear el index.html
        // Borrar el index.html temporal

        // Corregir la ruta del CSS
        $cabecera = Str::replace('../', '', $cabecera);

        // Concatenar la cabecera y el pie al indice principal para crear el index.html
        $html = $cabecera . $html . $pie;

        // Escribir el index.html general
        Storage::disk('temp')->put($directorio . '/index.html', $html);

        // Copiar el bootstrap.min a la carpeta
        Storage::disk('temp')->put(
            $directorio . '/css/bootstrap.min.css',
            Storage::disk('templates')->get('bootstrap.min.css')
        );

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
