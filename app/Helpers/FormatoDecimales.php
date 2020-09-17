<?php

if (!function_exists('formato_decimales')) {

    function formato_decimales($valor, $decimales = 0): string
    {
        $locale = app()->getLocale();
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter($locale, $formatStyle);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimales);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimales);

        return $formatter->format($valor);
    }
}
