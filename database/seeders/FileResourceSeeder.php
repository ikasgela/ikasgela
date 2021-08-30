<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\FileResource;
use Illuminate\Database\Seeder;

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

        File::create([
            'uploadable_id' => $file_resource->id,
            'uploadable_type' => FileResource::class,
            'path' => '32912ec806cd1ce9ecbd59fef06b5171/01_introduccion_programacion.pdf',
            'title' => '01_introduccion_programacion.pdf',
            'size' => 1909079,
        ]);
    }
}
