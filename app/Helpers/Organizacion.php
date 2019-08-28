<?php

if (!function_exists('subdominio')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function organizacion()
    {
        return !empty(subdominio()) ? subdominio() : 'ikasgela';
    }
}
