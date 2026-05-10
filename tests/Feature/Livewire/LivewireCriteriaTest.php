<?php

namespace Tests\Feature\Livewire;

use App\Livewire\CriteriaComponent;
use App\Livewire\CriteriaGroupComponent;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireCriteriaTest extends TestCase
{
    use DatabaseTransactions;

    private Rubric $rubric;
    private CriteriaGroup $group;
    private Criteria $criteria;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();

        $this->rubric = Rubric::factory()->create();
        $this->group = CriteriaGroup::factory()->create(['rubric_id' => $this->rubric->id]);
        $this->criteria = Criteria::factory()->create([
            'criteria_group_id' => $this->group->id,
            'puntuacion' => 5,
            'seleccionado' => false,
        ]);
    }

    // CriteriaComponent tests
    public function testCriteriaComponentRender()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaComponent::class, ['criteria' => $this->criteria])
            ->assertOk();
    }

    public function testCriteriaComponentRefresh()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaComponent::class, ['criteria' => $this->criteria])
            ->dispatch('hideModal')
            ->assertOk();
    }

    public function testCriteriaComponentIsFirstInGroup()
    {
        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaComponent::class, ['criteria' => $this->criteria]);

        $this->assertTrue($component->get('is_first_in_group'));
    }

    public function testCriteriaComponentIsLastInGroup()
    {
        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaComponent::class, ['criteria' => $this->criteria]);

        $this->assertTrue($component->get('is_last_in_group'));
    }

    public function testCriteriaComponentIsRubricCompleted()
    {
        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaComponent::class, ['criteria' => $this->criteria]);

        $this->assertFalse((bool)$component->get('is_rubric_completed'));
    }

    // CriteriaGroupComponent tests
    public function testCriteriaGroupComponentRender()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->assertOk()
            ->assertSet('titulo', $this->group->titulo);
    }

    public function testCriteriaGroupComponentSeleccionar()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->call('seleccionar', $this->criteria->id)
            ->assertDispatched('hideModal');

        $this->criteria->refresh();
        $this->assertTrue((bool)$this->criteria->seleccionado);
    }

    public function testCriteriaGroupComponentAddCriteria()
    {
        $before = $this->group->criterias()->count();

        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->call('add_criteria', $this->group->id);

        $this->assertEquals($before + 1, $this->group->criterias()->count());
    }

    public function testCriteriaGroupComponentDeleteCriteria()
    {
        $extra = Criteria::factory()->create(['criteria_group_id' => $this->group->id]);

        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->call('delete_criteria', $extra->id);

        $this->assertNull(Criteria::find($extra->id));
    }

    public function testCriteriaGroupComponentLeftRightCriteria()
    {
        $c2 = Criteria::factory()->create(['criteria_group_id' => $this->group->id]);

        $orden1 = $this->criteria->orden;
        $orden2 = $c2->orden;

        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->call('right_criteria', $this->criteria->id);

        $this->criteria->refresh();
        $c2->refresh();
        $this->assertEquals($orden2, $this->criteria->orden);
        $this->assertEquals($orden1, $c2->orden);

        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->call('left_criteria', $this->criteria->id);

        $this->criteria->refresh();
        $c2->refresh();
        $this->assertEquals($orden1, $this->criteria->orden);
        $this->assertEquals($orden2, $c2->orden);
    }

    public function testCriteriaGroupComponentToggleEdit()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->assertSet('is_editing', false)
            ->call('toggle_edit')
            ->assertSet('is_editing', true);
    }

    public function testCriteriaGroupComponentToggleCabeceraHorizontal()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->call('toggle_cabecera_horizontal');

        $this->group->refresh();
        $this->assertTrue((bool)$this->group->cabecera_horizontal);
    }

    public function testCriteriaGroupComponentSave()
    {
        $group = CriteriaGroup::factory()->create([
            'rubric_id' => $this->rubric->id,
            'titulo' => 'Titulo inicial',
            'descripcion' => 'Desc inicial',
        ]);

        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $group])
            ->set('titulo', 'Nuevo titulo')
            ->set('descripcion', 'Nueva descripcion')
            ->call('save')
            ->assertSet('is_editing', false);

        $group->refresh();
        $this->assertEquals('Nuevo titulo', $group->titulo);
    }

    public function testCriteriaGroupComponentRefresh()
    {
        Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group])
            ->dispatch('hideModal')
            ->assertOk();
    }

    public function testCriteriaGroupComponentTotal()
    {
        $this->criteria->update(['seleccionado' => true, 'puntuacion' => 7]);

        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group]);

        $this->assertEquals(7, $component->get('total'));
    }

    public function testCriteriaGroupComponentMaxTotal()
    {
        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group]);

        $this->assertEquals(5, $component->get('max_total'));
    }

    public function testCriteriaGroupComponentIsRubricCompleted()
    {
        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group]);

        $this->assertFalse((bool)$component->get('is_rubric_completed'));
    }

    public function testCriteriaGroupComponentIsFirstAndLast()
    {
        $component = Livewire::actingAs($this->alumno)
            ->test(CriteriaGroupComponent::class, ['criteria_group' => $this->group]);

        // With only one group, it should be both first and last
        $isFirst = $component->instance()->is_first_criteria_group($this->group->id);
        $isLast = $component->instance()->is_last_criteria_group($this->group->id);
        $this->assertTrue($isFirst);
        $this->assertTrue($isLast);

        // Create second group - it will have a higher UUID (orderedUuid is time-based)
        $group2 = CriteriaGroup::factory()->create([
            'rubric_id' => $this->rubric->id,
        ]);

        // First group is still first, second group is now last
        $this->assertTrue($component->instance()->is_first_criteria_group($this->group->id));
        $this->assertFalse($component->instance()->is_last_criteria_group($this->group->id));
        $this->assertFalse($component->instance()->is_first_criteria_group($group2->id));
        $this->assertTrue($component->instance()->is_last_criteria_group($group2->id));
    }
}
