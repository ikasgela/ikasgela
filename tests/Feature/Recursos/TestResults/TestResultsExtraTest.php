<?php

namespace Tests\Feature\Recursos\TestResults;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\TestResult;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TestResultsExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testToggleTituloVisible()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        $test_result = TestResult::factory()->create(['curso_id' => $curso->id]);
        $actividad->test_results()->attach($test_result);

        $this->assertDatabaseHas('actividad_test_result', [
            'test_result_id' => $test_result->id,
            'titulo_visible' => true,
        ]);

        // When
        $response = $this->post(route('test_results.toggle.titulo_visible', [$actividad, $test_result]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_test_result', [
            'test_result_id' => $test_result->id,
            'titulo_visible' => false,
        ]);
    }

    public function testToggleDescripcionVisible()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        $test_result = TestResult::factory()->create(['curso_id' => $curso->id]);
        $actividad->test_results()->attach($test_result);

        $this->assertDatabaseHas('actividad_test_result', [
            'test_result_id' => $test_result->id,
            'descripcion_visible' => true,
        ]);

        // When
        $response = $this->post(route('test_results.toggle.descripcion_visible', [$actividad, $test_result]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_test_result', [
            'test_result_id' => $test_result->id,
            'descripcion_visible' => false,
        ]);
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();
        $count = TestResult::count();

        // When
        $response = $this->post(route('test_results.duplicar', $test_result));

        // Then
        $response->assertRedirect(route('test_results.index'));
        $this->assertSame($count + 1, TestResult::count());
    }

    public function testRellenar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create(['completado' => false]);

        // When
        $response = $this->put(route('test_results.rellenar', $test_result), [
            'num_correctas' => 8,
            'num_incorrectas' => 2,
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('test_results', [
            'id' => $test_result->id,
            'completado' => true,
            'num_correctas' => 8,
            'num_incorrectas' => 2,
        ]);
    }

    public function testNotAuthNotToggle()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->post(route('test_results.toggle.titulo_visible', [$actividad, $test_result]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->post(route('test_results.duplicar', $test_result));

        // Then
        $response->assertForbidden();
    }
}
