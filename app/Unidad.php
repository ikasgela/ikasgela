<?php

namespace App;

use App\Traits\Etiquetas;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use Etiquetas;

    protected $table = 'unidades';

    protected $fillable = [
        'curso_id', 'codigo', 'nombre', 'descripcion', 'slug', 'qualification_id', 'orden', 'tags'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function scopeOrganizacionActual($query)
    {
        return $query->whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    public function scopeCursoActual($query)
    {
        return $query->where('curso_id', setting_usuario('curso_actual'));
    }

    public function num_actividades($etiqueta)
    {
        $total = 0;

        foreach ($this->actividades()->where('plantilla', true)->get() as $actividad) {
            if ($actividad->hasEtiqueta($etiqueta))
                $total += 1;
        }

        return $total;
    }
}
