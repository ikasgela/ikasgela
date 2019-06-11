<?php

if (!function_exists('subdominio')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function subdominio()
    {
        return explode('.', Request::getHost())[0];
    }
}
