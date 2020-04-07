<?php

namespace Tests\Feature;

use App\Qualification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QualificationsTest extends TestCase
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
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.index'));

        // Then
        $response->assertSee($qualification->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('qualifications.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('qualifications.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('qualifications.create'));

        // Then
        $response->assertSeeInOrder([__('New qualification'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('qualifications.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('qualifications.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->make();
        $total = Qualification::all()->count();

        // When
        $this->post(route('qualifications.store'), $qualification->toArray());

        // Then
        $this->assertEquals($total + 1, Qualification::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $qualification = factory(Qualification::class)->make();

        // When
        // Then
        $this->post(route('qualifications.store'), $qualification->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $qualification = factory(Qualification::class)->make();

        // When
        // Then
        $this->post(route('qualifications.store'), $qualification->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('qualifications.store'), $qualification->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresOrganization()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->make(['organization_id' => null]);

        // When
        // Then
        $this->post(route('qualifications.store'), $qualification->toArray())
            ->assertSessionHasErrors('organization_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.show', $qualification));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $qualification = factory(Qualification::class)->create();

        // When
        // Then
        $this->get(route('qualifications.show', $qualification))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        // Then
        $this->get(route('qualifications.show', $qualification))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.edit', $qualification), $qualification->toArray());

        // Then
        $response->assertSeeInOrder([$qualification->name, $qualification->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $qualification = factory(Qualification::class)->create();

        // When
        // Then
        $this->get(route('qualifications.edit', $qualification), $qualification->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        // Then
        $this->get(route('qualifications.edit', $qualification), $qualification->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";

        // When
        $this->put(route('qualifications.update', $qualification), $qualification->toArray());

        // Then
        $this->assertDatabaseHas('qualifications', ['id' => $qualification->id, 'name' => $qualification->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";

        // When
        // Then
        $this->put(route('qualifications.update', $qualification), $qualification->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";

        // When
        // Then
        $this->put(route('qualifications.update', $qualification), $qualification->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->create();

        // When
        $qualification->name = null;

        // Then
        $this->put(route('qualifications.update', $qualification), $qualification->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresOrganization()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->create();

        // When
        $qualification->organization_id = null;

        // Then
        $this->put(route('qualifications.update', $qualification), $qualification->toArray())
            ->assertSessionHasErrors('organization_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $qualification = factory(Qualification::class)->create();

        // When
        $this->delete(route('qualifications.destroy', $qualification));

        // Then
        $this->assertDatabaseMissing('qualifications', $qualification->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $qualification = factory(Qualification::class)->create();

        // When
        // Then
        $this->delete(route('qualifications.destroy', $qualification))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        // Then
        $this->delete(route('qualifications.destroy', $qualification))
            ->assertRedirect(route('login'));
    }
}
