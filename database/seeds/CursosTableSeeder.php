<?php

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

        DB::table('cursos')->insert([
            'nombre' => $nombre,
            'descripcion' => 'Fundamentos de Programación en Java.',
            'slug' => Str::slug($nombre)
        ]);
    }
}
