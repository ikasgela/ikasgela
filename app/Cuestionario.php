<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    use Cloneable;

    protected $cloneable_relations = ['preguntas'];
    protected $clone_exempt_attributes = ['plantilla', 'respondido'];

    protected $fillable = [
        'titulo', 'descripcion', 'plantilla', 'respondido'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }

    public function scopePlantilla($query)
    {
        return $query->where('plantilla', true);
    }
}
