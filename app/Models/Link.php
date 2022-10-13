<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;
    use Cloneable;

    protected $fillable = [
        'url', 'descripcion', 'orden', 'link_collection_id',
    ];

    public function link_collection()
    {
        return $this->belongsTo(LinkCollection::class);
    }
}
