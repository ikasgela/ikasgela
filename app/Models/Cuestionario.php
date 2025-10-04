<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperCuestionario
 */
class Cuestionario extends Model
{
    use HasFactory;
    use Cloneable;
    use HasCachedQueries;

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

    #[Scope]
    protected function plantilla($query)
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

    public function duplicar(?Curso $curso_destino)
    {
        $clon = $this->duplicate();
        if (is_null($curso_destino)) {
            $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        } else {
            $clon->curso_id = $curso_destino->id;
        }
        $clon->plantilla = $this->plantilla;
        $clon->save();

        return $clon;
    }
}
