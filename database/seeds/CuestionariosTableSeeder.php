<?php

use App\Cuestionario;
use Illuminate\Database\Seeder;

class CuestionariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Cuestionario::class)->create([
            'titulo' => 'Cuestionario de ejemplo',
            'descripcion' => 'Preguntas de repaso sencillas.',
            'plantilla' => true,
        ]);
    }
}
