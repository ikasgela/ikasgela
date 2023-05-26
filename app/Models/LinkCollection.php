<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperLinkCollection
 */
class LinkCollection extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['links'];

    protected $fillable = [
        'titulo', 'descripcion', 'curso_id', '__import_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function links()
    {
        return $this->hasMany(Link::class)
            ->orderBy('orden');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->link_collections()->find($this->id)->pivot;
    }
}
