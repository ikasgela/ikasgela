<?php

namespace Tests\Feature;

use App\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsTest extends TestCase
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
        $skill = factory(Skill::class)->create();

        // When
        $response = $this->get(route('skills.index'));

        // Then
        $response->assertSee($skill->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('skills.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('skills.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('skills.create'));

        // Then
        $response->assertSeeInOrder([__('New skill'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('skills.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('skills.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->make();

        // When
        $this->post(route('skills.store'), $skill->toArray());

        // Then
        $this->assertEquals(1, Skill::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $skill = factory(Skill::class)->make();

        // When
        // Then
        $this->post(route('skills.store'), $skill->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $skill = factory(Skill::class)->make();

        // When
        // Then
        $this->post(route('skills.store'), $skill->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('skills.store'), $skill->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresOrganization()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->make(['organization_id' => null]);

        // When
        // Then
        $this->post(route('skills.store'), $skill->toArray())
            ->assertSessionHasErrors('organization_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->create();

        // When
        $response = $this->get(route('skills.show', ['id' => $skill->id]));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $skill = factory(Skill::class)->create();

        // When
        // Then
        $this->get(route('skills.show', ['id' => $skill->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $skill = factory(Skill::class)->create();

        // When
        // Then
        $this->get(route('skills.show', ['id' => $skill->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->create();

        // When
        $response = $this->get(route('skills.edit', ['id' => $skill->id]), $skill->toArray());

        // Then
        $response->assertSeeInOrder([$skill->name, $skill->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $skill = factory(Skill::class)->create();

        // When
        // Then
        $this->get(route('skills.edit', ['id' => $skill->id]), $skill->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $skill = factory(Skill::class)->create();

        // When
        // Then
        $this->get(route('skills.edit', ['id' => $skill->id]), $skill->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->create();
        $skill->name = "Updated";

        // When
        $this->put(route('skills.update', ['id' => $skill->id]), $skill->toArray());

        // Then
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => $skill->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $skill = factory(Skill::class)->create();
        $skill->name = "Updated";

        // When
        // Then
        $this->put(route('skills.update', ['id' => $skill->id]), $skill->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $skill = factory(Skill::class)->create();
        $skill->name = "Updated";

        // When
        // Then
        $this->put(route('skills.update', ['id' => $skill->id]), $skill->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->create();

        // When
        $skill->name = null;

        // Then
        $this->put(route('skills.update', ['id' => $skill->id]), $skill->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresOrganization()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->create();

        // When
        $skill->organization_id = null;

        // Then
        $this->put(route('skills.update', ['id' => $skill->id]), $skill->toArray())
            ->assertSessionHasErrors('organization_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $skill = factory(Skill::class)->create();

        // When
        $this->delete(route('skills.destroy', ['id' => $skill->id]));

        // Then
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $skill = factory(Skill::class)->create();

        // When
        // Then
        $this->delete(route('skills.destroy', ['id' => $skill->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $skill = factory(Skill::class)->create();

        // When
        // Then
        $this->delete(route('skills.destroy', ['id' => $skill->id]))
            ->assertRedirect(route('login'));
    }
}
