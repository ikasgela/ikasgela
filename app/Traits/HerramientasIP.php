<?php

namespace App\Traits;

trait HerramientasIP
{
    // REF: https://dev.to/rogeriotaques/an-easy-way-to-get-the-real-client-ip-in-php-4pii

    public function clientIP()
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER["HTTP_CF_CONNECTING_IP"] # when behind cloudflare
            ?? $_SERVER['HTTP_X_FORWARDED']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_FORWARDED']
            ?? $_SERVER['HTTP_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    /**
     * Check if a given ip is in a network
     * @param string $ip IP to check in IPV4 format eg. 127.0.0.1
     * @param string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     * @return boolean true if the ip is in this range / false if not.
     */
    public function ip_in_range($ip, $ranges)
    {
        $resultado = false;

        foreach ($ranges as $range) {
            if (!strpos((string)$range, '/')) {
                $range .= '/32';
            }

            // $range is in IP/CIDR format eg 127.0.0.1/24
            [$range, $netmask] = explode('/', (string)$range, 2);
            $range_decimal = ip2long($range);
            $ip_decimal = ip2long($ip);
            $wildcard_decimal = 2 ** (32 - $netmask) - 1;
            $netmask_decimal = ~$wildcard_decimal;

            $resultado |= (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
        }

        return $resultado;
    }
}
