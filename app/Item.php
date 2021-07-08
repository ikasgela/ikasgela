<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
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

    public function scopePlantilla($query)
    {
        return $query->whereHas('pregunta.cuestionario', function ($query) {
            $query->where('plantilla', true);
        });
    }
}
