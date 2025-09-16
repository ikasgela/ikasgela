<?php

namespace App\Models;

use App\Traits\Etiquetas;
use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperActividad
 */
class Actividad extends Model
{
    use HasFactory;
    use Cloneable;
    use LogsActivity;
    use SoftDeletes;
    use Etiquetas;

    protected $cloneable_relations = [
        'intellij_projects',
        'youtube_videos',
        'markdown_texts',
        'cuestionarios',
        'file_uploads',
        'file_resources',
        'feedbacks',
        'link_collections',
        'selectors',
        'rubrics',
    ];

    protected $clone_exempt_attributes = ['plantilla', 'siguiente_overriden'];

    protected $table = 'actividades';

    protected $fillable = [
        'unidad_id', 'nombre', 'descripcion', 'puntuacion', 'plantilla', 'slug', 'final', 'siguiente_id', 'auto_avance', 'qualification_id', 'orden',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite',
        'fecha_comienzo', 'fecha_finalizacion',
        'destacada', 'tags', 'multiplicador', 'siguiente_overriden',
        '__import_id',
    ];

    public function getFullNameAttribute()
    {
        return $this->unidad->nombre . ' - '
            . $this->nombre;
    }

    public function getPrettyNameAttribute()
    {
        return $this->unidad->nombre . ' » '
            . $this->nombre;
    }

    public function setCloneableRelations($relations)
    {
        $this->cloneable_relations = $relations;
    }

    public function duplicar_recursos_consumibles()
    {
        if ($this->intellij_projects()->count() > 1) {
            $intellij_project_ids = $this->intellij_projects()->get()->pluck('id')->toArray();
            $random = array_rand($intellij_project_ids);
            $this->intellij_projects()->sync([$intellij_project_ids[$random]]);
        }

        foreach ($this->cuestionarios as $cuestionario) {
            $copia = $cuestionario->duplicate();
            $this->cuestionarios()->detach($cuestionario);
            $this->cuestionarios()->attach($copia, ['orden' => $cuestionario->pivot->orden]);
        }

        foreach ($this->file_uploads as $file_upload) {
            $copia = $file_upload->duplicate();
            $this->reconectar($this->file_uploads(), $file_upload, $copia);
        }

        foreach ($this->rubrics as $rubric) {
            $copia = $rubric->duplicate();
            $this->reconectar($this->rubrics(), $rubric, $copia);
        }
    }

    public function duplicar_recursos(?Curso $curso_destino)
    {
        foreach ($this->file_resources as $file_resource) {
            $copia = $file_resource->duplicar($curso_destino);
            $this->reconectar($this->file_resources(), $file_resource, $copia);
        }

        foreach ($this->file_uploads as $file_upload) {
            $copia = $file_upload->duplicar($curso_destino);
            $this->reconectar($this->file_uploads(), $file_upload, $copia);
        }

        foreach ($this->youtube_videos as $youtube_video) {
            $copia = $youtube_video->duplicar($curso_destino);
            $this->reconectar($this->youtube_videos(), $youtube_video, $copia);
        }

        foreach ($this->markdown_texts as $markdown_text) {
            $copia = $markdown_text->duplicar($curso_destino);
            $this->reconectar($this->markdown_texts(), $markdown_text, $copia);
        }

        foreach ($this->intellij_projects as $intellij_project) {
            $copia = $intellij_project->duplicar($curso_destino);
            $this->reconectar($this->intellij_projects(), $intellij_project, $copia);
        }

        foreach ($this->link_collections as $link_collection) {
            $copia = $link_collection->duplicar($curso_destino);
            $this->reconectar($this->link_collections(), $link_collection, $copia);
        }

        foreach ($this->cuestionarios as $cuestionario) {
            $copia = $cuestionario->duplicar($curso_destino);
            $this->reconectar($this->cuestionarios(), $cuestionario, $copia);
        }

        foreach ($this->rubrics as $rubric) {
            $copia = $rubric->duplicar($curso_destino);
            $this->reconectar($this->rubrics(), $rubric, $copia);
        }

        foreach ($this->selectors as $selector) {
            $copia = $selector->duplicar($curso_destino);
            $this->reconectar($this->selectors(), $selector, $copia);
        }
    }

    public function trasladar_recursos(?Curso $curso_destino)
    {
        foreach ($this->file_resources as $file_resource) {
            $file_resource->curso_id = $curso_destino->id;
            $file_resource->save();
        }

        foreach ($this->file_uploads as $file_upload) {
            $file_upload->curso_id = $curso_destino->id;
            $file_upload->save();
        }

        foreach ($this->youtube_videos as $youtube_video) {
            $youtube_video->curso_id = $curso_destino->id;
            $youtube_video->save();
        }

        foreach ($this->markdown_texts as $markdown_text) {
            $markdown_text->curso_id = $curso_destino->id;

            // Al mover a otro curso, duplicar el repositorio
            $clonado = $markdown_text->duplicar_repositorio($curso_destino);
            $markdown_text->repositorio = $clonado['path_with_namespace'];

            $markdown_text->save();

            Cache::forget($markdown_text->cacheKey());
        }

        foreach ($this->intellij_projects as $intellij_project) {
            $intellij_project->curso_id = $curso_destino->id;

            // Al mover a otro curso, duplicar el repositorio
            $clonado = $intellij_project->duplicar_repositorio($curso_destino);
            $intellij_project->repositorio = $clonado['path_with_namespace'];

            $intellij_project->save();

            Cache::forget($intellij_project->templateCacheKey());
        }

        foreach ($this->link_collections as $link_collection) {
            $link_collection->curso_id = $curso_destino->id;
            $link_collection->save();
        }

        foreach ($this->cuestionarios as $cuestionario) {
            $cuestionario->curso_id = $curso_destino->id;
            $cuestionario->save();
        }

        foreach ($this->rubrics as $rubric) {
            $rubric->curso_id = $curso_destino->id;
            $rubric->save();
        }

        foreach ($this->selectors as $selector) {
            $selector->curso_id = $curso_destino->id;
            $selector->save();
        }
    }

    private function reconectar($coleccion, $original, $copia)
    {
        $coleccion->detach($original);
        $coleccion->attach($copia, [
            'orden' => $original->pivot->orden,
            'titulo_visible' => $original->pivot->titulo_visible,
            'descripcion_visible' => $original->pivot->descripcion_visible,
            'columnas' => $original->pivot->columnas,
        ]);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tareas')
            ->using(Tarea::class)
            ->as('tarea')
            ->withPivot([
                'id',
                'estado',
                'feedback',
                'puntuacion',
                'intentos'
            ]);
    }

    public function youtube_videos()
    {
        return $this
            ->belongsToMany(YoutubeVideo::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function intellij_projects()
    {
        return $this
            ->belongsToMany(IntellijProject::class)
            ->withTimestamps()
            ->withPivot([
                'fork',
                'archivado',
                'fork_status',
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
                'incluir_siempre',
            ]);
    }

    public function getSiguienteAttribute()
    {
        return Actividad::find($this->siguiente_id);
    }

    public function setSiguienteAttribute($value)
    {
        $this->attributes['siguiente_id'] = $value;
    }

    public function original()
    {
        return $this->belongsTo(Actividad::class, 'plantilla_id');
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function markdown_texts()
    {
        return $this
            ->belongsToMany(MarkdownText::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function cuestionarios()
    {
        return $this
            ->belongsToMany(Cuestionario::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    #[Scope]
    protected function plantilla($query)
    {
        return $query->where('plantilla', true);
    }

    #[Scope]
    protected function cursoActual($query)
    {
        return $query->whereHas('unidad.curso', function ($query) {
            $query->where('cursos.id', setting_usuario('curso_actual'));
        });
    }

    public function file_uploads()
    {
        return $this
            ->belongsToMany(FileUpload::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function file_resources()
    {
        return $this
            ->belongsToMany(FileResource::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function link_collections()
    {
        return $this
            ->belongsToMany(LinkCollection::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function selectors()
    {
        return $this
            ->belongsToMany(Selector::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function rubrics()
    {
        return $this
            ->belongsToMany(Rubric::class)
            ->withPivot([
                'orden',
                'titulo_visible', 'descripcion_visible', 'columnas',
            ])
            ->withTimestamps();
    }

    public function envioPermitido()
    {
        $enviar = true;

        if ($this->intellij_projects()->count() > 0) {
            if ($this->intellij_projects()->wherePivot('fork', null)->count() > 0)
                $enviar = false;
        }

        if ($this->cuestionarios()->count() > 0) {
            if (!($this->cuestionarios()->where('respondido', true)->count() == $this->cuestionarios()->count()))
                $enviar = false;
        }

        if ($this->file_uploads()->count() > 0) {
            foreach ($this->file_uploads()->get() as $file_upload) {
                if (!($file_upload->files()->count() > 0))
                    $enviar = false;
            }
        }

        return $enviar;
    }

    public function puntos()
    {
        return $this->puntuacion * ($this->multiplicador ?: 1);
    }

    public function feedbacks()
    {
        return $this->morphMany(Feedback::class, 'comentable');
    }

    #[Scope]
    protected function enPlazo($query)
    {
        return $query->where(function ($query) {
            $query->where('fecha_disponibilidad', '<=', now())->orWhereNull('fecha_disponibilidad');
        })->where(function ($query) {
            $query->where('fecha_limite', '>=', now())->orWhereNull('fecha_limite');
        });
    }

    #[Scope]
    protected function enPlazoOrCorregida($query)
    {
        return $query->where(function ($query) {
            $query->where('fecha_disponibilidad', '<=', now())->orWhereNull('fecha_disponibilidad')
                ->orWhereIn('estado', [40, 41, 42]);
        })->where(function ($query) {
            $query->where('fecha_limite', '>=', now())->orWhereNull('fecha_limite')
                ->orWhereIn('estado', [40, 41, 42]);
        });
    }

    #[Scope]
    protected function caducada($query)
    {
        return $query->where('fecha_limite', '<', now());
    }

    #[Scope]
    protected function estados($query, $estados)
    {
        return $query->whereHas('users', function ($q) use ($estados) {
            $q->where('user_id', Auth::user()->id)->whereIn('estado', $estados);
        });
    }

    #[Scope]
    protected function autoAvance($query)
    {
        return $query->where('auto_avance', true);
    }

    public function getIsAvailableAttribute()
    {
        return isset($this->fecha_disponibilidad) && $this->fecha_disponibilidad <= now();
    }

    public function getIsFinishedAttribute()
    {
        return isset($this->fecha_entrega) && $this->fecha_entrega < now()
            || !isset($this->fecha_entrega) && isset($this->fecha_limite) && $this->fecha_limite < now();
    }

    public function getIsExpiredAttribute()
    {
        if ($this->auto_avance)
            return false;

        return isset($this->fecha_limite) && $this->fecha_limite < now()
            || !isset($this->fecha_limite) && isset($this->fecha_entrega) && $this->fecha_entrega < now();
    }

    public function teams()
    {
        return $this
            ->belongsToMany(Team::class)
            ->withTimestamps();
    }

    public function getRecursosAttribute()
    {
        $recursos = new Collection();

        foreach ($this->youtube_videos()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->intellij_projects()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->markdown_texts()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->file_resources()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->file_uploads()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->link_collections()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->cuestionarios()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->selectors()->get() as $recurso) {
            $recursos->add($recurso);
        }

        foreach ($this->rubrics()->get() as $recurso) {
            $recursos->add($recurso);
        }

        return $recursos->sortBy('pivot.orden');
    }

    public function establecerFechaEntrega($fecha_override = null): void
    {
        if (!isset($this->fecha_disponibilidad)) {
            $this->fecha_disponibilidad = now();
        }

        if (!isset($this->fecha_entrega)) {
            if (is_null($fecha_override)) {
                $plazo_actividad_curso = $this->unidad->curso->plazo_actividad;

                if ($plazo_actividad_curso > 0) {
                    $this->fecha_entrega = now()->addDays($plazo_actividad_curso);
                }
            } else {
                $this->fecha_entrega = $fecha_override;
            }
        }

        if (isset($this->fecha_entrega) && !isset($this->fecha_limite)) {
            $this->fecha_limite = $this->fecha_entrega->addMinutes(10);
        }

        $this->save();
    }

    public function ampliarPlazo($dias)
    {
        $this->fecha_entrega = now()->addDays(intval($dias));
        $this->fecha_limite = $this->fecha_entrega->addMinutes(10);
        $this->save();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected function casts(): array
    {
        return [
            'fecha_disponibilidad' => 'datetime:Y-m-d H:i:s',
            'fecha_entrega' => 'datetime:Y-m-d H:i:s',
            'fecha_limite' => 'datetime:Y-m-d H:i:s',
            'fecha_comienzo' => 'datetime:Y-m-d H:i:s',
            'fecha_finalizacion' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
