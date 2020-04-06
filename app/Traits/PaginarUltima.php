<?php

namespace App\Traits;

trait PaginarUltima
{
    public function paginate_ultima($coleccion, int $items_per_page = 25, string $key = 'pagina')
    {
        // Paginar
        $temp = $coleccion->paginate($items_per_page, ['*'], $key);

        // Situar el paginador en la última página si pagina no está definida
        if (!request()->has($key))
            return $coleccion->paginate($items_per_page, ['*'], $key, $temp->lastPage());
        else
            return $temp;
    }
}
