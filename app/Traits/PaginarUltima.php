<?php

namespace App\Traits;

trait PaginarUltima
{
    public function paginate_ultima($coleccion, int $items_per_page = -1, string $key = 'pagina')
    {
        if ($items_per_page == -1) {
            $items_per_page = config('ikasgela.pagination_medium');
        }

        $route_name = request()->route()?->getName() ?? request()->path();
        $session_key = 'paginador_' . $route_name . '_' . $key;

        if (request()->has($key)) {
            session([$session_key => (int) request()->input($key)]);
            return $coleccion->paginate($items_per_page, ['*'], $key);
        }

        $total = $coleccion->count();
        $last_page = max(1, (int)ceil($total / $items_per_page));

        $page = session($session_key);
        $page = $page !== null ? min((int)$page, $last_page) : $last_page;

        return $coleccion->paginate($items_per_page, ['*'], $key, $page);
    }
}
