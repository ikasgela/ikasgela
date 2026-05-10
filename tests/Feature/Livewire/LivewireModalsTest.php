<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Modals;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireModalsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();
    }

    public function testRender()
    {
        Livewire::actingAs($this->alumno)
            ->test(Modals::class)
            ->assertOk();
    }

    public function testShowModalWithValidComponent()
    {
        $rubric = Rubric::factory()->create();
        $group = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);
        $criteria = Criteria::factory()->create(['criteria_group_id' => $group->id]);

        Livewire::actingAs($this->alumno)
            ->test(Modals::class)
            ->call('showModal', 'edit-criteria', ['criteria_id' => $criteria->id], 'modal-md')
            ->assertSet('alias', 'edit-criteria')
            ->assertSet('size', 'modal-md')
            ->assertDispatched('showBootstrapModal');
    }

    public function testShowModalWithEmptyAlias()
    {
        Livewire::actingAs($this->alumno)
            ->test(Modals::class)
            ->call('showModal', '')
            ->assertSet('alias', null);
    }

    public function testShowModalWithNonStringAlias()
    {
        Livewire::actingAs($this->alumno)
            ->test(Modals::class)
            ->call('showModal', null)
            ->assertSet('alias', null);
    }

    public function testResetModal()
    {
        $rubric = Rubric::factory()->create();
        $group = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);
        $criteria = Criteria::factory()->create(['criteria_group_id' => $group->id]);

        Livewire::actingAs($this->alumno)
            ->test(Modals::class)
            ->call('showModal', 'edit-criteria', ['criteria_id' => $criteria->id])
            ->call('resetModal')
            ->assertSet('alias', null)
            ->assertSet('activeModal', null);
    }
}
