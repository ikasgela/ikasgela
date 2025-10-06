<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperGroup
 */
class Group extends Model
{
    use HasFactory;
    use HasCachedQueries;

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

    public function getPrettyNameAttribute()
    {
        return $this->period->organization->name . ' » '
            . $this->period->name . ' » '
            . $this->name;
    }

    public function cursos()
    {
        return $this
            ->belongsToMany(Curso::class)
            ->withTimestamps();
    }
}
