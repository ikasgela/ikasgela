<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Cohensive\Embed\Facades\Embed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperYoutubeVideo
 */
class YoutubeVideo extends Model
{
    use HasFactory;
    use Cloneable;

    protected $fillable = [
        'titulo', 'descripcion', 'codigo',
        '__import_id', 'curso_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function getVideoHtmlAttribute()
    {
        $embed = Embed::make($this->codigo)->parseUrl();

        if (!$embed)
            return '';

        $embed->setAttribute(['rel' => 0, 'modestbranding' => 1]);

        return $embed->getHtml();
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->youtube_videos()->find($this->id)->pivot;
    }
}
