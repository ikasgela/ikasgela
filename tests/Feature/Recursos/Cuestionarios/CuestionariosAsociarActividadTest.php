<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use App\Actividad;
use App\Cuestionario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuestionariosAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

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
        $actividad = factory(Actividad::class)->create();
        $cuestionario1 = factory(Cuestionario::class)->create();
        $cuestionario2 = factory(Cuestionario::class)->create();
        $cuestionario3 = factory(Cuestionario::class)->create();

        $actividad->cuestionarios()->attach($cuestionario1);
        $actividad->cuestionarios()->attach($cuestionario3);

        // When
        $response = $this->get(route('cuestionarios.actividad', $actividad));

        // Then
        $response->assertSeeInOrder([
            __('Resources: Questionnaires'),
            __('Assigned resources'),
            $cuestionario1->repositorio,
            $cuestionario3->repositorio,
            __('Available resources'),
            $cuestionario2->repositorio,
        ]);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = factory(Actividad::class)->create();
        $cuestionario1 = factory(Cuestionario::class)->create();
        $cuestionario2 = factory(Cuestionario::class)->create();

        // When
        $this->post(route('cuestionarios.asociar', $actividad), ['seleccionadas' => [$cuestionario1, $cuestionario2]]);

        // Then
        $this->assertCount(2, $actividad->cuestionarios()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = factory(Actividad::class)->create();

        // When
        $response = $this->post(route('cuestionarios.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = factory(Actividad::class)->create();
        $cuestionario1 = factory(Cuestionario::class)->create();
        $cuestionario2 = factory(Cuestionario::class)->create();

        $actividad->cuestionarios()->attach($cuestionario1);
        $actividad->cuestionarios()->attach($cuestionario2);

        // When
        $this->delete(route('cuestionarios.desasociar', [$actividad, $cuestionario1]));

        // Then
        $this->assertCount(1, $actividad->cuestionarios()->get());
    }
}
