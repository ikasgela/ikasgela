<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


/**
 * @mixin IdeHelperMilestone
 */
class Milestone extends Model
{
    use HasFactory;
    use Cloneable;
    use Cachable;

    protected $fillable = [
        'name', 'date', 'start_date', 'published', 'curso_id',
        '__import_id',
        'decimals', 'truncate',
        'normalizar_nota', 'ajuste_proporcional_nota',
        'etiquetas', 'num_actividades_obligatorias',
        'qualification_id',
    ];

    public function getFullNameAttribute()
    {
        return is_null($this->date) ? $this->name : $this->name . " - " . $this->date->format('d/m/Y');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Cualificación propia del milestone (R-EVAL-20). Si existe, el cálculo de
     * la nota la usa como cualificación base en lugar de la del curso.
     */
    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    /**
     * Etiquetas (CSV) con las que se cuentan las actividades obligatorias del
     * alumno en este milestone. Por defecto se usa 'base'.
     *
     * @return array
     */
    public function getEtiquetasObligatoriasAttribute()
    {
        $etiquetas = array_values(array_filter(array_map('trim', explode(',', (string) $this->etiquetas))));

        return $etiquetas ?: ['base'];
    }

    public function getCacheKeyAttribute()
    {
        return "_" . Str::slug($this->name) . "_" . ($this->date ?? $this->start_date)?->timestamp ?? 0;
    }

    #[Scope]
    protected function published($query)
    {
        return $query->where('published', true);
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'start_date' => 'datetime',
            'num_actividades_obligatorias' => 'integer',
        ];
    }

    public function duplicar(?Curso $curso_destino)
    {
        $clon = $this->duplicate();
        if (is_null($curso_destino)) {
            $clon->name = $clon->name . " (" . __("Copy") . ')';
        } else {
            $clon->curso_id = $curso_destino->id;
        }
        $clon->published = false;
        $clon->save();

        return $clon;
    }
}
