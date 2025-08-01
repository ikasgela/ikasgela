<?php

namespace App\Jobs;

use App\Models\Actividad;
use App\Models\AllowedApp;
use App\Models\AllowedUrl;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Cuestionario;
use App\Models\Curso;
use App\Models\Feedback;
use App\Models\File;
use App\Models\FileResource;
use App\Models\FileUpload;
use App\Models\IntellijProject;
use App\Models\Item;
use App\Models\Link;
use App\Models\LinkCollection;
use App\Models\MarkdownText;
use App\Models\Milestone;
use App\Models\Pregunta;
use App\Models\Qualification;
use App\Models\Rubric;
use App\Models\Rule;
use App\Models\RuleGroup;
use App\Models\SafeExam;
use App\Models\Selector;
use App\Models\Skill;
use App\Models\Unidad;
use App\Models\YoutubeVideo;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCurso implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(protected string $category_id, protected string $directorio, protected string $ruta)
    {
        $this->onQueue('low');
    }

    public function handle()
    {
        $category_id = $this->category_id;
        $directorio = $this->directorio;
        $ruta = $this->ruta;

        // Importar los datos desde los archivos JSON
        $import_ids = [
            'cursos', 'unidades', 'actividades',
            'qualifications', 'skills',
            'intellij_projects', 'markdown_texts', 'youtube_videos',
            'file_resources', 'files',
            'file_uploads',
            'cuestionarios', 'preguntas', 'items',
            'feedback',
            'milestones',
            'link_collections', 'links',
            'safe_exams', 'allowed_apps', 'allowed_urls',
            'selectors', 'rule_groups', 'rules',
            'rubrics', 'criteria_groups', 'criterias',
        ];

        // Añadir la columa __import_id a las tablas
        foreach ($import_ids as $import_id) {
            $this->addImportId($import_id);
        }

        // Borrar la caché antes de importar
        Cache::flush();

        // Curso
        $json = $this->cargarFichero($ruta, 'curso.json');

        $sufijo = '-' . bin2hex(openssl_random_pseudo_bytes(3));

        $slug_curso = Str::slug($json['nombre'] . $sufijo);
        $gitea_organization = Str::limit(Str::slug($json['nombre']), 33, '') . $sufijo;

        $json['nombre'] .= $sufijo;
        $json['slug'] = $slug_curso;
        $json['gitea_organization'] = $gitea_organization;

        Log::debug('Iniciando importación de curso...', [
            'curso' => $slug_curso
        ]);

        // Curso -- Qualification
        $temp_curso_qualification_id = $json['qualification_id'];

        // Curso -- Tarea de bienvenida
        $temp_tarea_bienvenida_id = $json['tarea_bienvenida_id'];

        // Curso
        $curso = Curso::create(array_merge($json, [
            'qualification_id' => null,
            'tarea_bienvenida_id' => null,
            'category_id' => $category_id,
        ]));

        // Curso -- "*" Qualification
        $json = $this->cargarFichero($ruta, 'qualifications.json');
        foreach ($json as $objeto) {
            Qualification::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Curso -- Qualification
        $qualification = !is_null($temp_curso_qualification_id) ? Qualification::where('__import_id', $temp_curso_qualification_id)->first() : null;
        $curso->qualification_id = $qualification?->id;
        $curso->save();

        // Curso -- "*" Skill
        $json = $this->cargarFichero($ruta, 'skills.json');
        foreach ($json as $objeto) {
            Skill::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Qualification "*" -- "*" Skill
        $json = $this->cargarFichero($ruta, 'qualification_skill.json');
        foreach ($json as $objeto) {
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $skill = Skill::where('__import_id', $objeto['skill_id'])->first();
            $qualification?->skills()->attach($skill, [
                'percentage' => $objeto['percentage'],
                'orden' => $objeto['orden'],
            ]);
        }

        // Curso -- "*" Unidad
        // Unidad -- Qualification
        $json = $this->cargarFichero($ruta, 'unidades.json');
        foreach ($json as $objeto) {
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $unidad = Unidad::create(array_merge($objeto, [
                'curso_id' => $curso->id,
                'qualification_id' => $qualification?->id,
            ]));
            $unidad->orden = $unidad->id;
            $unidad->save();
        }

        // Unidad -- "*" Actividad
        // Unidad -- Qualification

        Schema::disableForeignKeyConstraints();

        $total = 0;
        $json = $this->cargarFichero($ruta, 'actividades.json');
        foreach ($json as $objeto) {
            $unidad = !is_null($objeto['unidad_id']) ? Unidad::where('__import_id', $objeto['unidad_id'])->first() : null;
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $actividad = Actividad::create(array_merge($objeto, [
                'unidad_id' => $unidad->id,
                'qualification_id' => $qualification?->id,
            ]));
            $actividad->orden = $actividad->id;
            $actividad->save();
            $total += 1;
        }
        Log::info("Elementos importados.", ['actividades' => $total]);

        // Actividad --> Actividad: siguiente
        $actividades = Actividad::whereNotNull('__import_id')->get();
        foreach ($actividades as $actividad) {
            $siguiente = !is_null($actividad->siguiente_id) ? Actividad::where('__import_id', $actividad->siguiente_id)->first() : null;
            $actividad->siguiente_id = $siguiente?->id;
            $actividad->save();
        }

        Schema::enableForeignKeyConstraints();

        // Curso -- Tarea de bienvenida
        $actividad = !is_null($temp_tarea_bienvenida_id) ? Actividad::where('__import_id', $temp_tarea_bienvenida_id)->first() : null;
        $curso->tarea_bienvenida_id = $actividad?->id;
        $curso->save();

        // Curso -- "*" IntellijProject
        $total = 0;
        $json = $this->cargarFichero($ruta, 'intellij_projects.json');
        foreach ($json as $key => $objeto) {
            $nombre_repositorio = Str::replaceMatches(
                pattern: '#^.*/#',
                replace: '',
                subject: $objeto['repositorio'],
            );

            IntellijProject::create(array_merge($objeto, [
                'curso_id' => $curso->id,
                'repositorio' => "$gitea_organization/$nombre_repositorio",
            ]));

            if ($key === array_key_first($json)) {
                GiteaClient::organization($gitea_organization, $curso->nombre);
            }

            $nombre_exportacion = Str::replace('/', '@', $objeto['repositorio']);

            $ok = $this->importarRepositorio($ruta . '/repositorios/', $nombre_exportacion, $nombre_repositorio, $gitea_organization);
            if ($ok) {
                $total += 1;
            }
        }
        Log::info("Elementos importados.", ['intellij_projects' => $total]);

        // Actividad "*" - "*" IntellijProject
        $json = $this->cargarFichero($ruta, 'actividad_intellij_project.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $intellij_project = !is_null($objeto['intellij_project_id']) ? IntellijProject::where('__import_id', $objeto['intellij_project_id'])->first() : null;
            $actividad?->intellij_projects()->attach($intellij_project, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // Curso -- "*" MarkdownText
        $total = 0;
        $json = $this->cargarFichero($ruta, 'markdown_texts.json');
        foreach ($json as $key => $objeto) {
            $nombre_repositorio = Str::replaceMatches(
                pattern: '#^.*/#',
                replace: '',
                subject: $objeto['repositorio'],
            );

            MarkdownText::create(array_merge($objeto, [
                'curso_id' => $curso->id,
                'repositorio' => "$gitea_organization/$nombre_repositorio",
            ]));

            if ($key === array_key_first($json)) {
                GiteaClient::organization($gitea_organization, $curso->nombre);
            }

            $nombre_exportacion = Str::replace('/', '@', $objeto['repositorio']);

            $ok = $this->importarRepositorio($ruta . '/markdown/', $nombre_exportacion, $nombre_repositorio, $gitea_organization);
            if ($ok) {
                $total += 1;
            }
        }
        Log::info("Elementos importados.", ['markdown_texts' => $total]);

        // Actividad "*" - "*" MarkdownText
        $json = $this->cargarFichero($ruta, 'actividad_markdown_text.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $markdown_text = !is_null($objeto['markdown_text_id']) ? MarkdownText::where('__import_id', $objeto['markdown_text_id'])->first() : null;
            $actividad?->markdown_texts()->attach($markdown_text, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // Curso -- "*" YoutubeVideo
        $json = $this->cargarFichero($ruta, 'youtube_videos.json');
        foreach ($json as $objeto) {
            YoutubeVideo::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" YoutubeVideo
        $json = $this->cargarFichero($ruta, 'actividad_youtube_video.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $youtube_video = !is_null($objeto['youtube_video_id']) ? YoutubeVideo::where('__import_id', $objeto['youtube_video_id'])->first() : null;
            $actividad?->youtube_videos()->attach($youtube_video, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // Curso -- "*" FileResource
        $json = $this->cargarFichero($ruta, 'file_resources.json');
        foreach ($json as $objeto) {
            FileResource::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" FileResource
        $json = $this->cargarFichero($ruta, 'actividad_file_resource.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $file_resource = !is_null($objeto['file_resource_id']) ? FileResource::where('__import_id', $objeto['file_resource_id'])->first() : null;
            $actividad?->file_resources()->attach($file_resource, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // FileResource -- "*" File
        $total = 0;
        $json = $this->cargarFichero($ruta, 'file_resources_files.json');
        foreach ($json as $objeto) {
            $file_resource = !is_null($objeto['uploadable_id']) ? FileResource::where('__import_id', $objeto['uploadable_id'])->first() : null;

            $fichero = Str::replaceMatches(
                pattern: '#^.*/#',
                replace: '',
                subject: $objeto['path'],
            );

            $filename = md5(time()) . '/' . $fichero;

            try {
                Storage::disk('s3')->put('documents/' . $filename, file_get_contents($ruta . '/file_resources/' . $objeto['path']));
                $total += 1;
            } catch (Exception $e) {
                Log::error('Error al crear el archivo.', [
                    'exception' => $e->getMessage(),
                ]);
            }

            $file = File::create(array_merge($objeto, [
                'path' => $filename,
                'uploadable_id' => $file_resource->id,
                'user_id' => null,
            ]));

            $file->orden = $file->id;
            $file->save();
        }
        Log::info("Elementos importados.", ['file_resources_files' => $total]);

        // Curso -- "*" LinkCollection
        $json = $this->cargarFichero($ruta, 'link_collections.json');
        foreach ($json as $objeto) {
            LinkCollection::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" LinkCollection
        $json = $this->cargarFichero($ruta, 'actividad_link_collection.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $link_collection = !is_null($objeto['link_collection_id']) ? LinkCollection::where('__import_id', $objeto['link_collection_id'])->first() : null;
            $actividad?->link_collections()->attach($link_collection, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // LinkCollection -- "*" Link
        $json = $this->cargarFichero($ruta, 'link_collections_links.json');
        foreach ($json as $objeto) {
            $link_collection = !is_null($objeto['link_collection_id']) ? LinkCollection::where('__import_id', $objeto['link_collection_id'])->first() : null;
            $link = Link::create(array_merge($objeto, [
                'link_collection_id' => $link_collection->id,
            ]));
            $link->orden = $link->id;
            $link->save();
        }

        // Curso -- "*" FileUpload
        $json = $this->cargarFichero($ruta, 'file_uploads.json');
        foreach ($json as $objeto) {
            FileUpload::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" FileUpload
        $json = $this->cargarFichero($ruta, 'actividad_file_upload.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $file_upload = !is_null($objeto['file_upload_id']) ? FileUpload::where('__import_id', $objeto['file_upload_id'])->first() : null;
            $actividad?->file_uploads()->attach($file_upload, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // Curso -- "*" Cuestionario
        $json = $this->cargarFichero($ruta, 'cuestionarios.json');
        foreach ($json as $objeto) {
            Cuestionario::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Cuestionario -- "*" Pregunta
        $json = $this->cargarFichero($ruta, 'preguntas.json');
        foreach ($json as $objeto) {
            $cuestionario = !is_null($objeto['cuestionario_id']) ? Cuestionario::where('__import_id', $objeto['cuestionario_id'])->first() : null;
            $pregunta = Pregunta::create(array_merge($objeto, [
                'cuestionario_id' => $cuestionario?->id,
            ]));
            $pregunta->orden = $pregunta->id;
            $pregunta->save();
        }

        // Pregunta -- "*" Item
        $json = $this->cargarFichero($ruta, 'items.json');
        foreach ($json as $objeto) {
            $pregunta = !is_null($objeto['pregunta_id']) ? Pregunta::where('__import_id', $objeto['pregunta_id'])->first() : null;
            $item = Item::create(array_merge($objeto, [
                'pregunta_id' => $pregunta?->id,
            ]));
            $item->orden = $item->id;
            $item->save();
        }

        // Actividad "*" - "*" Cuestionario
        $json = $this->cargarFichero($ruta, 'actividad_cuestionario.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $cuestionario = !is_null($objeto['cuestionario_id']) ? Cuestionario::where('__import_id', $objeto['cuestionario_id'])->first() : null;
            $actividad?->cuestionarios()->attach($cuestionario, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // Curso -- "*" Feedback
        $json = $this->cargarFichero($ruta, 'feedbacks_curso.json');
        foreach ($json as $objeto) {
            $feedback = Feedback::create(array_merge($objeto, [
                'comentable_id' => $curso->id,
                'comentable_type' => Curso::class,
            ]));
            $feedback->orden = $feedback->id;
            $feedback->save();
        }

        // Actividad -- "*" Feedback
        $json = $this->cargarFichero($ruta, 'feedbacks_actividades.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['comentable_id']) ? Actividad::where('__import_id', $objeto['comentable_id'])->first() : null;
            if (!is_null($actividad)) {
                $feedback = Feedback::create(array_merge($objeto, [
                    'comentable_id' => $actividad->id,
                    'comentable_type' => Actividad::class,
                ]));
                $feedback->orden = $feedback->id;
                $feedback->save();
            }
        }

        // Curso -- "*" Milestone
        $json = $this->cargarFichero($ruta, 'milestones.json');
        foreach ($json as $objeto) {
            Milestone::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Curso -- SafeExam
        $objeto = $this->cargarFichero($ruta, 'safe_exam.json');
        if (!is_null($objeto)) {
            SafeExam::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));

            // SafeExam -- "*" AllowedApp
            $json = $this->cargarFichero($ruta, 'safe_exam_allowed_apps.json');
            foreach ($json as $objeto) {
                $safe_exam = !is_null($objeto['safe_exam_id']) ? SafeExam::where('__import_id', $objeto['safe_exam_id'])->first() : null;
                AllowedApp::create(array_merge($objeto, [
                    'safe_exam_id' => $safe_exam?->id,
                ]));
            }

            // SafeExam -- "*" AllowedUrl
            $json = $this->cargarFichero($ruta, 'safe_exam_allowed_urls.json');
            foreach ($json as $objeto) {
                $safe_exam = !is_null($objeto['safe_exam_id']) ? SafeExam::where('__import_id', $objeto['safe_exam_id'])->first() : null;
                AllowedUrl::create(array_merge($objeto, [
                    'safe_exam_id' => $safe_exam?->id,
                ]));
            }
        }

        // Curso -- "*" Selector
        $json = $this->cargarFichero($ruta, 'selectors.json');
        if (!is_null($json)) {
            foreach ($json as $objeto) {
                Selector::create(array_merge($objeto, [
                    'curso_id' => $curso->id,
                ]));
            }

            // Selector -- "*" RuleGroup
            $json = $this->cargarFichero($ruta, 'selectors_rule_groups.json');
            foreach ($json as $objeto) {
                $selector = !is_null($objeto['selector_id']) ? Selector::where('__import_id', $objeto['selector_id'])->first() : null;
                RuleGroup::create(array_merge($objeto, [
                    'selector_id' => $selector?->id,
                ]));
            }

            // RuleGroup -- "*" Rule
            $json = $this->cargarFichero($ruta, 'selectors_rules.json');
            foreach ($json as $objeto) {
                $rule_group = !is_null($objeto['rule_group_id']) ? RuleGroup::where('__import_id', $objeto['rule_group_id'])->first() : null;
                Rule::create(array_merge($objeto, [
                    'rule_group_id' => $rule_group?->id,
                ]));
            }

            // Actividad "*" - "*" Selector
            $json = $this->cargarFichero($ruta, 'actividad_selector.json');
            foreach ($json as $objeto) {
                $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
                $selector = !is_null($objeto['selector_id']) ? Selector::where('__import_id', $objeto['selector_id'])->first() : null;
                $actividad?->selectors()->attach($selector, [
                    'orden' => $objeto['orden'],
                    'titulo_visible' => $objeto['titulo_visible'],
                    'descripcion_visible' => $objeto['descripcion_visible'],
                    'columnas' => $objeto['columnas'],
                ]);
            }
        }


        // Curso -- "*" Rubric
        $json = $this->cargarFichero($ruta, 'rubrics.json');
        foreach ($json as $objeto) {
            Rubric::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Rubric -- "*" CriteriaGroup
        $json = $this->cargarFichero($ruta, 'criteria_groups.json');
        foreach ($json as $objeto) {
            $rubric = !is_null($objeto['rubric_id']) ? Rubric::where('__import_id', $objeto['rubric_id'])->first() : null;
            CriteriaGroup::create(array_merge($objeto, [
                'rubric_id' => $rubric?->id,
                'orden' => $objeto['orden'],
            ]));
        }

        // CriteriaGroup -- "*" Criteria
        $json = $this->cargarFichero($ruta, 'criterias.json');
        foreach ($json as $objeto) {
            $criteria_group = !is_null($objeto['criteria_group_id']) ? CriteriaGroup::where('__import_id', $objeto['criteria_group_id'])->first() : null;
            Criteria::create(array_merge($objeto, [
                'criteria_group_id' => $criteria_group?->id,
                'orden' => $objeto['orden'],
            ]));
        }

        // Actividad "*" - "*" Rubric
        $json = $this->cargarFichero($ruta, 'actividad_rubric.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $rubric = !is_null($objeto['rubric_id']) ? Rubric::where('__import_id', $objeto['rubric_id'])->first() : null;
            $actividad?->rubrics()->attach($rubric, [
                'orden' => $objeto['orden'],
                'titulo_visible' => $objeto['titulo_visible'],
                'descripcion_visible' => $objeto['descripcion_visible'],
                'columnas' => $objeto['columnas'],
            ]);
        }

        // Quitar la columa __import_id de las tablas
        foreach ($import_ids as $import_id) {
            $this->removeImportId($import_id);
        }

        // Borrar el directorio temporal
        Storage::disk('temp')->deleteDirectory($directorio);

        Log::debug('Importación de curso terminada.', [
            'curso' => $slug_curso
        ]);
    }

    private function addImportId($tabla): void
    {
        // Añadir la columna __import_id
        if (!Schema::hasColumn($tabla, '__import_id')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->bigInteger('__import_id')->unsigned()->nullable();
            });
        }
    }

    private function removeImportId($tabla): void
    {
        // Quitar la columna
        if (Schema::hasColumn($tabla, '__import_id')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn('__import_id');
            });
        }
    }

    private function replaceKeys($oldKey, $newKey, array $input)
    {
        $return = [];
        foreach ($input as $key => $value) {
            if ($key === $oldKey)
                $key = $newKey;

            if (is_array($value))
                $value = $this->replaceKeys($oldKey, $newKey, $value);

            $return[$key] = $value;
        }
        return $return;
    }

    // REF: https://stackoverflow.com/a/1708914
    private function removeKey(&$array, $unwanted_key)
    {
        unset($array[$unwanted_key]);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->removeKey($value, $unwanted_key);
            }
        }
    }

    private function cargarFichero(string $ruta, $fichero): array|null
    {
        try {
            $path = $ruta . '/' . $fichero;
            $json = json_decode(file_get_contents($path), true);
            $json = $this->replaceKeys('id', '__import_id', $json);
            $this->removeKey($json, 'created_at');
            $this->removeKey($json, 'updated_at');
            $this->removeKey($json, 'deleted_at');
        } catch (Exception) {
            $json = null;
        }
        return $json;
    }

    private function importarRepositorio(string $ruta, string $directorio, string $repositorio, string $organizacion)
    {
        $path = $ruta . $directorio;

        try {
            $rama = Process::path($path)
                ->run('git rev-parse --abbrev-ref HEAD')->output();

            $result = Process::path($path)
                ->run('git push -f --set-upstream http://root:' . config('gitea.token') . '@gitea:3000/'
                    . $organizacion . '/' . $repositorio . '.git ' . $rama);

            if ($result->failed()) {
                Log::error('Error al crear el repositorio.', [
                    'exception' => $result->errorOutput(),
                    'repositorio' => $organizacion . '/' . $repositorio,
                ]);
            }
            return true;
        } catch (Exception $e) {
            Log::error('Error al crear el repositorio.', [
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
