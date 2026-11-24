<?php

namespace Tests\Feature\Alumno;

use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
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

    public function testIndexConMilestoneAjusteFijo()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
        ]);
        session(['filtrar_milestone_actual' => $milestone->id]);

        // Unidad para que se renderice el bloque de completadas
        Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful()->assertSee(__('Number of mandatory activities'));
    }

    public function testIndexConNormalizarNota()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create(['normalizar_nota' => true]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful();
    }

    public function testIndexOcultarGlobalesSinEvaluacionesPublicadas()
    {
        $this->actingAs($this->alumno);

        // Given - curso que oculta los resultados globales y sin evaluaciones published
        $curso = Curso::factory()->create(['ocultar_resultados_globales' => true]);
        Milestone::factory()->create(['curso_id' => $curso->id, 'published' => false]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertNotFound();
    }

    public function testIndexOcultarGlobalesSinMostrarCalificaciones()
    {
        $this->actingAs($this->alumno);

        // Given - curso que oculta los resultados globales y no muestra calificaciones
        $curso = Curso::factory()->create([
            'mostrar_calificaciones' => false,
            'ocultar_resultados_globales' => true,
        ]);
        Milestone::factory()->create(['curso_id' => $curso->id]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertNotFound();
    }

    public function testIndexOcultarGlobalesAutoSeleccionaEvaluacion()
    {
        $this->actingAs($this->alumno);

        // Given - curso que oculta los resultados globales con 2 evaluaciones published
        $curso = Curso::factory()->create(['ocultar_resultados_globales' => true]);
        Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => Carbon::now()->subMonths(2)->second(0),
        ]);
        $reciente = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => Carbon::now()->subMonth()->second(0),
        ]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        // Then - auto-selecciona la más reciente y no ofrece "whole course"
        $response->assertSuccessful();
        $this->assertEquals($reciente->id, session('filtrar_milestone_actual'));
        $response->assertDontSee(__('--- None (whole course) ---'));
    }

    public function testIndexOcultarGlobalesConEvaluacionSeleccionada()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create(['ocultar_resultados_globales' => true]);
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        $response->assertSuccessful();
    }

    public function testPdfOcultarGlobalesSinEvaluacionesPublicadas()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create(['ocultar_resultados_globales' => true]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.pdf'));

        $response->assertNotFound();
    }

    public function testIndexAsProfesorOcultarGlobales()
    {
        // Given - los docentes no se ven afectados: ven el curso completo
        $this->actingAs($this->profesor);

        $curso = Curso::factory()->create(['ocultar_resultados_globales' => true]);
        Milestone::factory()->create(['curso_id' => $curso->id]);
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('results.index'));

        $response->assertSuccessful()->assertSee(__('--- None (whole course) ---'));
    }
}
