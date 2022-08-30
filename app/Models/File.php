<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperFile
 */
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'title', 'size', 'uploadable_id', 'user_id', 'uploadable_type', 'description', 'archived', 'extension',
        'orden',
    ];

    public $appends = ['url', 'uploaded_time', 'size_in_kb'];

    public function getUrlAttribute()
    {
        return Storage::disk('s3')->temporaryUrl($this->path, now()->addDays(2));
    }

    public function imageUrl($path = '')
    {
        return Storage::disk('s3')->temporaryUrl($path . '/' . $this->path, now()->addDays(2));
    }

    public function getUploadedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getSizeInKbAttribute()
    {
        return formato_decimales($this->size / 1024, 2);
    }

    public function uploadable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function delete()
    {
        $contenedor = pathinfo($this->path, PATHINFO_DIRNAME);

        if ($contenedor == '.') {
            Storage::disk('s3')->delete('images/' . $this->path);
            Storage::disk('s3')->delete('thumbnails/' . $this->path);
            Storage::disk('s3')->delete('documents/' . $this->path);
        } else {
            Storage::disk('s3')->deleteDir('images/' . $contenedor);
            Storage::disk('s3')->deleteDir('thumbnails/' . $contenedor);
            Storage::disk('s3')->deleteDir('documents/' . $contenedor);
        }

        return parent::delete();
    }
}
