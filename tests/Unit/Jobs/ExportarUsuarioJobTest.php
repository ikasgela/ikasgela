<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ExportarUsuarioJob;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportarUsuarioJobTest extends TestCase
{
    use DatabaseTransactions;

    public function testHandleCreatesZipInTempDiskForUserWithoutCourses()
    {
        Storage::fake('temp');
        Storage::fake('templates');
        Mail::fake();

        Storage::disk('templates')->put('header.html', '<html><body>');
        Storage::disk('templates')->put('footer.html', '</body></html>');
        Storage::disk('templates')->put('bootstrap.min.css', 'body{}');

        $user = User::factory()->create();
        $job = new ExportarUsuarioJob($user);

        $job->handle();

        $zipFiles = array_filter(
            Storage::disk('temp')->allFiles('/'),
            fn($file) => str_ends_with($file, '.zip')
        );

        $this->assertNotEmpty($zipFiles);
    }

    public function testZipDirectoryWithSubdirsCreatesZipFile()
    {
        Storage::fake('temp');

        Storage::disk('temp')->put('/sample/a.txt', 'A');
        Storage::disk('temp')->put('/sample/nested/b.txt', 'B');

        $user = User::factory()->create();
        $job = new ExportarUsuarioJob($user);

        $zipPath = $job->zipDirectoryWithSubdirs('sample.zip', '/sample');

        $this->assertIsString($zipPath);
        $this->assertFileExists($zipPath);
    }
}

