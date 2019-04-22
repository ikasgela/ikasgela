<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'category_id', 'nombre', 'descripcion', 'slug'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }
}
