<?php

namespace App\Models;

use App\Traits\Etiquetas;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


/**
 * @mixin IdeHelperUnidad
 */
class Unidad extends Model
{
    use HasFactory;
    use Etiquetas;
    use Cachable;

    protected $table = 'unidades';

    protected $fillable = [
        'curso_id', 'codigo', 'nombre', 'descripcion', 'slug', 'qualification_id', 'orden', 'tags',
        'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite', 'minimo_entregadas',
        'visible',
        '__import_id',
    ];

    public function getFullNameAttribute()
    {
        $full_name = $this->nombre;

        if (!empty($this->codigo)) {
            $full_name = $this->codigo . ' - ' . $full_name;
        }

        return $full_name;
    }

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

    #[Scope]
    protected function organizacionActual($query)
    {
        return $query->whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    #[Scope]
    protected function cursoActual($query)
    {
        return $query->where('curso_id', setting_usuario('curso_actual'));
    }

    public function num_actividades($etiqueta, $plantilla = true)
    {
        return $this->actividades()
            ->where('plantilla', $plantilla)
            ->tag($etiqueta)
            ->count();
    }

    public function puntos()
    {
        return (float) $this->actividades()
            ->where('plantilla', true)
            ->selectRaw('COALESCE(SUM(puntuacion * COALESCE(multiplicador, 1)), 0) as total')
            ->value('total');
    }

    protected function casts(): array
    {
        return [
            'fecha_disponibilidad' => 'datetime:Y-m-d H:i:s',
            'fecha_entrega' => 'datetime:Y-m-d H:i:s',
            'fecha_limite' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
