<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use App\Pregunta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PreguntasAnyadirItemTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function anyadir_item_a_pregunta()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = factory(Pregunta::class)->create();

        // When
        $response = $this->get(route('items.anyadir', $pregunta));

        // Then
        $response->assertSeeInOrder([
            __('New item'),
            $pregunta->titulo,
            __('Save'),
        ]);
    }
}
