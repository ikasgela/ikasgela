<?php

namespace Tests\Feature\Recursos\LinkCollections;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\LinkCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LinkCollectionsExtraTest extends TestCase
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
        $link_collection = LinkCollection::factory()->create(['curso_id' => $curso->id]);
        $actividad->link_collections()->attach($link_collection);

        $this->assertDatabaseHas('actividad_link_collection', [
            'link_collection_id' => $link_collection->id,
            'titulo_visible' => true,
        ]);

        // When
        $response = $this->post(route('link_collections.toggle.titulo_visible', [$actividad, $link_collection]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_link_collection', [
            'link_collection_id' => $link_collection->id,
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
        $link_collection = LinkCollection::factory()->create(['curso_id' => $curso->id]);
        $actividad->link_collections()->attach($link_collection);

        $this->assertDatabaseHas('actividad_link_collection', [
            'link_collection_id' => $link_collection->id,
            'descripcion_visible' => true,
        ]);

        // When
        $response = $this->post(route('link_collections.toggle.descripcion_visible', [$actividad, $link_collection]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_link_collection', [
            'link_collection_id' => $link_collection->id,
            'descripcion_visible' => false,
        ]);
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $count = LinkCollection::count();

        // When
        $response = $this->post(route('link_collections.duplicar', $link_collection));

        // Then
        $response->assertRedirect(route('link_collections.index'));
        $this->assertSame($count + 1, LinkCollection::count());
    }

    public function testNotAuthNotToggle()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->post(route('link_collections.toggle.titulo_visible', [$actividad, $link_collection]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->post(route('link_collections.duplicar', $link_collection));

        // Then
        $response->assertForbidden();
    }
}
