<?php

namespace Tests\Feature;

use App\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileUploadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.index'));

        // Then
        $response->assertSee($file_upload->name);
    }

    public function testNotProfesorNotIndex()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('file_uploads.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('file_uploads.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('file_uploads.create'));

        // Then
        $response->assertSeeInOrder([__('New file upload'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('file_uploads.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('file_uploads.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->make();

        // When
        $this->post(route('file_uploads.store'), $file_upload->toArray());

        // Then
        $this->assertEquals(1, FileUpload::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $file_upload = factory(FileUpload::class)->make();

        // When
        // Then
        $this->post(route('file_uploads.store'), $file_upload->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $file_upload = factory(FileUpload::class)->make();

        // When
        // Then
        $this->post(route('file_uploads.store'), $file_upload->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->make(['titulo' => null]);

        // When
        // Then
        $this->post(route('file_uploads.store'), $file_upload->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testStoreRequiresMaxFiles()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->make(['max_files' => null]);

        // When
        // Then
        $this->post(route('file_uploads.store'), $file_upload->toArray())
            ->assertSessionHasErrors('max_files');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.show', $file_upload));

        // Then
        $response->assertSeeInOrder([__('File upload'), $file_upload->titulo]);
    }

    public function testNotProfesorNotShow()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        // Then
        $this->get(route('file_uploads.show', $file_upload))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        // Then
        $this->get(route('file_uploads.show', $file_upload))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        $response = $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray());

        // Then
        $response->assertSeeInOrder([$file_upload->titulo, $file_upload->descripcion, $file_upload->max_files, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        // Then
        $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        // Then
        $this->get(route('file_uploads.edit', $file_upload), $file_upload->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();
        $file_upload->titulo = "Updated";

        // When
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray());

        // Then
        $this->assertDatabaseHas('file_uploads', ['id' => $file_upload->id, 'titulo' => $file_upload->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $file_upload = factory(FileUpload::class)->create();
        $file_upload->titulo = "Updated";

        // When
        // Then
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $file_upload = factory(FileUpload::class)->create();
        $file_upload->titulo = "Updated";

        // When
        // Then
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        $file_upload->titulo = null;

        // Then
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testUpdateRequiresMaxFiles()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        $file_upload->max_files = null;

        // Then
        $this->put(route('file_uploads.update', $file_upload), $file_upload->toArray())
            ->assertSessionHasErrors('max_files');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        $this->delete(route('file_uploads.destroy', $file_upload));

        // Then
        $this->assertDatabaseMissing('file_uploads', $file_upload->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $file_upload = factory(FileUpload::class)->create();

        // When
        // Then
        $this->delete(route('file_uploads.destroy', $file_upload))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $file_upload = factory(FileUpload::class)->create();

        // When
        // Then
        $this->delete(route('file_uploads.destroy', $file_upload))
            ->assertRedirect(route('login'));
    }
}
