<?php

namespace Tests\Feature\Recursos\Links;

use Override;
use App\Models\Link;
use App\Models\LinkCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LinksControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->post(route('links.store'), [
            'url' => 'https://example.com',
            'descripcion' => 'Descripción de prueba',
            'link_collection_id' => $link_collection->id,
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('links', [
            'url' => 'https://example.com',
            'link_collection_id' => $link_collection->id,
        ]);
    }

    public function testStoreRequiresUrl()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->post(route('links.store'), [
            'link_collection_id' => $link_collection->id,
        ]);

        // Then
        $response->assertSessionHasErrors('url');
    }

    public function testNotAuthNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // When
        $response = $this->post(route('links.store'), []);

        // Then
        $response->assertForbidden();
    }

    public function testDestroy()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link = Link::factory()->create();

        // When
        $response = $this->delete(route('links.destroy', $link));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }

    public function testNotAuthNotDestroy()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $link = Link::factory()->create();

        // When
        $response = $this->delete(route('links.destroy', $link));

        // Then
        $response->assertForbidden();
    }

    public function testReordenar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $link1 = Link::factory()->create(['link_collection_id' => $link_collection->id, 'orden' => 1]);
        $link2 = Link::factory()->create(['link_collection_id' => $link_collection->id, 'orden' => 2]);

        // When
        $response = $this->post(route('links.reordenar', [$link1, $link2]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('links', ['id' => $link1->id, 'orden' => 2]);
        $this->assertDatabaseHas('links', ['id' => $link2->id, 'orden' => 1]);
    }

    public function testNotAuthNotReordenar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $link1 = Link::factory()->create(['link_collection_id' => $link_collection->id]);
        $link2 = Link::factory()->create(['link_collection_id' => $link_collection->id]);

        // When
        $response = $this->post(route('links.reordenar', [$link1, $link2]));

        // Then
        $response->assertForbidden();
    }
}
