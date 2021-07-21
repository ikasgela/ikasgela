<?php

namespace App\Models;

use App\Traits\Etiquetas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Unidad extends Model
{
    use HasFactory;
    use Etiquetas;
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'unidad';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $table = 'unidades';

    protected $fillable = [
        'curso_id', 'codigo', 'nombre', 'descripcion', 'slug', 'qualification_id', 'orden', 'tags',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite', 'minimo_entregadas',
        '__import_id',
    ];

    public function getFullNameAttribute()
    {
        $full_name = $this->nombre;

        if (!is_null($this->codigo)) {
            $full_name = $this->codigo . ' - ' . $full_name;
        }

        return $full_name;
    }

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function scopeOrganizacionActual($query)
    {
        return $query->whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    public function scopeCursoActual($query)
    {
        return $query->where('curso_id', setting_usuario('curso_actual'));
    }

    public function num_actividades($etiqueta, $plantilla = true)
    {
        $total = 0;

        foreach ($this->actividades()->where('plantilla', $plantilla)->get() as $actividad) {
            if ($actividad->hasEtiqueta($etiqueta))
                $total += 1;
        }

        return $total;
    }

    public function puntos()
    {
        $total = 0;

        foreach ($this->actividades()->where('plantilla', true)->get() as $actividad) {
            $total += $actividad->puntos();
        }

        return $total;
    }
}
