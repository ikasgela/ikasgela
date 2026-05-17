<?php

namespace Tests\Feature\Alumno;

use App\Models\Curso;
use App\Models\Milestone;
use App\Models\User;
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

    public function testIndexAsProfesor()
    {
        $this->actingAs($this->profesor);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testIndexAsTutor()
    {
        $this->actingAs($this->tutor);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testPdfAsProfesor()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.pdf'));

        $response->assertSuccessful();
    }

    public function testPdfAsTutor()
    {
        $this->actingAs($this->tutor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.pdf'));

        $response->assertSuccessful();
    }

    public function testPdfNoCurso()
    {
        $this->actingAs($this->alumno);

        // When - no curso_actual set, pdf() will abort 404
        $response = $this->get(route('results.pdf'));

        // Then - 404 because no curso_actual
        $response->assertNotFound();
    }

    public function testPdfWithBajaAnsiedad()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // Set baja_ansiedad flag
        $this->alumno->update(['baja_ansiedad' => true]);

        $response = $this->get(route('results.pdf'));

        $response->assertNotFound();
    }

    public function testIndexConFiltroUserId()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $alumno = User::factory()->create();

        $response = $this->get(route('results.index', ['user_id' => $alumno->id]));

        $response->assertSuccessful();
    }

    public function testIndexConFiltroUserIdNegativo()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        // Primero establecer un filtro en sesión
        session(['filtrar_user_actual' => $this->alumno->id]);

        $response = $this->get(route('results.index', ['user_id' => -1]));

        $response->assertSuccessful();
    }

    public function testIndexConSesionFiltrarUser()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        session(['filtrar_user_actual' => $this->alumno->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testIndexConFiltroMilestoneId()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        $response->assertSuccessful();
    }

    public function testIndexConFiltroMilestoneIdNegativo()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        session(['filtrar_milestone_actual' => $milestone->id]);

        $response = $this->get(route('results.index', ['milestone_id' => -1]));

        $response->assertSuccessful();
    }

    public function testIndexConSesionFiltrarMilestone()
    {
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        session(['filtrar_milestone_actual' => $milestone->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testIndexConAjusteMedia()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create(['ajuste_proporcional_nota' => 'media']);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testIndexConAjusteMediana()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create(['ajuste_proporcional_nota' => 'mediana']);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testIndexConNormalizarNota()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create(['normalizar_nota' => true]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }
}
