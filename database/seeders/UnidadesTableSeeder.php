<?php

namespace Database\Seeders;

use App\Curso;
use App\Unidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnidadesTableSeeder extends Seeder
{
    public function run()
    {
        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        $codigo = 'Unidad 1';
        $nombre = 'Introducción a la programación';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'introduccion'
        ]);

        $codigo = 'Unidad 2';
        $nombre = 'Programación estructurada';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'Unidad 3';
        $nombre = 'Programación modular';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'Unidad 4';
        $nombre = 'Estructuras de datos';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'estructuras-datos'
        ]);

        $codigo = '';
        $nombre = 'Seguimiento U2';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'tags' => 'examen',
            'slug' => 'seguimiento-u2'
        ]);
    }

    private function generarUnidades($curso): void
    {
        $codigo = 'UD1';
        $nombre = 'Introducción a la programación';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'introduccion'
        ]);

        $codigo = 'UD2';
        $nombre = 'Programación estructurada';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD3';
        $nombre = 'Programación modular';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD4';
        $nombre = 'Estructuras de datos I';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'arrays'
        ]);

        $codigo = 'UD5';
        $nombre = 'Programación orientada a objetos';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'poo'
        ]);

        $codigo = 'UD6';
        $nombre = 'Estructuras de datos II';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'colecciones'
        ]);

        $codigo = 'UD7';
        $nombre = 'Programación funcional';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD8';
        $nombre = 'GUI';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD9';
        $nombre = 'Persistencia';
        Unidad::factory()->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);
    }
}
