<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperTeam
 */
class Team extends Model
{
    use HasFactory;
    use HasCachedQueries;

    protected $fillable = [
        'group_id', 'name', 'slug'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function users()
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        if (!$this->relationLoaded('group') || is_null($this->group)) {
            return $this->name;
        }

        return $this->group->full_name . ' - ' . $this->name;
    }

    public function getPrettyNameAttribute()
    {
        if (!$this->relationLoaded('group') || is_null($this->group)) {
            return $this->name;
        }

        return $this->group->pretty_name . ' » ' . $this->name;
    }
}
