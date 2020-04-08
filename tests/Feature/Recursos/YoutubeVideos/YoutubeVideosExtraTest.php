<?php

namespace Tests\Feature\Recursos\YoutubeVideos;

use App\Actividad;
use App\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YoutubeVideosExtraTest extends TestCase
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

    public function testAsociar()
    {
        // Given
        $this->actingAs($this->profesor);

        $actividad = factory(Actividad::class)->create();
        $youtube_video1 = factory(YoutubeVideo::class)->create();
        $youtube_video2 = factory(YoutubeVideo::class)->create();

        // When
        $this->post(route('youtube_videos.asociar', $actividad), ['seleccionadas' => [$youtube_video1, $youtube_video2]]);

        // Then
        $this->assertCount(2, $actividad->youtube_videos()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Given
        $this->actingAs($this->profesor);

        $actividad = factory(Actividad::class)->create();

        // When
        $response = $this->post(route('youtube_videos.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Given
        $this->actingAs($this->profesor);

        $actividad = factory(Actividad::class)->create();
        $youtube_video1 = factory(YoutubeVideo::class)->create();
        $youtube_video2 = factory(YoutubeVideo::class)->create();

        $actividad->youtube_videos()->attach($youtube_video1);
        $actividad->youtube_videos()->attach($youtube_video2);

        // When
        $this->delete(route('youtube_videos.desasociar', [$actividad, $youtube_video1]));

        // Then
        $this->assertCount(1, $actividad->youtube_videos()->get());
    }
}
