<?php

namespace Tests\Feature\Recursos\FileUploads;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileUploadsAsociarActividadTest extends TestCase
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
        $file_upload1 = FileUpload::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $file_upload2 = FileUpload::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $file_upload3 = FileUpload::factory()->create([
            'curso_id' => $curso->id,
        ]);

        $actividad->file_uploads()->attach($file_upload1);
        $actividad->file_uploads()->attach($file_upload3);

        // When
        $response = $this->get(route('file_uploads.actividad', $actividad));

        // Then
        $response->assertSeeInOrder([
            __('Resources: Image uploads'),
            __('Assigned resources'),
            $file_upload1->repositorio,
            $file_upload3->repositorio,
            __('Available resources'),
            $file_upload2->repositorio,
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
        $file_upload1 = FileUpload::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $file_upload2 = FileUpload::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('file_uploads.asociar', $actividad), ['seleccionadas' => [$file_upload1, $file_upload2]]);

        // Then
        $this->assertCount(2, $actividad->file_uploads()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('file_uploads.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $file_upload1 = FileUpload::factory()->create();
        $file_upload2 = FileUpload::factory()->create();

        $actividad->file_uploads()->attach($file_upload1);
        $actividad->file_uploads()->attach($file_upload2);

        // When
        $this->delete(route('file_uploads.desasociar', [$actividad, $file_upload1]));

        // Then
        $this->assertCount(1, $actividad->file_uploads()->get());
    }
}
