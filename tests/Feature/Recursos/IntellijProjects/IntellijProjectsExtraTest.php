<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class IntellijProjectsExtraTest extends TestCase
{
    use DatabaseTransactions;

    private Actividad $actividad;
    private IntellijProject $project;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $curso = Curso::factory()->create();

        $this->actingAs($this->profesor);
        setting_usuario(['curso_actual' => $curso->id]);

        $this->actividad = Actividad::factory()->create();
        $this->project = IntellijProject::factory()->create(['curso_id' => $curso->id]);

        // Attach with pivot data
        $this->actividad->intellij_projects()->attach($this->project, [
            'titulo_visible' => true,
            'descripcion_visible' => true,
            'incluir_siempre' => false,
        ]);
    }

    public function testToggleTituloVisible()
    {
        $this->actingAs($this->profesor);

        $response = $this->post(route('intellij_projects.toggle.titulo_visible', [
            $this->actividad, $this->project
        ]));

        $response->assertRedirect();

        // Check pivot was updated
        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertFalse((bool)$pivot->titulo_visible);
    }

    public function testToggleDescripcionVisible()
    {
        $this->actingAs($this->profesor);

        $response = $this->post(route('intellij_projects.toggle.descripcion_visible', [
            $this->actividad, $this->project
        ]));

        $response->assertRedirect();

        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertFalse((bool)$pivot->descripcion_visible);
    }

    public function testToggleIncluirSiempre()
    {
        $this->actingAs($this->profesor);

        $response = $this->post(route('intellij_projects.toggle.incluir_siempre', [
            $this->actividad, $this->project
        ]));

        $response->assertRedirect();

        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertTrue((bool)$pivot->incluir_siempre);
    }

    public function testLock()
    {
        $response = $this->post(route('intellij_projects.lock', [
            $this->project, $this->actividad
        ]));

        $response->assertRedirect();
    }

    public function testUnlock()
    {
        $response = $this->post(route('intellij_projects.unlock', [
            $this->project, $this->actividad
        ]));

        $response->assertRedirect();
    }

    public function testEditFork()
    {
        $response = $this->get(route('intellij_projects.edit_fork', [
            $this->project, $this->actividad
        ]));

        $response->assertSuccessful();
    }

    public function testUpdateFork()
    {
        $response = $this->put(route('intellij_projects.update_fork', [
            $this->project, $this->actividad
        ]), [
            'repositorio' => 'user/new-fork-name',
        ]);

        $response->assertRedirect();
    }
}
