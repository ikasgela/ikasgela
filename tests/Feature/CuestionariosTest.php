<?php

namespace Tests\Feature;

use App\Cuestionario;
use App\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuestionariosTest extends TestCase
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
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        $response = $this->get(route('cuestionarios.index'));

        // Then
        $response->assertSee($cuestionario->name);
    }

    public function testNotProfesorNotIndex()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('cuestionarios.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('cuestionarios.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('cuestionarios.create'));

        // Then
        $response->assertSeeInOrder([__('New questionnaire'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('cuestionarios.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('cuestionarios.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->make();

        // When
        $this->post(route('cuestionarios.store'), $cuestionario->toArray());

        // Then
        $this->assertEquals(1, Cuestionario::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $cuestionario = factory(Cuestionario::class)->make();

        // When
        // Then
        $this->post(route('cuestionarios.store'), $cuestionario->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $cuestionario = factory(Cuestionario::class)->make();

        // When
        // Then
        $this->post(route('cuestionarios.store'), $cuestionario->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->make(['titulo' => null]);

        // When
        // Then
        $this->post(route('cuestionarios.store'), $cuestionario->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        $response = $this->get(route('cuestionarios.show', $cuestionario));

        // Then
        $response->assertSeeInOrder([__('Questionnaire'), $cuestionario->titulo]);
    }

    public function testNotProfesorNotShow()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        // Then
        $this->get(route('cuestionarios.show', $cuestionario))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        // Then
        $this->get(route('cuestionarios.show', $cuestionario))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        $response = $this->get(route('cuestionarios.edit', $cuestionario), $cuestionario->toArray());

        // Then
        $response->assertSeeInOrder([$cuestionario->titulo, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        // Then
        $this->get(route('cuestionarios.edit', $cuestionario), $cuestionario->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        // Then
        $this->get(route('cuestionarios.edit', $cuestionario), $cuestionario->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->create();
        $cuestionario->titulo = "Updated";

        // When
        $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray());

        // Then
        $this->assertDatabaseHas('cuestionarios', ['id' => $cuestionario->id, 'titulo' => $cuestionario->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $cuestionario = factory(Cuestionario::class)->create();
        $cuestionario->titulo = "Updated";

        // When
        // Then
        $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $cuestionario = factory(Cuestionario::class)->create();
        $cuestionario->titulo = "Updated";

        // When
        // Then
        $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        $cuestionario->titulo = null;

        // Then
        $this->put(route('cuestionarios.update', $cuestionario), $cuestionario->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        $this->delete(route('cuestionarios.destroy', $cuestionario));

        // Then
        $this->assertDatabaseMissing('cuestionarios', $cuestionario->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        // Then
        $this->delete(route('cuestionarios.destroy', $cuestionario))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        // Then
        $this->delete(route('cuestionarios.destroy', $cuestionario))
            ->assertRedirect(route('login'));
    }
}
