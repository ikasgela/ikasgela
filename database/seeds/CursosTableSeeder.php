<?php

use App\Category;
use App\Curso;
use App\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CursosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::whereHas('period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        $nombre = 'Programación';

        factory(Curso::class)->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Fundamentos de Programación con Java.',
            'slug' => Str::slug($nombre)
        ]);

        $category = Category::whereHas('period.organization', function ($query) {
            $query->where('organizations.slug', 'deusto');
        })
            ->where('slug', 'programacion')
            ->first();

        $nombre = 'Programación I';

        factory(Curso::class)->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Asignatura: Programación I, Grado Dual en Industria Digital.',
            'slug' => Str::slug($nombre)
        ]);

        $category = Category::whereHas('period.organization', function ($query) {
            $query->where('organizations.slug', 'egibide');
        })
            ->where('slug', 'programacion')
            ->first();

        $nombre = 'Programación';

        factory(Curso::class)->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Módulo: Programación, CFGS en Desarrollo de Aplicaciones Multiplataforma.',
            'slug' => Str::slug($nombre)
        ]);
    }
}
