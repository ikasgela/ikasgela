<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use Override;
use App\Models\Cuestionario;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CuestionariosRespuestasTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    #[Test]
    public function cuestionario_has_respuestas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $cuestionario = Cuestionario::factory()->create();

        // When
        $response = $this->put(route('cuestionarios.respuesta', $cuestionario), ['respuestas' => null]);

        // Then
        $response->assertSessionHasErrors('respuestas');
    }

    #[Test]
    public function respuesta_correcta_seleccionada()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create([
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
        $response->assertSuccessful()->assertSeeInOrder(['valid-feedback', 'Feedback OK']);
    }

    #[Test]
    public function respuesta_incorrecta_seleccionada()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create([
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
        $response->assertSuccessful()->assertSeeInOrder(['invalid-feedback', 'Feedback ERROR']);
    }

    #[Test]
    public function respuesta_correcta_no_seleccionada()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create([
            'correcto' => false,
            'feedback' => 'Feedback ERROR',
        ]);

        $item2 = Item::factory()->create([
            'correcto' => true,
            'feedback' => 'Feedback OK no seleccionado',
        ]);

        $pregunta = $item->pregunta;
        $cuestionario = $item->pregunta->cuestionario;

        $pregunta->items()->save($item2);

        // When
        $this->get(route('cuestionarios.show', $cuestionario));
        $response = $this->put(route('cuestionarios.respuesta', $cuestionario), ['respuestas' => [$pregunta->id => [$item->id]]]);

        // Then
        $response->assertRedirect(route('cuestionarios.show', $cuestionario));

        $response = $this->get(route('cuestionarios.show', $cuestionario));
        $response->assertSuccessful()->assertSeeInOrder(['invalid-feedback', 'Feedback ERROR', 'invalid-feedback', 'Feedback OK no seleccionado']);
    }
}
