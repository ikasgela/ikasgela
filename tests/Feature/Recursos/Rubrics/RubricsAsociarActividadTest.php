<?php

namespace Tests\Feature\Recursos\Rubrics;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class RubricsAsociarActividadTest extends TestCase
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
        $rubric1 = Rubric::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $rubric2 = Rubric::factory()->create([
            'curso_id' => $curso->id,
            'plantilla' => true,
        ]);
        $rubric3 = Rubric::factory()->create([
            'curso_id' => $curso->id,
        ]);

        $actividad->rubrics()->attach($rubric1);
        $actividad->rubrics()->attach($rubric3);

        // When
        $response = $this->get(route('rubrics.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Resources: Rubrics'),
            __('Assigned resources'),
            $rubric1->titulo,
            $rubric3->titulo,
            __('Available resources'),
            $rubric2->titulo,
        ]);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $rubric1 = Rubric::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $rubric2 = Rubric::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('rubrics.asociar', $actividad), ['seleccionadas' => [$rubric1, $rubric2]]);

        // Then
        $this->assertCount(2, $actividad->rubrics()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('rubrics.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $rubric1 = Rubric::factory()->create();
        $rubric2 = Rubric::factory()->create();

        $actividad->rubrics()->attach($rubric1);
        $actividad->rubrics()->attach($rubric2);

        // When
        $this->delete(route('rubrics.desasociar', [$actividad, $rubric1]));

        // Then
        $this->assertCount(1, $actividad->rubrics()->get());
    }
}
