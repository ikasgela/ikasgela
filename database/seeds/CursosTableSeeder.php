<?php

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
        $nombre = 'Programación';

        factory(Curso::class)->create([
            'nombre' => $nombre,
            'descripcion' => 'Fundamentos de Programación en Java.',
            'slug' => Str::slug($nombre)
        ]);
    }
}
