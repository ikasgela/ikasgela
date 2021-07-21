<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Group extends Model
{
    use HasFactory;
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'group';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'period_id', 'name', 'slug'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function getFullNameAttribute()
    {
        return $this->period->organization->name . ' - '
            . $this->period->name . ' - '
            . $this->name;
    }

    public function cursos()
    {
        return $this
            ->belongsToMany(Curso::class)
            ->withTimestamps();
    }
}
