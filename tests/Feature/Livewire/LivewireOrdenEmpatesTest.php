<?php

namespace Tests\Feature\Livewire;

use App\Livewire\CriteriaComponent;
use App\Livewire\CriteriaGroupComponent;
use App\Livewire\RubricComponent;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresión ADR-005: la remapa one-shot COMB→UUIDv7 **no es inyectiva** — dos
 * COMB generados en la misma ventana de 10 µs (bucles cerrados: seeders,
 * `Rubric::duplicar()`) se re-codifican al mismo v7, dejando `orden` duplicados en
 * la serie (BD de producción: 387 series de `criteria_groups` y 869 de `criterias`).
 *
 * Con empates, la lógica anterior del editor se rompía:
 * - `down`/`right` (`where(orden, '>', X)`): saltaban la fila empatada y
 *   intercambiaban con la fila del siguiente valor distinto → la fila «saltaba
 *   dos posiciones».
 * - `up`/`left` (`max(orden < X)` + `where(orden, max)->first()`): el empate
 *   devolvía el primer miembro del bloque (orden de PK, arbitrario) → la fila
 *   «salía al sitio equivocado».
 * - `is_first/is_last` (`orden == min/max(orden)`): la segunda de un empate
 *   contaba como primera/última.
 *
 * La corrección resuelve la adyacencia y el estado primera/última sobre la
 * secuencia `(orden, id)` (desempate determinista) y la migración
 * `deduplicar_orden_uuid7` garantiza series estrictamente crecientes.
 */
class LivewireOrdenEmpatesTest extends TestCase
{
    use DatabaseTransactions;

    private Rubric $rubric;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();

        $this->rubric = Rubric::factory()->create();
    }

    /**
     * Construye un `orden` v7 determinista: `$ts` en milisegundos + fracción12
     * (mismo formato que `Str::uuid7()` / la remapa).
     */
    private function orden(int $ts, int $fraccion = 0): string
    {
        return sprintf('%08x-%04x-7%03x-8000-000000000000', $ts >> 16, $ts & 0xffff, $fraccion);
    }

    /**
     * Serie con el `orden` mínimo empatado (el caso que deja la remapa):
     * A y B comparten `orden` (A con id menor → va antes en `(orden, id)`),
     * C por detrás. Devuelve [A, B, C].
     */
    private function gruposConEmpate(): array
    {
        $a = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'A', 'orden' => $this->orden(2000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'B', 'orden' => $this->orden(2000)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'C', 'orden' => $this->orden(3000)]);

        return [$a, $b, $c];
    }

    // ------------------------------------------------------------------
    // RubricComponent: grupos
    // ------------------------------------------------------------------

    public function testDownGrupoPrimeroDelEmpateNoSaltaDosPosiciones(): void
    {
        // Serie: A(v2000, id<), B(v2000, id>), C(v3000)
        [$a, $b, $c] = $this->gruposConEmpate();

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('down_criteria_group', $a->id);

        // A (primero del empate) intercambia con B, su vecino en `(orden, id)`
        // (la fila empatada): swap de valores iguales → no-op visible, sin salto.
        // El código anterior usaba `where(orden, '>', X)`, que saltaba el empate
        // y lo intercambiaba con C (dos posiciones abajo).
        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $c->id], $ids);
        $this->assertSame($this->orden(3000), $c->fresh()->orden, 'C no debía tocarse');
    }

    public function testDownGrupoSegundoDelEmpateCambiaAlSiguiente(): void
    {
        // Serie: A(v2000, id<), B(v2000, id>), C(v3000)
        [$a, $b, $c] = $this->gruposConEmpate();

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('down_criteria_group', $b->id);

        // B (segundo del empate) «baja» con su siguiente en `(orden, id)` — C:
        // un salto de una posición, no dos.
        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $c->id, $b->id], $ids);
        $this->assertSame($this->orden(2000), $a->fresh()->orden, 'A no debía tocarse');
    }

    public function testUpGrupoUltimoNoSaltaDosPosiciones(): void
    {
        // Serie: A(v1000), B(v2000, id<), C(v2000, id>) — C es la última.
        $a = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'A', 'orden' => $this->orden(1000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'B', 'orden' => $this->orden(2000)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'C', 'orden' => $this->orden(2000)]);

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('up_criteria_group', $c->id);

        // C (segundo del empate, id mayor) «sube» con el anterior en `(orden, id)`
        // — B, su fila empatada: swap de valores iguales → no-op (la serie no
        // cambia). El código anterior la intercambiaba con A (el primer valor
        // estrictamente menor) y saltaba dos posiciones hacia arriba.
        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $c->id], $ids);
        $this->assertSame($this->orden(1000), $a->fresh()->orden, 'A no debía tocarse');
    }

    public function testUpGrupoTrasElEmpateCambiaAlVecino(): void
    {
        // Serie: A(v1000), B(v2000, id<), C(v2000, id>) y D(v4000) posterior.
        $a = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'A', 'orden' => $this->orden(1000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'B', 'orden' => $this->orden(2000)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'C', 'orden' => $this->orden(2000)]);
        $d = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'D', 'orden' => $this->orden(4000)]);

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('up_criteria_group', $d->id);

        // D intercambia con C (el último del bloque de empatados en `(orden, id)`),
        // no con B (el que devolvía `max(orden < X)` + `first()` por orden de PK):
        // un salto de una posición, no dos.
        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $d->id, $c->id], $ids);
    }

    // ------------------------------------------------------------------
    // CriteriaGroupComponent: criterios
    // ------------------------------------------------------------------

    public function testRightCriterioSegundoDelEmpateNoSaltaDosPosiciones(): void
    {
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);

        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'A', 'orden' => $this->orden(1000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'B', 'orden' => $this->orden(2000)]);
        $c = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'C', 'orden' => $this->orden(2000)]);
        $d = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'D', 'orden' => $this->orden(3000)]);

        Livewire::actingAs($this->profesor)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $grupo])
            ->call('right_criteria', $b->id);

        // B (primero del empate) intercambia con C (vecino en `(orden, id)`):
        // swap de valores iguales → no-op, sin saltos.
        $ids = $grupo->fresh()->criterias()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $c->id, $d->id], $ids);

        // C (segundo del empate) se mueve una posición: intercambia con D.
        Livewire::actingAs($this->profesor)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $grupo])
            ->call('right_criteria', $c->id);

        $ids = $grupo->fresh()->criterias()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $d->id, $c->id], $ids);
    }

    public function testLeftCriterioTrasElEmpateNoSaltaDosPosiciones(): void
    {
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);

        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'A', 'orden' => $this->orden(1000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'B', 'orden' => $this->orden(2000)]);
        $c = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'C', 'orden' => $this->orden(2000)]);
        $d = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'texto' => 'D', 'orden' => $this->orden(3000)]);

        Livewire::actingAs($this->profesor)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $grupo])
            ->call('left_criteria', $d->id);

        // D intercambia con C (último del bloque en `(orden, id)`), no con A.
        $ids = $grupo->fresh()->criterias()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $d->id, $c->id], $ids);
    }

    // ------------------------------------------------------------------
    // Estado primera/última (botones) con empates
    // ------------------------------------------------------------------

    public function testIsFirstCriteriaGroupConEmpate(): void
    {
        // Empate en el `orden` mínimo: A(v2000, id<), B(v2000, id>), C(v3000)
        [$a, $b, $c] = $this->gruposConEmpate();

        $componente = $this->componenteGrupo($a);

        $this->assertTrue($componente->is_first_criteria_group($a->id), 'A (id menor del empate) es el primero');
        // B NO es el primero (A va antes en `(orden, id)`): con `orden == min`
        // contaba como primera y su botón «up» quedaba desactivado.
        $this->assertFalse($componente->is_first_criteria_group($b->id));
        $this->assertFalse($componente->is_first_criteria_group($c->id));
    }

    public function testIsLastCriteriaGroupConEmpate(): void
    {
        // Empate en el `orden` máximo: A(v1000), B(v2000, id<), C(v2000, id>)
        $a = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'orden' => $this->orden(1000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'orden' => $this->orden(2000)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'orden' => $this->orden(2000)]);

        $componente = $this->componenteGrupo($a);

        $this->assertFalse($componente->is_last_criteria_group($a->id));
        // B NO es la última (C, empate, id mayor, va después): con `orden == max`
        // contaba como última y su botón «down» quedaba desactivado.
        $this->assertFalse($componente->is_last_criteria_group($b->id));
        $this->assertTrue($componente->is_last_criteria_group($c->id));
    }

    /**
     * `Testable::get()` no admite parámetros (acceso por `data_get`), de modo que
     * el `#[Computed]` parametrizado se prueba llamando al método directamente.
     */
    private function componenteGrupo(CriteriaGroup $grupo): CriteriaGroupComponent
    {
        $componente = new CriteriaGroupComponent();
        $componente->criteria_group = $grupo;
        $componente->mount();

        return $componente;
    }

    public function testIsFirstInGroupConEmpate(): void
    {
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);
        // Empate en el `orden` mínimo: A(id<), B(id>), C por detrás
        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $c = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(3000)]);

        $componente = fn ($id) => Livewire::actingAs($this->profesor)
            ->test(CriteriaComponent::class, ['criteria' => Criteria::findOrFail($id)])
            ->get('is_first_in_group');

        $this->assertTrue($componente($a->id), 'A (id menor del empate) es el primero');
        // B NO (con `orden == min` contaba como primera): botón «izquierda» activo.
        $this->assertFalse($componente($b->id));
        $this->assertFalse($componente($c->id));
    }

    public function testIsLastInGroupConEmpate(): void
    {
        $grupo = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);
        // Empate en el `orden` máximo: A(id<), B(id<), C(id>)
        $a = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(1000)]);
        $b = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);
        $c = Criteria::factory()->create(['criteria_group_id' => $grupo->id, 'orden' => $this->orden(2000)]);

        $componente = fn ($id) => Livewire::actingAs($this->profesor)
            ->test(CriteriaComponent::class, ['criteria' => Criteria::findOrFail($id)])
            ->get('is_last_in_group');

        $this->assertFalse($componente($a->id));
        // B NO es la última (C, empate, id mayor, va después).
        $this->assertFalse($componente($b->id));
        $this->assertTrue($componente($c->id));
    }

    // ------------------------------------------------------------------
    // La relación renderiza empates en orden `(orden, id)` determinista
    // ------------------------------------------------------------------

    public function testRelacionOrdenaEmpatesPorId(): void
    {
        [$a, $b, $c] = $this->gruposConEmpate();

        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $c->id], $ids);
    }
}
