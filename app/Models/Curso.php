<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Watson\Rememberable\Rememberable;

class Curso extends Model
{
    use HasFactory;
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag;

    use HasRelationships;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'curso';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'category_id', 'nombre', 'descripcion', 'slug', 'qualification_id', 'max_simultaneas',
        'fecha_inicio', 'fecha_fin', 'plazo_actividad', 'minimo_entregadas', 'minimo_competencias',
        'minimo_examenes', 'examenes_obligatorios', 'maximo_recuperable_examenes_finales',
        '__import_id',
        'matricula_abierta',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
        'fecha_inicio', 'fecha_fin'
    ];

    public function getFullNameAttribute()
    {
        $full_name = $this->nombre;

        if (!is_null($this->category)) {
            $full_name = $this->category->period->name . ' - ' . $full_name;
            $full_name = $this->category->period->organization->name . ' - ' . $full_name;
        }

        return $full_name;
    }

    public function getPrettyNameAttribute()
    {
        $full_name = $this->nombre;

        if (!is_null($this->category)) {
            $full_name = $this->category->period->name . ' » ' . $full_name;
            $full_name = $this->category->period->organization->name . ' » ' . $full_name;
        }

        return $full_name;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }

    public function users()
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function feedbacks()
    {
        return $this->morphMany(Feedback::class, 'comentable');
    }

    public function scopeOrganizacionActual($query)
    {
        return $query->whereHas('category.period.organization', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    public function scopePeriodoActual($query)
    {
        return $query->whereHas('category.period', function ($query) {
            $query->where('periods.id', setting_usuario('_period_id'));
        });
    }

    public function profesores()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        });
    }

    public function actividades()
    {
        return $this->hasManyThrough('App\Models\Actividad', 'App\Models\Unidad');
    }

    public function disponible()
    {
        return isset($this->fecha_inicio) && $this->fecha_inicio->lt(now())
            && isset($this->fecha_fin) && $this->fecha_fin->gt(now());
    }

    public function intellij_projects()
    {
        return $this->hasMany(IntellijProject::class);
    }

    public function youtube_videos()
    {
        return $this->hasMany(YoutubeVideo::class);
    }

    public function markdown_texts()
    {
        return $this->hasMany(MarkdownText::class);
    }

    public function cuestionarios()
    {
        return $this->hasMany(Cuestionario::class);
    }

    public function preguntas()
    {
        return $this->hasManyThrough('App\Models\Pregunta', 'App\Models\Cuestionario');
    }

    public function items()
    {
        return $this->hasManyDeep('App\Models\Item', ['App\Models\Cuestionario', 'App\Models\Pregunta']);
    }

    public function file_uploads()
    {
        return $this->hasMany(FileUpload::class);
    }

    public function file_resources()
    {
        return $this->hasMany(FileResource::class);
    }

    public function file_resources_files()
    {
        return $this->hasManyDeep('App\Models\File', ['App\Models\FileResource'], [null, ['file_upload_type', 'file_upload_id']]);
    }

    public function file_uploads_files()
    {
        return $this->hasManyDeep('App\Models\File', ['App\Models\FileUpload'], [null, ['file_upload_type', 'file_upload_id']]);
    }

    public function hilos()
    {
        return $this->hasMany(Hilo::class);
    }

    public function groups()
    {
        return $this
            ->belongsToMany(Group::class)
            ->withTimestamps();
    }

    public function teams()
    {
        return $this->hasManyDeep('App\Models\Team', ['curso_group', 'App\Models\Group']);
    }
}