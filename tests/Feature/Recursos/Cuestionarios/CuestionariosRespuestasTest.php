<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use App\Actividad;
use App\Cuestionario;
use App\Item;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuestionariosRespuestasTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function cuestionario_has_respuestas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = factory(Cuestionario::class)->create();

        // When
        $response = $this->put(route('cuestionarios.respuesta', $cuestionario), ['respuestas' => null]);

        // Then
        $response->assertSessionHasErrors('respuestas');
    }

    /** @test */
    public function respuesta_ok()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = factory(Item::class)->create([
            'correcto' => true,
            'feedback' => 'Feedback OK',
        ]);

        $pregunta = $item->pregunta;
        $cuestionario = $item->pregunta->cuestionario;

        // When
        $this->get(route('cuestionarios.show', $cuestionario));
        $response = $this->put(route('cuestionarios.respuesta', $cuestionario), ['respuestas' => [$pregunta->id => [$item->id]]]);

        // Then
        $response->assertRedirect(route('cuestionarios.show', $cuestionario));

        $response = $this->get(route('cuestionarios.show', $cuestionario));
        $response->assertSeeInOrder(['valid-feedback', 'Feedback OK']);
    }

    /** @test */
    public function respuesta_error()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = factory(Item::class)->create([
            'correcto' => false,
            'feedback' => 'Feedback ERROR',
        ]);

        $pregunta = $item->pregunta;
        $cuestionario = $item->pregunta->cuestionario;

        // When
        $this->get(route('cuestionarios.show', $cuestionario));
        $response = $this->put(route('cuestionarios.respuesta', $cuestionario), ['respuestas' => [$pregunta->id => [$item->id]]]);

        // Then
        $response->assertRedirect(route('cuestionarios.show', $cuestionario));

        $response = $this->get(route('cuestionarios.show', $cuestionario));
        $response->assertSeeInOrder(['invalid-feedback', 'Feedback ERROR']);
    }
}
