<?php

namespace Tests\Feature\Usuarios;

use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeamsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'group_id'
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
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.index'));

        // Then
        $response->assertSee($team->name);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('teams.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('teams.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('teams.create'));

        // Then
        $response->assertSeeInOrder([__('New team'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('teams.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('teams.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $team = Team::factory()->make();
        $total = Team::all()->count();

        // When
        $this->post(route('teams.store'), $team->toArray());

        // Then
        $this->assertEquals($total + 1, Team::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $team = Team::factory()->make();

        // When
        $response = $this->post(route('teams.store'), $team->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $team = Team::factory()->make();

        // When
        $response = $this->post(route('teams.store'), $team->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Team::all()->count();

        $empty = new Team();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('teams.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $team = Team::factory()->make([$field => null]);

        // When
        $response = $this->post(route('teams.store'), $team->toArray());

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
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.show', $team));

        // Then
        $response->assertSee($team->name);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.show', $team));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.show', $team));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.edit', $team), $team->toArray());

        // Then
        $response->assertSeeInOrder([$team->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.edit', $team), $team->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->get(route('teams.edit', $team), $team->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $team = Team::factory()->create();
        $team->name = "Updated";

        // When
        $this->put(route('teams.update', $team), $team->toArray());

        // Then
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => $team->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $team = Team::factory()->create();
        $team->name = "Updated";

        // When
        $response = $this->put(route('teams.update', $team), $team->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $team = Team::factory()->create();
        $team->name = "Updated";

        // When
        $response = $this->put(route('teams.update', $team), $team->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $team = Team::factory()->create();
        $empty = new Team();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('teams.update', $team), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $team = Team::factory()->create();
        $team->$field = null;

        // When
        $response = $this->put(route('teams.update', $team), $team->toArray());

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
        $team = Team::factory()->create();

        // When
        $this->delete(route('teams.destroy', $team));

        // Then
        $this->assertDatabaseMissing('teams', $team->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->delete(route('teams.destroy', $team));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $team = Team::factory()->create();

        // When
        $response = $this->delete(route('teams.destroy', $team));

        // Then
        $response->assertRedirect(route('login'));
    }
}
