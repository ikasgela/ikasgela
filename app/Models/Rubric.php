<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['criteria_groups'];
    protected $clone_exempt_attributes = ['plantilla', 'completada'];

    protected $fillable = [
        'titulo', 'descripcion', 'plantilla', 'completada',
        'curso_id',
        '__import_id',
    ];

    public function criteria_groups()
    {
        return $this->hasMany(CriteriaGroup::class)
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
}
