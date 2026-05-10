<?php

namespace Tests\Feature\Profesor;

use App\Models\Curso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class TutorControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->tutor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('tutor.index'));

        // Then
        $response->assertSuccessful()->assertSee(__('Group report'));
    }

    public function testNotProfesorTutorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_profesor_tutor);

        // When
        $response = $this->get(route('tutor.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // When
        $response = $this->get(route('tutor.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testTareasEnviadas()
    {
        // Auth
        $this->actingAs($this->tutor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('tutor.tareas_enviadas'));

        // Then
        $response->assertSuccessful()->assertSee(__('Activities per day'));
    }

    public function testNotProfesorTutorNotTareasEnviadas()
    {
        // Auth
        $this->actingAs($this->not_profesor_tutor);

        // When
        $response = $this->get(route('tutor.tareas_enviadas'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotTareasEnviadas()
    {
        // When
        $response = $this->get(route('tutor.tareas_enviadas'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testExport()
    {
        // Auth
        $this->actingAs($this->tutor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('tutor.export'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotProfesorTutorNotExport()
    {
        // Auth
        $this->actingAs($this->not_profesor_tutor);

        // When
        $response = $this->get(route('tutor.export'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotExport()
    {
        // When
        $response = $this->get(route('tutor.export'));

        // Then
        $response->assertRedirect(route('login'));
    }
}
