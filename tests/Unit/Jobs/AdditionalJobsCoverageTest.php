<?php

namespace Tests\Unit\Jobs;

use App\Jobs\BorrarUsuario;
use App\Jobs\ForkGiteaRepo;
use App\Jobs\ImportCurso;
use App\Jobs\RunJPlag;
use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdditionalJobsCoverageTest extends TestCase
{
    use DatabaseTransactions;

    public function testRunJPlagConstructorAndHandle()
    {
        Storage::fake('temp');
        config(['ikasgela.jplag_delete_temp' => false]);
        $tarea = Tarea::factory()->create();

        $job = new RunJPlag($tarea);
        $this->assertInstanceOf(RunJPlag::class, $job);

        $stub = new class($tarea) extends RunJPlag {
            public bool $called = false;

            public function run_jplag(Tarea $tarea, string $ruta, string $directorio): void
            {
                $this->called = true;
            }
        };

        $stub->handle();
        $this->assertTrue($stub->called);
    }

    public function testForkGiteaRepoUniqueId()
    {
        $actividad = Actividad::factory()->create();
        $project = IntellijProject::factory()->create(['curso_id' => $actividad->unidad->curso_id]);
        $user = User::factory()->create();

        $job = new ForkGiteaRepo($actividad, $project, $user);

        $this->assertSame("{$user->id}-{$actividad->id}-{$project->id}", $job->uniqueId());
    }

    public function testBorrarUsuarioHandleWithGiteaDisabled()
    {
        config(['ikasgela.gitea_enabled' => false]);
        $user = User::factory()->create();

        $job = new BorrarUsuario($user);
        $job->handle();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testImportCursoPrivateHelpersViaReflection()
    {
        $job = new ImportCurso('1', 'unused', 'unused');
        $table = 'tmp_import_test';

        if (Schema::hasTable($table)) {
            Schema::drop($table);
        }

        Schema::create($table, function ($t) {
            $t->id();
            $t->string('name')->nullable();
        });

        $addImportId = new \ReflectionMethod($job, 'addImportId');
        $addImportId->setAccessible(true);
        $addImportId->invoke($job, $table);
        $this->assertTrue(Schema::hasColumn($table, '__import_id'));

        $removeImportId = new \ReflectionMethod($job, 'removeImportId');
        $removeImportId->setAccessible(true);
        $removeImportId->invoke($job, $table);
        $this->assertFalse(Schema::hasColumn($table, '__import_id'));

        $replaceKeys = new \ReflectionMethod($job, 'replaceKeys');
        $replaceKeys->setAccessible(true);
        $data = ['id' => 1, 'nested' => ['id' => 2, 'name' => 'x']];
        $replaced = $replaceKeys->invoke($job, 'id', '__import_id', $data);
        $this->assertArrayHasKey('__import_id', $replaced);
        $this->assertArrayHasKey('__import_id', $replaced['nested']);

        $removeKey = new \ReflectionMethod($job, 'removeKey');
        $removeKey->setAccessible(true);
        $input = ['remove' => 1, 'nested' => ['remove' => 2, 'ok' => 3]];
        $args = [&$input, 'remove'];
        $removeKey->invokeArgs($job, $args);
        $this->assertArrayNotHasKey('remove', $input);
        $this->assertArrayNotHasKey('remove', $input['nested']);

        $tempDir = sys_get_temp_dir() . '/importcurso-' . bin2hex(random_bytes(4));
        mkdir($tempDir, 0777, true);
        file_put_contents($tempDir . '/sample.json', json_encode([
            'id' => 10,
            'created_at' => 'x',
            'nested' => ['id' => 11, 'updated_at' => 'y'],
        ]));

        $cargarFichero = new \ReflectionMethod($job, 'cargarFichero');
        $cargarFichero->setAccessible(true);
        $json = $cargarFichero->invoke($job, $tempDir, 'sample.json');
        $this->assertArrayHasKey('__import_id', $json);
        $this->assertArrayNotHasKey('created_at', $json);
        $this->assertArrayHasKey('__import_id', $json['nested']);

        $importarRepositorio = new \ReflectionMethod($job, 'importarRepositorio');
        $importarRepositorio->setAccessible(true);
        $result = $importarRepositorio->invoke($job, '/path/that/does/not/exist', '/repo', 'repo', 'org');
        $this->assertFalse($result);

        Schema::dropIfExists($table);
        @unlink($tempDir . '/sample.json');
        @rmdir($tempDir);
    }
}
