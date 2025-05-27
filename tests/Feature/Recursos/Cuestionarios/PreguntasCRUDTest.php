<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use Override;
use App\Models\Pregunta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PreguntasCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo', 'texto', 'cuestionario_id'
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
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertStatus(404);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->make();
        $total = Pregunta::all()->count();

        // When
        $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $this->assertCount($total + 1, Pregunta::all());
    }

    public function testStoreThenEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->make();

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $guardado = Pregunta::orderBy('id', 'desc')->first();
        $response->assertLocation(route('preguntas.edit', $guardado));
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->make();

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->make();

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = Pregunta::all()->count();

        $empty = new Pregunta();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('preguntas.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->make([$field => null]);

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

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
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertStatus(404);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$pregunta->texto, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();
        $pregunta->texto = "Updated";

        // When
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $this->assertDatabaseHas('preguntas', ['id' => $pregunta->id, 'texto' => $pregunta->texto]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();
        $pregunta->texto = "Updated";

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();
        $pregunta->texto = "Updated";

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();
        $empty = new Pregunta();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();
        $pregunta->$field = null;

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

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
        $pregunta = Pregunta::factory()->create();

        // When
        $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $this->assertDatabaseMissing('preguntas', $pregunta->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $response->assertRedirect(route('login'));
    }
}
