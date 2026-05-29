<?php

namespace Tests\Feature;

use Override;
use App\Http\Middleware\UserLocale;
use App\Models\Curso;
use App\Models\File;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testGetFiles()
    {
        // Auth
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('files'));

        // Then
        $response->assertSuccessful();
    }

    public function testPostDelete()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given - file owned by the user
        $file = File::factory()->create(['user_id' => $this->alumno->id]);
        $count = File::count();

        // When
        $response = $this->delete(route('files.delete', $file));

        // Then
        $response->assertRedirect();
        $this->assertSame($count - 1, File::count());
    }

    public function testReordenar()
    {
        // Auth
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);

        // Given
        $curso = Curso::factory()->create();
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        Cache::tags('user_' . $this->profesor->id)->put('curso_actual', $curso->id, config('ikasgela.eloquent_cache_time'));

        $fileUpload = FileUpload::factory()->create(['curso_id' => $curso->id]);
        $a1 = File::factory()->create(['orden' => 1, 'uploadable_id' => $fileUpload->id, 'uploadable_type' => FileUpload::class]);
        $a2 = File::factory()->create(['orden' => 2, 'uploadable_id' => $fileUpload->id, 'uploadable_type' => FileUpload::class]);

        // When
        $response = $this->post(route('files.reordenar', [$a1, $a2]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('files', ['id' => $a1->id, 'orden' => 2]);
        $this->assertDatabaseHas('files', ['id' => $a2->id, 'orden' => 1]);
    }

    public function testNotAdminProfesorNotReordenar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $a1 = File::factory()->create(['orden' => 1]);
        $a2 = File::factory()->create(['orden' => 2]);

        // When
        $response = $this->post(route('files.reordenar', [$a1, $a2]));

        // Then
        $response->assertForbidden();
    }

    public function testToggleVisible()
    {
        // Auth
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);

        // Given
        $curso = Curso::factory()->create();
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        Cache::tags('user_' . $this->profesor->id)->put('curso_actual', $curso->id, config('ikasgela.eloquent_cache_time'));

        $fileUpload = FileUpload::factory()->create(['curso_id' => $curso->id]);
        $file = File::factory()->create([
            'visible' => true,
            'uploadable_id' => $fileUpload->id,
            'uploadable_type' => FileUpload::class,
        ]);

        // When
        $response = $this->post(route('files.toggle.visible', $file));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('files', ['id' => $file->id, 'visible' => false]);
    }

    public function testNotAdminProfesorNotToggleVisible()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file = File::factory()->create(['visible' => true]);

        // When
        $response = $this->post(route('files.toggle.visible', $file));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotToggleVisible()
    {
        // Given
        $file = File::factory()->create();

        // When
        $response = $this->post(route('files.toggle.visible', $file));

        // Then
        $response->assertRedirect(route('login'));
    }
}
