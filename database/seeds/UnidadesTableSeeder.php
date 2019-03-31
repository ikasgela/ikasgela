<?php

use App\Curso;
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
        $curso = Curso::where('nombre', 'Programación')->first();

        $nombre = 'Introducción';

        DB::table('unidades')->insert([
            'curso_id' => $curso->id,
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ]);
    }
}
