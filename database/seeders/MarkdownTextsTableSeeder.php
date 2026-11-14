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

        MarkdownText::factory()->create([
            'titulo' => 'PlantUML',
            'descripcion' => 'Ejemplo de actividad con PlantUML incrustado.',
            'repositorio' => 'root/plantuml',
            'rama' => 'master',
            'archivo' => 'README.md',
            'curso_id' => 1,
        ]);

        MarkdownText::factory()->create([
            'titulo' => 'Imagen embebida',
            'descripcion' => 'Ejemplo de texto imagen embebida en el Markdown.',
            'repositorio' => 'root/imagen-embebida',
            'rama' => 'master',
            'archivo' => 'README.md',
            'curso_id' => 1,
        ]);
    }
}
