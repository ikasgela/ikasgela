<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Posición de una plantilla dentro del grafo de encadenado (vista de nodos).
 *
 * No es un dato de dominio: es la preferencia de maquetación del grafo, por lo que
 * no usa SoftDeletes (borrar la fila solo significa "volver a la posición por defecto").
 *
 * @mixin IdeHelperActividadGrafo
 */
class ActividadGrafo extends Model
{
    protected $table = 'actividad_grafos';

    protected $fillable = [
        'actividad_id',
        'posicion_x',
        'posicion_y',
    ];

    protected $casts = [
        'posicion_x' => 'float',
        'posicion_y' => 'float',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}
