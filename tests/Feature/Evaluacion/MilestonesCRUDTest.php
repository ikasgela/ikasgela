<?php

namespace Tests\Feature\Evaluacion;

use Override;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MilestonesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'curso_id', 'name'
    ];

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $response = $this->get(route('milestones.index'));

        // Then
        $response->assertSuccessful()->assertSee($milestone->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('milestones.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('milestones.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('milestones.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New milestone'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('milestones.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('milestones.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make();
        setting_usuario(['curso_actual' => $milestone->curso_id]);
        $total = Milestone::all()->count();

        // When
        $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $this->assertEquals($total + 1, Milestone::all()->count());
    }

    public function testStoreFijoGuardaEtiquetasYNumero()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make();
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $this->post(route('milestones.store'), array_merge($milestone->toArray(), [
            'ajuste_proporcional_nota' => 'fijo',
            'etiquetas' => 'base,extra',
            'num_actividades_obligatorias' => 5,
        ]));

        // Then
        $this->assertDatabaseHas('milestones', [
            'curso_id' => $milestone->curso_id,
            'name' => $milestone->name,
            'ajuste_proporcional_nota' => 'fijo',
            'etiquetas' => 'base,extra',
            'num_actividades_obligatorias' => 5,
        ]);
    }

    public function testStoreFijoRequiereNumero()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make();

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray() + [
            'ajuste_proporcional_nota' => 'fijo',
        ]);

        // Then
        $response->assertSessionHasErrors('num_actividades_obligatorias');
    }

    public function testStoreNoFijoNullNumero()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make();
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $this->post(route('milestones.store'), $milestone->toArray() + [
            'ajuste_proporcional_nota' => 'media',
            'num_actividades_obligatorias' => 5,
        ]);

        // Then: el número solo tiene sentido con fijo
        $this->assertDatabaseHas('milestones', [
            'curso_id' => $milestone->curso_id,
            'name' => $milestone->name,
            'ajuste_proporcional_nota' => 'media',
            'num_actividades_obligatorias' => null,
        ]);
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->make();

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->make();

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Milestone::all()->count();

        $empty = new Milestone();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('milestones.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make([$field => null]);

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testStoreSinFechaGuardaNulas()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make(['date' => null, 'start_date' => null]);
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('milestones', [
            'curso_id' => $milestone->curso_id,
            'name' => $milestone->name,
            'date' => null,
            'start_date' => null,
        ]);
    }

    public function testStoreGuardaVentanaDeFechas()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given (sin fracción: la columna TIMESTAMP tiene precisión de segundo)
        $inicio = now()->subWeeks(2)->startOfSecond();
        $fin = now()->subWeek()->startOfSecond();
        $milestone = Milestone::factory()->make(['date' => $fin, 'start_date' => $inicio]);
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When (formato naive, como el datetime-local del navegador: el ISO-Z
        // lo interpretaría como UTC y derivaría una hora en el round-trip)
        $payload = $milestone->toArray();
        $payload['date'] = $fin->toDateTimeString();
        $payload['start_date'] = $inicio->toDateTimeString();
        $response = $this->post(route('milestones.store'), $payload);

        // Then
        $response->assertSessionHasNoErrors();
        $guardado = Milestone::where('curso_id', $milestone->curso_id)->where('name', $milestone->name)->first();
        $this->assertNotNull($guardado);
        $this->assertTrue($guardado->start_date->eq($inicio), 'Debe guardar start_date');
        $this->assertTrue($guardado->date->eq($fin), 'Debe guardar date');
    }

    public function testStoreInicioTrasFinDaError()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make([
            'date' => now()->subWeek(),
            'start_date' => now(),
        ]);
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then: start_date debe ser anterior o igual a date
        $response->assertSessionHasErrors('start_date');
    }

    public function testStoreInicioSinFinNoDaError()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given: sin fecha de corte la validación de orden no aplica
        $milestone = Milestone::factory()->make(['date' => null, 'start_date' => now()]);
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.show', $milestone));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.show', $milestone));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.show', $milestone));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.edit', $milestone), $milestone->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$milestone->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.edit', $milestone), $milestone->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.edit', $milestone), $milestone->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        $milestone->name = "Updated";

        // When
        $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $this->assertDatabaseHas('milestones', ['id' => $milestone->id, 'name' => $milestone->name]);
    }

    public function testUpdateFijoGuardaEtiquetasYNumero()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $this->put(route('milestones.update', $milestone), array_merge($milestone->toArray(), [
            'ajuste_proporcional_nota' => 'fijo',
            'etiquetas' => 'base,extra',
            'num_actividades_obligatorias' => 7,
        ]));

        // Then
        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'ajuste_proporcional_nota' => 'fijo',
            'etiquetas' => 'base,extra',
            'num_actividades_obligatorias' => 7,
        ]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();
        $milestone->name = "Updated";

        // When
        $response = $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->create();
        $milestone->name = "Updated";

        // When
        $response = $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        $empty = new Milestone();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('milestones.update', $milestone), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        $milestone->$field = null;

        // When
        $response = $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->updateRequires($field);
        }
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $this->delete(route('milestones.destroy', $milestone));

        // Then
        $this->assertDatabaseMissing('milestones', $milestone->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->delete(route('milestones.destroy', $milestone));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->delete(route('milestones.destroy', $milestone));

        // Then
        $response->assertRedirect(route('login'));
    }
}
