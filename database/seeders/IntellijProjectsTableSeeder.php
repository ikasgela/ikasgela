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
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Tres en raya';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Reservas';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Apuntes';
        IntellijProject::factory()->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);
    }
}
