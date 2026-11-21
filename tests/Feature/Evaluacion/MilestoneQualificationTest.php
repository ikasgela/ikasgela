<?php

namespace Tests\Feature\Evaluacion;

use App\Livewire\MilestoneForm;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Qualification;
use App\Models\Skill;
use App\Models\Tarea;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Override;
use Tests\TestCase;

/**
 * Cualificación propia del milestone (R-EVAL-20): una evaluación parcial
 * pondera solo las competencias de su cualificación, no las del curso.
 */
class MilestoneQualificationTest extends TestCase
{
    use DatabaseTransactions;

    private Curso $curso;
    private Unidad $unidad;
    private ?array $cualif = null;

    /**
     * 8 skills del curso: 4 "del parcial" (las primeras) y 4 "de la segunda mitad".
     * Idempotente: devuelve siempre las mismas instancias.
     *
     * @return array{qc: Qualification, parcial: Qualification, skills: Skill[]}
     */
    private function crearCualificaciones(): array
    {
        return $this->cualif ??= (function () {
            $skills = [];
            for ($i = 0; $i < 8; $i++) {
                $skills[] = Skill::factory()->create([
                    'curso_id' => $this->curso->id,
                    'peso_examen' => 0,
                    'name' => "Skill {$i}",
                ]);
            }

            // Cualificación del curso: las 8 al 100% (Σ 800)
            $qc = Qualification::factory()->create(['curso_id' => $this->curso->id, 'name' => 'Cualificación curso']);
            foreach ($skills as $skill) {
                $qc->skills()->attach($skill->id, ['percentage' => 100, 'orden' => Str::uuid()]);
            }
            $this->curso->qualification_id = $qc->id;
            $this->curso->save();

            // Cualificación parcial: solo las 4 primeras, al 100% (Σ 400)
            $parcial = Qualification::factory()->create(['curso_id' => $this->curso->id, 'name' => 'Parcial 1']);
            foreach (array_slice($skills, 0, 4) as $skill) {
                $parcial->skills()->attach($skill->id, ['percentage' => 100, 'orden' => Str::uuid()]);
            }

            return ['qc' => $qc, 'parcial' => $parcial, 'skills' => $skills];
        })();
    }

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->actingAs($this->alumno);

        $this->curso = Curso::factory()->create([
            'fecha_inicio' => now()->subDays(30),
            'minimo_entregadas' => 0,
            'minimo_competencias' => 0,
            'examenes_obligatorios' => false,
            'normalizar_nota' => false,
            'ajuste_proporcional_nota' => null,
        ]);

        $this->crearCualificaciones();

        setting_usuario(['curso_actual' => $this->curso->id]);
        $this->curso->users()->attach($this->alumno->id);

        $this->unidad = Unidad::factory()->create([
            'curso_id' => $this->curso->id,
            'visible' => true,
            'tags' => '',
        ]);
    }

    /**
     * Cada actividad resuelve a una cualificación propia con UNA sola skill,
     * de modo que sus puntos caen solo en esa skill.
     */
    private function crearActividadConSkill(Skill $skill, int $max, int $obtenida, string $etiqueta = 'base'): Actividad
    {
        $qualification = Qualification::factory()->create(['curso_id' => $this->curso->id]);
        $qualification->skills()->attach($skill->id, ['percentage' => 100, 'orden' => Str::uuid()]);

        $actividad = Actividad::factory()->create([
            'unidad_id' => $this->unidad->id,
            'tags' => $etiqueta,
            'plantilla' => false,
            'puntuacion' => $max,
            'multiplicador' => 1,
            'fecha_finalizacion' => now(),
            'qualification_id' => $qualification->id,
        ]);

        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => 40,
            'puntuacion' => $obtenida,
        ]);

        return $actividad;
    }

    // ------------------------------------------------------------------
    // E10 — La cualificación del milestone pondera en lugar de la del curso
    // ------------------------------------------------------------------

    /**
     * El alumno obtiene 88/100, 100/100, 100/100 y 100/100 en las 4 skills del
     * parcial y no tiene actividad en las otras 4 del curso:
     * - sin cualificación propia: 10·388/800 = 4.85 (las 4 skills vacías la diluyen);
     * - con la cualificación parcial: 10·388/400 = 9.70 (nota realista).
     */
    public function testMilestoneConCualificacionParcial(): void
    {
        $skills = $this->crearCualificaciones()['skills'];

        $this->crearActividadConSkill($skills[0], 100, 88);
        $this->crearActividadConSkill($skills[1], 100, 100);
        $this->crearActividadConSkill($skills[2], 100, 100);
        $this->crearActividadConSkill($skills[3], 100, 100);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'published' => true,
            'qualification_id' => null,
        ]);

        $this->alumno->clearCache();
        $sin = $this->alumno->calcular_calificaciones(0, $milestone);
        $this->assertEqualsWithDelta(4.85, $sin->nota_numerica, 0.01,
            'Sin cualificación propia se usa la del curso (8 skills, Σ800).');
        $this->assertCount(8, $sin->resultados);

        $milestone->qualification_id = $this->crearCualificaciones()['parcial']->id;
        $milestone->save();
        $this->alumno->clearCache();

        $con = $this->alumno->calcular_calificaciones(0, $milestone);
        $this->assertEqualsWithDelta(9.7, $con->nota_numerica, 0.01,
            'Con la cualificación parcial solo se ponderan sus 4 skills (Σ400).');
        $this->assertCount(4, $con->resultados);
        $this->assertCount(4, $con->skills_curso);
    }

    /**
     * Sin `qualification_id` se conserva el comportamiento histórico (cualificación
     * del curso).
     */
    public function testSinCualificacionMilestoneUsaLaDelCurso(): void
    {
        $skills = $this->crearCualificaciones()['skills'];

        $this->crearActividadConSkill($skills[0], 100, 88);
        $this->crearActividadConSkill($skills[1], 100, 100);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'qualification_id' => null,
        ]);

        $this->alumno->clearCache();
        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertCount(8, $calificaciones->resultados, 'El DTO se construye con las 8 skills del curso');
        // 10 · (88 + 100 + 0·6) / 800 = 2.35
        $this->assertEqualsWithDelta(2.35, $calificaciones->nota_numerica, 0.01);
    }

    /**
     * Con `qualification_id` en colgante (FK sin restricción dura en datos
     * legacy) se vuelve a la cualificación del curso sin crasar.
     */
    public function testCualificacionMilestoneEnColganteUsaLaDelCurso(): void
    {
        $skills = $this->crearCualificaciones()['skills'];

        $this->crearActividadConSkill($skills[0], 100, 100);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
        ]);

        Schema::disableForeignKeyConstraints();
        $milestone->qualification_id = 999999;
        $milestone->save();
        Schema::enableForeignKeyConstraints();

        $this->alumno->clearCache();
        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertNotNull($calificaciones);
        $this->assertCount(8, $calificaciones->resultados, 'Cualificación en colgante → la del curso');
    }

    /**
     * Una actividad cuya cualificación propia trae una skill **fuera** de la
     * cualificación del milestone se incorpora al DTO (R-EVAL-3/7) y cuenta.
     */
    public function testSkillFueraDeLaCualificacionDelMilestoneSeIncorpora(): void
    {
        $skills = $this->crearCualificaciones()['skills'];

        // 2 skills dentro del parcial + 1 fuera (la 5ª del curso)
        $this->crearActividadConSkill($skills[0], 100, 100);
        $this->crearActividadConSkill($skills[1], 100, 100);
        $this->crearActividadConSkill($skills[4], 100, 100);

        $parcial = $this->crearCualificaciones()['parcial'];
        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'qualification_id' => $parcial->id,
        ]);

        $this->alumno->clearCache();
        $calificaciones = $this->alumno->calcular_calificaciones(0, $milestone);

        $this->assertArrayHasKey($skills[4]->id, $calificaciones->resultados,
            'La skill fuera del parcial se incorpora al DTO con el percentage de su cualificación (100).');
        // 4 skills del parcial (100·2 + 0·2) + la incorporada (100) → Σ 500
        $this->assertEqualsWithDelta(10 * (100 + 100 + 100) / 500, $calificaciones->nota_numerica, 0.01);
    }

    // ------------------------------------------------------------------
    // Caché: al cambiar la cualificación del milestone se recalcula
    // ------------------------------------------------------------------

    public function testCambiaLaCualificacionDelMilestoneSePurgaLaCaché(): void
    {
        $skills = $this->crearCualificaciones()['skills'];
        $parcial = $this->crearCualificaciones()['parcial'];

        $this->crearActividadConSkill($skills[0], 100, 88);
        $this->crearActividadConSkill($skills[1], 100, 100);
        $this->crearActividadConSkill($skills[2], 100, 100);
        $this->crearActividadConSkill($skills[3], 100, 100);

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'date' => now()->addDays(30),
            'qualification_id' => $parcial->id,
        ]);

        $this->alumno->clearCache();

        $con = $this->alumno->calcular_calificaciones(0, $milestone);
        $this->assertEqualsWithDelta(9.7, $con->nota_numerica, 0.01);

        // El profesor quita la cualificación (sin tocar nombre/fecha): la key de
        // caché no cambia, pero el observer purga la caché del alumno.
        $milestone->qualification_id = null;
        $milestone->save();

        $sin = $this->alumno->calcular_calificaciones(0, $milestone);
        $this->assertEqualsWithDelta(4.85, $sin->nota_numerica, 0.01,
            'Tras quitar la cualificación se recalcula sobre la del curso.');
    }

    // ------------------------------------------------------------------
    // CRUD: formulario y controlador
    // ------------------------------------------------------------------

    public function testFormMilestoneGuardaLaCualificacion(): void
    {
        $this->actingAs($this->admin);

        $parcial = $this->crearCualificaciones()['parcial'];

        $milestone = Livewire::test(MilestoneForm::class, ['curso_id' => $this->curso->id])
            ->set('name', 'Primera evaluación')
            ->set('date', now()->addDays(30)->format('Y-m-d\TH:i:s'))
            ->set('qualification_id', (string) $parcial->id)
            ->call('save')
            ->assertHasNoErrors();

        $creado = Milestone::where('curso_id', $this->curso->id)->where('name', 'Primera evaluación')->first();
        $this->assertNotNull($creado);
        $this->assertEquals($parcial->id, $creado->qualification_id);
    }

    public function testFormMilestoneEditaLaCualificacion(): void
    {
        $this->actingAs($this->admin);

        $skills = $this->crearCualificaciones()['skills'];
        $parcial = $this->crearCualificaciones()['parcial'];

        $milestone = Milestone::factory()->create([
            'curso_id' => $this->curso->id,
            'name' => 'Primera evaluación',
            'date' => now()->addDays(30),
            'qualification_id' => $this->crearCualificaciones()['qc']->id,
        ]);

        Livewire::test(MilestoneForm::class, ['milestone' => $milestone])
            ->assertSet('qualification_id', (string) $milestone->qualification_id)
            ->set('qualification_id', (string) $parcial->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals($parcial->id, $milestone->fresh()->qualification_id);
    }

    public function testControladorStoreYUpdateAceptanCualificacion(): void
    {
        $this->actingAs($this->admin);

        $parcial = $this->crearCualificaciones()['parcial'];
        $qc = $this->crearCualificaciones()['qc'];

        $response = $this->post(route('milestones.store'), [
            'curso_id' => $this->curso->id,
            'name' => 'Primera evaluación',
            'date' => now()->addDays(30)->toDateTimeString(),
            'qualification_id' => $parcial->id,
        ]);
        $response->assertSessionHasNoErrors();

        $milestone = Milestone::where('curso_id', $this->curso->id)->where('name', 'Primera evaluación')->first();
        $this->assertEquals($parcial->id, $milestone->qualification_id);

        $response = $this->put(route('milestones.update', $milestone), [
            'curso_id' => $this->curso->id,
            'name' => 'Primera evaluación',
            'date' => now()->addDays(30)->toDateTimeString(),
            'qualification_id' => $qc->id,
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertEquals($qc->id, $milestone->fresh()->qualification_id);
    }

    public function testControladorValidaCualificacionInexistente(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('milestones.store'))->post(route('milestones.store'), [
            'curso_id' => $this->curso->id,
            'name' => 'Primera evaluación',
            'date' => now()->addDays(30)->toDateTimeString(),
            'qualification_id' => 999999,
        ]);

        $response->assertSessionHasErrors('qualification_id');
        $this->assertNull(Milestone::where('name', 'Primera evaluación')->first());
    }
}
