<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Actividad extends Model
{
    use Cloneable;
    use LogsActivity;
    use SoftDeletes;

    protected $cloneable_relations = [
        'intellij_projects',
        'youtube_videos',
        'markdown_texts',
        'cuestionarios',
        'file_uploads'
    ];

    protected $clone_exempt_attributes = ['plantilla'];

    protected $table = 'actividades';

    protected $fillable = [
        'unidad_id', 'nombre', 'descripcion', 'puntuacion', 'plantilla', 'slug', 'final', 'siguiente', 'auto_avance', 'qualification_id', 'orden',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite', 'destacada', 'tags'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite'
    ];

    public function setCloneableRelations($relations)
    {
        $this->cloneable_relations = $relations;
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
                'fork'
            ]);
    }

    public function siguiente()
    {
        return $this->hasOne(Actividad::class, 'siguiente_id');
    }

    public function anterior()
    {
        return $this->belongsTo(Actividad::class, 'siguiente_id');
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

    public function etiquetas()
    {
        return array_map('trim', explode(',', $this->tags));
    }

    public function hasEtiqueta($etiqueta)
    {
        return in_array($etiqueta, $this->etiquetas());
    }
}
