<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use Cloneable;

    protected $cloneable_relations = ['skills'];

    protected $clone_exempt_attributes = ['template'];

    protected $fillable = [
        'name', 'description', 'template', 'organization_id'
    ];

    public function skills()
    {
        return $this
            ->belongsToMany(Skill::class)
            ->withTimestamps()
            ->withPivot([
                'percentage'
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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
