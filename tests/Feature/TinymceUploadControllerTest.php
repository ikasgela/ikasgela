<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
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
}
