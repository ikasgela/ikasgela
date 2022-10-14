<?php

namespace Tests\Feature\Recursos\YoutubeVideos;

use App\Models\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YoutubeVideosCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo', 'codigo'
    ];

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.index'));

        // Then
        $response->assertSuccessful()->assertSee($youtube_video->titulo);
    }

    public function testIndexAdminFiltro()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->post(route('youtube_videos.index.filtro', ['curso_id' => $youtube_video->curso_id]));

        // Then
        $response->assertSuccessful()->assertSee($youtube_video->titulo);
    }

    public function testNotProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('youtube_videos.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('youtube_videos.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('youtube_videos.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New YouTube video'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('youtube_videos.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('youtube_videos.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->make();
        $total = YoutubeVideo::all()->count();

        // When
        $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $this->assertCount($total + 1, YoutubeVideo::all());
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->make();

        // When
        $response = $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $youtube_video = YoutubeVideo::factory()->make();

        // When
        $response = $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = YoutubeVideo::all()->count();

        $empty = new YoutubeVideo();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('youtube_videos.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, YoutubeVideo::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->make([$field => null]);

        // When
        $response = $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.show', $youtube_video));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('YouTube video'), $youtube_video->titulo]);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.show', $youtube_video));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Auth
        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.show', $youtube_video));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$youtube_video->titulo, $youtube_video->slug, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();
        $youtube_video->titulo = "Updated";

        // When
        $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $this->assertDatabaseHas('youtube_videos', ['id' => $youtube_video->id, 'titulo' => $youtube_video->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();
        $youtube_video->titulo = "Updated";

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $youtube_video = YoutubeVideo::factory()->create();
        $youtube_video->titulo = "Updated";

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();
        $empty = new YoutubeVideo();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();
        $youtube_video->$field = null;

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->updateRequires($field);
        }
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $this->delete(route('youtube_videos.destroy', $youtube_video));

        // Then
        $this->assertDatabaseMissing('youtube_videos', $youtube_video->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->delete(route('youtube_videos.destroy', $youtube_video));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $youtube_video = YoutubeVideo::factory()->create();

        // When
        $response = $this->delete(route('youtube_videos.destroy', $youtube_video));

        // Then
        $response->assertRedirect(route('login'));
    }
}
