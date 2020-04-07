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
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.index'));

        // Then
        $response->assertSee($youtube_video->titulo);
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
        $response->assertSeeInOrder([__('New YouTube video'), __('Save')]);
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
        $youtube_video = factory(YoutubeVideo::class)->make();
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
        $youtube_video = factory(YoutubeVideo::class)->make();

        // When
        $response = $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $youtube_video = factory(YoutubeVideo::class)->make();

        // When
        $response = $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreThereAreRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $empty = new YoutubeVideo();

        // When
        $response = $this->post(route('youtube_videos.store'), $empty->toArray());

        // Then
        $response->assertSessionHasErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->make([$field => null]);

        // When
        $response = $this->post(route('youtube_videos.store'), $youtube_video->toArray());

        // Then
        $response->assertSessionHasErrors($field);
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
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.show', $youtube_video));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.show', $youtube_video));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Auth
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

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
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertSeeInOrder([$youtube_video->titulo, $youtube_video->slug, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->get(route('youtube_videos.edit', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

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
        $youtube_video = factory(YoutubeVideo::class)->create();
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
        $youtube_video = factory(YoutubeVideo::class)->create();
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
        $youtube_video = factory(YoutubeVideo::class)->create();
        $youtube_video->titulo = "Updated";

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateThereAreRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();
        $empty = new YoutubeVideo();

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $empty->toArray());

        // Then
        $response->assertSessionHasErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();
        $youtube_video->$field = null;

        // When
        $response = $this->put(route('youtube_videos.update', $youtube_video), $youtube_video->toArray());

        // Then
        $response->assertSessionHasErrors($field);
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
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

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
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->delete(route('youtube_videos.destroy', $youtube_video));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $youtube_video = factory(YoutubeVideo::class)->create();

        // When
        $response = $this->delete(route('youtube_videos.destroy', $youtube_video));

        // Then
        $response->assertRedirect(route('login'));
    }
}
