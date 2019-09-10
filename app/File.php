<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'path', 'title', 'size', 'file_upload_id', 'user_id'
    ];

    public $appends = ['url', 'uploaded_time', 'size_in_kb'];

    public function getUrlAttribute()
    {
        return Storage::disk('s3')->temporaryUrl($this->path, Carbon::now()->addMinutes(5));
    }

    public function imageUrl($path = '')
    {
        return Storage::disk('s3')->temporaryUrl($path . '/' . $this->path, Carbon::now()->addMinutes(5));
    }

    public function getUploadedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getSizeInKbAttribute()
    {
        return round($this->size / 1024, 2);
    }

    public function file_upload()
    {
        return $this->belongsTo(FileUpload::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
