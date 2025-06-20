<?php

namespace Tests\Feature\Evaluacion;

use Override;
use App\Models\Skill;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SkillsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'curso_id'
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
        $skill = Skill::factory()->create();
        session(['filtrar_curso_actual' => $skill->curso_id]);

        // When
        $response = $this->get(route('skills.index'));

        // Then
        $response->assertSuccessful()->assertSee($skill->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('skills.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('skills.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('skills.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New skill'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('skills.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('skills.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $skill = Skill::factory()->make();
        $total = Skill::all()->count();

        // When
        $this->post(route('skills.store'), $skill->toArray());

        // Then
        $this->assertEquals($total + 1, Skill::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $skill = Skill::factory()->make();

        // When
        $response = $this->post(route('skills.store'), $skill->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $skill = Skill::factory()->make();

        // When
        $response = $this->post(route('skills.store'), $skill->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Skill::all()->count();

        $empty = new Skill();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('skills.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $skill = Skill::factory()->make([$field => null]);

        // When
        $response = $this->post(route('skills.store'), $skill->toArray());

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
        $skill = Skill::factory()->create();

        // When
        $response = $this->get(route('skills.show', $skill));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->get(route('skills.show', $skill));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->get(route('skills.show', $skill));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->get(route('skills.edit', $skill), $skill->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$skill->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->get(route('skills.edit', $skill), $skill->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->get(route('skills.edit', $skill), $skill->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $skill = Skill::factory()->create();
        sleep(2);
        $skill->name = "Updated";
        // When
        $this->put(route('skills.update', $skill), $skill->toArray());

        // Then
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => $skill->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $skill = Skill::factory()->create();

        // When
        $skill->name = "Updated";
        $response = $this->put(route('skills.update', $skill), $skill->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $skill = Skill::factory()->create();

        // When
        $skill->name = "Updated";
        $response = $this->put(route('skills.update', $skill), $skill->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $skill = Skill::factory()->create();
        $empty = new Skill();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('skills.update', $skill), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $skill = Skill::factory()->create();

        // When
        $skill->$field = null;
        $response = $this->put(route('skills.update', $skill), $skill->toArray());

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
        $skill = Skill::factory()->create();

        // When
        $this->delete(route('skills.destroy', $skill));

        // Then
        $this->assertDatabaseMissing('skills', $skill->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->delete(route('skills.destroy', $skill));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $skill = Skill::factory()->create();

        // When
        $response = $this->delete(route('skills.destroy', $skill));

        // Then
        $response->assertRedirect(route('login'));
    }
}
