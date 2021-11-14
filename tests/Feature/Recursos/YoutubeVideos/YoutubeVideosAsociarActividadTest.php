<?php

namespace Tests\Feature\Recursos\YoutubeVideos;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YoutubeVideosAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testActividad()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $youtube_video1 = YoutubeVideo::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $youtube_video2 = YoutubeVideo::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $youtube_video3 = YoutubeVideo::factory()->create([
            'curso_id' => $curso->id,
        ]);

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
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $youtube_video1 = YoutubeVideo::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $youtube_video2 = YoutubeVideo::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('youtube_videos.asociar', $actividad), ['seleccionadas' => [$youtube_video1, $youtube_video2]]);

        // Then
        $this->assertCount(2, $actividad->youtube_videos()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('youtube_videos.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $youtube_video1 = YoutubeVideo::factory()->create();
        $youtube_video2 = YoutubeVideo::factory()->create();

        $actividad->youtube_videos()->attach($youtube_video1);
        $actividad->youtube_videos()->attach($youtube_video2);

        // When
        $this->delete(route('youtube_videos.desasociar', [$actividad, $youtube_video1]));

        // Then
        $this->assertCount(1, $actividad->youtube_videos()->get());
    }
}
