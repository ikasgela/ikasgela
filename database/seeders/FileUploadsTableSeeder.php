<?php

namespace Database\Seeders;

use App\Models\FileUpload;
use Illuminate\Database\Seeder;

class FileUploadsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        FileUpload::factory()->create([
            'titulo' => 'Diagrama de flujo',
            'descripcion' => 'Dibuja el diagrama del enunciado en papel y sube una foto para calificar.',
            'max_files' => 1,
            'plantilla' => true,
            'curso_id' => 1,
        ]);
    }
}
