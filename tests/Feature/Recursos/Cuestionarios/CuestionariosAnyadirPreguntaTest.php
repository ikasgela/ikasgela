<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use App\Models\Cuestionario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuestionariosAnyadirPreguntaTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function anyadir_pregunta_a_cuestionario()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('preguntas.anyadir', $cuestionario));

        // Then
        $response->assertSeeInOrder([
            __('New question'),
            $cuestionario->titulo,
            __('Save'),
        ]);
    }
}
