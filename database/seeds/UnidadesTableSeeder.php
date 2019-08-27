<?php

use App\Category;
use App\Curso;
use App\Period;
use App\Unidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnidadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Egibide

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'egibide');
        })
            ->where('slug', 'programacion')
            ->first();

        $this->generarUnidades($curso);

        // Deusto

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'deusto');
        })
            ->where('slug', 'programacion-i')
            ->first();

        $nombre = 'Introducción';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Programación estructurada';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Programación modular';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Estructuras de datos';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => 'estructuras-datos'
        ]);

        // Ikasgela

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        $this->generarUnidades($curso);

    }

    private function generarUnidades($curso): void
    {
        $nombre = 'Introducción';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Programación estructurada';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Programación modular';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Estructuras de datos I';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => 'arrays'
        ]);

        $nombre = 'Programación orientada a objetos';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => 'poo'
        ]);

        $nombre = 'Estructuras de datos II';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => 'colecciones'
        ]);

        $nombre = 'Programación funcional';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'GUI';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $nombre = 'Persistencia';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);
    }
}
