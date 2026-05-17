<?php

namespace Tests\Feature\Evaluacion;

use Override;
use App\Models\Curso;
use App\Models\Qualification;
use App\Models\Skill;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QualificationsExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testGetFullNameSinCurso()
    {
        $qualification = Qualification::factory()->create(['name' => 'Mi rúbrica']);

        // Access without eager-loading curso
        $q = Qualification::find($qualification->id);

        $this->assertEquals('Mi rúbrica', $q->full_name);
    }

    public function testDuplicarConCursoDestino()
    {
        $curso_destino = Curso::factory()->create();
        $qualification = Qualification::factory()->create(['name' => 'Original']);

        $clon = $qualification->duplicar($curso_destino);

        $this->assertEquals('Original', $clon->name);
        $this->assertEquals($curso_destino->id, $clon->curso_id);
    }

    public function testDuplicarConCursoDestinoYSkills()
    {
        $curso_destino = Curso::factory()->create();
        $qualification = Qualification::factory()->create();
        $skill = Skill::factory()->create(['curso_id' => $qualification->curso_id]);
        $qualification->skills()->attach($skill, ['percentage' => 100, 'orden' => 1]);

        $clon = $qualification->duplicar($curso_destino);

        // Skills on the clone should have been moved to the destination curso
        $this->assertEquals($curso_destino->id, $clon->skills->first()->curso_id);
    }
}
