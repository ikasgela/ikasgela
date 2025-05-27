<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperFileResource
 */
class FileResource extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['files'];

    protected $fillable = [
        'titulo', 'descripcion',
        '__import_id', 'curso_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function files()
    {
        return $this->morphMany(File::class, 'uploadable')
            ->orderBy('orden');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->file_resources()->find($this->id)->pivot;
    }
}
