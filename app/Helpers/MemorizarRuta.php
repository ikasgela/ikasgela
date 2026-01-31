<?php

if (!function_exists('memorizar_ruta')) {

    function memorizar_ruta()
    {
        // REF: https://stackoverflow.com/a/36098635/5136913

        if (request()->method() == 'GET') {

            $actual = request()->path(); // cursos/create

            // Verificar exclusiones (pueden tener prefijo de idioma como /es/)
            $excluir = str_contains($actual, 'tinymce') ||
                       str_contains($actual, 'livewire');

            if (!$excluir) {
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

    function es_ruta_valida($ruta)
    {
        // Excluir rutas que contengan tinymce o livewire (pueden tener prefijo de idioma)
        if (str_contains($ruta, 'tinymce') || str_contains($ruta, 'livewire')) {
            return false;
        }

        return true;
    }

    function anterior(int $niveles = 1)
    {
        $rutas = session('_rutas') ?? [];

        // Buscar la primera ruta válida a partir del nivel indicado
        for ($i = $niveles; $i < count($rutas); $i++) {
            if (es_ruta_valida($rutas[$i])) {
                return url($rutas[$i]);
            }
        }

        return url('/');
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
        $rutas = session('_rutas') ?? [];

        // Buscar la primera ruta válida a partir del nivel indicado
        for ($i = $niveles; $i < count($rutas); $i++) {
            if (es_ruta_valida($rutas[$i])) {
                olvidar($i);
                return redirect(url($rutas[$i]));
            }
        }

        olvidar($niveles);
        return redirect(url('/'));
    }
}
