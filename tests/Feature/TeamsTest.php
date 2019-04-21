<?php

namespace Tests\Feature;

use App\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamsTest extends TestCase
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
        $team = factory(Team::class)->create();

        // When
        $response = $this->get(route('teams.index'));

        // Then
        $response->assertSee($team->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('teams.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('teams.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('teams.create'));

        // Then
        $response->assertSeeInOrder([__('New team'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('teams.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('teams.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->make();

        // When
        $this->post(route('teams.store'), $team->toArray());

        // Then
        $this->assertEquals(1, Team::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $team = factory(Team::class)->make();

        // When
        // Then
        $this->post(route('teams.store'), $team->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $team = factory(Team::class)->make();

        // When
        // Then
        $this->post(route('teams.store'), $team->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('teams.store'), $team->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresGroup()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->make(['group_id' => null]);

        // When
        // Then
        $this->post(route('teams.store'), $team->toArray())
            ->assertSessionHasErrors('group_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->create();

        // When
        $response = $this->get(route('teams.show', ['id' => $team->id]));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $team = factory(Team::class)->create();

        // When
        // Then
        $this->get(route('teams.show', ['id' => $team->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $team = factory(Team::class)->create();

        // When
        // Then
        $this->get(route('teams.show', ['id' => $team->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->create();

        // When
        $response = $this->get(route('teams.edit', ['id' => $team->id]), $team->toArray());

        // Then
        $response->assertSeeInOrder([$team->name, $team->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $team = factory(Team::class)->create();

        // When
        // Then
        $this->get(route('teams.edit', ['id' => $team->id]), $team->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $team = factory(Team::class)->create();

        // When
        // Then
        $this->get(route('teams.edit', ['id' => $team->id]), $team->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->create();
        $team->name = "Updated";

        // When
        $this->put(route('teams.update', ['id' => $team->id]), $team->toArray());

        // Then
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => $team->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $team = factory(Team::class)->create();
        $team->name = "Updated";

        // When
        // Then
        $this->put(route('teams.update', ['id' => $team->id]), $team->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $team = factory(Team::class)->create();
        $team->name = "Updated";

        // When
        // Then
        $this->put(route('teams.update', ['id' => $team->id]), $team->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->create();

        // When
        $team->name = null;

        // Then
        $this->put(route('teams.update', ['id' => $team->id]), $team->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresGroup()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->create();

        // When
        $team->group_id = null;

        // Then
        $this->put(route('teams.update', ['id' => $team->id]), $team->toArray())
            ->assertSessionHasErrors('group_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $team = factory(Team::class)->create();

        // When
        $this->delete(route('teams.destroy', ['id' => $team->id]));

        // Then
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $team = factory(Team::class)->create();

        // When
        // Then
        $this->delete(route('teams.destroy', ['id' => $team->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $team = factory(Team::class)->create();

        // When
        // Then
        $this->delete(route('teams.destroy', ['id' => $team->id]))
            ->assertRedirect(route('login'));
    }
}
