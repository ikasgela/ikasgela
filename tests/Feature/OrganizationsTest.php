<?php

namespace Tests\Feature;

use App\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationsTest extends TestCase
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
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.index'));

        // Then
        $response->assertSee($organization->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('organizations.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('organizations.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('organizations.create'));

        // Then
        $response->assertSee(__('New organization'));
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('organizations.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('organizations.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->make();

        // When
        $this->post(route('organizations.store'), $organization->toArray());

        // Then
        $this->assertEquals(1, Organization::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->make();

        // When
        // Then
        $this->post(route('organizations.store'), $organization->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $organization = factory(Organization::class)->make();

        // When
        // Then
        $this->post(route('organizations.store'), $organization->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('organizations.store'), $organization->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.show', ['id' => $organization->id]));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.show', ['id' => $organization->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.show', ['id' => $organization->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.edit', ['id' => $organization->id]), $organization->toArray());

        // Then
        $response->assertSeeInOrder([$organization->name, $organization->slug]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.edit', ['id' => $organization->id]), $organization->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.edit', ['id' => $organization->id]), $organization->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        $this->put(route('organizations.update', ['id' => $organization->id]), $organization->toArray());

        // Then
        $this->assertDatabaseHas('organizations', $organization->toArray());
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        // Then
        $this->put(route('organizations.update', ['id' => $organization->id]), $organization->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        // Then
        $this->put(route('organizations.update', ['id' => $organization->id]), $organization->toArray())
            ->assertRedirect(route('login'));
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();

        // When
        $this->delete(route('organizations.destroy', ['id' => $organization->id]));

        // Then
        $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->delete(route('organizations.destroy', ['id' => $organization->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->delete(route('organizations.destroy', ['id' => $organization->id]))
            ->assertRedirect(route('login'));
    }
}
