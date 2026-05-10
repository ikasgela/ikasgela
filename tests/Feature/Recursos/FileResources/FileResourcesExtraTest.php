<?php

namespace Tests\Feature\Recursos\FileResources;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FileResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileResourcesExtraTest extends TestCase
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
        $file_resource = FileResource::factory()->create(['curso_id' => $curso->id]);
        $actividad->file_resources()->attach($file_resource);

        $this->assertDatabaseHas('actividad_file_resource', [
            'file_resource_id' => $file_resource->id,
            'titulo_visible' => true,
        ]);

        // When
        $response = $this->post(route('file_resources.toggle.titulo_visible', [$actividad, $file_resource]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_file_resource', [
            'file_resource_id' => $file_resource->id,
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
        $file_resource = FileResource::factory()->create(['curso_id' => $curso->id]);
        $actividad->file_resources()->attach($file_resource);

        $this->assertDatabaseHas('actividad_file_resource', [
            'file_resource_id' => $file_resource->id,
            'descripcion_visible' => true,
        ]);

        // When
        $response = $this->post(route('file_resources.toggle.descripcion_visible', [$actividad, $file_resource]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_file_resource', [
            'file_resource_id' => $file_resource->id,
            'descripcion_visible' => false,
        ]);
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->create();
        $count = FileResource::count();

        // When
        $response = $this->post(route('file_resources.duplicar', $file_resource));

        // Then
        $response->assertRedirect(route('file_resources.index'));
        $this->assertSame($count + 1, FileResource::count());
    }

    public function testNotAuthNotToggle()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->post(route('file_resources.toggle.titulo_visible', [$actividad, $file_resource]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->post(route('file_resources.duplicar', $file_resource));

        // Then
        $response->assertForbidden();
    }
}
