<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperItem
 */
class Item extends Model
{
    use HasFactory;
    use Cloneable;

    protected $clone_exempt_attributes = ['seleccionado'];

    protected $fillable = [
        'texto', 'correcto', 'seleccionado', 'feedback', 'orden', 'pregunta_id',
        '__import_id',
    ];

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }

    #[Scope]
    protected function plantilla($query)
    {
        return $query->whereHas('pregunta.cuestionario', function ($query) {
            $query->where('plantilla', true);
        });
    }
}
