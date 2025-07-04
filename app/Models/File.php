<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Override;

/**
 * @mixin IdeHelperFile
 */
class File extends Model
{
    use HasFactory;
    use Cloneable;

    protected $fillable = [
        'path', 'title', 'size', 'uploadable_id', 'user_id', 'uploadable_type', 'description', 'archived', 'extension',
        'orden',
        'visible',
    ];

    public $appends = ['url', 'uploaded_time', 'size_in_kb'];

    public function getUrlAttribute()
    {
        return Storage::disk('s3-urls')->temporaryUrl($this->path, now()->addDays(2));
    }

    public function imageUrl($path = '')
    {
        return Storage::disk('s3-urls')->temporaryUrl($path . '/' . $this->path, now()->addDays(2));
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

    #[Override]
    public function delete()
    {
        $contenedor = pathinfo($this->path, PATHINFO_DIRNAME);

        if ($contenedor == '.') {
            Storage::disk('s3')->delete('images/' . $this->path);
            Storage::disk('s3')->delete('thumbnails/' . $this->path);
            Storage::disk('s3')->delete('documents/' . $this->path);
        } else {
            Storage::disk('s3')->deleteDirectory('images/' . $contenedor);
            Storage::disk('s3')->deleteDirectory('thumbnails/' . $contenedor);
            Storage::disk('s3')->deleteDirectory('documents/' . $contenedor);
        }

        return parent::delete();
    }
}
