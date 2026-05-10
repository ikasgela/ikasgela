<?php

namespace Tests\Feature\Recursos\FlashDecks;

use App\Models\FlashDeck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class FlashDecksExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();
        $total = FlashDeck::all()->count();

        // When
        $this->post(route('flash_decks.duplicar', $flash_deck));

        // Then
        $this->assertEquals($total + 1, FlashDeck::all()->count());
    }

    public function testNotAdminProfesorNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->post(route('flash_decks.duplicar', $flash_deck));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        // Given
        $flash_deck = FlashDeck::factory()->create();

        // When
        $response = $this->post(route('flash_decks.duplicar', $flash_deck));

        // Then
        $response->assertRedirect(route('login'));
    }
}
