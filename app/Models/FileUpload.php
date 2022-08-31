<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperFileUpload
 */
class FileUpload extends Model
{
    use HasFactory;
    use Cloneable;

    protected $clone_exempt_attributes = ['plantilla'];

    protected $fillable = [
        'titulo', 'descripcion', 'max_files', 'plantilla',
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
        return $this->morphMany('App\Models\File', 'uploadable');
    }

    public function scopePlantilla($query)
    {
        return $query->where('plantilla', true);
    }

    public function getNotArchivedFilesAttribute()
    {
        return $this->files()->where('archived', false)->get();
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->file_uploads()->find($this->id)->pivot;
    }
}
