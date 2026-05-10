<?php

namespace Tests\Unit;

use App\Http\Controllers\ProfesorController;
use App\Livewire\TarjetaIntellij;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FlashDeck;
use App\Models\IntellijProject;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RemainingMethodsCoverageTest extends TestCase
{
    use DatabaseTransactions;

    public function testProfesorJplagDownloadReturnsResponse()
    {
        $controller = new ProfesorController();
        $tarea = Tarea::factory()->create();

        $result = $controller->jplag_download($tarea);

        $this->assertNotNull($result);
    }

    public function testFlashDeckPivoteThrowsErrorWithNoMatchingRubric()
    {
        $flashDeck = FlashDeck::factory()->create();
        $actividad = Actividad::factory()->create();

        $this->expectException(\ErrorException::class);
        $flashDeck->pivote($actividad);
    }

    public function testJplagRunnerMethodRunsWithFakedProcess()
    {
        Process::fake([
            '*' => Process::result(output: 'ok', exitCode: 0),
        ]);
        Storage::fake('temp');

        $curso = Curso::factory()->create();
        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);
        $plantilla = Actividad::factory()->create(['unidad_id' => $unidad->id]);
        $actividad = Actividad::factory()->create([
            'unidad_id' => $unidad->id,
            'plantilla_id' => $plantilla->id,
        ]);
        $user = User::factory()->create();
        $tarea = Tarea::factory()->create([
            'actividad_id' => $actividad->id,
            'user_id' => $user->id,
        ]);

        $project = IntellijProject::factory()->create([
            'curso_id' => $curso->id,
            'open_with' => 'idea',
        ]);
        $actividad->intellij_projects()->attach($project);

        $directorio = '/jplag';
        Storage::disk('temp')->makeDirectory($directorio);
        $ruta = Storage::disk('temp')->path($directorio);

        $controller = new ProfesorController();
        $controller->run_jplag($tarea, $ruta, $directorio);

        Process::assertRan(fn($process) => str_contains($process->command, 'java -jar /opt/jplag.jar'));
    }

    public function testTarjetaIntellijForkWhenAlreadyForking()
    {
        $curso = Curso::factory()->create();
        $actividad = Actividad::factory()->create([
            'unidad_id' => Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);
        $project = IntellijProject::factory()->create(['curso_id' => $curso->id]);
        $actividad->intellij_projects()->attach($project, [
            'fork_status' => 1,
            'fork' => 'owner/repo',
        ]);
        $attached = $actividad->intellij_projects()->find($project->id);

        $component = new TarjetaIntellij();
        $component->actividad = $actividad;
        $component->intellij_project = $attached;
        $component->fork_status = 1;
        $component->fork();

        $this->assertSame(1, $component->fork_status);
    }

    public function testTarjetaIntellijForkedUpdatesState()
    {
        $curso = Curso::factory()->create();
        $actividad = Actividad::factory()->create([
            'unidad_id' => Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);
        $project = IntellijProject::factory()->create(['curso_id' => $curso->id]);
        $actividad->intellij_projects()->attach($project, [
            'fork_status' => 2,
            'fork' => 'owner/repo',
        ]);

        $component = new class extends TarjetaIntellij {
            public function dispatch($event, ...$params)
            {
                return null;
            }
        };
        $component->actividad = $actividad;
        $component->intellij_project = $actividad->intellij_projects()->find($project->id);
        $component->forked(['intellij_project' => ['id' => $project->id]]);

        $this->assertSame(2, $component->fork_status);
    }
}
