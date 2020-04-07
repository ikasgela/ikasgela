<?php

namespace Tests\Feature;

use App\IntellijProject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IntellijProjectsCRUDTest extends TestCase
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
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        $response = $this->get(route('intellij_projects.index'));

        // Then
        $response->assertSee($intellij_project->repositorio);
    }

    public function testNotAllowedRoleNotIndex()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('intellij_projects.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('intellij_projects.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('intellij_projects.create'));

        // Then
        $response->assertSeeInOrder([__('New IntelliJ project'), __('Save')]);
    }

    public function testNotAllowedRoleNotCreate()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('intellij_projects.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('intellij_projects.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->make();
        $total = IntellijProject::all()->count();

        // When
        $this->post(route('intellij_projects.store'), $intellij_project->toArray());

        // Then
        $this->assertCount($total + 1, IntellijProject::all());
    }

    public function testNotAllowedRoleNotStore()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $intellij_project = factory(IntellijProject::class)->make();

        // When
        // Then
        $this->post(route('intellij_projects.store'), $intellij_project->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $intellij_project = factory(IntellijProject::class)->make();

        // When
        // Then
        $this->post(route('intellij_projects.store'), $intellij_project->toArray())
            ->assertRedirect(route('login'));
    }

    private function storeRequires(string $field)
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->make([$field => null]);

        // When
        // Then
        $this->post(route('intellij_projects.store'), $intellij_project->toArray())
            ->assertSessionHasErrors($field);
    }

    public function testStoreRequiresRepositorio()
    {
        $this->storeRequires('repositorio');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        $response = $this->get(route('intellij_projects.show', $intellij_project));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAllowedRoleNotShow()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        // Then
        $this->get(route('intellij_projects.show', $intellij_project))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        // Then
        $this->get(route('intellij_projects.show', $intellij_project))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        $response = $this->get(route('intellij_projects.edit', $intellij_project), $intellij_project->toArray());

        // Then
        $response->assertSeeInOrder([$intellij_project->repositorio, $intellij_project->slug, __('Save')]);
    }

    public function testNotAllowedRoleNotEdit()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        // Then
        $this->get(route('intellij_projects.edit', $intellij_project), $intellij_project->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        // Then
        $this->get(route('intellij_projects.edit', $intellij_project), $intellij_project->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->create();
        $intellij_project->repositorio = "Updated";

        // When
        $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray());

        // Then
        $this->assertDatabaseHas('intellij_projects', ['id' => $intellij_project->id, 'repositorio' => $intellij_project->repositorio]);
    }

    public function testNotAllowedRoleNotUpdate()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $intellij_project = factory(IntellijProject::class)->create();
        $intellij_project->repositorio = "Updated";

        // When
        // Then
        $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $intellij_project = factory(IntellijProject::class)->create();
        $intellij_project->repositorio = "Updated";

        // When
        // Then
        $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray())
            ->assertRedirect(route('login'));
    }

    private function updateRequires(string $field)
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        $intellij_project->$field = null;

        // Then
        $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray())
            ->assertSessionHasErrors($field);
    }

    public function testUpdateRequiresRepositorio()
    {
        $this->updateRequires('repositorio');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        $this->delete(route('intellij_projects.destroy', $intellij_project));

        // Then
        $this->assertDatabaseMissing('intellij_projects', $intellij_project->toArray());
    }

    public function testNotAllowedRoleNotDelete()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        // Then
        $this->delete(route('intellij_projects.destroy', $intellij_project))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $intellij_project = factory(IntellijProject::class)->create();

        // When
        // Then
        $this->delete(route('intellij_projects.destroy', $intellij_project))
            ->assertRedirect(route('login'));
    }
}
