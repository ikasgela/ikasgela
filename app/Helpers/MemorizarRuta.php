<?php

if (!function_exists('memorizar_ruta')) {

    function memorizar_ruta()
    {
        // REF: https://stackoverflow.com/a/36098635/5136913

        $exclusiones = ['tinymce_url'];

        if (request()->method() == 'GET') {

            $actual = request()->path(); // cursos/create

            if (!in_array($actual, $exclusiones)) {
                $accion = request()->route()->getActionMethod(); // index

                if ($accion == 'index')
                    $rutas = [];
                else
                    $rutas = session('_rutas') ?? [];

                if (count($rutas) == 0 || $rutas[0] != $actual) // Vacío o no es un page reload
                    array_unshift($rutas, $actual);

                if (count($rutas) > 2 && $rutas[0] == $rutas[2]) { // Después de un cancelar
                    array_shift($rutas);
                    array_shift($rutas);
                }

                session(['_rutas' => $rutas]);
            }
        }
    }

    function anterior(int $niveles = 1)
    {
        $ruta = session('_rutas')[$niveles] ?? '/';

        return url($ruta);
    }

    function olvidar(int $niveles = 1)
    {
        $rutas = session('_rutas') ?? [];

        for ($i = 0; $i < $niveles; $i++) {
            array_shift($rutas);
        }

        session(['_rutas' => $rutas]);
    }

    function retornar(int $niveles = 1)
    {
        $ruta = session('_rutas')[$niveles] ?? '/';

        olvidar($niveles);

        return redirect(url($ruta));
    }
}
