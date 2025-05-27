<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Attributes\Scope;
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
        return $this->morphMany(File::class, 'uploadable');
    }

    #[Scope]
    protected function plantilla($query)
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

    public function delete_with_files()
    {
        foreach ($this->files as $file) {
            $file->delete();
        }

        $this->delete();
    }
}
