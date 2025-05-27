<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('subdominio')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function subdominio()
    {
        $hostname = explode('.', Request::getHost())[0];
        return $hostname != 'host' ? $hostname : 'ikasgela';
    }
}
