<?php

namespace Tests\Feature\Recursos\FileResources;

use Override;
use App\Models\FileResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileResourcesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo'
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
        $file_resource = FileResource::factory()->create();
        session(['filtrar_curso_actual' => $file_resource->curso_id]);

        // When
        $response = $this->get(route('file_resources.index'));

        // Then
        $response->assertSuccessful()->assertSee($file_resource->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('file_resources.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('file_resources.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('file_resources.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New files resource'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('file_resources.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('file_resources.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->make();
        $total = FileResource::all()->count();

        // When
        $this->post(route('file_resources.store'), $file_resource->toArray());

        // Then
        $this->assertEquals($total + 1, FileResource::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_resource = FileResource::factory()->make();

        // When
        $response = $this->post(route('file_resources.store'), $file_resource->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $file_resource = FileResource::factory()->make();

        // When
        $response = $this->post(route('file_resources.store'), $file_resource->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = FileResource::all()->count();

        $empty = new FileResource();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('file_resources.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, FileResource::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->make([$field => null]);

        // When
        $response = $this->post(route('file_resources.store'), $file_resource->toArray());

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
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->get(route('file_resources.show', $file_resource));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$file_resource->titulo, __('Upload')]);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->get(route('file_resources.show', $file_resource));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->get(route('file_resources.show', $file_resource));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->get(route('file_resources.edit', $file_resource), $file_resource->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$file_resource->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->get(route('file_resources.edit', $file_resource), $file_resource->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->get(route('file_resources.edit', $file_resource), $file_resource->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->create();
        $file_resource->titulo = "Updated";

        // When
        $this->put(route('file_resources.update', $file_resource), $file_resource->toArray());

        // Then
        $this->assertDatabaseHas('file_resources', ['id' => $file_resource->id, 'titulo' => $file_resource->titulo]);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_resource = FileResource::factory()->create();
        $file_resource->titulo = "Updated";

        // When
        $response = $this->put(route('file_resources.update', $file_resource), $file_resource->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $file_resource = FileResource::factory()->create();
        $file_resource->titulo = "Updated";

        // When
        $response = $this->put(route('file_resources.update', $file_resource), $file_resource->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->create();
        $empty = new FileResource();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('file_resources.update', $file_resource), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_resource = FileResource::factory()->create();
        $file_resource->$field = null;

        // When
        $response = $this->put(route('file_resources.update', $file_resource), $file_resource->toArray());

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
        $file_resource = FileResource::factory()->create();

        // When
        $this->delete(route('file_resources.destroy', $file_resource));

        // Then
        $this->assertDatabaseMissing('file_resources', $file_resource->toArray());
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->delete(route('file_resources.destroy', $file_resource));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $file_resource = FileResource::factory()->create();

        // When
        $response = $this->delete(route('file_resources.destroy', $file_resource));

        // Then
        $response->assertRedirect(route('login'));
    }
}
