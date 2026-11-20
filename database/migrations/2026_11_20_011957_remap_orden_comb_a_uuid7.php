<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Re-codifica los valores `orden` en formato COMB (`Str::orderedUuid()`:
 * 48 bits de timestamp a 10 µs + 80 bits de azar, nible de versión `4`) a
 * UUIDv7 (`Str::uuid7()`: 48 bits de milisegundos + 12 bits de fracción,
 * nible de versión `7`) — ADR-005.
 *
 * La escala del timestamp cambia ×100, de modo que sin esta migración un v7
 * nuevo ordenaría antes que cualquier COMB viejo en la misma serie.
 *
 * - INYECTIVA: `ms = ts/100`, `fracción12 = (ts % 100) · 4096 / 100`
 *   (monótona) y contador = 0: preserva el orden relativo dentro de cada serie.
 * - IDEMPOTENTE: solo toca valores de 36 chars con nible de versión `4` y
 *   timestamp en la escala 10 µs; los v7 (versión `7`, escala ms) se saltan.
 * - Solo cubre las columnas `orden` CHAR(36) que reciben UUIDs ordenables;
 *   las de tipo entero (`unidades`, `actividades`, `preguntas`, `links`,
 *   `files`, `feedback`, `items`) no se tocan.
 */
return new class extends Migration
{
    private array $tablas = [
        'criterias',
        'criteria_groups',
        'flash_cards',
        'actividad_cuestionario',
        'actividad_file_resource',
        'actividad_file_upload',
        'actividad_flash_deck',
        'actividad_intellij_project',
        'actividad_link_collection',
        'actividad_markdown_text',
        'actividad_rubric',
        'actividad_selector',
        'actividad_test_result',
        'actividad_youtube_video',
        'qualification_skill',
    ];

    /**
     * Escala 10 µs del timestamp COMB: [6·10¹³, 10¹⁶) ≈ 1970 – 31688 d.C.
     * Los v7 (escala ms: [6·10¹¹, 10¹³)) quedan fuera por la versión '7'.
     */
    private const TS_MIN = 60000000000000;

    private const TS_MAX = 10000000000000000;

    public function up(): void
    {
        DB::transaction(function () {
            foreach ($this->tablas as $tabla) {
                if (!Schema::hasTable($tabla)) {
                    continue;
                }

                $ordenes = DB::table($tabla)
                    ->select('orden')
                    ->whereNotNull('orden')
                    ->whereRaw("CHAR_LENGTH(orden) = 36 AND SUBSTRING(orden, 15, 1) = '4'")
                    ->pluck('orden')
                    ->unique()
                    ->all();

                foreach ($ordenes as $orden) {
                    $nuevo = self::combAV7((string) $orden);
                    if ($nuevo !== null) {
                        DB::table($tabla)->where('orden', $orden)->update(['orden' => $nuevo]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Sin revertir: la remapa sustituye la cola aleatoria (azar) original por
        // cero (fracción y contador v7), así que el valor COMB previo no es
        // recuperable. Revertir el cambio de generación es una decisión manual.
    }

    /**
     * Re-codifica un `orden` COMB a UUIDv7, o null si no es COMB.
     */
    private static function combAV7(string $orden): ?string
    {
        $ts = hexdec(substr($orden, 0, 8)) * 0x10000 + hexdec(substr($orden, 9, 4));

        if ($ts < self::TS_MIN || $ts >= self::TS_MAX) {
            return null;
        }

        $ms = intdiv($ts, 100);
        $fraccion = intdiv(($ts % 100) * 4096, 100);

        return sprintf('%08x-%04x-7%01x%02x-8000-000000000000', $ms >> 16, $ms & 0xffff, $fraccion >> 8, $fraccion & 0xff);
    }
};
