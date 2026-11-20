<?php

namespace Tests\Feature\Migraciones;

use App\Models\Actividad;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Migración one-shot `deduplicar_orden_uuid7` (ADR-005): la remapa COMB→v7 no
 * es inyectiva (dos COMB de la misma ventana de 10 µs → el mismo v7), dejando
 * `orden` duplicados en la serie y rompiendo el editor (movimientos que saltan
 * dos posiciones, estados primera/última ambiguos, renders inestables).
 *
 * La migración regenera el `orden` de las series afectadas (UUIDv7 frescos)
 * preservando el orden visual `(orden, id)`; las series sanas no se tocan.
 */
class DeduplicarOrdenUuid7Test extends TestCase
{
    use DatabaseTransactions;

    private $migration;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();

        $this->migration = require database_path('migrations/2026_11_20_103127_deduplicar_orden_uuid7.php');
    }

    /** `orden` v7 determinista: `$ts` en ms + fracción12 (formato remapa/uuid7). */
    private function orden(int $ts, int $fraccion = 0): string
    {
        return sprintf('%08x-%04x-7%03x-8000-000000000000', $ts >> 16, $ts & 0xffff, $fraccion);
    }

    /**
     * Comprueba que la serie de $tabla para el padre $padreId queda estrictamente
     * creciente (por `orden`) y que el orden visual `(orden, id)` se preservó
     * ($idsEsperados en el orden en que aparecían antes de la reparación).
     */
    private function assertSerieReparada(string $tabla, string $padre, $padreId, array $idsEsperados): void
    {
        $ordenes = DB::table($tabla)->where($padre, $padreId)->whereNotNull('orden')
            ->orderBy('orden')->orderBy('id')->pluck('orden')->all();

        $uniques = array_unique($ordenes);
        $this->assertCount(count($ordenes), $uniques, "La serie debe quedar sin `orden` duplicados");

        $ordenado = $ordenes;
        sort($ordenado);
        $this->assertSame($ordenado, $ordenes, 'La serie debe ser estrictamente creciente');

        $ids = DB::table($tabla)->where($padre, $padreId)->whereNotNull('orden')
            ->orderBy('orden')->orderBy('id')->pluck('id')->all();
        $this->assertSame($idsEsperados, $ids, 'El orden visual (orden, id) debe preservarse');
    }

    // ------------------------------------------------------------------
    // criterias
    // ------------------------------------------------------------------

    public function testReparaCriteriasConEmpatePreservandoElOrdenVisual(): void
    {
        $rubric = Rubric::factory()->create();
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);

        // Serie A, B, C, D con B y C empatados (el resto de la remapa)
        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(1000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $c = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $d = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(3000)]);

        $this->migration->up();

        $this->assertSerieReparada('criterias', 'criteria_group_id', $grupo->id, [$a->id, $b->id, $c->id, $d->id]);

        // Las filas conservan su contenido (solo cambia `orden`)
        $b->refresh();
        $this->assertNotEmpty($b->texto);
    }

    public function testNoTocaSeriesSinDuplicados(): void
    {
        $rubric = Rubric::factory()->create();
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);

        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(1000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);

        $this->migration->up();

        $this->assertSame($this->orden(1000), $a->fresh()->orden);
        $this->assertSame($this->orden(2000), $b->fresh()->orden);
    }

    public function testIgnoraCriteriasSoftDeleted(): void
    {
        $rubric = Rubric::factory()->create();
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);

        // Solo una fila viva; la soft-deleted comparte su `orden`: la serie viva
        // es única → no debe regenerarse.
        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $borrada = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $borrada->delete();

        $this->migration->up();

        $this->assertSame($this->orden(2000), $a->fresh()->orden, 'La serie viva (única) no debe tocarse');
        $this->assertSame($this->orden(2000), DB::table('criterias')->where('id', $borrada->id)->value('orden'));
    }

    // ------------------------------------------------------------------
    // criteria_groups
    // ------------------------------------------------------------------

    public function testReparaGruposConEmpateComoEnProduccion(): void
    {
        // Patrón de la BD de producción: el seeder dejó dos grupos del mismo curso
        // (misma ventana de 10 µs → mismo v7 tras la remapa) entre otros sanos.
        $rubric = Rubric::factory()->create();

        $a = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id, 'orden' => $this->orden(1000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id, 'orden' => $this->orden(2000)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id, 'orden' => $this->orden(2000)]);
        $d = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id, 'orden' => $this->orden(3000)]);

        $this->migration->up();

        $this->assertSerieReparada('criteria_groups', 'rubric_id', $rubric->id, [$a->id, $b->id, $c->id, $d->id]);

        // La serie es estrictamente creciente → la lógica del editor vuelve a
        // funcionar: down en el primer grupo lo mueve exactamente una posición.
        $ordenes = CriteriaGroup::where('rubric_id', $rubric->id)->orderBy('orden')->pluck('orden')->all();
        $this->assertSame($ordenes[0], $ordenes[0]); // trivial: sanity
        $this->assertNotSame($ordenes[0], $ordenes[1]);
    }

    // ------------------------------------------------------------------
    // Pivotes (orden nullable)
    // ------------------------------------------------------------------

    public function testReparaPivoteConEmpateYOrdenNull(): void
    {
        $actividad = Actividad::factory()->create();
        $r1 = Rubric::factory()->create();
        $r2 = Rubric::factory()->create();
        $r3 = Rubric::factory()->create();
        $r4 = Rubric::factory()->create();

        $actividad->rubrics()->attach($r1, ['orden' => $this->orden(1000)]);
        $actividad->rubrics()->attach($r2, ['orden' => $this->orden(2000)]);
        $actividad->rubrics()->attach($r3, ['orden' => $this->orden(2000)]);
        // r4 con `orden` NULL: queda fuera del orden definido (ADR-005) y no
        // debe recibir valor de la reparación.
        $actividad->rubrics()->attach($r4, ['orden' => $this->orden(3000)]);
        DB::table('actividad_rubric')->where('rubric_id', $r4->id)->where('actividad_id', $actividad->id)->update(['orden' => null]);

        $this->migration->up();

        // La serie tenía duplicados (r2/r3) → se regenera íntegra: r1, r2, r3
        // reciben v7 frescos en su orden visual (r1, r2, r3); r4 (NULL) no.
        $filas = DB::table('actividad_rubric')
            ->where('actividad_id', $actividad->id)
            ->orderBy('orden')->orderBy('id')
            ->get(['rubric_id', 'orden'])
            ->keyBy('rubric_id');

        // NULL ordena primero en MariaDB (asc): r4 encabeza la serie.
        $todas = DB::table('actividad_rubric')->where('actividad_id', $actividad->id)
            ->orderBy('orden')->orderBy('id')->get(['rubric_id', 'orden'])->all();
        $this->assertSame($r4->id, $todas[0]->rubric_id);
        $this->assertNull($todas[0]->orden);

        $this->assertNotSame($this->orden(1000), $filas[$r1->id]->orden, 'r1 debía regenerarse (serie afectada)');
        $this->assertNotSame($this->orden(2000), $filas[$r2->id]->orden);
        $this->assertNotSame($this->orden(2000), $filas[$r3->id]->orden);
        $this->assertNotSame($filas[$r2->id]->orden, $filas[$r3->id]->orden, 'La serie debe quedar sin empates');

        // Orden visual preservado: r1 < r2 < r3
        $noNull = array_slice($todas, 1);
        $this->assertSame([$r1->id, $r2->id, $r3->id], array_column($noNull, 'rubric_id'));
    }

    // ------------------------------------------------------------------
    // Idempotencia
    // ------------------------------------------------------------------

    public function testEsIdempotente(): void
    {
        $rubric = Rubric::factory()->create();
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);

        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);

        $this->migration->up();
        $trasPrimera = [$a->fresh()->orden, $b->fresh()->orden, $a->fresh()->updated_at?->toDateTimeString()];

        $this->migration->up();

        $this->assertSame($trasPrimera[0], $a->fresh()->orden, 'La segunda ejecución no debe tocar nada');
        $this->assertSame($trasPrimera[1], $b->fresh()->orden);
    }
}
