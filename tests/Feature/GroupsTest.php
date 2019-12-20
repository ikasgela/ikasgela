<?php

namespace Tests\Feature;

use App\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupsTest extends TestCase
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
        $group = factory(Group::class)->create();

        // When
        $response = $this->get(route('groups.index'));

        // Then
        $response->assertSee($group->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('groups.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('groups.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('groups.create'));

        // Then
        $response->assertSeeInOrder([__('New group'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('groups.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('groups.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->make();

        // When
        $this->post(route('groups.store'), $group->toArray());

        // Then
        $this->assertEquals(1, Group::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $group = factory(Group::class)->make();

        // When
        // Then
        $this->post(route('groups.store'), $group->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $group = factory(Group::class)->make();

        // When
        // Then
        $this->post(route('groups.store'), $group->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('groups.store'), $group->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresPeriod()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->make(['period_id' => null]);

        // When
        // Then
        $this->post(route('groups.store'), $group->toArray())
            ->assertSessionHasErrors('period_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->create();

        // When
        $response = $this->get(route('groups.show', $group));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $group = factory(Group::class)->create();

        // When
        // Then
        $this->get(route('groups.show', $group))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $group = factory(Group::class)->create();

        // When
        // Then
        $this->get(route('groups.show', $group))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->create();

        // When
        $response = $this->get(route('groups.edit', $group), $group->toArray());

        // Then
        $response->assertSeeInOrder([$group->name, $group->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $group = factory(Group::class)->create();

        // When
        // Then
        $this->get(route('groups.edit', $group), $group->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $group = factory(Group::class)->create();

        // When
        // Then
        $this->get(route('groups.edit', $group), $group->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->create();
        $group->name = "Updated";

        // When
        $this->put(route('groups.update', $group), $group->toArray());

        // Then
        $this->assertDatabaseHas('groups', ['id' => $group->id, 'name' => $group->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $group = factory(Group::class)->create();
        $group->name = "Updated";

        // When
        // Then
        $this->put(route('groups.update', $group), $group->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $group = factory(Group::class)->create();
        $group->name = "Updated";

        // When
        // Then
        $this->put(route('groups.update', $group), $group->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->create();

        // When
        $group->name = null;

        // Then
        $this->put(route('groups.update', $group), $group->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresPeriod()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->create();

        // When
        $group->period_id = null;

        // Then
        $this->put(route('groups.update', $group), $group->toArray())
            ->assertSessionHasErrors('period_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $group = factory(Group::class)->create();

        // When
        $this->delete(route('groups.destroy', $group));

        // Then
        $this->assertDatabaseMissing('groups', $group->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $group = factory(Group::class)->create();

        // When
        // Then
        $this->delete(route('groups.destroy', $group))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $group = factory(Group::class)->create();

        // When
        // Then
        $this->delete(route('groups.destroy', $group))
            ->assertRedirect(route('login'));
    }
}
