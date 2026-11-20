<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Intercalado en series de `orden` UUIDv7 (ADR-005): calcula el `orden` de una
 * fila nueva que aparezca **justo después** de $fila dentro de su serie — por
 * ejemplo, la copia de una fila duplicada (ADR-008) — y, si no hay hueco para
 * intercalar (muchas inserciones seguidas en el mismo punto), regenera la serie
 * completa con la nueva fila interpolada.
 *
 * Los `orden` son UUIDv7 (`Str::uuid7()`): los 48 bits iniciales son el epoch
 * en milisegundos y el resto, la secuencia monotónica del proceso, de modo que
 * el orden lexicográfico coincide con el orden de generación.
 *
 * Lo usan `RubricComponent` (serie de `criteria_groups` de la rúbrica) y
 * `CriteriaGroupComponent` (serie de `criterias` del grupo).
 */
trait OrdenUuid
{
    /**
     * Calcula un `orden` (UUIDv7 ordenable, ADR-005) para que una fila nueva
     * aparezca justo después de $fila en la serie devuelta por $coleccion
     * (es decir, entre esa fila y su siguiente vecino). Si la serie no tiene
     * hueco para intercalar, se regenera la serie completa con la nueva fila
     * interpolada y se devuelve el `orden` correspondiente a la nueva fila.
     *
     * @param callable(): Collection $coleccion Devuelve la
     *        serie ordenada de `orden` que contiene a $fila.
     */
    protected function ordenTras(Model $fila, callable $coleccion): string
    {
        if (empty($fila->orden)) {
            return Str::uuid7();
        }

        $siguiente = $coleccion()
            ->filter(fn($f) => !empty($f->orden) && $f->orden > $fila->orden)
            ->sortBy('orden')
            ->first();

        $ts_origen = self::tsOrden($fila->orden);
        $ts_clon = $ts_origen + 1;
        $cola = substr($fila->orden, 14); // versión/variante/secuencia (grupos 3-5)

        if ($siguiente !== null) {
            $hueco = self::tsOrden($siguiente->orden) - $ts_origen;

            if ($hueco < 2) {
                return $this->regenerarSerieTras($fila, $coleccion, $cola);
            }

            $ts_clon = $ts_origen + ($hueco >> 1);
        }

        return self::ordenConTs($ts_clon, $cola);
    }

    /**
     * No hay hueco entre la fila y su vecino (tras muchas inserciones en el
     * mismo punto, ADR-005): regenera la serie completa de `orden` con la
     * nueva fila interpolada justo después de $original, y devuelve el `orden`
     * correspondiente a la nueva fila.
     *
     * @param callable(): Collection $coleccion Devuelve la
     *        serie ordenada de `orden` que contiene a $original.
     */
    protected function regenerarSerieTras(Model $original, callable $coleccion, string $cola): string
    {
        $ts_actual = self::tsOrdenAhora();
        $ts_max = $coleccion()
            ->filter(fn($f) => !empty($f->orden))
            ->map(fn($f) => self::tsOrden($f->orden))
            ->max();

        $base = max($ts_actual, $ts_max ?? 0) + 1;

        $orden_nueva = null;

        foreach ($coleccion() as $fila) {
            $fila->orden = self::ordenConTs($base++, $cola);
            $fila->save();

            if ($fila->id === $original->id && $orden_nueva === null) {
                $orden_nueva = self::ordenConTs($base++, $cola);
            }
        }

        return $orden_nueva ?? self::ordenConTs($base, $cola);
    }

    /**
     * Extrae los 48 bits de timestamp (primera parte del UUID) de un `orden`
     * generado con `Str::uuid7()`: 12 hex = epoch Unix en **milisegundos**,
     * seguido de la secuencia (fracción de 12 bits + contador).
     */
    protected static function tsOrden(string $orden): int
    {
        return hexdec(substr($orden, 0, 8)) * 0x10000 + hexdec(substr($orden, 9, 4));
    }

    /**
     * Timestamp actual en la misma escala que `Str::uuid7()` (milisegundos),
     * decodificando un v7 recién generado: la fuente única del reloj para los
     * cálculos de intercalado.
     */
    protected static function tsOrdenAhora(): int
    {
        return self::tsOrden((string) Str::uuid7());
    }

    /**
     * Construye un `orden` con el timestamp dado y el resto (versión/variante/
     * secuencia) tomado de $cola (el mismo formato que `Str::uuid7()`).
     */
    protected static function ordenConTs(int $ts, string $cola = '7000-8000-000000000000'): string
    {
        return sprintf('%08x-%04x-%s', $ts >> 16, $ts & 0xffff, $cola);
    }
}
