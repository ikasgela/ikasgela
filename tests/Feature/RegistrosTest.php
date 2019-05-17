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
        $this->actingAs($this->profesor);
        $registro = factory(Registro::class)->create();

        // When
        $response = $this->get(route('registros.index'));

        // Then
        $response->assertSee($registro->timestamp);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('registros.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('registros.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('registros.create'));

        // Then
        $response->assertSeeInOrder([__('New registro'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('registros.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('registros.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->make();

        // When
        $this->post(route('registros.store'), $registro->toArray());

        // Then
        $this->assertEquals(1, Registro::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $registro = factory(Registro::class)->make();

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertForbidden();
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

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresGroup()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->make(['group_id' => null]);

        // When
        // Then
        $this->post(route('registros.store'), $registro->toArray())
            ->assertSessionHasErrors('group_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->create();

        // When
        $response = $this->get(route('registros.show', ['id' => $registro->id]));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $registro = factory(Registro::class)->create();

        // When
        // Then
        $this->get(route('registros.show', ['id' => $registro->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $registro = factory(Registro::class)->create();

        // When
        // Then
        $this->get(route('registros.show', ['id' => $registro->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->create();

        // When
        $response = $this->get(route('registros.edit', ['id' => $registro->id]), $registro->toArray());

        // Then
        $response->assertSeeInOrder([$registro->name, $registro->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $registro = factory(Registro::class)->create();

        // When
        // Then
        $this->get(route('registros.edit', ['id' => $registro->id]), $registro->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $registro = factory(Registro::class)->create();

        // When
        // Then
        $this->get(route('registros.edit', ['id' => $registro->id]), $registro->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->create();
        $registro->name = "Updated";

        // When
        $this->put(route('registros.update', ['id' => $registro->id]), $registro->toArray());

        // Then
        $this->assertDatabaseHas('registros', ['id' => $registro->id, 'name' => $registro->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $registro = factory(Registro::class)->create();
        $registro->name = "Updated";

        // When
        // Then
        $this->put(route('registros.update', ['id' => $registro->id]), $registro->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $registro = factory(Registro::class)->create();
        $registro->name = "Updated";

        // When
        // Then
        $this->put(route('registros.update', ['id' => $registro->id]), $registro->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->create();

        // When
        $registro->name = null;

        // Then
        $this->put(route('registros.update', ['id' => $registro->id]), $registro->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresGroup()
    {
        // Given
        $this->actingAs($this->admin);
        $registro = factory(Registro::class)->create();

        // When
        $registro->group_id = null;

        // Then
        $this->put(route('registros.update', ['id' => $registro->id]), $registro->toArray())
            ->assertSessionHasErrors('group_id');
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
