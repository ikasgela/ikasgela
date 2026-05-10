<?php

namespace Tests\Feature\Recursos\FileUploads;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileUploadsExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testToggleTituloVisible()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        $file_upload = FileUpload::factory()->create(['curso_id' => $curso->id]);
        $actividad->file_uploads()->attach($file_upload);

        $this->assertDatabaseHas('actividad_file_upload', [
            'file_upload_id' => $file_upload->id,
            'titulo_visible' => true,
        ]);

        // When
        $response = $this->post(route('file_uploads.toggle.titulo_visible', [$actividad, $file_upload]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_file_upload', [
            'file_upload_id' => $file_upload->id,
            'titulo_visible' => false,
        ]);
    }

    public function testToggleDescripcionVisible()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        $file_upload = FileUpload::factory()->create(['curso_id' => $curso->id]);
        $actividad->file_uploads()->attach($file_upload);

        $this->assertDatabaseHas('actividad_file_upload', [
            'file_upload_id' => $file_upload->id,
            'descripcion_visible' => true,
        ]);

        // When
        $response = $this->post(route('file_uploads.toggle.descripcion_visible', [$actividad, $file_upload]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_file_upload', [
            'file_upload_id' => $file_upload->id,
            'descripcion_visible' => false,
        ]);
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->create();
        $count = FileUpload::count();

        // When
        $response = $this->post(route('file_uploads.duplicar', $file_upload));

        // Then
        $response->assertRedirect(route('file_uploads.index'));
        $this->assertSame($count + 1, FileUpload::count());
    }

    public function testNotAuthNotToggle()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->post(route('file_uploads.toggle.titulo_visible', [$actividad, $file_upload]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->post(route('file_uploads.duplicar', $file_upload));

        // Then
        $response->assertForbidden();
    }
}
