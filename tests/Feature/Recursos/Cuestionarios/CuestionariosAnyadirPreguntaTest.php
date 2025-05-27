<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use Override;
use App\Models\Cuestionario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CuestionariosAnyadirPreguntaTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    #[Test]
    public function anyadir_pregunta_a_cuestionario()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->get(route('preguntas.anyadir', $cuestionario));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('New question'),
            $cuestionario->titulo,
            __('Save'),
        ]);
    }
}
