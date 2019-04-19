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

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->make();

        // When
        $this->post(route('organizations.store'), $organization->toArray());

        // Then
        $this->assertEquals(1, Organization::all()->count());
    }

    public function testNotAuthNotCreate()
    {
        // Given
        $organization = factory(Organization::class)->make();

        // When
        // Then
        $this->post(route('organizations.store'), $organization->toArray())
            ->assertRedirect(route('login'));
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->make();

        // When
        // Then
        $this->post(route('organizations.store'), $organization->toArray())
            ->assertForbidden();
    }

    public function testRequiredName()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('organizations.store'), $organization->toArray())
            ->assertSessionHasErrors('name');
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
}
