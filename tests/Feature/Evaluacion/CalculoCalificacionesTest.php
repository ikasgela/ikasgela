<?php

namespace Tests\Feature\Evaluacion;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Qualification;
use App\Models\ResultadoCalificaciones;
use App\Models\Skill;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

/**
 * Suite de regresión del cálculo de la nota (User::calcular_calificaciones).
 *
 * Valida las reglas del spec 03-features/evaluation-grading.md (R-EVAL-3..14):
 * - robustez (sin crasheos): skills fuera de la cualificación del curso,
 *   cualificaciones en colgante, curso del milestone en colgante;
 * - normalización (PHP 8.5: 0.0/0.0 lanza DivisionByZeroError);
 * - denominador de la nota ponderada (Σ percentage de todas las skills);
 * - nota_publicar topada a 10 y nota_final con el valor real (puede superar 10);
 * - clave de caché con la referencia del grupo (media/mediana);
 * - mínimos, exámenes finales, nota manual, filtro por fecha de milestone.
 */
class CalculoCalificacionesTest extends TestCase
{
    use DatabaseTransactions;

    private Curso $curso;
    private Unidad $unidad;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->actingAs($this->alumno);

        $this->curso = Curso::factory()->create([
            'fecha_inicio' => now()->subDays(30),
            'minimo_entregadas' => 100,
            'minimo_competencias' => 0,
            'minimo_examenes' => null,
            'minimo_examenes_finales' => null,
            'maximo_recuperable_examenes_finales' => null,
            'examenes_obligatorios' => false,
            'normalizar_nota' => false,
            'ajuste_proporcional_nota' => null,
        ]);

        setting_usuario(['curso_actual' => $this->curso->id]);
        $this->curso->users()->attach($this->alumno->id);

        $this->unidad = Unidad::factory()->create([
            'curso_id' => $this->curso->id,
            'visible' => true,
            'tags' => '',
        ]);
    }

    private function crearSkill(float $pesoExamen = 0): Skill
    {
        return Skill::factory()->create([
            'curso_id' => $this->curso->id,
            'peso_examen' => $pesoExamen,
        ]);
    }

    private function crearQualification(array $skillsPorcentaje): Qualification
    {
        $q = Qualification::factory()->create(['curso_id' => $this->curso->id]);

        $q->skills()->sync(
            collect($skillsPorcentaje)->mapWithKeys(fn($pct, $id) => [
                $id => ['percentage' => $pct, 'orden' => Str::uuid()],
            ])
        );

        return $q;
    }

    private function asignarQualificationCursos(Qualification $q): void
    {
        $this->curso->qualification_id = $q->id;
        $this->curso->save();
    }

    private function crearActividad(
        string        $tags,
        int           $max,
        ?int          $puntuacion,
        bool          $plantilla        = false,
        int           $estado           = 40,
        ?Qualification $qualification   = null,
        float         $multiplicador    = 1,
        $fechaFinalizacion = null,
        ?Unidad       $unidad           = null,
    ): Actividad {
        $actividad = Actividad::factory()->create([
            'unidad_id' => $unidad?->id ?? $this->unidad->id,
            'tags' => $tags,
            'plantilla' => $plantilla,
            'puntuacion' => $max,
            'multiplicador' => $multiplicador,
            'fecha_finalizacion' => $fechaFinalizacion,
            'qualification_id' => $qualification?->id,
        ]);

        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => $estado,
            'puntuacion' => $puntuacion,
        ]);

        return $actividad;
    }

    // ------------------------------------------------------------------
    // 1. DENOMINADOR DE LA NOTA PONDERRADA (spec R-EVAL-8)
    // ------------------------------------------------------------------

    /**
     * El denominador es el Σ percentage de TODAS las skills del DTO.
     * El alumno completa 1 actividad (100/100) cuya cualificación propia solo
     * cubre S1 (60% del curso): S2 (40%) queda en 0.
     *
     * → nota = 10·(100·60/100 + 0·40/100) / (60+40) = 6.0
     */
    public function testDenominadorTodasLasSkills(): void
    {
        $s1 = $this->crearSkill(0);
        $s2 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 60, $s2->id => 40]);
        $this->asignarQualificationCursos($qc);

        $qa = $this->crearQualification([$s1->id => 100]);
        $this->crearActividad('base', 100, 100, qualification: $qa);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertEqualsWithDelta(6.0, $calificaciones->nota_numerica, 0.001,
            'Spec R-EVAL-8: el denominador incluye las skills sin actividad.');
    }

    /**
     * Skill que solo existe en la cualificación propia de la actividad:
     * se incorpora al DTO sin crasar (spec R-EVAL-7) y sus puntos entran en
     * la nota con el percentage de la cualificación que la aporta.
     *
     * → S3 (100% en Qa, pc 100) + S1 (100% del curso, pc 0):
     *   nota = 10·(0·100/100 + 100·100/100) / 200 = 5.0
     */
    public function testSkillFueraDeLaCualificacionDelCursoNoCrasha(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $s3 = $this->crearSkill(0);
        $qa = $this->crearQualification([$s3->id => 100]);
        $this->crearActividad('base', 100, 100, qualification: $qa);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertEqualsWithDelta(5.0, $calificaciones->nota_numerica, 0.001,
            'La skill S3 se incorpora (100% en Qa) y el denominador = 200.');
        $this->assertArrayHasKey($s3->id, $calificaciones->resultados);
    }

    /**
     * Mismo skill en las dos cualificaciones con porcentajes distintos:
     * los puntos se reparten con el % de la cualificación resuelta (100%),
     * pero el peso en la nota es el % de la cualificación del curso (60%).
     *
     * → pc(S1) = 100; pc(S2) = 0 → nota = 10·(100·60/100 + 0·40/100)/100 = 6.0
     */
    public function testPesoPorcentajeCualificacionActividad(): void
    {
        $s1 = $this->crearSkill(0);
        $s2 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 60, $s2->id => 40]);
        $this->asignarQualificationCursos($qc);

        $qa = $this->crearQualification([$s1->id => 100]);
        $this->crearActividad('base', 100, 100, qualification: $qa);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertEqualsWithDelta(6.0, $calificaciones->nota_numerica, 0.001);
    }

    /**
     * Una actividad cuya cualificación fue borrada (anomalía de datos) deja
     * qualification_id en colgante: se omite sin crasar.
     */
    public function testCualificacionBorradaEnActividadNoCrasha(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $qa = Qualification::factory()->create(['curso_id' => $this->curso->id]);
        $this->crearActividad('base', 100, 100, qualification: $qa);

        Schema::disableForeignKeyConstraints();
        $qa->delete();  // hard delete → qualification_id en colgante
        Schema::enableForeignKeyConstraints();
        $this->alumno->clearCache();

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertNotNull($calificaciones,
            'Cualificación huérfana: la actividad se omite (los puntos se pierden).');
    }

    // ------------------------------------------------------------------
    // 2. PUBLICACIÓN (spec R-EVAL-14)
    // ------------------------------------------------------------------

    /**
     * Ajuste propio 3/2 → nota 15. nota_final muestra el valor real calculado
     * (sin topado a 10) y nota_publicar topa a 10.
     */
    public function testNotaSobre10FinalRealYPublicarTopada(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        // 2 plantillas base (denominador) y 3 completadas (numerador)
        $this->crearActividad('base', 100, null, plantilla: true, estado: 10);
        $this->crearActividad('base', 100, null, plantilla: true, estado: 10);
        $this->crearActividad('base', 100, 100);
        $this->crearActividad('base', 100, 100);
        $this->crearActividad('base', 100, 100);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertEqualsWithDelta(15.0, $calificaciones->nota_numerica, 0.001);

        $milestone = Milestone::factory()->create(['curso_id' => $this->curso->id]);

        $this->assertSame('15.00', $calificaciones->nota_final(),
            'Spec R-EVAL-14: nota_final muestra la nota real calculada (sin topado a 10).');
        $this->assertSame('10.00', $calificaciones->nota_publicar($milestone),
            'Spec R-EVAL-14: nota_publicar = min(nota, 10).');
    }

    // ------------------------------------------------------------------
    // 3. NORMALIZACIÓN (spec R-EVAL-13)
    // ------------------------------------------------------------------

    /**
     * min == max → no se normaliza (en PHP 8.5, 0.0/0.0 lanza
     * DivisionByZeroError; el guard debe ser máx > mín).
     */
    public function testNormalizacionMinIgualMaxNoCrasha(): void
    {
        $rc = new ResultadoCalificaciones();
        $rc->nota_numerica = 5.0;

        $this->assertSame('5.00', $rc->nota_publicar(null, ['min' => 5.0, 'max' => 5.0]));
        $this->assertSame('5.00', $rc->nota_final(['min' => 5.0, 'max' => 5.0]));
    }

    /**
     * End-to-end: dos alumnos con la misma nota + normalizar → la página
     * de resultados debe servir 200 (antes: DivisionByZeroError → 500).
     */
    public function testNormalizacionEndToEndTodosIguales(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $otro = User::factory()->create();
        $otro->roles()->attach($this->alumno->roles()->first());
        $this->curso->users()->attach($otro->id);

        foreach ([$this->alumno, $otro] as $user) {
            $actividad = Actividad::factory()->create([
                'unidad_id' => $this->unidad->id,
                'tags' => 'base',
                'plantilla' => false,
                'puntuacion' => 100,
                'fecha_finalizacion' => now(),
            ]);
            Tarea::factory()->create([
                'user_id' => $user->id,
                'actividad_id' => $actividad->id,
                'estado' => 40,
                'puntuacion' => 50,
            ]);
        }

        $this->curso->normalizar_nota = true;
        $this->curso->save();
        $this->alumno->clearCache();
        $otro->clearCache();

        $this->actingAs($this->alumno);
        $response = $this->get(route('results.index'));
        $response->assertStatus(200);
        $this->assertStringNotContainsString('NaN', $response->getContent(),
            'La vista no debe publicar "NaN".');
    }

    // ------------------------------------------------------------------
    // 4. CACHÉ: la referencia del grupo (media/mediana) va en la clave
    // ------------------------------------------------------------------

    public function testCacheIncluyeLaReferenciaDelGrupo(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);
        $this->curso->ajuste_proporcional_nota = 'media';
        $this->curso->save();
        $this->alumno->clearCache();

        // 1 plantilla base → num_oblig = 1 (el brazo media/mediana se aplica)
        $this->crearActividad('base', 100, null, plantilla: true, estado: 10);
        $this->crearActividad('base', 100, 100);

        $r1 = $this->alumno->calcular_calificaciones(1, null);  // media = 1
        $r2 = $this->alumno->calcular_calificaciones(3, null);  // media = 3

        $this->assertSame(10.0, $r1->nota_numerica, 'con media 1 → 10·(1/1)');
        $this->assertEqualsWithDelta(10.0 * (1 / 3), $r2->nota_numerica, 0.001,
            'Con media 3 → 10·(1/3): la clave incluye la referencia y recalcula.');
    }

    // ------------------------------------------------------------------
    // 5. EXAMEN FINAL (spec R-EVAL-11)
    // ------------------------------------------------------------------

    /**
     * Con minimo_examenes_finales null, 0/100 en el final "supera"
     * (0 >= 0): comportamiento documentado (ver resumen de depuración).
     */
    public function testExamenFinalCeroPuntosMinimoNull(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->curso->update([
            'minimo_competencias' => 10,   // la continua falla (pc 0 < 10)
            'minimo_examenes_finales' => null,
        ]);
        $this->alumno->clearCache();

        $unidadFinal = Unidad::factory()->create([
            'curso_id' => $this->curso->id,
            'visible' => true,
            'tags' => 'examen, final',
        ]);

        $this->crearActividad('examen', 100, 0, unidad: $unidadFinal);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertFalse($calificaciones->evaluacion_continua_superada);
        $this->assertTrue($calificaciones->examen_final, 'La unidad examen+final activa la recuperación');
        $this->assertTrue($calificaciones->examen_final_superado,
            'Con mínimo null, 0 puntos supera (0 >= 0).');
    }

    /**
     * Spec E5: dos unidades final (78% y 90%), mínimo 60%, tope 80%.
     * → máx(7.8, 9.0) = 9.0 topado a 8.0; superada (78% ≥ 60%).
     */
    public function testExamenFinalMultiplesUnidadesConTope(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->curso->update([
            'minimo_competencias' => 90,   // pc 84% < 90 → continua no superada
            'minimo_examenes_finales' => 60,
            'maximo_recuperable_examenes_finales' => 80,
        ]);
        $this->alumno->clearCache();

        $u1 = Unidad::factory()->create(['curso_id' => $this->curso->id, 'visible' => true, 'tags' => 'examen, final']);
        $u2 = Unidad::factory()->create(['curso_id' => $this->curso->id, 'visible' => true, 'tags' => 'examen, final']);

        $this->crearActividad('examen', 100, 78, unidad: $u1);
        $this->crearActividad('examen', 100, 90, unidad: $u2);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertFalse($calificaciones->evaluacion_continua_superada);
        $this->assertEqualsWithDelta(8.0, $calificaciones->nota_numerica, 0.001,
            'máx(7.8, 9.0) = 9.0 topado por 8.0 → 8.0');
        $this->assertTrue($calificaciones->examen_final_superado);
        $this->assertEqualsWithDelta(8.0, (float) $calificaciones->nota_final(), 0.001);
    }

    // ------------------------------------------------------------------
    // 6. MÍNIMOS (spec R-EVAL-9/10)
    // ------------------------------------------------------------------

    public function testMinimosReales(): void
    {
        $s1 = $this->crearSkill(0);
        $s2 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 50, $s2->id => 50]);
        $this->asignarQualificationCursos($qc);

        $this->curso->update([
            'minimo_entregadas' => 100,
            'minimo_competencias' => 60,
            'examenes_obligatorios' => true,
            'minimo_examenes' => 70,
        ]);
        $this->alumno->clearCache();

        $this->crearActividad('base', 100, null, plantilla: true, estado: 10);
        $this->crearActividad('base', 100, null, plantilla: true, estado: 10);
        $this->crearActividad('base', 100, 40);

        $uExam = Unidad::factory()->create(['curso_id' => $this->curso->id, 'visible' => true, 'tags' => 'examen']);
        $this->crearActividad('examen', 100, 50, unidad: $uExam);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertFalse($calificaciones->actividades_obligatorias_superadas, '1 < 2·100% → no superadas');
        $this->assertFalse($calificaciones->competencias_50_porciento, '45% < 60% → no');
        $this->assertFalse($calificaciones->pruebas_evaluacion, '50% < 70% → no');
        $this->assertFalse($calificaciones->evaluacion_continua_superada);
    }

    public function testMinimosCon50PctEntregadas(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->curso->update(['minimo_entregadas' => 50, 'minimo_competencias' => null]);
        $this->alumno->clearCache();

        $this->crearActividad('base', 100, null, plantilla: true, estado: 10);
        $this->crearActividad('base', 100, 80, fechaFinalizacion: now());  // 1 de 1 → ratio 1/1

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertTrue($calificaciones->actividades_obligatorias_superadas);
        $this->assertEqualsWithDelta(8.0, $calificaciones->nota_numerica, 0.001, '80% · 1/1');
        $this->assertTrue($calificaciones->evaluacion_continua_superada,
            'nota 8 ≥ 5 y mínimos superados → continua superada');
    }

    // ------------------------------------------------------------------
    // 7. NOTA MANUAL (spec R-EVAL-12)
    // ------------------------------------------------------------------

    public function testNotaManualCursoSinMilestone(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->crearActividad('base', 100, 40);

        $this->alumno->cursos()->sync([$this->curso->id => ['nota' => 9]], false);
        $this->alumno->save();

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertSame(9.0, $calificaciones->nota_numerica);
        $this->assertTrue($calificaciones->hay_nota_manual);
        $this->assertTrue($calificaciones->nota_manual_superada);
    }

    public function testNotaManualMilestoneIgnoraLaDelCurso(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->crearActividad('base', 100, 40, fechaFinalizacion: now());

        $this->alumno->cursos()->sync([$this->curso->id => ['nota' => 9]], false);
        $this->alumno->save();

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'published' => true,
        ]);
        $this->alumno->clearCache();

        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertEqualsWithDelta(4.0, $calificaciones->nota_numerica, 0.001,
            'Con milestone activo, la nota manual del curso no se aplica.');
        $this->assertFalse($calificaciones->hay_nota_manual);
    }

    public function testNotaManualMilestone(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->crearActividad('base', 100, 40);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'published' => true,
        ]);

        $this->alumno->milestones()->sync([$milestone->id => ['nota' => 8]], false);
        $this->alumno->save();
        $this->alumno->clearCache();

        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertEqualsWithDelta(8.0, $calificaciones->nota_numerica, 0.001);
        $this->assertTrue($calificaciones->hay_nota_manual);
        $this->assertTrue($calificaciones->nota_manual_superada);
    }

    // ------------------------------------------------------------------
    // 8. MILESTONE: FILTRO POR FECHA
    // ------------------------------------------------------------------

    public function testMilestoneFiltroFechaFinalizacion(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now(),   // corte = ahora
            'published' => true,
        ]);

        // Dentro del rango
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDay());
        // Después del corte
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->addDay());
        // Null (nunca fechada)
        $this->crearActividad('base', 100, 100, fechaFinalizacion: null);

        $this->assertSame(1, $this->alumno->actividades_completadas($milestone)->count(),
            'Solo cuenta la actividad con fecha dentro de [curso.fecha_inicio, milestone.date].');
        $this->assertSame(3, $this->alumno->actividades_completadas(null)->count());
    }

    /**
     * Ventana con start_date y date: solo [start_date, date] (R-EVAL-6).
     */
    public function testMilestoneVentanaStartDateYDate(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->subDays(5),
            'start_date' => now()->subDays(10),
            'published' => true,
        ]);

        // Antes del inicio de la ventana (tras fecha_inicio del curso): no cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDays(15));
        // Dentro de la ventana: cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDays(7));
        // Tras el fin de la ventana: no cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDays(2));

        $this->assertSame(1, $this->alumno->actividades_completadas($milestone)->count(),
            'Solo cuenta las actividades finalizadas dentro de [start_date, date].');
    }

    /**
     * Milestone sin date: la ventana es [curso.fecha_inicio, ahora] (R-EVAL-6).
     */
    public function testMilestoneSinDateHastaAhora(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => null,
            'published' => true,
        ]);

        // Dentro de [fecha_inicio (hace 30 días), ahora]
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDay());
        // Antes del inicio del curso: no cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDays(60));

        $this->assertSame(1, $this->alumno->actividades_completadas($milestone)->count(),
            'Sin date, cuenta entre curso.fecha_inicio y ahora.');
    }

    /**
     * Milestone con start_date y sin date: la ventana es [start_date, ahora] (R-EVAL-6).
     */
    public function testMilestoneStartDateSinDate(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => null,
            'start_date' => now()->subDays(10),
            'published' => true,
        ]);

        // Antes del inicio de la ventana: no cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDays(15));
        // Tras el inicio (hasta ahora): cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->subDays(5));
        // En el futuro: no cuenta
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now()->addDay());

        $this->assertSame(1, $this->alumno->actividades_completadas($milestone)->count(),
            'Con start_date y sin date, cuenta entre start_date y ahora.');
    }

    /**
     * Milestone con curso_id en colgante (anomalía de datos): no crasa.
     */
    public function testMilestoneDelCursoBorradoNoCrasha(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->crearActividad('base', 100, 100);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now(),
        ]);

        Schema::disableForeignKeyConstraints();
        DB::table('milestones')->where('id', $milestone->id)->update(['curso_id' => 999999]);
        Schema::enableForeignKeyConstraints();
        $milestone->refresh();

        $this->alumno->clearCache();

        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertNotNull($calificaciones, 'Debe poder calcularse con el curso del milestone en colgante');
    }

    // ------------------------------------------------------------------
    // 9. MULTPLICADOR
    // ------------------------------------------------------------------

    /**
     * multiplicador 0.00 (string truthy "0.00") → la actividad vale 0 y se
     * excluye del cálculo (puntuacion_actividad <= 0).
     */
    public function testMultiplicadorCeroExcluyeLaActividad(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $this->crearActividad('base', 100, 50, multiplicador: 0);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertEqualsWithDelta(0.0, $calificaciones->nota_numerica, 0.001,
            'multiplicador 0 → actividad sin valor → se excluye.');
    }

    // ------------------------------------------------------------------
    // 10. PESO DE EXAMEN (spec E1)
    // ------------------------------------------------------------------

    public function testPesoExamenSpecE1(): void
    {
        $s1 = $this->crearSkill(0);      // 60%, sin peso examen
        $s2 = $this->crearSkill(100);    // 40%, todo examen
        $qc = $this->crearQualification([$s1->id => 60, $s2->id => 40]);
        $this->asignarQualificationCursos($qc);

        $this->crearActividad('base', 100, 60, fechaFinalizacion: now());
        $this->crearActividad('examen', 100, 80, fechaFinalizacion: now());

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        // pc(S1) = 60, pc(S2) = 80 → nota = 10·(60·60/100 + 80·40/100)/100 = 6.8
        $this->assertEqualsWithDelta(6.8, $calificaciones->nota_numerica, 0.001, 'Spec E1');
    }

    // ------------------------------------------------------------------
    // 11. CURSO SIN CALIFICACIÓN
    // ------------------------------------------------------------------

    public function testCursoSinQualification(): void
    {
        $this->crearActividad('base', 100, 100, fechaFinalizacion: now());

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        $this->assertEqualsWithDelta(0.0, $calificaciones->nota_numerica, 0.001);
    }

    // ------------------------------------------------------------------
    // 12. NO COMPUTABLE (−1)
    // ------------------------------------------------------------------

    public function testNoComputableSinExamenesFinales(): void
    {
        $s1 = $this->crearSkill(0);
        $qc = $this->crearQualification([$s1->id => 100]);
        $this->asignarQualificationCursos($qc);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'published' => true,
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 0,   // → -1
        ]);

        $this->crearActividad('base', 100, 100, fechaFinalizacion: now());

        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertSame(-1.0, $calificaciones->nota_numerica);
        $this->assertFalse($calificaciones->evaluacion_continua_superada);
        $this->assertFalse($calificaciones->examen_final, 'Sin unidades final, la nota queda en -1');
        $this->assertSame('-1.00', $calificaciones->nota_final());
    }
}
