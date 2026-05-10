<?php

namespace Tests\Feature\Estructura;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class CursosExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testMatricular()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        $user = $this->alumno;

        // When
        $this->post(route('cursos.matricular', [$curso, $user]));

        // Then
        $this->assertDatabaseHas('curso_user', ['curso_id' => $curso->id, 'user_id' => $user->id]);
    }

    public function testNotAlumnoProfesorTutorNotMatricular()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();
        $user = $this->alumno;

        // When
        $response = $this->post(route('cursos.matricular', [$curso, $user]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotMatricular()
    {
        // Auth
        // Given
        $curso = Curso::factory()->create();
        $user = $this->alumno;

        // When
        $response = $this->post(route('cursos.matricular', [$curso, $user]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCursoActual()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        $user = $this->alumno;

        // When
        $response = $this->post(route('cursos.curso_actual', [$curso, $user]));

        // Then
        $response->assertRedirect();
    }

    public function testNotAlumnoProfesorTutorNotCursoActual()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();
        $user = $this->alumno;

        // When
        $response = $this->post(route('cursos.curso_actual', [$curso, $user]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCursoActual()
    {
        // Auth
        // Given
        $curso = Curso::factory()->create();
        $user = $this->alumno;

        // When
        $response = $this->post(route('cursos.curso_actual', [$curso, $user]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testReset()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->delete(route('cursos.reset', $curso));

        // Then
        $response->assertRedirect();
    }

    public function testNotAdminNotReset()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->delete(route('cursos.reset', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotReset()
    {
        // Auth
        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->delete(route('cursos.reset', $curso));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testLimpiarCache()
    {
        // Auth
        $this->actingAs($this->not_profesor);  // alumno+admin+tutor: passes role:admin (controller) and role:profesor|tutor (route)

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('cursos.limpiar_cache', $curso));

        // Then
        $response->assertRedirect();
    }

    public function testNotProfesorTutorNotLimpiarCache()
    {
        // Auth
        $this->actingAs($this->not_profesor_tutor);  // admin+alumno: passes role:admin but fails role:profesor|tutor

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('cursos.limpiar_cache', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotLimpiarCache()
    {
        // Auth
        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('cursos.limpiar_cache', $curso));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testToggleMatriculaAbierta()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create(['matricula_abierta' => false]);

        // When
        $response = $this->post(route('cursos.toggle.matricula_abierta', $curso));

        // Then
        $response->assertRedirect();
        $curso->refresh();
        $this->assertTrue((bool)$curso->matricula_abierta);
    }

    public function testNotAdminNotToggleMatriculaAbierta()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('cursos.toggle.matricula_abierta', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotToggleMatriculaAbierta()
    {
        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('cursos.toggle.matricula_abierta', $curso));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testExport()
    {
        $this->actingAs($this->admin);

        // Create a curso with no resources (to avoid Gitea/S3 calls)
        $curso = Curso::factory()->create();

        $response = $this->post(route('cursos.export', $curso));

        // Should stream a zip file download
        $response->assertSuccessful();
    }

    public function testNotAdminNotExport()
    {
        $this->actingAs($this->not_admin);
        $curso = Curso::factory()->create();

        $response = $this->post(route('cursos.export', $curso));
        $response->assertForbidden();
    }

    public function testZipDirectoryWithSubdirs()
    {
        // Test the zipDirectoryWithSubdirs method directly
        $controller = new \App\Http\Controllers\CursoController();

        $directorio = '/' . \Illuminate\Support\Str::uuid() . '/';
        \Illuminate\Support\Facades\Storage::disk('temp')->makeDirectory($directorio);
        \Illuminate\Support\Facades\Storage::disk('temp')->put($directorio . '/test.json', '{"test":1}');

        $zipName = \Illuminate\Support\Str::uuid() . '.zip';
        $result = $controller->zipDirectoryWithSubdirs($zipName, $directorio);

        $this->assertNotNull($result);

        // Cleanup
        \Illuminate\Support\Facades\Storage::disk('temp')->deleteDirectory($directorio);
    }

    public function testImport()
    {
        $this->actingAs($this->admin);

        \Illuminate\Support\Facades\Queue::fake();

        $category = \App\Models\Category::factory()->create();

        // Create a minimal valid ZIP file
        $zipPath = sys_get_temp_dir() . '/test-import.zip';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('curso.json', json_encode(['name' => 'Test']));
        $zip->close();

        $file = new \Illuminate\Http\UploadedFile($zipPath, 'test-import.zip', 'application/zip', null, true);

        $response = $this->post(route('cursos.import'), [
            'file' => $file,
            'category_id' => $category->id,
        ]);

        $response->assertRedirect(route('cursos.index'));
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ImportCurso::class);

        @unlink($zipPath);
    }
}
