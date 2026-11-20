<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MilestoneForm;
use App\Models\Curso;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireMilestoneFormTest extends TestCase
{
    use DatabaseTransactions;

    private Curso $curso;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->curso = Curso::factory()->create();

        $this->actingAs($this->admin);
        setting_usuario(['curso_actual' => $this->curso->id]);
    }

    public function testRenderCreateMuestraElFormulario()
    {
        // When / Then
        Livewire::actingAs($this->admin)
            ->test(MilestoneForm::class, ['curso_id' => $this->curso->id])
            ->assertOk()
            ->assertSet('etiquetas', 'base')
            ->assertSee(__('Name'))
            ->assertSee(__('Proportional calification adjustment'))
            ->assertSee(__('Fixed'))
            ->assertSee(__('Mandatory activities tags'))
            ->assertDontSee(__('Number of mandatory activities'));
    }

    public function testAjusteFijoMuestraElNumeroDeActividades()
    {
        // When: el admin selecciona 'fijo' en el desplegable
        $component = Livewire::actingAs($this->admin)
            ->test(MilestoneForm::class, ['curso_id' => $this->curso->id])
            ->assertDontSee(__('Number of mandatory activities'))
            ->set('ajuste_proporcional_nota', 'fijo');

        // Then: aparece el campo adicional
        $component->assertSee(__('Number of mandatory activities'));

        // Y al deseleccionar 'fijo' desaparece
        $component->set('ajuste_proporcional_nota', 'media')
            ->assertDontSee(__('Number of mandatory activities'));
    }

    public function testCreateMilestoneFijo()
    {
        // Given
        $total = Milestone::all()->count();

        // When
        Livewire::actingAs($this->admin)
            ->test(MilestoneForm::class, ['curso_id' => $this->curso->id])
            ->fill([
                'name' => 'Evaluación fijo',
                'date' => '2026-12-01T00:00:00',
                'published' => true,
                'ajuste_proporcional_nota' => 'fijo',
                'num_actividades_obligatorias' => '5',
                'etiquetas' => 'base,extra',
            ])
            ->call('save')
            ->assertRedirect(route('milestones.index'));

        // Then
        $this->assertEquals($total + 1, Milestone::all()->count());
        $this->assertDatabaseHas('milestones', [
            'curso_id' => $this->curso->id,
            'name' => 'Evaluación fijo',
            'published' => true,
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
            'etiquetas' => 'base,extra',
        ]);
    }

    public function testCreateMilestoneFijoRequiereNumero()
    {
        // When: fijo sin número
        Livewire::actingAs($this->admin)
            ->test(MilestoneForm::class, ['curso_id' => $this->curso->id])
            ->fill([
                'name' => 'Evaluación fijo',
                'date' => '2026-12-01T00:00:00',
                'ajuste_proporcional_nota' => 'fijo',
                'num_actividades_obligatorias' => '',
            ])
            ->call('save')
            ->assertHasErrors('num_actividades_obligatorias');

        // Then: no se crea
        $this->assertDatabaseMissing('milestones', ['name' => 'Evaluación fijo']);
    }

    public function testEditMilestoneFijoActualiza()
    {
        // Given
        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
            'etiquetas' => 'base',
        ]);

        // When
        Livewire::actingAs($this->admin)
            ->test(MilestoneForm::class, ['milestone' => $milestone])
            ->assertSet('ajuste_proporcional_nota', 'fijo')
            ->assertSet('num_actividades_obligatorias', 5)
            ->assertSet('etiquetas', 'base')
            ->set('num_actividades_obligatorias', '7')
            ->call('save')
            ->assertRedirect(route('milestones.index'));

        // Then
        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'num_actividades_obligatorias' => 7,
        ]);
    }

    public function testEditMilestoneDeseleccionaFijoNullNumero()
    {
        // Given
        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
        ]);

        // When: pasa de fijo a media
        Livewire::actingAs($this->admin)
            ->test(MilestoneForm::class, ['milestone' => $milestone])
            ->set('ajuste_proporcional_nota', 'media')
            ->call('save')
            ->assertRedirect(route('milestones.index'));

        // Then: el número se limpia
        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'ajuste_proporcional_nota' => 'media',
            'num_actividades_obligatorias' => null,
        ]);
    }
}
