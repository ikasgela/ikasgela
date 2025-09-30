<?php

namespace Database\Seeders;

use App\Models\FlashCard;
use App\Models\FlashDeck;
use Illuminate\Database\Seeder;

class FlashDeckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mazo = FlashDeck::factory()->create([
            'titulo' => 'Mazo de ejemplo',
            'descripcion' => 'Tarjetas de repaso.',
            'plantilla' => true,
            'curso_id' => 1,
        ]);

        FlashCard::factory()->create([
            'titulo' => 'Ejercicio 1',
            'anverso' => 'Pregunta',
            'anverso_visible' => true,
            'reverso' => 'Respuesta',
            'reverso_visible' => false,
            'flash_deck_id' => $mazo->id,
        ]);
    }
}
