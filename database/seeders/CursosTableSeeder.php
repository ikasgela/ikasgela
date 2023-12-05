<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Curso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CursosTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
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

        Curso::factory()->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Fundamentos de Programación con Java.',
            'slug' => Str::slug($nombre),
            'fecha_inicio' => now()->addDays(-7),
            'fecha_fin' => now()->addMonths(3),
            'minimo_entregadas' => 80,
            'minimo_competencias' => 50,
            'minimo_examenes' => 50,
            'examenes_obligatorios' => false,
            'progreso_visible' => true,
        ]);

/*        $category = Category::whereHas('period.organization', function ($query) {
            $query->where('organizations.slug', 'deusto');
        })
            ->where('slug', 'programacion')
            ->first();

        $nombre = 'Programación I';

        Curso::factory()->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Asignatura: Programación I, Grado Dual en Industria Digital.',
            'slug' => Str::slug($nombre),
            'fecha_inicio' => now()->addDays(-7),
            'fecha_fin' => now()->addMonths(3),
        ]);

        $category = Category::whereHas('period.organization', function ($query) {
            $query->where('organizations.slug', 'egibide');
        })
            ->where('slug', 'programacion')
            ->first();

        $nombre = 'Programación';

        Curso::factory()->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Módulo: Programación, CFGS en Desarrollo de Aplicaciones Multiplataforma.',
            'slug' => Str::slug($nombre),
            'fecha_inicio' => now()->addDays(-7),
            'fecha_fin' => now()->addMonths(3),
        ]);*/
    }
}
