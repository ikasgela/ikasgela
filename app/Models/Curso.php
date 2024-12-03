<?php

namespace App\Models;

use App\Traits\Etiquetas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin IdeHelperCurso
 */
class Curso extends Model
{
    use HasFactory;
    use Etiquetas;
    use HasRelationships;

    protected $fillable = [
        'category_id', 'nombre', 'descripcion', 'slug', 'qualification_id', 'max_simultaneas',
        'fecha_inicio', 'fecha_fin', 'plazo_actividad', 'minimo_entregadas', 'minimo_competencias',
        'minimo_examenes', 'minimo_examenes_finales', 'examenes_obligatorios', 'maximo_recuperable_examenes_finales',
        '__import_id',
        'matricula_abierta',
        'tags',
        'progreso_visible',
        'silence_notifications',
        'tarea_bienvenida_id',
        'normalizar_nota', 'ajuste_proporcional_nota',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime', 'fecha_fin' => 'datetime',
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

    public function safe_exam()
    {
        return $this->hasOne(SafeExam::class);
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
        return $this->hasManyDeep('App\Models\File', ['App\Models\FileResource'], [null, ['uploadable_type', 'uploadable_id']]);
    }

    public function file_uploads_files()
    {
        return $this->hasManyDeep('App\Models\File', ['App\Models\FileUpload'], [null, ['uploadable_type', 'uploadable_id']]);
    }

    public function link_collections()
    {
        return $this->hasMany(LinkCollection::class);
    }

    public function link_collections_links()
    {
        return $this->hasManyThrough('App\Models\Link', 'App\Models\LinkCollection');
    }

    public function selectors()
    {
        return $this->hasMany(Selector::class);
    }

    public function rule_groups()
    {
        return $this->hasManyThrough('App\Models\RuleGroup', 'App\Models\Selector');
    }

    public function rules()
    {
        return $this->hasManyDeep('App\Models\Rule', ['App\Models\Selector', 'App\Models\RuleGroup']);
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

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function alumnos_activos()
    {
        return $this->users()->rolAlumno()->noBloqueado()->orderBy('name')->get();
    }

    public function media(Milestone $milestone = null)
    {
        $users = $this->alumnos_activos();

        $total_actividades_grupo = 0;
        foreach ($users as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base', null, $milestone);
        }
        $media = $users->count() > 0 ? $total_actividades_grupo / $users->count() : 0;

        return $media;
    }

    public function mediana(Milestone $milestone = null)
    {
        $users = $this->alumnos_activos();

        $total_actividades_usuarios = [];
        foreach ($users as $usuario) {
            $total_actividades_usuarios[] = $usuario->num_completadas('base', null, $milestone);
        }
        $mediana = count($total_actividades_usuarios) > 0 ? mediana($total_actividades_usuarios) : 0;

        return $mediana;
    }

    public function token_valido()
    {
        return Str::length($this->safe_exam?->token) == 0
            || Str::contains(Agent::getUserAgent(), "SEB/ikasgela (" . $this->safe_exam?->token . ")");
    }

    public function recuento_enviadas()
    {
        return Tarea::whereHas('actividad.unidad.curso', function ($query) {
            $query->where('cursos.id', $this->id);
        })->usuarioNoBloqueado()->noAutoAvance()->whereIn('estado', [30])->count();
    }

    public function recuento_caducadas()
    {
        return Tarea::whereHas('actividad.unidad.curso', function ($query) {
            $query->where('cursos.id', $this->id);
        })->whereHas('actividad', function ($query) {
            $query->where('fecha_limite', '<', now());
        })->usuarioNoBloqueado()->noAutoAvance()->whereNotIn('estado', [30, 40, 60, 62, 64])->count();
    }
}
