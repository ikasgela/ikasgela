<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Etiquetas
{
    public function etiquetas()
    {
        $limit = 1024;
        if (Str::length($this->tags) == 0)
            $limit = -1;
        return array_map('trim', explode(',', $this->tags, $limit));
    }

    public function hasEtiqueta($etiqueta)
    {
        return in_array($etiqueta, $this->etiquetas());
    }

    public function hasEtiquetas($etiquetas)
    {
        $encontradas = true;

        foreach ($etiquetas as $etiqueta) {
            if (!$this->hasEtiqueta($etiqueta)) {
                $encontradas = false;
            }
        }

        return $encontradas;
    }

    public function scopeTag($query, $tag, $exists = true)
    {
        return $this->buscarEtiqueta($query, $exists, $tag);
    }

    public function scopeTags($query, $tags, $exists = true)
    {
        if (!is_array($tags))
            $tags = array_map('trim', explode(',', $tags));

        foreach ($tags as $tag) {
            $query = $this->buscarEtiqueta($query, $exists, $tag);
        }

        return $query;
    }

    public function buscarEtiqueta($query, bool $exists, $tag)
    {
        $query = $query->where('tags', $exists ? 'LIKE' : 'NOT LIKE', "%$tag%");

        if (!$exists) {
            $query = $query->orWhereNull('tags');
        }

        return $query;
    }

    public function addEtiqueta($etiqueta)
    {
        if (!$this->hasEtiqueta($etiqueta)) {
            $this->tags .= ",$etiqueta";
        }
    }
}
