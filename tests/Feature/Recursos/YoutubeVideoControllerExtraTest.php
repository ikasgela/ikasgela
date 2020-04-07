<?php

namespace Tests\Feature;

use App\Actividad;
use App\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YoutubeVideoControllerExtraTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testActividad()
    {
        // Given
        $this->actingAs($this->profesor);

        $actividad = factory(Actividad::class)->create();
        $youtube_video1 = factory(YoutubeVideo::class)->create();
        $youtube_video2 = factory(YoutubeVideo::class)->create();
        $youtube_video3 = factory(YoutubeVideo::class)->create();

        $actividad->youtube_videos()->attach($youtube_video1);
        $actividad->youtube_videos()->attach($youtube_video3);

        // When
        $response = $this->get(route('youtube_videos.actividad', $actividad));

        // Then
        $response->assertSeeInOrder([
            __('Resources: YouTube videos'),
            __('Assigned resources'),
            $youtube_video1->titulo,
            $youtube_video3->titulo,
            __('Available resources'),
            $youtube_video2->titulo,
        ]);
    }

    /*    public function testAsociar()
        {
        }

        public function testDesasociar()
        {
            // Given
            // When
            // Then
        }*/

}
