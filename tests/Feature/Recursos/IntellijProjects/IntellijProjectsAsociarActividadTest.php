<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IntellijProjectsAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testActividad()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $intellij_project1 = IntellijProject::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $intellij_project2 = IntellijProject::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $intellij_project3 = IntellijProject::factory()->create([
            'curso_id' => $curso->id,
        ]);

        $actividad->intellij_projects()->attach($intellij_project1);
        $actividad->intellij_projects()->attach($intellij_project3);

        // When
        $response = $this->get(route('intellij_projects.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Resources: IntelliJ projects'),
            __('Assigned resources'),
            $intellij_project1->repositorio,
            $intellij_project3->repositorio,
            __('Available resources'),
            $intellij_project2->repositorio,
        ]);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $intellij_project1 = IntellijProject::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $intellij_project2 = IntellijProject::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('intellij_projects.asociar', $actividad), ['seleccionadas' => [$intellij_project1, $intellij_project2]]);

        // Then
        $this->assertCount(2, $actividad->intellij_projects()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('intellij_projects.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $intellij_project1 = IntellijProject::factory()->create();
        $intellij_project2 = IntellijProject::factory()->create();

        $actividad->intellij_projects()->attach($intellij_project1);
        $actividad->intellij_projects()->attach($intellij_project2);

        // When
        $this->delete(route('intellij_projects.desasociar', [$actividad, $intellij_project1]));

        // Then
        $this->assertCount(1, $actividad->intellij_projects()->get());
    }
}
