<?php

namespace Tests\Feature\Recursos\FileResources;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FileResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileResourcesAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

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
        $file_resource1 = FileResource::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $file_resource2 = FileResource::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $file_resource3 = FileResource::factory()->create([
            'curso_id' => $curso->id,
        ]);

        $actividad->file_resources()->attach($file_resource1);
        $actividad->file_resources()->attach($file_resource3);

        // When
        $response = $this->get(route('file_resources.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Resources: Files'),
            __('Assigned resources'),
            $file_resource1->repositorio,
            $file_resource3->repositorio,
            __('Available resources'),
            $file_resource2->repositorio,
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
        $file_resource1 = FileResource::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $file_resource2 = FileResource::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('file_resources.asociar', $actividad), ['seleccionadas' => [$file_resource1, $file_resource2]]);

        // Then
        $this->assertCount(2, $actividad->file_resources()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('file_resources.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $file_resource1 = FileResource::factory()->create();
        $file_resource2 = FileResource::factory()->create();

        $actividad->file_resources()->attach($file_resource1);
        $actividad->file_resources()->attach($file_resource2);

        // When
        $this->delete(route('file_resources.desasociar', [$actividad, $file_resource1]));

        // Then
        $this->assertCount(1, $actividad->file_resources()->get());
    }
}
