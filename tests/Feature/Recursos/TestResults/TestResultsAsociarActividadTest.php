<?php

namespace Tests\Feature\Recursos\TestResults;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\TestResult;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class TestResultsAsociarActividadTest extends TestCase
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
        $test_result1 = TestResult::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $test_result2 = TestResult::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $test_result3 = TestResult::factory()->create([
            'curso_id' => $curso->id,
        ]);

        $actividad->test_results()->attach($test_result1);
        $actividad->test_results()->attach($test_result3);

        // When
        $response = $this->get(route('test_results.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Resources: Test results'),
            __('Assigned resources'),
            $test_result1->repositorio,
            $test_result3->repositorio,
            __('Available resources'),
            $test_result2->repositorio,
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
        $test_result1 = TestResult::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $test_result2 = TestResult::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('test_results.asociar', $actividad), ['seleccionadas' => [$test_result1, $test_result2]]);

        // Then
        $this->assertCount(2, $actividad->test_results()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('test_results.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $test_result1 = TestResult::factory()->create();
        $test_result2 = TestResult::factory()->create();

        $actividad->test_results()->attach($test_result1);
        $actividad->test_results()->attach($test_result2);

        // When
        $this->delete(route('test_results.desasociar', [$actividad, $test_result1]));

        // Then
        $this->assertCount(1, $actividad->test_results()->get());
    }
}
