<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'category_id', 'nombre', 'descripcion', 'slug', 'qualification_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }

    public function users()
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
