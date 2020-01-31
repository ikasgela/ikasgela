<?php

use App\MarkdownText;
use Illuminate\Database\Seeder;

class MarkdownTextsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(MarkdownText::class)->create([
            'titulo' => 'Apuntes',
            'descripcion' => null,
            'repositorio' => 'root/apuntes',
            'rama' => 'master',
            'archivo' => 'README.md'
        ]);
    }
}
