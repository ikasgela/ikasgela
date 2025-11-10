<?php

namespace Tests\Feature\Recursos\TestResults;

use App\Models\TestResult;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class TestResultsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo', 'num_preguntas', 'valor_correcta', 'valor_incorrecta',
    ];

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create([
            'plantilla' => true,
        ]);
        session(['filtrar_curso_actual' => $test_result->curso_id]);

        // When
        $response = $this->get(route('test_results.index'));

        // Then
        $response->assertSuccessful()->assertSee($test_result->titulo);
    }

    public function testNotPlantillaNotIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();
        session(['filtrar_curso_actual' => $test_result->curso_id]);

        // When
        $response = $this->get(route('test_results.index'));

        // Then
        $response->assertDontSee($test_result->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('test_results.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('test_results.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('test_results.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New test result'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('test_results.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('test_results.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->make();
        $total = TestResult::all()->count();

        // When
        $this->post(route('test_results.store'), $test_result->toArray());

        // Then
        $this->assertEquals($total + 1, TestResult::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $test_result = TestResult::factory()->make();

        // When
        $response = $this->post(route('test_results.store'), $test_result->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $test_result = TestResult::factory()->make();

        // When
        $response = $this->post(route('test_results.store'), $test_result->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = TestResult::all()->count();

        $empty = new TestResult();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('test_results.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, TestResult::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->make([$field => null]);

        // When
        $response = $this->post(route('test_results.store'), $test_result->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->get(route('test_results.show', $test_result));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Test result'), $test_result->titulo]);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->get(route('test_results.show', $test_result));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->get(route('test_results.show', $test_result));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->get(route('test_results.edit', $test_result), $test_result->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$test_result->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->get(route('test_results.edit', $test_result), $test_result->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->get(route('test_results.edit', $test_result), $test_result->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();
        $test_result->titulo = "Updated";

        // When
        $this->put(route('test_results.update', $test_result), $test_result->toArray());

        // Then
        $this->assertDatabaseHas('test_results', ['id' => $test_result->id, 'titulo' => $test_result->titulo]);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $test_result = TestResult::factory()->create();
        $test_result->titulo = "Updated";

        // When
        $response = $this->put(route('test_results.update', $test_result), $test_result->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $test_result = TestResult::factory()->create();
        $test_result->titulo = "Updated";

        // When
        $response = $this->put(route('test_results.update', $test_result), $test_result->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();
        $empty = new TestResult();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('test_results.update', $test_result), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();
        $test_result->$field = null;

        // When
        $response = $this->put(route('test_results.update', $test_result), $test_result->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->updateRequires($field);
        }
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $this->delete(route('test_results.destroy', $test_result));

        // Then
        $this->assertDatabaseMissing('test_results', $test_result->toArray());
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->delete(route('test_results.destroy', $test_result));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $test_result = TestResult::factory()->create();

        // When
        $response = $this->delete(route('test_results.destroy', $test_result));

        // Then
        $response->assertRedirect(route('login'));
    }
}
