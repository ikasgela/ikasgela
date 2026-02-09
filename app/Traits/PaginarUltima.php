<?php

namespace App\Traits;

trait PaginarUltima
{
    public function paginate_ultima($coleccion, int $items_per_page = 25, string $key = 'pagina')
    {
        // Si no hay parámetro de página, calcular directamente la última página
        if (!request()->has($key)) {
            $total = $coleccion->count();
            $last_page = max(1, (int)ceil($total / $items_per_page));

            return $coleccion->paginate($items_per_page, ['*'], $key, $last_page);
        }

        return $coleccion->paginate($items_per_page, ['*'], $key);
    }
}
