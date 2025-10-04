<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperFlashDeck
 */
class FlashDeck extends Model
{
    use HasFactory;
    use Cloneable;
    use HasCachedQueries;

    protected $cloneable_relations = ['flash_cards'];
    protected $clone_exempt_attributes = ['plantilla', 'completado', 'posicion'];

    protected $fillable = [
        'titulo', 'descripcion', 'plantilla', 'completado', 'posicion',
        'curso_id',
        '__import_id',
    ];

    public function flash_cards()
    {
        return $this->hasMany(FlashCard::class)
            ->orderBy('orden');
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
        return $actividad->rubrics()->find($this->id)->pivot;
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
