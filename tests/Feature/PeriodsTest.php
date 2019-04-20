<?php

namespace Tests\Feature;

use App\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodsTest extends TestCase
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
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();

        // When
        $response = $this->get(route('periods.index'));

        // Then
        $response->assertSee($period->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('periods.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('periods.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('periods.create'));

        // Then
        $response->assertSee(__('New period'));
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('periods.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('periods.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->make();

        // When
        $this->post(route('periods.store'), $period->toArray());

        // Then
        $this->assertEquals(1, Period::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $period = factory(Period::class)->make();

        // When
        // Then
        $this->post(route('periods.store'), $period->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $period = factory(Period::class)->make();

        // When
        // Then
        $this->post(route('periods.store'), $period->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('periods.store'), $period->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresOrganization()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->make(['organization_id' => null]);

        // When
        // Then
        $this->post(route('periods.store'), $period->toArray())
            ->assertSessionHasErrors('organization_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();

        // When
        $response = $this->get(route('periods.show', ['id' => $period->id]));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $period = factory(Period::class)->create();

        // When
        // Then
        $this->get(route('periods.show', ['id' => $period->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $period = factory(Period::class)->create();

        // When
        // Then
        $this->get(route('periods.show', ['id' => $period->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();

        // When
        $response = $this->get(route('periods.edit', ['id' => $period->id]), $period->toArray());

        // Then
        $response->assertSeeInOrder([$period->name, $period->slug]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $period = factory(Period::class)->create();

        // When
        // Then
        $this->get(route('periods.edit', ['id' => $period->id]), $period->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $period = factory(Period::class)->create();

        // When
        // Then
        $this->get(route('periods.edit', ['id' => $period->id]), $period->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();
        $period->name = "Updated";

        // When
        $this->put(route('periods.update', ['id' => $period->id]), $period->toArray());

        // Then
        $this->assertDatabaseHas('periods', ['id' => $period->id, 'name' => $period->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $period = factory(Period::class)->create();
        $period->name = "Updated";

        // When
        // Then
        $this->put(route('periods.update', ['id' => $period->id]), $period->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $period = factory(Period::class)->create();
        $period->name = "Updated";

        // When
        // Then
        $this->put(route('periods.update', ['id' => $period->id]), $period->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();

        // When
        $period->name = null;

        // Then
        $this->put(route('periods.update', ['id' => $period->id]), $period->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresOrganization()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();

        // When
        $period->organization_id = null;

        // Then
        $this->put(route('periods.update', ['id' => $period->id]), $period->toArray())
            ->assertSessionHasErrors('organization_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $period = factory(Period::class)->create();

        // When
        $this->delete(route('periods.destroy', ['id' => $period->id]));

        // Then
        $this->assertDatabaseMissing('periods', ['id' => $period->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $period = factory(Period::class)->create();

        // When
        // Then
        $this->delete(route('periods.destroy', ['id' => $period->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $period = factory(Period::class)->create();

        // When
        // Then
        $this->delete(route('periods.destroy', ['id' => $period->id]))
            ->assertRedirect(route('login'));
    }
}
