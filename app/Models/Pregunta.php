<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPregunta
 */
class Pregunta extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['items'];
    protected $clone_exempt_attributes = ['respondida', 'correcta'];

    protected $fillable = [
        'titulo', 'texto', 'multiple', 'imagen', 'cuestionario_id', 'respondida', 'correcta',
        '__import_id',
        'orden',
    ];

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class)->orderBy('orden');
    }

    #[Scope]
    protected function plantilla($query)
    {
        return $query->whereHas('cuestionario', function ($query) {
            $query->where('plantilla', true);
        });
    }
}
