<?php

namespace Database\Seeders;

use App\Models\YoutubeVideo;
use Illuminate\Database\Seeder;

class YoutubeVideosTableSeeder extends Seeder
{
    public function run()
    {
        YoutubeVideo::factory()->create([
            'titulo' => 'Primeros pasos',
            'descripcion' => 'En este vídeo te explicamos cómo funciona Ikasgela.',
            'codigo' => 'https://youtu.be/uhDQNbaVpt4',
            'curso_id' => 1,
        ]);

        YoutubeVideo::factory()->create([
            'titulo' => 'How Not to Land an Orbital Rocket Booster',
            'descripcion' => 'Recopilación de pifias de SpaceX.',
            'codigo' => 'https://youtu.be/bvim4rsNHkQ',
            'curso_id' => 1,
        ]);
    }
}
