<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperQualification
 */
class Qualification extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['skills'];

    protected $clone_exempt_attributes = ['template'];

    protected $fillable = [
        'name', 'description', 'template', 'curso_id',
        '__import_id',
    ];

    public function getFullNameAttribute()
    {
        return $this->curso->full_name . ' - '
            . $this->name;
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function skills()
    {
        return $this
            ->belongsToMany(Skill::class)
            ->withTimestamps()
            ->withPivot([
                'percentage',
                'orden',
            ]);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }

    #[Scope]
    protected function cursoActual($query)
    {
        return $query->where('curso_id', setting_usuario('curso_actual'));
    }

    #[Scope]
    protected function plantilla($query)
    {
        return $query->where('template', true);
    }

    public function duplicar(?Curso $curso_destino)
    {
        $clon = $this->duplicate();
        if (is_null($curso_destino)) {
            $clon->name = $clon->name . " (" . __("Copy") . ')';
        } else {
            $clon->curso_id = $curso_destino->id;
        }
        $clon->template = $this->template;
        $clon->save();

        // Si copiamos a otro curso, recorrer y cambiar el curso de las skills asociadas
        if (!is_null($curso_destino)) {
            foreach ($clon->skills as $skill) {
                $skill->curso_id = $clon->curso_id;
                $skill->save();
            }
        }
        return $clon;
    }
}
