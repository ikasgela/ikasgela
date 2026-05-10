<?php

namespace Tests\Feature\Livewire;

use App\Livewire\EditCriteria;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireEditCriteriaTest extends TestCase
{
    use DatabaseTransactions;

    private Criteria $criteria;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();

        $rubric = Rubric::factory()->create();
        $group = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);
        $this->criteria = Criteria::factory()->create([
            'criteria_group_id' => $group->id,
            'texto' => 'Texto original',
            'puntuacion' => 5,
        ]);
    }

    public function testRender()
    {
        Livewire::actingAs($this->alumno)
            ->test(EditCriteria::class, ['criteria_id' => $this->criteria->id])
            ->assertOk()
            ->assertSet('texto', 'Texto original')
            ->assertSet('puntuacion', 5);
    }

    public function testSave()
    {
        Livewire::actingAs($this->alumno)
            ->test(EditCriteria::class, ['criteria_id' => $this->criteria->id])
            ->set('texto', 'Texto modificado')
            ->set('puntuacion', 10)
            ->call('save')
            ->assertDispatched('hideModal')
            ->assertDispatched('$parent.$refresh');

        $this->criteria->refresh();
        $this->assertEquals('Texto modificado', $this->criteria->texto);
        $this->assertEquals(10, $this->criteria->puntuacion);
    }
}
