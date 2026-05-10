<?php

namespace Tests\Feature\Recursos\YoutubeVideos;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YoutubeVideosExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testToggleTituloVisible()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        $youtube_video = YoutubeVideo::factory()->create(['curso_id' => $curso->id]);
        $actividad->youtube_videos()->attach($youtube_video);

        $this->assertDatabaseHas('actividad_youtube_video', [
            'youtube_video_id' => $youtube_video->id,
            'titulo_visible' => true,
        ]);

        // When
        $response = $this->post(route('youtube_videos.toggle.titulo_visible', [$actividad, $youtube_video]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_youtube_video', [
            'youtube_video_id' => $youtube_video->id,
            'titulo_visible' => false,
        ]);
    }

    public function testToggleDescripcionVisible()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        $youtube_video = YoutubeVideo::factory()->create(['curso_id' => $curso->id]);
        $actividad->youtube_videos()->attach($youtube_video);

        $this->assertDatabaseHas('actividad_youtube_video', [
            'youtube_video_id' => $youtube_video->id,
            'descripcion_visible' => true,
        ]);

        // When
        $response = $this->post(route('youtube_videos.toggle.descripcion_visible', [$actividad, $youtube_video]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_youtube_video', [
            'youtube_video_id' => $youtube_video->id,
            'descripcion_visible' => false,
        ]);
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();
        $count = YoutubeVideo::count();

        // When
        $response = $this->post(route('youtube_videos.duplicar', $youtube_video));

        // Then
        $response->assertRedirect(route('youtube_videos.index'));
        $this->assertSame($count + 1, YoutubeVideo::count());
    }

    public function testNotAuthNotToggle()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->post(route('youtube_videos.toggle.titulo_visible', [$actividad, $youtube_video]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->post(route('youtube_videos.duplicar', $youtube_video));

        // Then
        $response->assertForbidden();
    }
}
