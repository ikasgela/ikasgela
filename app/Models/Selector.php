<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selector extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 'descripcion', 'curso_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function rule_groups()
    {
        return $this->hasMany(RuleGroup::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->selectors()->find($this->id)->pivot;
    }
}
