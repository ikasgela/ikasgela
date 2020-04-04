<?php

if (!function_exists('memorizar_ruta')) {

    function memorizar_ruta()
    {
        // REF: https://stackoverflow.com/a/36098635/5136913

        $accion = Route::getCurrentRoute()->getActionMethod();

        if ($accion == 'index')
            $rutas = [];
        else
            $rutas = session()->has('_rutas') ? session('_rutas') : [];

        $actual = request()->path();

        array_unshift($rutas, $actual);

        session(['_rutas' => $rutas]);
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
