<?php

namespace Tests\Feature\Evaluacion;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Qualification;
use App\Models\Skill;
use App\Models\Tarea;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

/**
 * Cálculo de la nota de evaluación con ajuste proporcional 'fijo' por
 * milestone (y regresión de los ajustes propios/media/etiquetas).
 */
class MilestoneAjusteFijoTest extends TestCase
{
    use DatabaseTransactions;

    private Curso $curso;
    private Milestone $milestone;
    private Unidad $unidad;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->actingAs($this->alumno);

        $this->curso = Curso::factory()->create([
            'fecha_inicio' => now()->subDays(30),
        ]);

        $qualification = Qualification::factory()->create(['curso_id' => $this->curso->id]);
        $skill = Skill::factory()->create(['curso_id' => $this->curso->id, 'peso_examen' => 0]);
        $qualification->skills()->attach($skill->id, [
            'percentage' => 100,
            'orden' => Str::uuid(),
        ]);
        $this->curso->qualification_id = $qualification->id;
        $this->curso->save();

        setting_usuario(['curso_actual' => $this->curso->id]);

        // Matricular al alumno en el curso (para los recuentos de grupo)
        $this->curso->users()->attach($this->alumno->id);

        $this->unidad = Unidad::factory()->create(['curso_id' => $this->curso->id]);

        $this->milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'published' => true,
        ]);
    }

    private function crearActividadCompletada(string $etiquetas, int $obtenida, bool $plantilla = false, int $estado = 40): void
    {
        $actividad = Actividad::factory()->create([
            'unidad_id' => $this->unidad->id,
            'tags' => $etiquetas,
            'plantilla' => $plantilla,
            'puntuacion' => 100,
            'fecha_finalizacion' => now(),
        ]);

        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => $estado,
            'puntuacion' => $obtenida,
        ]);
    }

    // E9 — Ajuste fijo: solo cuentan las 'base' y se divide entre el valor fijo

    public function testFijoCuentaSoloLasEtiquetasDelMilestoneYDivideEntreElFijo()
    {
        // Given: 2 base (60/100) + 1 extra (0/100) hasta la fecha del milestone
        $this->crearActividadCompletada('base', 60);
        $this->crearActividadCompletada('base', 60);
        $this->crearActividadCompletada('extra', 0);

        $this->milestone->update([
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
        ]);

        // When
        $calificaciones = $this->alumno->calcular_calificaciones(0, $this->milestone);

        // Then: completadas = 2 (solo las 'base') y nota = 6.0 * 2/5 = 2.4
        $this->assertEquals(2, $calificaciones->numero_actividades_completadas);
        $this->assertEqualsWithDelta(2.4, $calificaciones->nota_numerica, 0.001);
    }

    // E9 — E9 con varias etiquetas: cualquiera de ellas cuenta

    public function testFijoConVariasEtiquetasCuentaCualquieraDeEllas()
    {
        // Given: 2 base (60/100) + 1 extra (0/100)
        $this->crearActividadCompletada('base', 60);
        $this->crearActividadCompletada('base', 60);
        $this->crearActividadCompletada('extra', 0);

        $this->milestone->update([
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 5,
            'etiquetas' => 'base,extra',
        ]);

        // When
        $calificaciones = $this->alumno->calcular_calificaciones(0, $this->milestone);

        // Then: completadas = 3 (base o extra) y nota = 6.0 * 3/5 = 3.6
        $this->assertEquals(3, $calificaciones->numero_actividades_completadas);
        $this->assertEqualsWithDelta(3.6, $calificaciones->nota_numerica, 0.001);
    }

    // E9 — Fijo con 0/null actividades: no computable (-1)

    public function testFijoSinNumeroFijoNoComputable()
    {
        $this->crearActividadCompletada('base', 60);

        $this->milestone->update([
            'ajuste_proporcional_nota' => 'fijo',
            'num_actividades_obligatorias' => 0,
        ]);

        $calificaciones = $this->alumno->calcular_calificaciones(0, $this->milestone);

        $this->assertEquals(-1, $calificaciones->nota_numerica);
    }

    // Regresión: ajuste propio (default) — el denominador sigue siendo el de las plantillas base

    public function testAjustePropioSinMilestone()
    {
        // Given: 2 plantillas base; 1 completada (60/100) y 1 solo enviada
        $this->crearActividadCompletada('base', 60, true);
        $this->crearActividadCompletada('base', 100, true, 30);

        $calificaciones = $this->alumno->calcular_calificaciones(0, null);

        // 1 de 2 base completadas → nota ponderada 6.0 * 1/2 = 3.0
        $this->assertEquals(1, $calificaciones->numero_actividades_completadas);
        $this->assertEquals(2, $calificaciones->num_actividades_obligatorias);
        $this->assertEqualsWithDelta(3.0, $calificaciones->nota_numerica, 0.001);
    }

    // Regresión: con milestone de ajuste null (hereda el curso, también null) → ajuste propio

    public function testMilestoneSinAjusteMantieneElAjustePropio()
    {
        $this->crearActividadCompletada('base', 60, true);
        $this->crearActividadCompletada('base', 100, true, 30);

        $calificaciones = $this->alumno->calcular_calificaciones(0, $this->milestone);

        $this->assertEquals(1, $calificaciones->numero_actividades_completadas);
        $this->assertEquals(2, $calificaciones->num_actividades_obligatorias);
        $this->assertEqualsWithDelta(3.0, $calificaciones->nota_numerica, 0.001);
    }

    // Media del grupo con milestone de etiquetas propias: la referencia usa las mismas etiquetas

    public function testMediaConMilestoneEtiquetasPropiasUsaLasMismasEtiquetas()
    {
        // Given: 2 base (60/100) + 1 extra (0/100)
        $this->crearActividadCompletada('base', 60);
        $this->crearActividadCompletada('base', 60);
        $this->crearActividadCompletada('extra', 0);

        $this->milestone->update([
            'ajuste_proporcional_nota' => 'media',
            'etiquetas' => 'base,extra',
        ]);

        $media = $this->curso->media($this->milestone);

        // El alumno es el único activo: media = 3 (base o extra)
        $this->assertEquals(3, $media);

        $calificaciones = $this->alumno->calcular_calificaciones($media, $this->milestone);

        // 3 completadas / media 3 → 100% de la nota ponderada = 6.0
        $this->assertEquals(3, $calificaciones->numero_actividades_completadas);
        $this->assertEqualsWithDelta(6.0, $calificaciones->nota_numerica, 0.001);
    }
}
