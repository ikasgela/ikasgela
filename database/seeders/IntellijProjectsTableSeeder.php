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
        factory(IntellijProject::class)->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Tres en raya';
        factory(IntellijProject::class)->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Reservas';
        factory(IntellijProject::class)->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);

        $nombre = 'Apuntes';
        factory(IntellijProject::class)->create([
            'titulo' => $nombre,
            'repositorio' => 'root/' . Str::slug($nombre),
            'curso_id' => 1,
        ]);
    }
}
