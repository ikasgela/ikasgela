<?php

use App\Cuestionario;
use App\Curso;
use App\Pregunta;
use Illuminate\Database\Seeder;

class PreguntasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cuestionario = Cuestionario::where('titulo', 'Cuestionario de ejemplo')->first();

        factory(Pregunta::class)->create([
            'cuestionario_id' => $cuestionario->id,
            'titulo' => '¿Correcto?',
            'texto' => 'Un bucle while puede no ejecutarse nunca.',
            'multiple' => false,
        ]);

        factory(Pregunta::class)->create([
            'cuestionario_id' => $cuestionario->id,
            'titulo' => '¿Son lenguajes de programación?',
            'texto' => 'Selecciona los nombres que corresponden a lenguajes de programación actuales.',
            'multiple' => true,
        ]);
    }
}
