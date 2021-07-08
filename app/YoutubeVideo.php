<?php

namespace App;

use Cohensive\Embed\Facades\Embed;
use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
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
}
