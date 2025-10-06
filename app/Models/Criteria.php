<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperCriteria
 */
class Criteria extends Model
{
    use HasFactory;
    use Cloneable;
    use SoftDeletes;
    use HasCachedQueries;

    protected $clone_exempt_attributes = ['seleccionado'];

    protected $fillable = [
        'texto', 'puntuacion', 'orden', 'seleccionado',
        'criteria_group_id',
        '__import_id',
    ];

    public function criteria_group()
    {
        return $this->belongsTo(CriteriaGroup::class);
    }

    #[Scope]
    protected function plantilla($query)
    {
        return $query->whereHas('criteria_group.rubric', function ($query) {
            $query->where('plantilla', true);
        });
    }
}
