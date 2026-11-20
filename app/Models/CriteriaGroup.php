<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @mixin IdeHelperCriteriaGroup
 */
class CriteriaGroup extends Model
{
    use HasFactory;
    use Cloneable;
    use SoftDeletes;
    use Cachable;

    protected $cloneable_relations = ['criterias'];

    protected $fillable = [
        'titulo', 'descripcion', 'orden',
        'rubric_id',
        'cabecera_horizontal',
        '__import_id',
    ];

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    public function criterias()
    {
        // `id` como desempate: orden determinista incluso con `orden` empatados
        // (restos de la remapa COMB→v7, ADR-005)
        return $this->hasMany(Criteria::class)
            ->orderBy('orden')
            ->orderBy('id');
    }

    #[Scope]
    protected function plantilla($query)
    {
        return $query->whereHas('rubric', function ($query) {
            $query->where('plantilla', true);
        });
    }

    public function getTotalAttribute()
    {
        return $this->criterias()->where('seleccionado', true)->sum('puntuacion');
    }
}
