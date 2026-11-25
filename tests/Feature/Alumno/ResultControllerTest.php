<?php

namespace Tests\Feature\Alumno;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Role;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
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
     * Curso con 2 unidades de `plantillasPorUnidad` plantillas 'base', el alumno
     * matriculado con `completadas` de ellas (más `noPlantilla` actividades 'base'
     * no plantilla) finalizadas dentro de la ventana del milestone.
     *
     * @return array{0: Curso, 1: Collection}
     */
    private function cursoConObligatorias(int $plantillasPorUnidad = 5, int $completadas = 3, int $noPlantilla = 0): array
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create([
            'minimo_entregadas' => 100,
            'minimo_competencias' => 50,
            'fecha_inicio' => now()->subDays(30),
        ]);
        setting_usuario(['curso_actual' => $curso->id]);
        $curso->users()->attach($this->alumno->id);

        $unidades = collect([
            Unidad::factory()->create(['curso_id' => $curso->id]),
            Unidad::factory()->create(['curso_id' => $curso->id]),
        ]);

        $actividades = [];

        for ($i = 0; $i < $plantillasPorUnidad * 2; $i++) {
            $actividades[] = Actividad::factory()->create([
                'unidad_id' => $unidades[intdiv($i, $plantillasPorUnidad)]->id,
                'tags' => 'base',
                'plantilla' => true,
                'puntuacion' => 100,
            ]);
        }

        for ($i = 0; $i < $noPlantilla; $i++) {
            $actividades[] = Actividad::factory()->create([
                'unidad_id' => $unidades[0]->id,
                'tags' => 'base',
                'plantilla' => false,
                'puntuacion' => 100,
            ]);
        }

        foreach (array_slice($actividades, 0, $completadas + $noPlantilla) as $actividad) {
            $actividad->update(['fecha_finalizacion' => now()]);
            Tarea::factory()->create([
                'user_id' => $this->alumno->id,
                'actividad_id' => $actividad->id,
                'estado' => 40,
                'puntuacion' => 100,
            ]);
        }

        return [$curso, $unidades];
    }

    /**
     * Segundo alumno matriculado en el curso con `completadas` actividades 'base'
     * no plantilla finalizadas (para la media/mediana del grupo).
     */
    private function segundoAlumnoConCompletadas(Curso $curso, Collection $unidades, int $completadas): User
    {
        $alumno = User::factory()->create();
        $alumno->roles()->attach(Role::where('name', 'alumno')->first());
        $curso->users()->attach($alumno->id);

        for ($i = 0; $i < $completadas; $i++) {
            $actividad = Actividad::factory()->create([
                'unidad_id' => $unidades[$i % $unidades->count()]->id,
                'tags' => 'base',
                'plantilla' => false,
                'puntuacion' => 100,
                'fecha_finalizacion' => now(),
            ]);
            Tarea::factory()->create([
                'user_id' => $alumno->id,
                'actividad_id' => $actividad->id,
                'estado' => 40,
                'puntuacion' => 100,
            ]);
        }

        return $alumno;
    }

    /**
     * Bloque de tarjeta (fondo + cabecera, y dato opcional) con el contenido dado.
     * `$indent` son los espacios de sangría de la cabecera (16 dentro de @if, 12
     * para las tarjetas a nivel superior como «Continuous evaluation»).
     */
    private function tarjetaCon(string $fondo, string $header, ?string $dato = null, int $indent = 16): string
    {
        $s = str_repeat(' ', $indent);

        $html = '<div class="card mb-3 ' . $fondo . '">' . "\n"
            . $s . '<div class="card-header">' . $header . '</div>';

        if (!is_null($dato)) {
            $html .= "\n"
                . $s . '<div class="card-body text-center">' . "\n"
                . str_repeat(' ', $indent + 4) . '<p class="card-text" style="font-size:150%;">' . $dato . '</p>';
        }

        return $html;
    }

    // R-EVAL-21 — fijo por debajo del divisor: ámbar y 3/5 (divisor del milestone)

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

        // 3 < 5 → ámbar y 3/5 (el divisor del milestone, no el 10 del curso)
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-warning', 'Mandatory activities'), false)
            ->assertSee('3/5')
            ->assertDontSee('3/10');
    }

    // R-EVAL-21 — fijo superado (13 ≥ 12): verde y el recuento 13/12 (nunca
    // «completed» en un parcial)

    public function testIndexConMilestoneFijoSuperadoMuestraElRecuento()
    {
        // 12 plantillas (6 por unidad) + 2 no plantilla = 13 completadas
        [$curso] = $this->cursoConObligatorias(6, 11, 2);

        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => now()->addDays(30),
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 12,
        ]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        // 13 ≥ 12 → verde, aunque el criterio de curso por unidad no se cumpla
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-success', 'Mandatory activities'), false)
            ->assertSee('13/12')
            ->assertDontSee('font-size:150%;">Completed</p>', false);
    }

    // R-EVAL-21 — media por debajo de la media del grupo: ámbar y 3/4.00

    public function testIndexConMilestoneMediaDivisorMediaGrupo()
    {
        [$curso, $unidades] = $this->cursoConObligatorias();      // 3 completadas
        $this->segundoAlumnoConCompletadas($curso, $unidades, 5); // media = 4

        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => now()->addDays(30),
            'ajuste_proporcional_nota' => 'media',
        ]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        // 3 < 4 (media del grupo) → ámbar y 3/4.00 (no el 10 del curso)
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-warning', 'Mandatory activities'), false)
            ->assertSee('3/4.00')
            ->assertDontSee('3/10');
    }

    // R-EVAL-21 — media por encima de la media del grupo: verde y la proporción 5/4.00

    public function testIndexConMilestoneMediaSuperadoMuestraElRecuento()
    {
        [$curso, $unidades] = $this->cursoConObligatorias(5, 5);    // 5 completadas
        $this->segundoAlumnoConCompletadas($curso, $unidades, 3);   // media = 4

        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => now()->addDays(30),
            'ajuste_proporcional_nota' => 'media',
        ]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        // 5 ≥ 4 (media) → verde y la proporción aplicada (nunca «completed» en un parcial)
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-success', 'Mandatory activities'), false)
            ->assertSee('5/4.00')
            ->assertDontSee('font-size:150%;">Completed</p>', false);
    }

    // R-EVAL-21 — sin milestone: divisor y criterio de todo el curso

    public function testIndexSinMilestoneDivisorCursoCompleto()
    {
        $this->cursoConObligatorias();

        $response = $this->get(route('results.index'));

        // 3 < 10 (curso completo) → ámbar y 3/10
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-warning', 'Mandatory activities'), false)
            ->assertSee('3/10');
    }

    // R-EVAL-21 — sin milestone y superado: verde y «completed» (solo el curso
    // completo lo muestra)

    public function testIndexSinMilestoneSuperadoMuestraCompleted()
    {
        $this->cursoConObligatorias(5, 10); // todas las 10 plantillas finalizadas

        $response = $this->get(route('results.index'));

        // 10 ≥ 10 (curso completo) → verde y «completed»
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-success', 'Mandatory activities'), false)
            ->assertSee('font-size:150%;">Completed</p>', false)
            ->assertDontSee('10/10');
    }

    // R-EVAL-22 — con milestone, los bloques de curso completo (Skills y
    // Continuous evaluation) se grisan y muestran «No aplicable»

    public function testIndexConMilestoneBloquesNoAplicables()
    {
        [$curso] = $this->cursoConObligatorias();

        $milestone = Milestone::factory()->create([
            'curso_id' => $curso->id,
            'date' => now()->addDays(30),
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
        ]);

        $response = $this->get(route('results.index', ['milestone_id' => $milestone->id]));

        // Criterio de curso completo en un parcial: gris y «No aplicable»
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-light', 'Skills', 'Not applicable'), false)
            ->assertSee($this->tarjetaCon('text-bg-light', 'Continuous evaluation', 'Not applicable', 12), false);
    }

    // R-EVAL-22 — sin milestone, los bloques muestran su criterio normal

    public function testIndexSinMilestoneBloquesConCriterio()
    {
        $this->cursoConObligatorias();

        $response = $this->get(route('results.index'));

        // Sin cualificación: competencias vacías (superadas) y evaluación continua no
        // superada (3 < 10 y nota 0 < 5)
        $response->assertSuccessful()
            ->assertSee($this->tarjetaCon('text-bg-success', 'Skills', 'Passed'), false)
            ->assertSee($this->tarjetaCon('text-bg-warning', 'Continuous evaluation', 'Not passed', 12), false)
            ->assertDontSee('Not applicable');
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

