<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbacksTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $curso = Curso::where('nombre', 'Programación')->first();

        Feedback::factory()->create([
            'comentable_id' => $curso,
            'titulo' => 'Buen trabajo, sigue así.',
            'mensaje' => 'Buen trabajo, sigue así.',
        ]);

        Feedback::factory()->create([
            'comentable_id' => $curso,
            'titulo' => 'Necesita mejoras, vuelve a intentarlo.',
            'mensaje' => 'Necesita mejoras, vuelve a intentarlo.',
        ]);
    }
}
