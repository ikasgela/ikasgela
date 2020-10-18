<?php

if (!function_exists('links_galeria')) {

    function links_galeria($texto_enlace, $id_galeria)
    {
        // REF: Envolver un elemento HTML en otro usando DOM: https://stackoverflow.com/a/2120923/14378620
        $dom = new DOMDocument();
        $dom->loadHTML($texto_enlace);

        foreach ($dom->getElementsByTagName('img') as $img) {
            $clone = $img->cloneNode();
            $src = $clone->getAttribute('src');

            $link = $dom->createElement('a');
            $link->setAttribute('data-fancybox', 'gallery_' . $id_galeria);
            $link->setAttribute('href', $src);
            $link->appendChild($clone);

            $img->parentNode->replaceChild($link, $img);
        }

        return $dom->saveHTML();
    }
}
