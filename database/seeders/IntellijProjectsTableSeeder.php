<?php

namespace Database\Seeders;

use App\Models\IntellijProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IntellijProjectsTableSeeder extends Seeder
{
    public function run()
    {
        $nombre = 'Agenda';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'descripcion' => 'Clona el repositorio y abre el proyecto en IntelliJ. El enunciado está dentro del proyecto, en el archivo README.md.',
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Tres en raya';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'descripcion' => 'Clona el repositorio y abre el proyecto en PhpStorm. El enunciado está dentro del proyecto, en el archivo README.md.',
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
            'open_with' => 'phpstorm',
        ]);

        $nombre = 'Reservas';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'descripcion' => 'Clona el repositorio y abre el proyecto en IntelliJ. El enunciado está dentro del proyecto, en el archivo README.md.',
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Apuntes';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'descripcion' => 'Proyecto de ejemplo para abrir con GitKraken.',
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
            'open_with' => 'gitkraken',
        ]);
    }
}
