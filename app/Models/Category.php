<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Category extends Model
{
    use HasFactory;
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'category';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'period_id', 'name', 'slug'
    ];

    public function getFullNameAttribute()
    {
        return $this->period->organization->name . ' - '
            . $this->period->name . ' - '
            . $this->name;
    }

    public function getPrettyNameAttribute()
    {
        return $this->period->organization->name . ' » '
            . $this->period->name . ' » '
            . $this->name;
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
