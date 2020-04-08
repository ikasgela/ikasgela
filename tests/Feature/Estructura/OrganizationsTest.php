<?php

namespace Tests\Feature\Estructura;

use App\Organization;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrganizationsTest extends TestCase
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
        $response->assertSeeInOrder([__('New organization'), __('Save')]);
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
        $total = Organization::all()->count();

        // When
        $this->post(route('organizations.store'), $organization->toArray());

        // Then
        $this->assertEquals($total + 1, Organization::all()->count());
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
        $response = $this->get(route('organizations.show', $organization));

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
        $this->get(route('organizations.show', $organization))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.show', $organization))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.edit', $organization), $organization->toArray());

        // Then
        $response->assertSeeInOrder([$organization->name, $organization->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.edit', $organization), $organization->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->get(route('organizations.edit', $organization), $organization->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        $this->put(route('organizations.update', $organization), $organization->toArray());

        // Then
        $this->assertDatabaseHas('organizations', ['id' => $organization->id, 'name' => $organization->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        // Then
        $this->put(route('organizations.update', $organization), $organization->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        // Then
        $this->put(route('organizations.update', $organization), $organization->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();

        // When
        $organization->name = null;

        // Then
        $this->put(route('organizations.update', $organization), $organization->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $organization = factory(Organization::class)->create();

        // When
        $this->delete(route('organizations.destroy', $organization));

        // Then
        $this->assertDatabaseMissing('organizations', $organization->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->delete(route('organizations.destroy', $organization))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        // Then
        $this->delete(route('organizations.destroy', $organization))
            ->assertRedirect(route('login'));
    }
}
