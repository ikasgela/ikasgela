<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'slug'
    ];

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }
}
