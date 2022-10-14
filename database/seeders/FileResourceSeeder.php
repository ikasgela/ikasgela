<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\FileResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FileResourceSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $file_resource = FileResource::factory()->create([
            'titulo' => 'Presentaciones',
            'descripcion' => 'Archivos PDF con las presentaciones.',
            'curso_id' => 1,
        ]);

        $filename = md5(time()) . '/test.pdf';

        Storage::disk('s3')->copy('test/test.pdf', 'documents/' . $filename);

        $file = File::create([
            'uploadable_id' => $file_resource->id,
            'uploadable_type' => FileResource::class,
            'path' => $filename,
            'title' => 'test.pdf',
            'size' => Storage::disk('s3')->size('documents/' . $filename),
            'extension' => 'pdf',
        ]);

        $file->orden = $file->id;
        $file->save();
    }
}
