<?php

namespace Tests\Feature;

use App\Registro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrosTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->alumno);
        $registro = factory(Registro::class)->create();

        // When
        $response = $this->get(route('registros.index'));

        // Then
        $response->assertSee($registro->timestamp);
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('registros.index'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->alumno);
        $registro = factory(Registro::class)->make();

        // When
        $this->post(route('registros.store'), $registro->toArray());

        // Then
        $this->assertEquals(1, Registro::all()->count());
    }

    public function testNotAuthNotStore()
    {
        // Given
        $registro = factory(Registro::class)->make();

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresUser()
    {
        // Given
        $this->actingAs($this->alumno);
        $registro = factory(Registro::class)->make(['user_id' => null]);

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertSessionHasErrors('user_id');
    }

    public function testStoreRequiresTarea()
    {
        // Given
        $this->actingAs($this->alumno);
        $registro = factory(Registro::class)->make(['tarea_id' => null]);

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertSessionHasErrors('tarea_id');
    }

    public function testStoreRequireEstado()
    {
        // Given
        $this->actingAs($this->alumno);
        $registro = factory(Registro::class)->make(['estado' => null]);

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertSessionHasErrors('estado');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->create();

        // When
        $this->delete(route('registros.destroy', ['id' => $registro->id]));

        // Then
        $this->assertDatabaseMissing('registros', ['id' => $registro->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $registro = factory(Registro::class)->create();

        // When
        // Then
        $this->delete(route('registros.destroy', ['id' => $registro->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $registro = factory(Registro::class)->create();

        // When
        // Then
        $this->delete(route('registros.destroy', ['id' => $registro->id]))
            ->assertRedirect(route('login'));
    }
}
