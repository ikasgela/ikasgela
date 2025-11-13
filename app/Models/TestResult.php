<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperTestResult
 */
class TestResult extends Model
{
    use HasFactory;
    use Cloneable;
    use HasCachedQueries;

    protected $cloneable_relations = [];
    protected $clone_exempt_attributes = ['plantilla', 'completado', 'num_correctas', 'num_incorrectas'];

    protected $fillable = [
        'titulo', 'descripcion', 'plantilla', 'completado',
        'num_preguntas', 'valor_correcta', 'valor_incorrecta', 'num_correctas', 'num_incorrectas',
        'curso_id',
        '__import_id',
    ];

    public function resultado()
    {
        if ($this->num_preguntas > 0) {
            $total = $this->num_correctas * $this->valor_correcta + $this->num_incorrectas * $this->valor_incorrecta;
            $nota = $total / $this->num_preguntas * 100;
            return formato_decimales($nota, 0);
        } else {
            return "?";
        }
    }

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

    #[Scope]
    protected function plantilla($query)
    {
        return $query->where('plantilla', true);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->test_results()->find($this->id)->pivot;
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
