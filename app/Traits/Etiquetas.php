<?php

namespace App\Traits;

trait Etiquetas
{
    public function etiquetas()
    {
        return array_map('trim', explode(',', $this->tags));
    }

    public function hasEtiqueta($etiqueta)
    {
        return in_array($etiqueta, $this->etiquetas());
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
}
