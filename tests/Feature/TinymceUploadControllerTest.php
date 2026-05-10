<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Override;
use Tests\TestCase;

class TinymceUploadControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testGetS3RedirectsToTemporaryUrl()
    {
        Storage::fake('s3-urls');

        $this->actingAs($this->profesor);

        $response = $this->get(route('tinymce.upload.url') . '?path=images/test/file.jpg');

        // Should redirect (temporaryUrl returns a URL, and response redirects to it)
        $response->assertRedirect();
    }

    public function testGetS3InvalidPathAborts()
    {
        $this->actingAs($this->profesor);

        $response = $this->get(route('tinymce.upload.url') . '?path=../etc/passwd');

        $response->assertNotFound();
    }

    public function testGetS3NoPathAborts()
    {
        $this->actingAs($this->profesor);

        $response = $this->get(route('tinymce.upload.url'));

        $response->assertNotFound();
    }

    public function testUploadImageStoresFileAndRegistersDatabaseRecord()
    {
        Storage::fake('s3');
        $this->actingAs($this->profesor);

        $response = $this->post(route('tinymce.upload.image'), [
            'image' => UploadedFile::fake()->image('tiny.png', 50, 50),
            'description' => 'Tiny image',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('path=images%2F', $response->getContent());
        $this->assertCount(1, Storage::disk('s3')->allFiles('images'));
        $this->assertDatabaseHas('files', [
            'user_id' => $this->profesor->id,
            'title' => 'tiny.png',
            'description' => 'Tiny image',
        ]);
    }
}
