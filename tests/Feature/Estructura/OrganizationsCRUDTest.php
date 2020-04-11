<?php

namespace Tests\Feature\Estructura;

use App\Organization;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrganizationsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name'
    ];

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.index'));

        // Then
        $response->assertSee($organization->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('organizations.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('organizations.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('organizations.create'));

        // Then
        $response->assertSeeInOrder([__('New organization'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('organizations.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('organizations.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->make();
        $total = Organization::all()->count();

        // When
        $this->post(route('organizations.store'), $organization->toArray());

        // Then
        $this->assertEquals($total + 1, Organization::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $organization = factory(Organization::class)->make();

        // When
        $response = $this->post(route('organizations.store'), $organization->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $organization = factory(Organization::class)->make();

        // When
        $response = $this->post(route('organizations.store'), $organization->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Organization::all()->count();

        $empty = new Organization();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('organizations.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->make([$field => null]);

        // When
        $response = $this->post(route('organizations.store'), $organization->toArray());

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
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.show', $organization));

        // Then
        $response->assertStatus(501);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.show', $organization));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.show', $organization));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.edit', $organization), $organization->toArray());

        // Then
        $response->assertSeeInOrder([$organization->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.edit', $organization), $organization->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->get(route('organizations.edit', $organization), $organization->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        $this->put(route('organizations.update', $organization), $organization->toArray());

        // Then
        $this->assertDatabaseHas('organizations', ['id' => $organization->id, 'name' => $organization->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        $response = $this->put(route('organizations.update', $organization), $organization->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $organization = factory(Organization::class)->create();
        $organization->name = "Updated";

        // When
        $response = $this->put(route('organizations.update', $organization), $organization->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();
        $empty = new Organization();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('organizations.update', $organization), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();
        $organization->$field = null;

        // When
        $response = $this->put(route('organizations.update', $organization), $organization->toArray());

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
        $this->actingAs($this->admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $this->delete(route('organizations.destroy', $organization));

        // Then
        $this->assertDatabaseMissing('organizations', $organization->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->delete(route('organizations.destroy', $organization));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this->delete(route('organizations.destroy', $organization));

        // Then
        $response->assertRedirect(route('login'));
    }
}
