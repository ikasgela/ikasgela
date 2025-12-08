<?php

namespace App\Models;

use App\Traits\Etiquetas;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperCurso
 */
class Curso extends Model
{
    use HasFactory;
    use Etiquetas;
    use HasRelationships;
    use HasCachedQueries;

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
        'mostrar_calificaciones',
        'gitea_organization',
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

    #[Scope]
    protected function organizacionActual($query)
    {
        return $query->whereHas('category.period.organization', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    #[Scope]
    protected function periodoActual($query)
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
        return $this->hasManyThrough(Actividad::class, Unidad::class);
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
        return $this->hasManyThrough(Pregunta::class, Cuestionario::class);
    }

    public function items()
    {
        return $this->hasManyDeep(Item::class, [Cuestionario::class, Pregunta::class]);
    }

    public function rubrics()
    {
        return $this->hasMany(Rubric::class);
    }

    public function criteria_groups()
    {
        return $this->hasManyThrough(CriteriaGroup::class, Rubric::class);
    }

    public function criterias()
    {
        return $this->hasManyDeep(Criteria::class, [Rubric::class, CriteriaGroup::class]);
    }

    public function flash_decks()
    {
        return $this->hasMany(FlashDeck::class);
    }

    public function flash_cards()
    {
        return $this->hasManyThrough(FlashCard::class, FlashDeck::class);
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
        return $this->hasManyDeep(File::class, [FileResource::class], [null, ['uploadable_type', 'uploadable_id']]);
    }

    public function file_uploads_files()
    {
        return $this->hasManyDeep(File::class, [FileUpload::class], [null, ['uploadable_type', 'uploadable_id']]);
    }

    public function link_collections()
    {
        return $this->hasMany(LinkCollection::class);
    }

    public function link_collections_links()
    {
        return $this->hasManyThrough(Link::class, LinkCollection::class);
    }

    public function selectors()
    {
        return $this->hasMany(Selector::class);
    }

    public function rule_groups()
    {
        return $this->hasManyThrough(RuleGroup::class, Selector::class);
    }

    public function rules()
    {
        return $this->hasManyDeep(Rule::class, [Selector::class, RuleGroup::class]);
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
        return $this->hasManyDeep(Team::class, ['curso_group', Group::class]);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function alumnos_activos()
    {
        return $this->users()->rolAlumno()->noBloqueado()->orderBy('name')->get();
    }

    public function test_results()
    {
        return $this->hasMany(TestResult::class);
    }

    public function media(?Milestone $milestone = null)
    {
        $users = $this->alumnos_activos();

        $total_actividades_grupo = 0;
        foreach ($users as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base', null, $milestone);
        }
        $media = $users->count() > 0 ? $total_actividades_grupo / $users->count() : 0;

        return $media;
    }

    public function mediana(?Milestone $milestone = null)
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
        return Cache::tags('curso_' . $this->id)
            ->remember('recuento_enviadas', config('ikasgela.eloquent_cache_time'), function () {
                return Tarea::whereHas('actividad.unidad.curso', function ($query) {
                    $query->where('cursos.id', $this->id);
                })->usuarioNoBloqueado()->noAutoAvance()->whereIn('estado', [30])->count();
            });
    }

    public function recuento_caducadas()
    {
        return Cache::tags('curso_' . $this->id)
            ->remember('recuento_caducadas', config('ikasgela.eloquent_cache_time'), function () {
                return Tarea::whereHas('actividad.unidad.curso', function ($query) {
                    $query->where('cursos.id', $this->id);
                })->whereHas('actividad', function ($query) {
                    $query->whereNull('deleted_at')->where('fecha_limite', '<', now());
                })->usuarioNoBloqueado()->noAutoAvance()->whereNotIn('estado', [30, 40, 60, 62, 64])->count();
            });
    }

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime:Y-m-d H:i:s',
            'fecha_fin' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
