<?php

namespace Tests\Feature\Estructura;

use App\Actividad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActividadesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'nombre', 'unidad_id'
    ];

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
        $actividad = Actividad::factory()->create();
        session(['filtrar_curso_actual' => $actividad->unidad->curso_id]);

        // When
        $response = $this->get(route('actividades.index'));

        // Then
        $response->assertSee($actividad->nombre);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('actividades.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('actividades.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('actividades.create'));

        // Then
        $response->assertSeeInOrder([__('New activity'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('actividades.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('actividades.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->make();
        $total = Actividad::all()->count();

        // When
        $this->post(route('actividades.store'), $actividad->toArray());

        // Then
        $this->assertEquals($total + 1, Actividad::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->make();

        // When
        $response = $this->post(route('actividades.store'), $actividad->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $actividad = Actividad::factory()->make();

        // When
        $response = $this->post(route('actividades.store'), $actividad->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Actividad::all()->count();

        $empty = new Actividad();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('actividades.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->make([$field => null]);

        // When
        $response = $this->post(route('actividades.store'), $actividad->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.show', $actividad));

        // Then
        $response->assertSee($actividad->nombre);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.show', $actividad));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.show', $actividad));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.edit', $actividad), $actividad->toArray());

        // Then
        $response->assertSeeInOrder([$actividad->nombre, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.edit', $actividad), $actividad->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.edit', $actividad), $actividad->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();
        $actividad->nombre = "Updated";

        // When
        $this->put(route('actividades.update', $actividad), $actividad->toArray());

        // Then
        $this->assertDatabaseHas('actividades', ['id' => $actividad->id, 'nombre' => $actividad->nombre]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();
        $actividad->nombre = "Updated";

        // When
        $response = $this->put(route('actividades.update', $actividad), $actividad->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $actividad = Actividad::factory()->create();
        $actividad->nombre = "Updated";

        // When
        $response = $this->put(route('actividades.update', $actividad), $actividad->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();
        $empty = new Actividad();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('actividades.update', $actividad), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();
        $actividad->$field = null;

        // When
        $response = $this->put(route('actividades.update', $actividad), $actividad->toArray());

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
        $actividad = Actividad::factory()->create();

        // When
        $this->delete(route('actividades.destroy', $actividad));

        // Then
        $this->assertDatabaseMissing('actividades', $actividad->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->delete(route('actividades.destroy', $actividad));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->delete(route('actividades.destroy', $actividad));

        // Then
        $response->assertRedirect(route('login'));
    }
}
