<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarkdownText extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'repositorio', 'rama', 'archivo'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }
}
