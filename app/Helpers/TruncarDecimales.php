<?php

if (!function_exists('truncar_decimales')) {

    /**
     * Formateador con 2 decimales, truncando en vez de redondear y en el idioma del usuario o en_US si exportamos a Excel
     * @param $valor
     * @param int $decimales
     * @param false $exportar
     * @return string
     */
    function truncar_decimales($valor, $decimales = 0, $exportar = false): string
    {
        $locale = app()->getLocale();
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter(!$exportar ? $locale : 'en_US', $formatStyle);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimales);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimales);

        // Redondear hacia el cero para truncar (5.9 -> 5)
        $formatter->setAttribute(NumberFormatter::ROUNDING_MODE, NumberFormatter::ROUND_DOWN);

        return $formatter->format($valor);
    }
}
