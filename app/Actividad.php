<?php

namespace App;

use App\Traits\Etiquetas;
use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;

class Actividad extends Model
{
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
    ];

    protected $clone_exempt_attributes = ['plantilla', 'siguiente_overriden'];

    protected $table = 'actividades';

    protected $fillable = [
        'unidad_id', 'nombre', 'descripcion', 'puntuacion', 'plantilla', 'slug', 'final', 'siguiente_id', 'auto_avance', 'qualification_id', 'orden',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite', 'destacada', 'tags', 'multiplicador', 'siguiente_overriden',
        '__import_id',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite'
    ];

    public function setCloneableRelations($relations)
    {
        $this->cloneable_relations = $relations;
    }

    public function onCloned($src)
    {
        if ($this->intellij_projects()->count() > 1) {
            $intellij_project_ids = $this->intellij_projects()->get()->pluck('id')->toArray();

            $random = array_rand($intellij_project_ids);

            $this->intellij_projects()->detach($intellij_project_ids[$random]);
        }

        if (!is_null($src->qualification)) {
            $cualificacion = $src->qualification->duplicate();
            $cualificacion->name .= " - " . $this->nombre . ' (' . $this->id . ')';
            $cualificacion->save();
            $this->save(['qualification_id' => $cualificacion]);
        }

        foreach ($src->cuestionarios as $cuestionario) {
            $copia = $cuestionario->duplicate();
            $this->cuestionarios()->detach($cuestionario);
            $this->cuestionarios()->attach($copia);
        }

        foreach ($src->file_uploads as $file_upload) {
            $copia = $file_upload->duplicate();
            $this->file_uploads()->detach($file_upload);
            $this->file_uploads()->attach($copia);
        }
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'tareas')
            ->using('App\Tarea')
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
            ->withTimestamps();
    }

    public function cuestionarios()
    {
        return $this
            ->belongsToMany(Cuestionario::class)
            ->withTimestamps();
    }

    public function scopePlantilla($query)
    {
        return $query->where('plantilla', true);
    }

    public function scopeCursoActual($query)
    {
        return $query->whereHas('unidad.curso', function ($query) {
            $query->where('cursos.id', setting_usuario('curso_actual'));
        });
    }

    public function file_uploads()
    {
        return $this
            ->belongsToMany(FileUpload::class)
            ->withTimestamps();
    }

    public function file_resources()
    {
        return $this
            ->belongsToMany(FileResource::class)
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
        return $this->morphMany('App\Feedback', 'curso');
    }

    public function scopeEnPlazo($query)
    {
        return $query->where(function ($query) {
            $query->where('fecha_disponibilidad', '<=', now())->orWhereNull('fecha_disponibilidad');
        })->where(function ($query) {
            $query->where('fecha_limite', '>=', now())->orWhereNull('fecha_limite');
        });
    }

    public function scopeEnPlazoOrCorregida($query)
    {
        return $query->where(function ($query) {
            $query->where('fecha_disponibilidad', '<=', now())->orWhereNull('fecha_disponibilidad')
                ->orWhereIn('estado', [40, 41, 42]);
        })->where(function ($query) {
            $query->where('fecha_limite', '>=', now())->orWhereNull('fecha_limite')
                ->orWhereIn('estado', [40, 41, 42]);
        });
    }

    public function scopeCaducada($query)
    {
        return $query->where('fecha_limite', '<', now());
    }

    public function scopeEstados($query, $estados)
    {
        return $query->whereHas('users', function ($q) use ($estados) {
            $q->where('user_id', Auth::user()->id)->whereIn('estado', $estados);
        });
    }

    public function scopeAutoAvance($query)
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
        return isset($this->fecha_limite) && $this->fecha_limite < now()
            || !isset($this->fecha_limite) && isset($this->fecha_entrega) && $this->fecha_entrega < now();
    }

    public function teams()
    {
        return $this
            ->belongsToMany(Team::class)
            ->withTimestamps();
    }
}
