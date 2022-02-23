<?php

if (!function_exists('formato_decimales')) {

    /**
     * Formateador con 2 decimales y en el idioma del usuario o en_US si exportamos a Excel
     * @param $valor
     * @param int $decimales
     * @param false $exportar
     * @return string
     */
    function formato_decimales($valor, $decimales = 0, $exportar = false): string
    {
        $locale = app()->getLocale();
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter(!$exportar ? $locale : 'en_US', $formatStyle);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimales);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimales);

        // Redondear hacia arriba (5.5 -> 6)
        $formatter->setAttribute(NumberFormatter::ROUNDING_MODE, NumberFormatter::ROUND_HALFUP);

        return $formatter->format($valor);
    }
}
