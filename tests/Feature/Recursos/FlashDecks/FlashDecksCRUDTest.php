<?php

namespace Tests\Feature\Recursos\FlashDecks;

use App\Models\FlashDeck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class FlashDecksCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo',
    ];

    #[Override]
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
        $flash_deck = FlashDeck::factory()->create(['plantilla' => true]);
        session(['filtrar_curso_actual' => $flash_deck->curso_id]);

        // When
        $response = $this->get(route('flash_decks.index'));

        // Then
        $response->assertSuccessful()->assertSee($flash_deck->titulo);
    }

    public function testIndexAdminFiltro()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $flash_deck = FlashDeck::factory()->create(['plantilla' => true]);

        // When
        $response = $this->post(route('flash_decks.index.filtro', ['curso_id' => $flash_deck->curso_id]));

        // Then
        $response->assertSuccessful()->assertSee($flash_deck->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('flash_decks.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('flash_decks.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('flash_decks.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New deck'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('flash_decks.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('flash_decks.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->make();
        $total = FlashDeck::all()->count();

        // When
        $this->post(route('flash_decks.store'), $flash_deck->toArray());

        // Then
        $this->assertEquals($total + 1, FlashDeck::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $flash_deck = FlashDeck::factory()->make();

        // When
        $response = $this->post(route('flash_decks.store'), $flash_deck->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $flash_deck = FlashDeck::factory()->make();

        // When
        $response = $this->post(route('flash_decks.store'), $flash_deck->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = FlashDeck::all()->count();
        $empty = new FlashDeck();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('flash_decks.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, FlashDeck::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->make([$field => null]);

        // When
        $response = $this->post(route('flash_decks.store'), $flash_deck->toArray());

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
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->get(route('flash_decks.show', $flash_deck));

        // Then
        $response->assertSuccessful()->assertSee($flash_deck->titulo);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->get(route('flash_decks.show', $flash_deck));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->get(route('flash_decks.show', $flash_deck));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->get(route('flash_decks.edit', $flash_deck));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$flash_deck->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->get(route('flash_decks.edit', $flash_deck));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->get(route('flash_decks.edit', $flash_deck));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();
        $flash_deck->titulo = 'Updated';

        // When
        $this->put(route('flash_decks.update', $flash_deck), $flash_deck->toArray());

        // Then
        $this->assertDatabaseHas('flash_decks', ['id' => $flash_deck->id, 'titulo' => 'Updated']);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();
        $flash_deck->titulo = 'Updated';

        // When
        $response = $this->put(route('flash_decks.update', $flash_deck), $flash_deck->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $flash_deck = FlashDeck::factory()->create();
        $flash_deck->titulo = 'Updated';

        // When
        $response = $this->put(route('flash_decks.update', $flash_deck), $flash_deck->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();
        $empty = new FlashDeck();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('flash_decks.update', $flash_deck), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();
        $flash_deck->$field = null;

        // When
        $response = $this->put(route('flash_decks.update', $flash_deck), $flash_deck->toArray());

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
        $flash_deck = FlashDeck::factory()->create();

        // When
        $this->delete(route('flash_decks.destroy', $flash_deck));

        // Then
        $this->assertDatabaseMissing('flash_decks', ['id' => $flash_deck->id]);
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->delete(route('flash_decks.destroy', $flash_deck));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->delete(route('flash_decks.destroy', $flash_deck));

        // Then
        $response->assertRedirect(route('login'));
    }
}
