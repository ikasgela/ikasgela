<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $fillable = [
        'name', 'description', 'template'
    ];

    public function skills()
    {
        return $this
            ->belongsToMany(Skill::class)
            ->withTimestamps()
            ->withPivot([
                'percentage'
            ]);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
