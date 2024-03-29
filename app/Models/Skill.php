<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

/**
 * @mixin IdeHelperSkill
 */
class Skill extends Model
{
    use HasFactory;
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'skill';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'name', 'description', 'curso_id', 'peso_examen', 'minimo_competencias',
        '__import_id',
    ];

    public function getFullNameAttribute()
    {
        return $this->curso->full_name . ' - '
            . $this->name;
    }

    public function qualifications()
    {
        return $this
            ->belongsToMany('App\Models\Qualification')
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
