<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use Override;
use App\Models\IntellijProject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IntellijProjectsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'repositorio', 'titulo', 'host'
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
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.index'));

        // Then
        $response->assertSuccessful()->assertSee($intellij_project->repositorio);
    }

    public function testIndexAdminFiltro()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->post(route('intellij_projects.index.filtro', ['curso_id' => $intellij_project->curso_id]));

        // Then
        $response->assertSuccessful()->assertSee($intellij_project->repositorio);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('intellij_projects.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('intellij_projects.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('intellij_projects.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New IntelliJ project'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('intellij_projects.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('intellij_projects.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->make();
        $total = IntellijProject::all()->count();

        // When
        $this->post(route('intellij_projects.store'), $intellij_project->toArray());

        // Then
        $this->assertCount($total + 1, IntellijProject::all());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $intellij_project = IntellijProject::factory()->make();

        // When
        $response = $this->post(route('intellij_projects.store'), $intellij_project->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $intellij_project = IntellijProject::factory()->make();

        // When
        $response = $this->post(route('intellij_projects.store'), $intellij_project->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = IntellijProject::all()->count();

        $empty = new IntellijProject();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('intellij_projects.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, IntellijProject::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->make([$field => null]);

        // When
        $response = $this->post(route('intellij_projects.store'), $intellij_project->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.show', $intellij_project));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('IntelliJ project'), $intellij_project->titulo]);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.show', $intellij_project));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Auth
        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.show', $intellij_project));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.edit', $intellij_project), $intellij_project->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$intellij_project->repositorio, $intellij_project->slug, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.edit', $intellij_project), $intellij_project->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->get(route('intellij_projects.edit', $intellij_project), $intellij_project->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();
        $intellij_project->repositorio = "Updated";

        // When
        $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray());

        // Then
        $this->assertDatabaseHas('intellij_projects', ['id' => $intellij_project->id, 'repositorio' => $intellij_project->repositorio]);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();
        $intellij_project->repositorio = "Updated";

        // When
        $response = $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $intellij_project = IntellijProject::factory()->create();
        $intellij_project->repositorio = "Updated";

        // When
        $response = $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();
        $empty = new IntellijProject();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('intellij_projects.update', $intellij_project), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();
        $intellij_project->$field = null;

        // When
        $response = $this->put(route('intellij_projects.update', $intellij_project), $intellij_project->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $this->delete(route('intellij_projects.destroy', $intellij_project));

        // Then
        $this->assertDatabaseMissing('intellij_projects', $intellij_project->toArray());
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->delete(route('intellij_projects.destroy', $intellij_project));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $intellij_project = IntellijProject::factory()->create();

        // When
        $response = $this->delete(route('intellij_projects.destroy', $intellij_project));

        // Then
        $response->assertRedirect(route('login'));
    }
}
