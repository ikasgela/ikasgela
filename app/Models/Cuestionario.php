<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCuestionario
 */
class Cuestionario extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['preguntas'];
    protected $clone_exempt_attributes = ['plantilla', 'respondido'];

    protected $fillable = [
        'titulo', 'descripcion', 'plantilla', 'respondido',
        '__import_id', 'curso_id',
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

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->cuestionarios()->find($this->id)->pivot;
    }
}
