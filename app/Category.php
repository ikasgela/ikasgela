<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Category extends Model
{
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag = 'category';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'period_id', 'name', 'slug'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
