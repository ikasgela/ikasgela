<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use App\Pregunta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PreguntasTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertSee($pregunta->name);
    }

    public function testNotProfesorNotIndex()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('preguntas.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('preguntas.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertSeeInOrder([__('New question'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('preguntas.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('preguntas.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->make();
        $total = Pregunta::all()->count();

        // When
        $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $this->assertEquals($total + 1, Pregunta::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $pregunta = factory(Pregunta::class)->make();

        // When
        // Then
        $this->post(route('preguntas.store'), $pregunta->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $pregunta = factory(Pregunta::class)->make();

        // When
        // Then
        $this->post(route('preguntas.store'), $pregunta->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->make(['titulo' => null]);

        // When
        // Then
        $this->post(route('preguntas.store'), $pregunta->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testStoreRequiresTexto()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->make(['texto' => null]);

        // When
        // Then
        $this->post(route('preguntas.store'), $pregunta->toArray())
            ->assertSessionHasErrors('texto');
    }

    public function testStoreRequiresCuestionario()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->make(['cuestionario_id' => null]);

        // When
        // Then
        $this->post(route('preguntas.store'), $pregunta->toArray())
            ->assertSessionHasErrors('cuestionario_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotProfesorNotShow()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        // Then
        $this->get(route('preguntas.show', $pregunta))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $pregunta = factory(Pregunta::class)->create();

        // When
        // Then
        $this->get(route('preguntas.show', $pregunta))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertSeeInOrder([$pregunta->titulo, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        // Then
        $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $pregunta = factory(Pregunta::class)->create();

        // When
        // Then
        $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();
        $pregunta->titulo = "Updated";

        // When
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $this->assertDatabaseHas('preguntas', ['id' => $pregunta->id, 'titulo' => $pregunta->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $pregunta = factory(Pregunta::class)->create();
        $pregunta->titulo = "Updated";

        // When
        // Then
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $pregunta = factory(Pregunta::class)->create();
        $pregunta->titulo = "Updated";

        // When
        // Then
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $pregunta->titulo = null;

        // Then
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testUpdateRequiresTexto()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $pregunta->texto = null;

        // Then
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray())
            ->assertSessionHasErrors('texto');
    }

    public function testUpdateRequiresCuestionario()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $pregunta->cuestionario_id = null;

        // Then
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray())
            ->assertSessionHasErrors('cuestionario_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $this->assertDatabaseMissing('preguntas', $pregunta->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $pregunta = factory(Pregunta::class)->create();

        // When
        // Then
        $this->delete(route('preguntas.destroy', $pregunta))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $pregunta = factory(Pregunta::class)->create();

        // When
        // Then
        $this->delete(route('preguntas.destroy', $pregunta))
            ->assertRedirect(route('login'));
    }
}
