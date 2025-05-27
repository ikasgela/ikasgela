<?php

namespace Tests\Feature\Estructura;

use Override;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UnidadesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'nombre', 'curso_id'
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
        $unidad = Unidad::factory()->create();
        session(['filtrar_curso_actual' => $unidad->curso_id]);

        // When
        $response = $this->get(route('unidades.index'));

        // Then
        $response->assertSuccessful()->assertSee($unidad->nombre);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('unidades.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('unidades.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('unidades.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New unit'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('unidades.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('unidades.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->make();
        $total = Unidad::all()->count();

        // When
        $this->post(route('unidades.store'), $unidad->toArray());

        // Then
        $this->assertEquals($total + 1, Unidad::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $unidad = Unidad::factory()->make();

        // When
        $response = $this->post(route('unidades.store'), $unidad->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $unidad = Unidad::factory()->make();

        // When
        $response = $this->post(route('unidades.store'), $unidad->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Unidad::all()->count();

        $empty = new Unidad();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('unidades.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->make([$field => null]);

        // When
        $response = $this->post(route('unidades.store'), $unidad->toArray());

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
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->get(route('unidades.show', $unidad));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->get(route('unidades.show', $unidad));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->get(route('unidades.show', $unidad));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->get(route('unidades.edit', $unidad), $unidad->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$unidad->nombre, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->get(route('unidades.edit', $unidad), $unidad->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->get(route('unidades.edit', $unidad), $unidad->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->create();
        $unidad->nombre = "Updated";

        // When
        $this->put(route('unidades.update', $unidad), $unidad->toArray());

        // Then
        $this->assertDatabaseHas('unidades', ['id' => $unidad->id, 'nombre' => $unidad->nombre]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $unidad = Unidad::factory()->create();
        $unidad->nombre = "Updated";

        // When
        $response = $this->put(route('unidades.update', $unidad), $unidad->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $unidad = Unidad::factory()->create();
        $unidad->nombre = "Updated";

        // When
        $response = $this->put(route('unidades.update', $unidad), $unidad->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->create();
        $empty = new Unidad();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('unidades.update', $unidad), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->create();
        $unidad->$field = null;

        // When
        $response = $this->put(route('unidades.update', $unidad), $unidad->toArray());

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
        $unidad = Unidad::factory()->create();

        // When
        $this->delete(route('unidades.destroy', $unidad));

        // Then
        unset($unidad->curso);
        $this->assertDatabaseMissing('unidades', $unidad->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->delete(route('unidades.destroy', $unidad));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $unidad = Unidad::factory()->create();

        // When
        $response = $this->delete(route('unidades.destroy', $unidad));

        // Then
        $response->assertRedirect(route('login'));
    }
}
