<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
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

    protected $cloneable_relations = ['criterias'];

    protected $fillable = [
        'titulo', 'descripcion', 'orden', 'desactivado',
        'rubric_id',
        '__import_id',
    ];

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    public function criterias()
    {
        return $this->hasMany(Criteria::class)
            ->orderBy('orden');
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
