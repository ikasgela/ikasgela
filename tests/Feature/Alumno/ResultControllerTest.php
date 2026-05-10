<?php

namespace Tests\Feature\Alumno;

use App\Models\Curso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class ResultControllerTest extends TestCase
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
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('results.index'));

        // Then
        $response->assertSuccessful()->assertSee(__('Results'));
    }

    public function testIndexWithCurso()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('results.index'));

        // Then
        $response->assertSuccessful()->assertSee(__('Results'));
    }

    public function testNotAlumnoProfesorTutorNotIndex()
    {
        // Auth - admin has no alumno/profesor/tutor role
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('results.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // When
        $response = $this->get(route('results.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testPdf()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('results.pdf'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAlumnoProfesorTutorNotPdf()
    {
        // Auth - admin has no alumno/profesor/tutor role
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('results.pdf'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotPdf()
    {
        // When
        $response = $this->get(route('results.pdf'));

        // Then
        $response->assertRedirect(route('login'));
    }
}
