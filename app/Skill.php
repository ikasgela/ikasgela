<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Skill extends Model
{
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
        'name', 'description', 'curso_id', 'peso_examen', 'minimo_competencias'
    ];

    public function qualifications()
    {
        return $this
            ->belongsToMany('App\Qualification')
            ->withTimestamps()
            ->withPivot([
                'percentage'
            ]);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
