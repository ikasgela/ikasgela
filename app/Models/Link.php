<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin IdeHelperLink
 */
class Link extends Model
{
    use HasFactory;
    use Cloneable;
    use Cachable;

    protected $fillable = [
        'url', 'descripcion', 'orden', 'link_collection_id',
    ];

    public function link_collection()
    {
        return $this->belongsTo(LinkCollection::class);
    }
}
