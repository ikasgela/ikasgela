<?php

namespace Tests\Feature;

use App\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class YoutubeVideosCRUDTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.index'));

        // Then
        $response->assertSee($youtube_video->titulo);
    }

    public function testNotProfesorNotIndex()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('youtube_videos.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('youtube_videos.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('youtube_videos.create'));

        // Then
        $response->assertSeeInOrder([__('New YouTube video'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('youtube_videos.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('youtube_videos.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->make();
        $total = YoutubeVideo::all()->count();

        // When
        $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $this->assertCount($total + 1, YoutubeVideo::all());
    }

    public function testNotProfesorNotStore()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $youtube_video = factory(YoutubeVideo::class)->make();

        // When
        // Then
        $this->post(route('youtube_videos.store'), $youtube_video->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $youtube_video = factory(YoutubeVideo::class)->make();

        // When
        // Then
        $this->post(route('youtube_videos.store'), $youtube_video->toArray())
            ->assertRedirect(route('login'));
    }

    private function storeRequires(string $field)
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->make([$field => null]);

        // When
        // Then
        $this->post(route('youtube_videos.store'), $youtube_video->toArray())
            ->assertSessionHasErrors($field);
    }

    public function testStoreRequiresTitulo()
    {
        $this->storeRequires('titulo');
    }

    public function testStoreRequiresCodigo()
    {
        $this->storeRequires('codigo');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.show', $youtube_video));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotProfesorNotShow()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        // Then
        $this->get(route('youtube_videos.show', $youtube_video))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        // Then
        $this->get(route('youtube_videos.show', $youtube_video))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertSeeInOrder([$youtube_video->titulo, $youtube_video->slug, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        // Then
        $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        // Then
        $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();
        $youtube_video->titulo = "Updated";

        // When
        $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $this->assertDatabaseHas('youtube_videos', ['id' => $youtube_video->id, 'titulo' => $youtube_video->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();
        $youtube_video->titulo = "Updated";

        // When
        // Then
        $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();
        $youtube_video->titulo = "Updated";

        // When
        // Then
        $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray())
            ->assertRedirect(route('login'));
    }

    private function updateRequires(string $field)
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $youtube_video->$field = null;

        // Then
        $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray())
            ->assertSessionHasErrors($field);
    }

    public function testUpdateRequiresTitulo()
    {
        $this->updateRequires('titulo');
    }

    public function testUpdateRequiresCodigo()
    {
        $this->updateRequires('codigo');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $this->delete(route('youtube_videos.destroy', $youtube_video));

        // Then
        $this->assertDatabaseMissing('youtube_videos', $youtube_video->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        // Then
        $this->delete(route('youtube_videos.destroy', $youtube_video))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        // Then
        $this->delete(route('youtube_videos.destroy', $youtube_video))
            ->assertRedirect(route('login'));
    }
}
