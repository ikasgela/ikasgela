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

    public function testDuplicateCriteriaGroup()
    {
        $initialCount = $this->rubric->criteria_groups()->count();

        Livewire::actingAs($this->profesor)
            ->test(RubricComponent::class, ['rubric' => $this->rubric])
            ->call('duplicate_criteria_group', $this->group->id)
            ->assertOk();

        $this->assertEquals($initialCount + 1, $this->rubric->fresh()->criteria_groups()->count());
    }
}
