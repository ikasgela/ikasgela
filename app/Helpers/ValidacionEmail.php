<?php

if (!function_exists('email_rule')) {

    /**
     * Devuelve la regla de validación de email con DNS si no estamos en entorno de test.
     * En test, la resolución DNS puede fallar para dominios ficticios.
     */
    function email_rule(): string
    {
        return app()->environment('testing') ? 'email:rfc' : 'email:rfc,dns';
    }
}
