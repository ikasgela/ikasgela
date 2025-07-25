<?php

namespace App\Jobs;

use App\Mail\ExportCompletado;
use App\Models\Actividad;
use App\Models\User;
use App\Models\UserExport;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ExportarUsuarioJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user->withoutRelations();
        $this->onQueue('low');
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

        // Definir la localización
        App::setLocale($this->user->preferredLocale());

        // Definir el copyright localizado
        $copyright = '&copy; ' . date('Y') . ' Ion Jaureguialzo Sarasola. ' . __('All rights reserved.');
        $pie = Str::replace('COPYRIGHT_IKASGELA', $copyright, $pie);

        // Crear un fichero index.html.temporal
        $html = "";

        $total_cursos = 0;

        foreach ($this->user->cursos as $curso) {
            if (isset($curso->fecha_fin) && $curso->fecha_fin->lt(now())) {

                $total_cursos += 1;

                // Añadir el nombre del curso
                $html .= '<h2>' . $curso->nombre . ' ' . $curso->category->period->name . '</h2>';

                // Recuperar las actividades del usuario
                $actividades = Actividad::whereHas('users', function ($query) use ($curso) {
                    $query->where('user_id', $this->user->id);
                })->whereHas('unidad.curso', function ($query) use ($curso) {
                    $query->where('curso_id', $curso->id);
                })->get();

                // Recorrer las actividades del usuario
                $total = 0;

                foreach ($actividades as $actividad) {

                    if ($total == 0) {
                        $html .= '<ul class="mb-4">';
                    }

                    $total += 1;

                    // Crear una carpeta para la actividad
                    $subdirectorio = Str::slug($actividad->full_name);
                    $ruta = $directorio . '/' . $subdirectorio;

                    // Evitar colisiones de nombre
                    while (Storage::disk('temp')->directoryExists($ruta)) {
                        $secuencia = bin2hex(openssl_random_pseudo_bytes(3));
                        $subdirectorio = Str::slug($actividad->full_name) . '-' . $secuencia;
                        $ruta = $directorio . '/' . $subdirectorio;
                    }

                    Storage::disk('temp')->makeDirectory($ruta);

                    // Crear un fichero index.html.temporal
                    $html_actividad = '<h2>' . $actividad->full_name . '</h2>';

                    // Recorrer los recursos de la actividad
                    $html_actividad .= '<ul class="mb-4">';

                    $total_recursos = 0;

                    // Texto Markdown
                    foreach ($actividad->markdown_texts as $markdown_text) {
                        try {
                            // Descargar el fichero
                            $nombre_fichero = Str::slug($markdown_text->titulo) . '.md';
                            Storage::disk('temp')->put(
                                $ruta . '/' . $nombre_fichero,
                                $markdown_text->raw()
                            );

                            $total_recursos += 1;

                            // Enlazarlo en el HTML
                            $html_actividad .= '<li>';
                            $html_actividad .= __('Markdown text') . ': <a target="_blank" href="' . $nombre_fichero . '">' . $markdown_text->titulo . '</a>';
                            $html_actividad .= '</li>';
                        } catch (Exception) {
                        }
                    }

                    // Vídeos de YouTube
                    foreach ($actividad->youtube_videos as $youtube_video) {
                        $total_recursos += 1;

                        // Enlazarlo en el HTML
                        $html_actividad .= '<li>';
                        $html_actividad .= __('YouTube video') . ': <a target="_blank" href="' . $youtube_video->codigo . '">' . $youtube_video->titulo . '</a>';
                        $html_actividad .= '</li>';
                    }

                    // Enlaces
                    foreach ($actividad->link_collections as $link_collection) {
                        foreach ($link_collection->links as $link) {
                            $total_recursos += 1;

                            // Enlazarlo en el HTML
                            $html_actividad .= '<li>';
                            $html_actividad .= __('Link') . ': <a target="_blank" href="' . $link->url . '">' . ($link->descripcion ?: $link->url) . '</a>';
                            $html_actividad .= '</li>';
                        }
                    }

                    // Archivos
                    foreach ($actividad->file_resources as $file_resource) {
                        foreach ($file_resource->files as $file) {
                            try {
                                // Descargar el fichero
                                $nombre_fichero = Str::replace('/', '-', $file->path);
                                Storage::disk('temp')->put(
                                    $ruta . '/' . $nombre_fichero,
                                    Storage::disk('s3')->get('documents/' . $file->path) ?: ''
                                );

                                $total_recursos += 1;

                                // Enlazarlo en el HTML
                                $html_actividad .= '<li>';
                                $html_actividad .= __('File') . ': <a target="_blank" href="' . $nombre_fichero . '">' . ($file->description ?: $file->title) . '</a>';
                                $html_actividad .= '</li>';
                            } catch (Exception) {
                            }
                        }
                    }

                    // Cuestionarios

                    // Subidas de imágenes
                    foreach ($actividad->file_uploads as $file_upload) {
                        foreach ($file_upload->files as $file) {
                            try {
                                // Descargar el fichero
                                $nombre_fichero = Str::replace('/', '-', $file->path);
                                Storage::disk('temp')->put(
                                    $ruta . '/' . $nombre_fichero,
                                    Storage::disk('s3')->get('images/' . $file->path) ?: ''
                                );

                                $total_recursos += 1;

                                // Enlazarlo en el HTML
                                $html_actividad .= '<li>';
                                $html_actividad .= __('Image') . ': <a target="_blank" href="' . $nombre_fichero . '">' . ($file->description ?: $file->title) . '</a>';
                                $html_actividad .= '</li>';
                            } catch (Exception) {
                            }
                        }
                    }

                    // Proyectos de IntelliJ
                    foreach ($actividad->intellij_projects as $intellij_project) {
                        try {
                            // Descargar el repositorio
                            if (!$intellij_project->isForked()) {
                                $repositorio = GiteaClient::repo($intellij_project->repositorio);
                            } else {
                                $repositorio = GiteaClient::repo($intellij_project->pivot->fork);
                            }
                            $descarga = GiteaClient::download($repositorio['owner'], $repositorio['name'], 'HEAD.zip');
                            $nombre_fichero = Str::slug($repositorio['name']) . '.zip';
                            Storage::disk('temp')->put($ruta . '/' . $nombre_fichero, $descarga ?: '');

                            $total_recursos += 1;

                            // Enlazarlo en el HTML
                            $html_actividad .= '<li>';
                            $html_actividad .= __('IntelliJ project') . ': <a href="' . $nombre_fichero . '">' . $repositorio['name'] . '</a>';
                            $html_actividad .= '</li>';
                        } catch (Exception) {
                        }
                    }

                    $html_actividad .= '</ul>';

                    if ($total_recursos == 0) {
                        $html_actividad .= '<p>' . __('There are no resources in this activity.') . '</p>';
                    }

                    // Añadir el enlace de retorno
                    $html_actividad .= '<a href="../index.html">' . __('Back') . '</a>';

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
                    $html .= '<p>' . __('There are no activities.') . '</p>';
                }
            }
        }

        if ($total_cursos == 0) {
            $html .= '<p>' . __('There are no courses available') . '.</p>';
        }

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
        $nombre_fichero = Str::slug('ikasgela-' . $this->user->full_name . '-' . now()->format('YmdHis')) . '.zip';
        $this->zipDirectoryWithSubdirs($nombre_fichero, $directorio);

        if (config('app.env') === 'production') {
            // Subir el .zip a S3
            Storage::disk('s3')->put(
                'exports/' . $nombre_fichero,
                Storage::disk('temp')->get($nombre_fichero)
            );

            // Borrar el zip
            Storage::disk('temp')->delete($nombre_fichero);

            // Borrar la carpeta
            Storage::disk('temp')->deleteDirectory($directorio);

            // Obtener la URL temporal del fichero
            $fecha_caducidad = now()->addHours(24);
            $url = Storage::disk('s3-urls')->temporaryUrl('exports/' . $nombre_fichero, $fecha_caducidad);

            // Enviar el email con el enlace al fichero de S3
            Mail::to($this->user)->queue(new ExportCompletado($url));

            // Registrar cuando se puede hacer la siguiente exportación
            UserExport::updateOrCreate(['user_id' => $this->user->id], [
                'fecha' => $fecha_caducidad,
                'url' => $url,
                'fichero' => 'exports/' . $nombre_fichero,
            ]);
        } else {
            Mail::to($this->user)->queue(new ExportCompletado('https://example.org'));
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

            return $zip_path;
        } else {
            return "Failed to create the zip file.";
        }
    }
}
