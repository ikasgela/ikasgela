<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Deduplicado de los `orden` generados por la remapa COMB→UUIDv7 (ADR-005).
 *
 * La remapa `2026_11_20_011957_remap_orden_comb_a_uuid7` re-codifica el timestamp
 * de 48 bits del COMB (escalón 10 µs) a milisegundos + fracción12, pero **no es
 * inyectiva**: dos COMB generados dentro de la misma ventana de 10 µs (bucles
 * cerrados: seeders, regeneración de serie en `Rubric::duplicar()`, factories)
 * comparten el timestamp y se re-codifican al *mismo* UUIDv7, dejando `orden`
 * duplicados en la serie.
 *
 * Con `orden` duplicados la lógica del editor (ADR-005/ADR-013) se rompe: los
 * movimientos usan comparaciones estrictas `>`/`<` sobre `orden` (saltan al
 * siguiente valor *distinto* → la fila se mueve dos posiciones), el estado
 * primera/última queda ambiguo, y la ordenación `orderBy('orden')` con empates
 * es inestable (la fila «salta» de sitio entre renders).
 *
 * Solución: para cada serie (tabla + padre) que tenga `orden` duplicados,
 * regenera el `orden` de **todas** sus filas con UUIDv7 frescos (monótonos en el
 * proceso, `Str::uuid7()`) en su orden visual actual `(orden, id)` — el mismo
 * mecanismo de re-orden completo que ya usa la app (`regenerarSerieTras` de
 * `App\Traits\OrdenUuid`, `Rubric::duplicar()`). Las series sin duplicados no se
 * tocan.
 *
 * - IDEMPOTENTE: tras la reparación no hay duplicados → la migración no hace nada.
 * - Solo toca filas vivas (`deleted_at is null` donde existe) con `orden` no nulo:
 *   las filas borradas o sin `orden` no participan en la serie de la app.
 */
return new class extends Migration
{
    /** Tabla => columna padre que define la serie (mismas 15 tablas que la remapa). */
    private array $tablas = [
        'criterias' => 'criteria_group_id',
        'criteria_groups' => 'rubric_id',
        'flash_cards' => 'flash_deck_id',
        'actividad_cuestionario' => 'actividad_id',
        'actividad_file_resource' => 'actividad_id',
        'actividad_file_upload' => 'actividad_id',
        'actividad_flash_deck' => 'actividad_id',
        'actividad_intellij_project' => 'actividad_id',
        'actividad_link_collection' => 'actividad_id',
        'actividad_markdown_text' => 'actividad_id',
        'actividad_rubric' => 'actividad_id',
        'actividad_selector' => 'actividad_id',
        'actividad_test_result' => 'actividad_id',
        'actividad_youtube_video' => 'actividad_id',
        'qualification_skill' => 'qualification_id',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            foreach ($this->tablas as $tabla => $padre) {
                if (!Schema::hasTable($tabla)) {
                    continue;
                }

                $series = $this->seriesAfectadas($tabla, $padre);

                foreach ($series as $serieId) {
                    $this->regenerarSerie($tabla, $padre, $serieId);
                }
            }
        });
    }

    public function down(): void
    {
        // Sin revertir: los valores remapeados (con los duplicados) no son
        // recuperables; volver a la escala COMB es una decisión manual.
    }

    /**
     * Padres (series) de la tabla con alguna `orden` repetida entre sus filas
     * vivas.
     *
     * @return list<int|string>
     */
    private function seriesAfectadas(string $tabla, string $padre): array
    {
        return DB::table($tabla)
            ->select($padre)
            ->whereNotNull($padre)
            ->whereNotNull('orden')
            ->when($this->tieneSoftDelete($tabla), fn ($q) => $q->whereNull('deleted_at'))
            ->groupBy($padre)
            ->havingRaw('count(*) > count(distinct orden)')
            ->pluck($padre)
            ->all();
    }

    /**
     * Regenera el `orden` de todas las filas de la serie (UUIDv7 frescos)
     * preservando su orden visual actual `(orden, id)` — id es el desempate que
     * la app usa para renderizar empates.
     */
    private function regenerarSerie(string $tabla, string $padre, mixed $serieId): void
    {
        $ids = DB::table($tabla)
            ->where($padre, $serieId)
            ->whereNotNull('orden')
            ->when($this->tieneSoftDelete($tabla), fn ($q) => $q->whereNull('deleted_at'))
            ->orderBy('orden')
            ->orderBy('id')
            ->pluck('id');

        $actualiza = Schema::hasColumn($tabla, 'updated_at') ? ['updated_at' => now()] : [];

        foreach ($ids as $id) {
            DB::table($tabla)
                ->where('id', $id)
                ->update($actualiza + ['orden' => Str::uuid7()]);
        }
    }

    private function tieneSoftDelete(string $tabla): bool
    {
        return Schema::hasColumn($tabla, 'deleted_at');
    }
};
