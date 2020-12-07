<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'path', 'title', 'size', 'file_upload_id', 'user_id', 'file_upload_type', 'description', 'archived'
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

    public function file_upload()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
