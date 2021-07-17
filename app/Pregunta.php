<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['items'];
    protected $clone_exempt_attributes = ['plantilla', 'respondida', 'correcta'];

    protected $fillable = [
        'titulo', 'texto', 'multiple', 'imagen', 'cuestionario_id', 'respondida', 'correcta',
        '__import_id',
    ];

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class)->orderBy('orden');
    }

    public function scopePlantilla($query)
    {
        return $query->whereHas('cuestionario', function ($query) {
            $query->where('plantilla', true);
        });
    }
}
