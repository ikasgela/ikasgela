<?php

use App\Item;
use App\Pregunta;
use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pregunta = Pregunta::where('titulo', '¿Correcto?')->first();

        factory(Item::class)->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'Sí',
            'correcto' => true,
            'feedback' => 'Correcto, es un bucle de 0 a N.',
            'orden' => 1,
        ]);

        factory(Item::class)->create([
            'pregunta_id' => $pregunta->id,
            'texto' => 'No',
            'correcto' => false,
            'feedback' => 'El bucle while es un bucle de 0 a N. La condición se evalua antes de entrar en el bucle y puede que nunca lleguen a ejecutarse las instrucciones del cuerpo del bucle si la primera vez se evalua a false.',
            'orden' => 2,
        ]);
    }
}
