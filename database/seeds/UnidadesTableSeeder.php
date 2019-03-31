<?php

use App\Curso;
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
        $curso = Curso::where('nombre', 'Programación')->first();

        $nombre = 'Introducción';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Diseño de algoritmos';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Programación estructurada';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Funciones';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Arrays';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Programación orientada a objetos';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = 'poo';
        $curso->unidades()->save($unidad);

        $nombre = 'Colecciones';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Programación funcional';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'GUI';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);

        $nombre = 'Persistencia';
        $unidad = new Unidad();
        $unidad->nombre = $nombre;
        $unidad->slug = Str::slug($nombre);
        $curso->unidades()->save($unidad);
    }
}
