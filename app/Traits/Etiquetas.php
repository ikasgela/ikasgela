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
        // Limpiar la etiqueta de posibles caracteres de expresión regular (para poder poner "*" como etiqueta, por ejemplo)
        // REF: https://stackoverflow.com/a/4936376
        $tag = preg_quote($tag);

        $regex = "(^|,)\s*{$tag}\s*(,|$)";  // Tag separado por comas y con espacios delante o detrás

        $query = $query->where('tags', $exists ? 'regexp' : 'not regexp', $regex);

        if (!$exists) {
            $query = $query->orWhereNull('tags');
        }

        return $query;
    }

    public function addEtiqueta($etiqueta)
    {
        $tags = array_map('trim', explode(',', $etiqueta));

        foreach ($tags as $tag) {
            if (!$this->hasEtiqueta($tag)) {
                $this->tags .= ",$tag";
            }
        }
    }
}
