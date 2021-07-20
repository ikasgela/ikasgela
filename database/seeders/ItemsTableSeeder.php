<?php

namespace Database\Seeders;

use App\Item;
use App\Pregunta;
use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $pregunta = Pregunta::where('titulo', '¿Correcto?')->first();

        Item::factory()->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'Sí',
            'correcto' => true,
            'feedback' => 'Correcto, es un bucle de 0 a N.',
        ]);

        Item::factory()->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'No',
            'correcto' => false,
            'feedback' => 'El bucle while es un bucle de 0 a N. La condición se evalua antes de entrar en el bucle y puede que nunca lleguen a ejecutarse las instrucciones del cuerpo del bucle si la primera vez se evalua a false.',
        ]);

        $pregunta = Pregunta::where('titulo', '¿Son lenguajes de programación?')->first();

        Item::factory()->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'Java',
            'correcto' => true,
            'feedback' => 'Creado por Sun Microsystems en 1996.',
        ]);

        Item::factory()->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'C',
            'correcto' => true,
            'feedback' => 'Tiene su origen en los sistemas UNIX de los 1970s.',
        ]);

        Item::factory()->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'Google',
            'correcto' => false,
            'feedback' => 'No, no es un lenguaje de programación.',
        ]);

        Item::factory()->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'Python',
            'correcto' => true,
            'feedback' => 'Creado por Gido Van Rossum, creo.',
        ]);
    }
}
