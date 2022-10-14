<?php

namespace Database\Seeders;

use App\Models\MarkdownText;
use Illuminate\Database\Seeder;

class MarkdownTextsTableSeeder extends Seeder
{
    public function run()
    {
        MarkdownText::factory()->create([
            'titulo' => 'Apuntes',
            'descripcion' => 'Ejemplo de texto Markdown.',
            'repositorio' => 'root/apuntes',
            'rama' => 'master',
            'archivo' => 'README.md',
            'curso_id' => 1,
        ]);
    }
}
