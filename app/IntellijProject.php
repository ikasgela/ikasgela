<?php

namespace App;

use GitLab;
use Illuminate\Database\Eloquent\Model;

class IntellijProject extends Model
{
    protected $fillable = [
        'repositorio'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function gitlab()
    {
        if (!$this->isForked())
            return GitLab::projects()->show($this->repositorio);
        else
            return GitLab::projects()->show($this->pivot->fork);
    }

    public function isForked()
    {
        return isset($this->pivot->fork) && strlen($this->pivot->fork) > 2;
    }
}
