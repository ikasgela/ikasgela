<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;



/**
 * @mixin IdeHelperSkill
 */
class Skill extends Model
{
    use HasFactory;
    use Cachable;

    protected $fillable = [
        'name', 'description', 'curso_id', 'peso_examen', 'minimo_competencias',
        '__import_id',
    ];

    public function getFullNameAttribute()
    {
        if (!$this->relationLoaded('curso') || is_null($this->curso)) {
            return $this->name;
        }

        return $this->curso->full_name . ' - ' . $this->name;
    }

    public function qualifications()
    {
        return $this
            ->belongsToMany(Qualification::class)
            ->withTimestamps()
            ->withPivot([
                'percentage',
                'orden',
            ]);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
