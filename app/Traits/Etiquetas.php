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
        $query = $query->where('tags', $exists ? 'LIKE' : 'NOT LIKE', "%$tag%");

        if (!$exists) {
            $query = $query->orWhereNull('tags');
        }

        return $query;
    }
}
