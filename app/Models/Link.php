<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'url', 'descripcion', 'orden', 'link_collection_id',
    ];

    public function link_collection()
    {
        return $this->belongsTo(LinkCollection::class);
    }
}
