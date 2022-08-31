<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 'descripcion', 'curso_id', '__import_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function links()
    {
        return $this->hasMany(Link::class)
            ->orderBy('orden');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->link_collections()->find($this->id)->pivot;
    }
}
