<?php

if (!function_exists('memorizar_ruta')) {

    function memorizar_ruta()
    {
        // REF: https://stackoverflow.com/a/36098635/5136913

        $excluidas = ['settings/api'];
        $reset = ['index'];

        $actual = request()->path();

        if (!in_array($actual, $excluidas)) {

            $accion = Route::getCurrentRoute()->getActionMethod();

            if (in_array($accion, $reset))
                $rutas = [];
            else
                $rutas = session()->has('_rutas') ? session('_rutas') : [];

            if (count($rutas) == 0 || $rutas[0] != $actual)
                array_unshift($rutas, $actual);

            session(['_rutas' => $rutas]);
        }
    }

    function ruta_memorizada(int $niveles = 1)
    {
        return session('_rutas')[$niveles];
    }

    function anterior(int $niveles = 1)
    {
        return url(ruta_memorizada($niveles));
    }
}
