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

    public function onCloned($src)
    {
        if ($this->intellij_projects()->count() > 1) {
            $intellij_project_ids = $this->intellij_projects()->get()->pluck('id')->toArray();

            $random = array_rand($intellij_project_ids);

            $this->intellij_projects()->sync([$intellij_project_ids[$random]]);
        }

        foreach ($src->cuestionarios as $cuestionario) {
            $copia = $cuestionario->duplicate();
            $this->cuestionarios()->detach($cuestionario);
            $this->cuestionarios()->attach($copia, ['orden' => $cuestionario->pivot->orden]);
        }

        foreach ($src->file_uploads as $file_upload) {
            $copia = $file_upload->duplicate();
            $this->file_uploads()->detach($file_upload);
            $this->file_uploads()->attach($copia, [
                'orden' => $file_upload->pivot->orden,
                'titulo_visible' => $file_upload->pivot->titulo_visible,
                'descripcion_visible' => $file_upload->pivot->descripcion_visible,
                'columnas' => $file_upload->pivot->columnas,
            ]);
        }

        foreach ($src->rubrics as $rubric) {
            $copia = $rubric->duplicate();
            $this->rubrics()->detach($rubric);
            $this->rubrics()->attach($copia, [
                'orden' => $rubric->pivot->orden,
                'titulo_visible' => $rubric->pivot->titulo_visible,
                'descripcion_visible' => $rubric->pivot->descripcion_visible,
                'columnas' => $rubric->pivot->columnas,
            ]);
        }

        $this->orden = $this->id;
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
        $ahora = now();

        if (!isset($this->fecha_disponibilidad)) {
            $this->fecha_disponibilidad = $ahora;
        }

        if (!isset($this->fecha_entrega)) {
            if (is_null($fecha_override)) {
                $plazo_actividad_curso = $this->unidad->curso->plazo_actividad;

                if ($plazo_actividad_curso > 0) {
                    $this->fecha_entrega = $ahora->addDays($plazo_actividad_curso);
                }
            } else {
                $this->fecha_entrega = $fecha_override;
            }
        }

        if (!isset($this->fecha_limite)) {
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
