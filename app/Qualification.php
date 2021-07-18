<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Qualification extends Model
{
    use HasFactory;
    use Cloneable;
    use Rememberable;

    protected $cloneable_relations = ['skills'];

    protected $clone_exempt_attributes = ['template'];

    protected $rememberFor;
    protected $rememberCacheTag;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'qualification';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

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

    public function scopeCursoActual($query)
    {
        return $query->where('curso_id', setting_usuario('curso_actual'));
    }

    public function scopePlantilla($query)
    {
        return $query->where('template', true);
    }
}
