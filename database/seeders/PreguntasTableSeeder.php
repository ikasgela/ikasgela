<?php

namespace Database\Seeders;

use App\Models\Cuestionario;
use App\Models\Pregunta;
use Illuminate\Database\Seeder;

class PreguntasTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $cuestionario = Cuestionario::where('titulo', 'Cuestionario de ejemplo')->first();

        Pregunta::factory()->create([
            'cuestionario_id' => $cuestionario->id,
            'titulo' => '¿Correcto?',
            'texto' => 'Un bucle while puede no ejecutarse nunca.',
            'multiple' => false,
        ]);

        Pregunta::factory()->create([
            'cuestionario_id' => $cuestionario->id,
            'titulo' => '¿Son lenguajes de programación?',
            'texto' => 'Selecciona los nombres que corresponden a lenguajes de programación actuales.',
            'multiple' => true,
        ]);
    }
}
