<?php

use App\Category;
use App\Curso;
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
        $category = Category::where('name', 'Programación')->first();

        $nombre = 'Programación';

        factory(Curso::class)->create([
            'category_id' => $category->id,
            'nombre' => $nombre,
            'descripcion' => 'Fundamentos de Programación en Java.',
            'slug' => Str::slug($nombre)
        ]);
    }
}
