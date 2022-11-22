<?php

if (!function_exists('mediana')) {

    // REF: Calcular la mediana de un array: https://codereview.stackexchange.com/a/276032
    function mediana(array $array): int|float
    {
        if (!$array) {
            throw new LengthException('Cannot calculate median because Argument #1 ($array) is empty');
        }
        sort($array);
        $middleIndex = count($array) / 2;
        if (is_float($middleIndex)) {
            return $array[(int)$middleIndex];
        }
        return ($array[$middleIndex] + $array[$middleIndex - 1]) / 2;
    }
}
