<?php

namespace Tests\Feature\Recursos\FlashDecks;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FlashDeck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class FlashDecksAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
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
        $flash_deck1 = FlashDeck::factory()->create(['curso_id' => $curso->id, 'plantilla' => true]);
        $flash_deck2 = FlashDeck::factory()->create(['curso_id' => $curso->id, 'plantilla' => true]);
        $flash_deck3 = FlashDeck::factory()->create(['curso_id' => $curso->id, 'plantilla' => true]);

        $actividad->flash_decks()->attach($flash_deck1);
        $actividad->flash_decks()->attach($flash_deck3);

        // When
        $response = $this->get(route('flash_decks.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSee($flash_deck1->titulo);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $flash_deck1 = FlashDeck::factory()->create(['curso_id' => $curso->id]);
        $flash_deck2 = FlashDeck::factory()->create(['curso_id' => $curso->id]);

        // When
        $this->post(route('flash_decks.asociar', $actividad), ['seleccionadas' => [$flash_deck1->id, $flash_deck2->id]]);

        // Then
        $this->assertCount(2, $actividad->flash_decks()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('flash_decks.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $flash_deck1 = FlashDeck::factory()->create();
        $flash_deck2 = FlashDeck::factory()->create();

        $actividad->flash_decks()->attach($flash_deck1);
        $actividad->flash_decks()->attach($flash_deck2);

        // When
        $this->delete(route('flash_decks.desasociar', [$actividad, $flash_deck1]));

        // Then
        $this->assertCount(1, $actividad->flash_decks()->get());
    }
}
