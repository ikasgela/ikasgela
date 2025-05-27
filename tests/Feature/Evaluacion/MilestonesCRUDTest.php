<?php

namespace Tests\Feature\Evaluacion;

use Override;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MilestonesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'curso_id', 'name', 'date'
    ];

    #[Override]
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
        $milestone = Milestone::factory()->create();
        setting_usuario(['curso_actual' => $milestone->curso_id]);

        // When
        $response = $this->get(route('milestones.index'));

        // Then
        $response->assertSuccessful()->assertSee($milestone->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('milestones.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('milestones.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('milestones.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New milestone'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('milestones.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('milestones.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make();
        setting_usuario(['curso_actual' => $milestone->curso_id]);
        $total = Milestone::all()->count();

        // When
        $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $this->assertEquals($total + 1, Milestone::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->make();

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->make();

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Milestone::all()->count();

        $empty = new Milestone();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('milestones.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->make([$field => null]);

        // When
        $response = $this->post(route('milestones.store'), $milestone->toArray());

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
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.show', $milestone));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.show', $milestone));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.show', $milestone));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.edit', $milestone), $milestone->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$milestone->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.edit', $milestone), $milestone->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->get(route('milestones.edit', $milestone), $milestone->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        $milestone->name = "Updated";

        // When
        $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $this->assertDatabaseHas('milestones', ['id' => $milestone->id, 'name' => $milestone->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();
        $milestone->name = "Updated";

        // When
        $response = $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->create();
        $milestone->name = "Updated";

        // When
        $response = $this->put(route('milestones.update', $milestone), $milestone->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        $empty = new Milestone();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('milestones.update', $milestone), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $milestone = Milestone::factory()->create();
        $milestone->$field = null;

        // When
        $response = $this->put(route('milestones.update', $milestone), $milestone->toArray());

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
        $milestone = Milestone::factory()->create();

        // When
        $this->delete(route('milestones.destroy', $milestone));

        // Then
        $this->assertDatabaseMissing('milestones', $milestone->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->delete(route('milestones.destroy', $milestone));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $milestone = Milestone::factory()->create();

        // When
        $response = $this->delete(route('milestones.destroy', $milestone));

        // Then
        $response->assertRedirect(route('login'));
    }
}
