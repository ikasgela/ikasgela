<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function duplicar(?Curso $curso_destino)
    {
        $clon = $this->duplicate();
        if (is_null($curso_destino)) {
            $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        } else {
            $clon->curso_id = $curso_destino->id;
        }
        $clon->save();

        // Recorrer y duplicar los ficheros en S3
        foreach ($clon->files as $file) {
            $old_path = $file->path;
            $filename = basename((string)$old_path);
            $new_path = md5(time()) . '/' . $filename;

            Storage::disk('s3')->copy('documents/' . $old_path, 'documents/' . $new_path);

            $file->path = $new_path;
            $file->save();
        }

        return $clon;
    }
}
