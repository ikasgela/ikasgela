<?php

namespace Tests\Unit;

use App\Http\Controllers\ProfesorController;
use App\Livewire\TarjetaIntellij;
use App\Models\Actividad;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Curso;
use App\Models\File;
use App\Models\FileResource;
use App\Models\FlashDeck;
use App\Models\IntellijProject;
use App\Models\Rubric;
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

    public function testRubricDuplicarConCursoDestino()
    {
        // Given
        $curso_destino = Curso::factory()->create();
        $rubric = Rubric::factory()->create();
        $cg = CriteriaGroup::factory()->create(['rubric_id' => $rubric->id]);
        Criteria::factory()->create(['criteria_group_id' => $cg->id]);

        // When
        $clon = $rubric->duplicar($curso_destino);

        // Then
        $this->assertNotEquals($rubric->id, $clon->id);
        $this->assertEquals($curso_destino->id, $clon->curso_id);
        $this->assertEquals(1, $clon->criteria_groups()->count());

        // UUIDs regenerados en grupos y criterios
        $cgClon = $clon->criteria_groups()->first();
        $this->assertNotEquals($cg->orden, $cgClon->orden);
        $this->assertNotEquals(
            $cg->criterias()->first()->orden,
            $cgClon->criterias()->first()->orden
        );
    }

    public function testFileResourceDuplicarConCursoDestino()
    {
        // Given
        Storage::fake('s3');

        $curso_destino = Curso::factory()->create();
        $file_resource = FileResource::factory()->create();
        $old_path = 'subdir/myfile.pdf';
        File::factory()->create([
            'uploadable_id' => $file_resource->id,
            'uploadable_type' => FileResource::class,
            'path' => $old_path,
        ]);
        Storage::disk('s3')->put('documents/' . $old_path, 'file content');

        // When
        $clon = $file_resource->duplicar($curso_destino);

        // Then
        $this->assertNotEquals($file_resource->id, $clon->id);
        $this->assertEquals($curso_destino->id, $clon->curso_id);

        $clonedFile = $clon->files()->first();
        $this->assertNotEquals($old_path, $clonedFile->path);
        Storage::disk('s3')->assertExists('documents/' . $clonedFile->path);
    }
}
