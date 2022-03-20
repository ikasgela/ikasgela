<?php

namespace Tests\Feature\Usuarios;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RolesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'description'
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
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.index'));

        // Then
        $response->assertSuccessful()->assertSee($role->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('roles.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('roles.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('roles.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New role'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('roles.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('roles.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $role = Role::factory()->make();
        $total = Role::all()->count();

        // When
        $this->post(route('roles.store'), $role->toArray());

        // Then
        $this->assertEquals($total + 1, Role::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $role = Role::factory()->make();

        // When
        $response = $this->post(route('roles.store'), $role->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $role = Role::factory()->make();

        // When
        $response = $this->post(route('roles.store'), $role->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Role::all()->count();

        $empty = new Role();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('roles.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $role = Role::factory()->make([$field => null]);

        // When
        $response = $this->post(route('roles.store'), $role->toArray());

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
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.show', $role));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.show', $role));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.show', $role));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.edit', $role), $role->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$role->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.edit', $role), $role->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->get(route('roles.edit', $role), $role->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $role = Role::factory()->create();
        $role->name = "Updated";

        // When
        $this->put(route('roles.update', $role), $role->toArray());

        // Then
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => $role->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $role = Role::factory()->create();
        $role->name = "Updated";

        // When
        $response = $this->put(route('roles.update', $role), $role->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $role = Role::factory()->create();
        $role->name = "Updated";

        // When
        $response = $this->put(route('roles.update', $role), $role->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $role = Role::factory()->create();
        $empty = new Role();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('roles.update', $role), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $role = Role::factory()->create();
        $role->$field = null;

        // When
        $response = $this->put(route('roles.update', $role), $role->toArray());

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
        $role = Role::factory()->create();

        // When
        $this->delete(route('roles.destroy', $role));

        // Then
        $this->assertDatabaseMissing('roles', $role->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->delete(route('roles.destroy', $role));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $role = Role::factory()->create();

        // When
        $response = $this->delete(route('roles.destroy', $role));

        // Then
        $response->assertRedirect(route('login'));
    }
}
