<?php

namespace Tests\Feature\Usuarios;

use App\Models\Group;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GroupsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'period_id'
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
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.index'));

        // Then
        $response->assertSee($group->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('groups.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('groups.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('groups.create'));

        // Then
        $response->assertSeeInOrder([__('New group'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('groups.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('groups.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $group = Group::factory()->make();
        $total = Group::all()->count();

        // When
        $this->post(route('groups.store'), $group->toArray());

        // Then
        $this->assertEquals($total + 1, Group::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $group = Group::factory()->make();

        // When
        $response = $this->post(route('groups.store'), $group->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $group = Group::factory()->make();

        // When
        $response = $this->post(route('groups.store'), $group->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Group::all()->count();

        $empty = new Group();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('groups.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $group = Group::factory()->make([$field => null]);

        // When
        $response = $this->post(route('groups.store'), $group->toArray());

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
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.show', $group));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.show', $group));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.show', $group));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.edit', $group), $group->toArray());

        // Then
        $response->assertSeeInOrder([$group->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.edit', $group), $group->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->get(route('groups.edit', $group), $group->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $group = Group::factory()->create();
        $group->name = "Updated";

        // When
        $this->put(route('groups.update', $group), $group->toArray());

        // Then
        $this->assertDatabaseHas('groups', ['id' => $group->id, 'name' => $group->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $group = Group::factory()->create();
        $group->name = "Updated";

        // When
        $response = $this->put(route('groups.update', $group), $group->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $group = Group::factory()->create();
        $group->name = "Updated";

        // When
        $response = $this->put(route('groups.update', $group), $group->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $group = Group::factory()->create();
        $empty = new Group();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('groups.update', $group), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $group = Group::factory()->create();
        $group->$field = null;

        // When
        $response = $this->put(route('groups.update', $group), $group->toArray());

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
        $group = Group::factory()->create();

        // When
        $this->delete(route('groups.destroy', $group));

        // Then
        $this->assertDatabaseMissing('groups', $group->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->delete(route('groups.destroy', $group));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $group = Group::factory()->create();

        // When
        $response = $this->delete(route('groups.destroy', $group));

        // Then
        $response->assertRedirect(route('login'));
    }
}
