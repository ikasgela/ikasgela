<?php

if (!function_exists('memorizar_ruta')) {

    function memorizar_ruta()
    {
        session(['_ruta_actual' => url()->full()]);
    }

    function ruta_memorizada()
    {
        return session('_ruta_actual') ?: url()->previous();
    }
}
