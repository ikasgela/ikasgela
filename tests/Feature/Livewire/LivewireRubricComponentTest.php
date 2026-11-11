<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RubricComponent;
use App\Models\Actividad;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireRubricComponentTest extends TestCase
{
    use DatabaseTransactions;

    private Rubric $rubric;
    private CriteriaGroup $group;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();

        $this->rubric = Rubric::factory()->create([
            'titulo' => 'Rubric Original',
            'descripcion' => 'Desc original',
            'titulo_visible' => true,
            'descripcion_visible' => true,
            'excluir_no_seleccionadas' => false,
        ]);

        $this->group = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);
    }

    public function testRender()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->assertOk();
    }

    public function testToggleEdit()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->assertSet('rubric_is_editing', false)
            ->call('toggle_edit')
            ->assertSet('rubric_is_editing', true)
            ->call('toggle_edit')
            ->assertSet('rubric_is_editing', false);
    }

    public function testToggleEditCabecera()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->assertSet('is_editing_cabecera', false)
            ->call('toggle_edit_cabecera')
            ->assertSet('is_editing_cabecera', true);
    }

    public function testToggleTituloVisible()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('toggle_titulo_visible')
            ->assertSet('titulo_visible', false);

        $this->rubric->refresh();
        $this->assertFalse((bool)$this->rubric->titulo_visible);
    }

    public function testToggleDescripcionVisible()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('toggle_descripcion_visible')
            ->assertSet('descripcion_visible', false);

        $this->rubric->refresh();
        $this->assertFalse((bool)$this->rubric->descripcion_visible);
    }

    public function testToggleExcluirNoSeleccionadas()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('toggle_excluir_no_seleccionadas')
            ->assertSet('excluir_no_seleccionadas', true);

        $this->rubric->refresh();
        $this->assertTrue((bool)$this->rubric->excluir_no_seleccionadas);
    }

    public function testSave()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->set('titulo', 'Nuevo Titulo')
            ->set('descripcion', 'Nueva Desc')
            ->call('save')
            ->assertSet('is_editing_cabecera', false);

        $this->rubric->refresh();
        $this->assertEquals('Nuevo Titulo', $this->rubric->titulo);
        $this->assertEquals('Nueva Desc', $this->rubric->descripcion);
    }

    public function testAddCriteriaGroup()
    {
        $before = $this->rubric->criteria_groups()->count();

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('add_criteria_group');

        $this->assertEquals($before + 1, $this->rubric->criteria_groups()->count());
    }

    public function testDeleteCriteriaGroup()
    {
        $group = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('delete_criteria_group', $group->id);

        $this->assertNull(CriteriaGroup::find($group->id));
    }

    public function testUpDownCriteriaGroup()
    {
        $g1 = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);
        $g2 = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);

        $orden1 = $g1->orden;
        $orden2 = $g2->orden;

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('down_criteria_group', $g1->id);

        $g1->refresh();
        $g2->refresh();
        $this->assertEquals($orden2, $g1->orden);
        $this->assertEquals($orden1, $g2->orden);

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('up_criteria_group', $g1->id);

        $g1->refresh();
        $g2->refresh();
        $this->assertEquals($orden1, $g1->orden);
        $this->assertEquals($orden2, $g2->orden);
    }

    public function testRefresh()
    {
        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->dispatch('hideModal')
            ->assertOk();
    }

    public function testTotalAndMaxTotal()
    {
        $criteria = Criteria::factory()->create([
            'criteria_group_id' => $this->group->id,
            'puntuacion' => 10,
            'seleccionado' => true,
        ]);

        $component = Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric]);

        $component->assertOk();
        $this->assertEquals(10, $component->get('total'));
        $this->assertEquals(10, $component->get('max_total'));
    }

    /**
     * Construye un `orden` con el timestamp dado (mismo formato que
     * `Str::orderedUuid()`) para tener un control determinista del orden en
     * los tests.
     */
    private function ordenTs(int $ts): string
    {
        return sprintf('%08x-%04x-4000-8000-000000000000', $ts >> 16, $ts & 0xffff);
    }

    public function testDuplicateCriteriaGroup()
    {
        $this->group->update(['orden' => $this->ordenTs(1000), 'titulo' => 'Original']);
        Criteria::factory()->create([
            'criteria_group_id' => $this->group->id,
            'texto' => 'Criterio 1',
            'puntuacion' => 5,
        ]);

        $inicial = $this->rubric->criteria_groups()->count();

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('duplicate_criteria_group', $this->group->id)
            ->assertOk();

        $grupos = $this->rubric->fresh()->criteria_groups()->get();

        $this->assertEquals($inicial + 1, $grupos->count());
        // Con un único grupo, la copia queda justo después (al final)
        $this->assertSame($this->group->id, $grupos->first()->id);

        $clon = $grupos->last();
        $this->assertNotEquals($this->group->id, $clon->id);
        $this->assertEquals('Original', $clon->titulo);
        $this->assertEquals($this->group->rubric_id, $clon->rubric_id);
        $this->assertTrue($this->group->orden < $clon->orden);

        // Los criterios se clonan con sus valores
        $criterias = $clon->criterias()->get();
        $this->assertCount(1, $criterias);
        $this->assertEquals('Criterio 1', $criterias->first()->texto);
        $this->assertEquals(5, $criterias->first()->puntuacion);
    }

    public function testDuplicateCriteriaGroupQuedaTrasElOriginal()
    {
        $this->group->delete();

        $a = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'A', 'orden' => $this->ordenTs(1000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'B', 'orden' => $this->ordenTs(2000)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'C', 'orden' => $this->ordenTs(3000)]);

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('duplicate_criteria_group', $b->id)
            ->assertOk();

        $clon = $this->rubric->criteria_groups()->where('titulo', 'B')->where('id', '!=', $b->id)->first();
        $this->assertNotNull($clon);

        // La copia queda como fila siguiente de B (A, B, B-copia, C)
        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $b->id, $clon->id, $c->id], $ids);
        $this->assertTrue($b->orden < $clon->orden && $clon->orden < $c->orden);

        // Solo se intercala: los demás grupos conservan su `orden`
        $this->assertSame($this->ordenTs(1000), $a->fresh()->orden);
        $this->assertSame($this->ordenTs(2000), $b->fresh()->orden);
        $this->assertSame($this->ordenTs(3000), $c->fresh()->orden);
    }

    public function testDuplicateCriteriaGroupSinHuecoReordenaLaSerie()
    {
        $this->group->delete();

        // A y B crean en el mismo 100 µs: no hay hueco para intercalar
        $a = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'A', 'orden' => $this->ordenTs(1000)]);
        $b = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'B', 'orden' => $this->ordenTs(1001)]);
        $c = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id, 'titulo' => 'C', 'orden' => $this->ordenTs(9000000)]);

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('duplicate_criteria_group', $a->id)
            ->assertOk();

        $clon = $this->rubric->criteria_groups()->where('titulo', 'A')->where('id', '!=', $a->id)->first();
        $this->assertNotNull($clon);

        // El orden relativo se mantiene, con la copia tras A
        $ids = $this->rubric->fresh()->criteria_groups()->pluck('id')->all();
        $this->assertSame([$a->id, $clon->id, $b->id, $c->id], $ids);

        // La serie completa se regeneró: `orden` únicos y ordenados
        $ordenes = $this->rubric->fresh()->criteria_groups()->pluck('orden')->all();
        $this->assertCount(4, array_unique($ordenes));
        $ordenado = $ordenes;
        sort($ordenado);
        $this->assertSame($ordenes, $ordenado);
    }
}
