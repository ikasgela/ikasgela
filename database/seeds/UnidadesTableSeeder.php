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

        $codigo = 'Unidad 1';
        $nombre = 'Introducción a la programación';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'introduccion'
        ]);

        $codigo = 'Unidad 2';
        $nombre = 'Programación estructurada';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'Unidad 3';
        $nombre = 'Programación modular';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'Unidad 4';
        $nombre = 'Estructuras de datos';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
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
        $codigo = 'UD1';
        $nombre = 'Introducción a la programación';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'introduccion'
        ]);

        $codigo = 'UD2';
        $nombre = 'Programación estructurada';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD3';
        $nombre = 'Programación modular';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD4';
        $nombre = 'Estructuras de datos I';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'arrays'
        ]);

        $codigo = 'UD5';
        $nombre = 'Programación orientada a objetos';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'poo'
        ]);

        $codigo = 'UD6';
        $nombre = 'Estructuras de datos II';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => 'colecciones'
        ]);

        $codigo = 'UD7';
        $nombre = 'Programación funcional';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD8';
        $nombre = 'GUI';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);

        $codigo = 'UD9';
        $nombre = 'Persistencia';
        factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);
    }
}
