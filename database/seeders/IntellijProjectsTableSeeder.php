<?php

namespace Database\Seeders;

use App\IntellijProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IntellijProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
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
