<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\FileResource;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Override;
use Tests\TestCase;

class FileControllerExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testImageUpload()
    {
        Storage::fake('s3');

        $fileUpload = FileUpload::factory()->create(['max_files' => 5]);

        $this->actingAs($this->profesor);

        $fakeImage = UploadedFile::fake()->image('photo.jpg', 100, 100);

        $response = $this->post(route('files.upload.image'), [
            'files' => [$fakeImage],
            'file_upload_id' => $fileUpload->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function testDocumentUpload()
    {
        Storage::fake('s3');

        $fileResource = FileResource::factory()->create();

        $this->actingAs($this->admin);

        $fakeFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post(route('files.upload.document'), [
            'file' => $fakeFile,
            'file_resource_id' => $fileResource->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function testRotateLeft()
    {
        // Create a real PNG file in fake S3 storage
        Storage::fake('s3');

        $file = File::factory()->create(['path' => 'testpath/image.jpg', 'user_id' => $this->profesor->id]);

        // Put a real small PNG in the fake S3 at the expected paths
        $pngContent = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
        Storage::disk('s3')->put('images/testpath/image.jpg', $pngContent);
        Storage::disk('s3')->put('thumbnails/testpath/image.jpg', $pngContent);

        $this->actingAs($this->profesor);

        $response = $this->post(route('files.rotate_left', $file));

        $response->assertRedirect();
    }

    public function testRotateRight()
    {
        Storage::fake('s3');

        $file = File::factory()->create(['path' => 'testpath/image2.jpg', 'user_id' => $this->profesor->id]);

        $pngContent = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
        Storage::disk('s3')->put('images/testpath/image2.jpg', $pngContent);
        Storage::disk('s3')->put('thumbnails/testpath/image2.jpg', $pngContent);

        $this->actingAs($this->profesor);

        $response = $this->post(route('files.rotate_right', $file));

        $response->assertRedirect();
    }
}
