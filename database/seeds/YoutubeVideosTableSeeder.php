<?php

use App\YoutubeVideo;
use Illuminate\Database\Seeder;

class YoutubeVideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $video = new YoutubeVideo();
        $video->titulo = 'Primeros pasos';
        $video->descripcion = 'En este vídeo te explicamos cómo funciona Ikasgela.';
        $video->codigo = 'https://youtu.be/uhDQNbaVpt4';
        $video->save();

        $video = new YoutubeVideo();
        $video->titulo = 'How Not to Land an Orbital Rocket Booster';
        $video->codigo = 'https://youtu.be/bvim4rsNHkQ';
        $video->save();
    }
}
