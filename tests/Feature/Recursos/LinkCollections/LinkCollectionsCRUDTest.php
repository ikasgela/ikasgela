<?php

namespace Tests\Feature\Recursos\LinkCollections;

use App\Models\LinkCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LinkCollectionsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo'
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
        $link_collection = LinkCollection::factory()->create();
        session(['filtrar_curso_actual' => $link_collection->curso_id]);

        // When
        $response = $this->get(route('link_collections.index'));

        // Then
        $response->assertSuccessful()->assertSee($link_collection->titulo);
    }

    public function testNotProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('link_collections.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('link_collections.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('link_collections.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New link collection'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('link_collections.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('link_collections.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->make();
        $total = LinkCollection::all()->count();

        // When
        $this->post(route('link_collections.store'), $link_collection->toArray());

        // Then
        $this->assertEquals($total + 1, LinkCollection::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $link_collection = LinkCollection::factory()->make();

        // When
        $response = $this->post(route('link_collections.store'), $link_collection->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $link_collection = LinkCollection::factory()->make();

        // When
        $response = $this->post(route('link_collections.store'), $link_collection->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = LinkCollection::all()->count();

        $empty = new LinkCollection();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('link_collections.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, LinkCollection::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->make([$field => null]);

        // When
        $response = $this->post(route('link_collections.store'), $link_collection->toArray());

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
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->get(route('link_collections.show', $link_collection));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$link_collection->titulo, __('Add')]);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->get(route('link_collections.show', $link_collection));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->get(route('link_collections.show', $link_collection));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->get(route('link_collections.edit', $link_collection), $link_collection->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$link_collection->titulo, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->get(route('link_collections.edit', $link_collection), $link_collection->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->get(route('link_collections.edit', $link_collection), $link_collection->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $link_collection->titulo = "Updated";

        // When
        $this->put(route('link_collections.update', $link_collection), $link_collection->toArray());

        // Then
        $this->assertDatabaseHas('link_collections', ['id' => $link_collection->id, 'titulo' => $link_collection->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $link_collection->titulo = "Updated";

        // When
        $response = $this->put(route('link_collections.update', $link_collection), $link_collection->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $link_collection = LinkCollection::factory()->create();
        $link_collection->titulo = "Updated";

        // When
        $response = $this->put(route('link_collections.update', $link_collection), $link_collection->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $empty = new LinkCollection();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('link_collections.update', $link_collection), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();
        $link_collection->$field = null;

        // When
        $response = $this->put(route('link_collections.update', $link_collection), $link_collection->toArray());

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
        $link_collection = LinkCollection::factory()->create();

        // When
        $this->delete(route('link_collections.destroy', $link_collection));

        // Then
        $this->assertDatabaseMissing('link_collections', $link_collection->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->delete(route('link_collections.destroy', $link_collection));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $link_collection = LinkCollection::factory()->create();

        // When
        $response = $this->delete(route('link_collections.destroy', $link_collection));

        // Then
        $response->assertRedirect(route('login'));
    }
}
