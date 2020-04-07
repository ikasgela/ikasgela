<?php

namespace Tests\Feature;

use App\FileUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileUploadsCRUDTest extends TestCase
{
    use DatabaseTransactions;

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
        $file_upload = factory(FileUpload::class)->create([
            'plantilla' => true,
        ]);

        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertSee($file_upload->titulo);
    }

    public function testNotPlantillaNotIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertDontSee($file_upload->titulo);
    }

    public function testNotProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_profesor);

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
        $response->assertSeeInOrder([__('New file upload'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

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
        $file_upload = factory(FileUpload::class)->make();
        $total = FileUpload::all()->count();

        // When
        $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $this->assertEquals($total + 1, FileUpload::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $file_upload = factory(FileUpload::class)->make();

        // When
        $response = $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $file_upload = factory(FileUpload::class)->make();

        // When
        $response = $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreThereAreRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $empty = new FileUpload();

        // When
        $response = $this->post(route('file_uploads.store'), $empty->toArray());

        // Then
        $response->assertSessionHasErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = factory(FileUpload::class)->make([$field => null]);

        // When
        $response = $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreRequiresTitulo()
    {
        $this->storeRequires('titulo');
    }

    public function testStoreRequiresMaxFiles()
    {
        $this->storeRequires('max_files');
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.show', $file_upload));

        // Then
        $response->assertSeeInOrder([__('File upload'), $file_upload->titulo]);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.show', $file_upload));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $file_upload = factory(FileUpload::class)->create();

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
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray());

        // Then
        $response->assertSeeInOrder([$file_upload->titulo, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $file_upload = factory(FileUpload::class)->create();

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
        $file_upload = factory(FileUpload::class)->create();
        $file_upload->titulo = "Updated";

        // When
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $this->assertDatabaseHas('file_uploads', ['id' => $file_upload->id, 'titulo' => $file_upload->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();
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
        $file_upload = factory(FileUpload::class)->create();
        $file_upload->titulo = "Updated";

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateThereAreRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();
        $empty = new FileUpload();

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $empty->toArray());

        // Then
        $response->assertSessionHasErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();
        $file_upload->$field = null;

        // When
        $response = $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateRequiresTitulo()
    {
        $this->updateRequires('titulo');
    }

    public function testUpdateRequiresMaxFiles()
    {
        $this->updateRequires('max_files');
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $this->assertDatabaseMissing('file_uploads', $file_upload->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $response->assertRedirect(route('login'));
    }
}
