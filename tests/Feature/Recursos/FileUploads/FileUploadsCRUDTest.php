<?php

namespace Tests\Feature\Recursos\FileUploads;

use Override;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileUploadsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo', 'max_files'
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
        $file_upload = FileUpload::factory()->create([
            'plantilla' => true,
        ]);
        session(['filtrar_curso_actual' => $file_upload->curso_id]);

        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertSuccessful()->assertSee($file_upload->titulo);
    }

    public function testNotPlantillaNotIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->create();
        session(['filtrar_curso_actual' => $file_upload->curso_id]);

        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertDontSee($file_upload->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('file_uploads.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New image upload'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('file_uploads.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('file_uploads.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->make();
        $total = FileUpload::all()->count();

        // When
        $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $this->assertEquals($total + 1, FileUpload::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_upload = FileUpload::factory()->make();

        // When
        $response = $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $file_upload = FileUpload::factory()->make();

        // When
        $response = $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = FileUpload::all()->count();

        $empty = new FileUpload();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('file_uploads.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, FileUpload::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->make([$field => null]);

        // When
        $response = $this->post(route('file_uploads.store'), $file_upload->toArray());

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
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->get(route('file_uploads.show', $file_upload));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Image upload'), $file_upload->titulo]);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->get(route('file_uploads.show', $file_upload));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->get(route('file_uploads.show', $file_upload));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$file_upload->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->create();
        $file_upload->titulo = "Updated";

        // When
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $this->assertDatabaseHas('file_uploads', ['id' => $file_upload->id, 'titulo' => $file_upload->titulo]);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_upload = FileUpload::factory()->create();
        $file_upload->titulo = "Updated";

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $file_upload = FileUpload::factory()->create();
        $file_upload->titulo = "Updated";

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->create();
        $empty = new FileUpload();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = FileUpload::factory()->create();
        $file_upload->$field = null;

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

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
        $file_upload = FileUpload::factory()->create();

        // When
        $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $this->assertDatabaseMissing('file_uploads', $file_upload->toArray());
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $file_upload = FileUpload::factory()->create();

        // When
        $response = $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $response->assertRedirect(route('login'));
    }
}
