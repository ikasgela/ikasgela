<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use Cloneable;

    protected $clone_exempt_attributes = ['plantilla'];

    protected $fillable = [
        'titulo', 'descripcion', 'max_files', 'plantilla'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function files()
    {
        return $this->morphMany('App\File', 'file_upload');
    }

    public function scopePlantilla($query)
    {
        return $query->where('plantilla', true);
    }
}
