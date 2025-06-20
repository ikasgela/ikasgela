<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use Override;
use App\Models\Cuestionario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuestionariosCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo'
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
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create([
            'plantilla' => true,
        ]);
        session(['filtrar_curso_actual' => $cuestionario->curso_id]);

        // When
        $response = $this->get(route('cuestionarios.index'));

        // Then
        $response->assertSuccessful()->assertSee($cuestionario->titulo);
    }

    public function testNotPlantillaNotIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.index'));

        // Then
        $response->assertDontSee($cuestionario->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('cuestionarios.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('cuestionarios.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('cuestionarios.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New questionnaire'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('cuestionarios.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('cuestionarios.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->make();
        $total = Cuestionario::all()->count();

        // When
        $this->post(route('cuestionarios.store'), $cuestionario->toArray());

        // Then
        $this->assertCount($total + 1, Cuestionario::all());
    }

    public function testStoreThenEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->make();

        // When
        $response = $this->post(route('cuestionarios.store'), $cuestionario->toArray());

        // Then
        $guardado = Cuestionario::orderBy('id', 'desc')->first();
        $response->assertLocation(route('cuestionarios.edit', $guardado));
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $cuestionario = Cuestionario::factory()->make();

        // When
        $response = $this->post(route('cuestionarios.store'), $cuestionario->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $cuestionario = Cuestionario::factory()->make();

        // When
        $response = $this->post(route('cuestionarios.store'), $cuestionario->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = Cuestionario::all()->count();

        $empty = new Cuestionario();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('cuestionarios.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->make([$field => null]);

        // When
        $response = $this->post(route('cuestionarios.store'), $cuestionario->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.show', $cuestionario));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Questionnaire'), $cuestionario->titulo]);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.show', $cuestionario));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.show', $cuestionario));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.edit', $cuestionario), $cuestionario->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$cuestionario->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.edit', $cuestionario), $cuestionario->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('cuestionarios.edit', $cuestionario), $cuestionario->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();
        $cuestionario->titulo = "Updated";

        // When
        $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray());

        // Then
        $this->assertDatabaseHas('cuestionarios', ['id' => $cuestionario->id, 'titulo' => $cuestionario->titulo]);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();
        $cuestionario->titulo = "Updated";

        // When
        $response = $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $cuestionario = Cuestionario::factory()->create();
        $cuestionario->titulo = "Updated";

        // When
        $response = $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();
        $empty = new Cuestionario();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('cuestionarios.update', $cuestionario), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();
        $cuestionario->$field = null;

        // When
        $response = $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $this->delete(route('cuestionarios.destroy', $cuestionario));

        // Then
        $this->assertDatabaseMissing('cuestionarios', $cuestionario->toArray());
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->delete(route('cuestionarios.destroy', $cuestionario));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->delete(route('cuestionarios.destroy', $cuestionario));

        // Then
        $response->assertRedirect(route('login'));
    }
}
