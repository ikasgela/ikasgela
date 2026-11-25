<?php

namespace Tests\Feature\Alumno;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Tarea;
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

    /**
     * Curso con 10 obligatorias (5 plantillas 'base' por unidad, 2 unidades) y el
     * alumno con 3 de ellas finalizadas dentro de la ventana del milestone
     * (2 en la primera unidad y 1 en la segunda; no superado: 3 < 10).
     *
     * @return array{0: Curso, 1: Unidad}
     */
    private function cursoConObligatorias(): array
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create([
            'minimo_entregadas' => 100,
            'fecha_inicio' => now()->subDays(30),
        ]);
        setting_usuario(['curso_actual' => $curso->id]);
        $curso->users()->attach($this->alumno->id);

        $unidades = collect([
            Unidad::factory()->create(['curso_id' => $curso->id]),
            Unidad::factory()->create(['curso_id' => $curso->id]),
        ]);

        for ($i = 0; $i < 10; $i++) {
            $actividad = Actividad::factory()->create([
                'unidad_id' => $unidades[$i < 5 ? 0 : 1]->id,
                'tags' => 'base',
                'plantilla' => true,
                'puntuacion' => 100,
            ]);

            if ($i < 3) {
                $actividad->update(['fecha_finalizacion' => now()]);
                Tarea::factory()->create([
                    'user_id' => $this->alumno->id,
                    'actividad_id' => $actividad->id,
                    'estado' => 40,
                    'puntuacion' => 100,
                ]);
            }
        }

        return [$curso, $unidades[0]];
    }

    // R-EVAL-21 — con milestone de ajuste 'fijo', el divisor es el del milestone

    public function testIndexConMilestoneFijoDivisorDelMilestone()
    {
        [$curso] = $this->cursoConObligatorias();

        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => now()->addDays(30),
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
        ]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        $response->assertSuccessful()
            ->assertSee('3/5')
            ->assertDontSee('3/10');
    }

    // R-EVAL-21 — con milestone de ajuste 'media', el divisor es la media del grupo

    public function testIndexConMilestoneMediaDivisorMediaGrupo()
    {
        [$curso] = $this->cursoConObligatorias();

        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => now()->addDays(30),
            'ajuste_proporcional_nota' => 'media',
        ]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        // El alumno es el único del grupo: media = 3 → divisor 3.00 (no 10 del curso)
        $response->assertSuccessful()
            ->assertSee('3/3.00')
            ->assertDontSee('3/10');
    }

    // R-EVAL-21 — sin milestone, el divisor sigue siendo el de todo el curso

    public function testIndexSinMilestoneDivisorCursoCompleto()
    {
        $this->cursoConObligatorias();

        $response = $this->get(route('results.index'));

        $response->assertSuccessful()
            ->assertSee('3/10');
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
