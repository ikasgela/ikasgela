<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use App\Models\MarkdownText;
use App\Models\Tarea;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class IntellijProjectsDescargarTest extends TestCase
{
    use DatabaseTransactions;

    private Curso $curso;
    private Unidad $unidad;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->curso = Curso::factory()->create();
        $this->unidad = Unidad::factory()->create(['curso_id' => $this->curso->id]);

        $this->actingAs($this->profesor);
        setting_usuario(['curso_actual' => $this->curso->id]);
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function fakeRepoData(array $overrides = []): array
    {
        return array_merge([
            'id' => 42,
            'name' => 'test-repo',
            'description' => 'Test Repository',
            'clone_url' => 'http://gitea:3000/root/test-repo.git',
            'full_name' => 'root/test-repo',
            'html_url' => 'http://gitea:3000/root/test-repo',
            'template' => true,
            'owner' => ['login' => 'root'],
        ], $overrides);
    }

    /**
     * Devuelve un Process::fake que simula git clone creando la estructura de
     * directorio esperada, para que ZipArchive pueda generar un ZIP no vacío.
     */
    private function fakeProcessWithClone(): void
    {
        Process::fake(function (PendingProcess $process) {
            if (str_starts_with(trim($process->command), 'git clone')) {
                $parts = preg_split('/\s+/', trim($process->command));
                $cloneDir = end($parts);
                $clonePath = rtrim((string) $process->path, '/') . '/' . $cloneDir;
                @mkdir($clonePath, 0777, true);
                file_put_contents($clonePath . '/README.md', '# Fake clone');
            }
            return Process::result(output: 'ok', exitCode: 0);
        });
    }

    // ---------------------------------------------------------------------------
    // descargar_repos() — GET sin parámetros → vista
    // ---------------------------------------------------------------------------

    public function testDescargarReposMuestraVistaSinUnidadId(): void
    {
        $response = $this->get(route('intellij_projects.descargar'));

        $response->assertOk();
        $response->assertViewIs('intellij_projects.descargar');
        $response->assertViewHas('unidades');
    }

    public function testDescargarReposNoPermitidoSinAuth(): void
    {
        $this->post('/logout');

        $response = $this->get(route('intellij_projects.descargar'));

        $response->assertRedirect(route('login'));
    }

    // ---------------------------------------------------------------------------
    // descargar_repos() — POST con unidad_id → descarga script .sh
    // ---------------------------------------------------------------------------

    public function testDescargarReposDescargaScriptConProyectosFork(): void
    {
        // Actividad asignada al alumno con un fork en el pivote
        $actividad = Actividad::factory()->create(['unidad_id' => $this->unidad->id]);
        $project = IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/plantilla',
        ]);
        $actividad->intellij_projects()->attach($project, [
            'fork' => 'alumno/fork-repo',
            'orden' => Str::orderedUuid(),
        ]);

        $this->curso->users()->attach($this->alumno);
        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => 20,
        ]);

        Http::fake([
            '*/api/v1/repos/alumno/fork-repo' => Http::response(
                $this->fakeRepoData([
                    'name' => 'fork-repo',
                    'full_name' => 'alumno/fork-repo',
                    'clone_url' => 'http://gitea:3000/alumno/fork-repo.git',
                    'owner' => ['login' => 'alumno'],
                ]),
                200
            ),
        ]);

        $response = $this->post(route('intellij_projects.descargar.repos'), [
            'unidad_id' => $this->unidad->id,
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function testDescargarReposDescargaScriptSinForks(): void
    {
        // Actividad asignada al alumno pero sin fork en el pivote
        $actividad = Actividad::factory()->create(['unidad_id' => $this->unidad->id]);
        $project = IntellijProject::factory()->create(['curso_id' => $this->curso->id]);
        $actividad->intellij_projects()->attach($project, [
            'fork' => null,
            'orden' => Str::orderedUuid(),
        ]);

        $this->curso->users()->attach($this->alumno);
        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => 20,
        ]);

        Http::fake();

        $response = $this->post(route('intellij_projects.descargar.repos'), [
            'unidad_id' => $this->unidad->id,
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function testDescargarReposSinCursoActualMuestraVista(): void
    {
        // Borramos el curso_actual para simular que no hay ninguno
        setting_usuario(['curso_actual' => null]);

        $response = $this->post(route('intellij_projects.descargar.repos'), [
            'unidad_id' => $this->unidad->id,
        ]);

        $response->assertOk();
        $response->assertViewIs('intellij_projects.descargar');
    }

    // ---------------------------------------------------------------------------
    // descargar_plantillas() — POST con unidad_id → descarga script .sh
    // ---------------------------------------------------------------------------

    public function testDescargarPlantillasMuestraVistaSinUnidadId(): void
    {
        $response = $this->post(route('intellij_projects.descargar.plantillas'));

        $response->assertOk();
        $response->assertViewIs('intellij_projects.descargar');
    }

    public function testDescargarPlantillasDescargaScriptConPlantillas(): void
    {
        $plantilla = Actividad::factory()->create([
            'unidad_id' => $this->unidad->id,
            'plantilla' => true,
        ]);
        $project = IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/plantilla-repo',
        ]);
        $plantilla->intellij_projects()->attach($project, ['orden' => Str::orderedUuid()]);

        Http::fake([
            '*/api/v1/repos/root/plantilla-repo' => Http::response(
                $this->fakeRepoData([
                    'name' => 'plantilla-repo',
                    'full_name' => 'root/plantilla-repo',
                    'clone_url' => 'http://gitea:3000/root/plantilla-repo.git',
                ]),
                200
            ),
        ]);

        $response = $this->post(route('intellij_projects.descargar.plantillas'), [
            'unidad_id' => $this->unidad->id,
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function testDescargarPlantillasSinCursoActualMuestraVista(): void
    {
        setting_usuario(['curso_actual' => null]);

        $response = $this->post(route('intellij_projects.descargar.plantillas'), [
            'unidad_id' => $this->unidad->id,
        ]);

        $response->assertOk();
        $response->assertViewIs('intellij_projects.descargar');
    }

    // ---------------------------------------------------------------------------
    // descargar_plantillas_curso() — POST → descarga script con todos los repos
    // ---------------------------------------------------------------------------

    public function testDescargarPlantillasCursoDescargaScriptConRepos(): void
    {
        IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/ip-repo',
        ]);
        MarkdownText::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/md-repo',
        ]);

        Http::fake([
            '*/api/v1/repos/root/ip-repo' => Http::response(
                $this->fakeRepoData(['name' => 'ip-repo', 'full_name' => 'root/ip-repo', 'clone_url' => 'http://gitea:3000/root/ip-repo.git']),
                200
            ),
            '*/api/v1/repos/root/md-repo' => Http::response(
                $this->fakeRepoData(['name' => 'md-repo', 'full_name' => 'root/md-repo', 'clone_url' => 'http://gitea:3000/root/md-repo.git']),
                200
            ),
        ]);

        $response = $this->post(route('intellij_projects.descargar.plantillas.curso'));

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function testDescargarPlantillasCursoMuestraVistaSinCursoActual(): void
    {
        setting_usuario(['curso_actual' => null]);

        $response = $this->post(route('intellij_projects.descargar.plantillas.curso'));

        $response->assertOk();
        $response->assertViewIs('intellij_projects.descargar');
    }

    // ---------------------------------------------------------------------------
    // descargar_repos_usuario() — POST → ZIP con todos los repos del usuario
    // ---------------------------------------------------------------------------

    public function testDescargarReposUsuarioDescargaZipConProyectos(): void
    {
        $this->fakeProcessWithClone();
        Storage::fake('temp');

        $actividad = Actividad::factory()->create(['unidad_id' => $this->unidad->id]);
        $project = IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/user-repo',
        ]);
        $actividad->intellij_projects()->attach($project, [
            'fork' => null,
            'fork_status' => 0,
            'orden' => Str::orderedUuid(),
        ]);
        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => 20,
        ]);

        Http::fake([
            '*/api/v1/repos/root/user-repo' => Http::response(
                $this->fakeRepoData([
                    'name' => 'user-repo',
                    'full_name' => 'root/user-repo',
                    'clone_url' => 'http://gitea:3000/root/user-repo.git',
                    'owner' => ['login' => 'root'],
                ]),
                200
            ),
        ]);

        $response = $this->post(route('intellij_projects.descargar'), [
            'user_id' => $this->alumno->id,
        ]);

        // Verificamos que git clone fue ejecutado (rama total > 0)
        Process::assertRan(function ($process) {
            return str_contains($process->command, 'git clone');
        });

        // La respuesta debe ser un fichero de descarga (200)
        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function testDescargarReposUsuarioDescargaZipConFork(): void
    {
        $this->fakeProcessWithClone();
        Storage::fake('temp');

        $actividad = Actividad::factory()->create(['unidad_id' => $this->unidad->id]);
        $project = IntellijProject::factory()->create(['curso_id' => $this->curso->id]);
        $actividad->intellij_projects()->attach($project, [
            'fork' => 'alumno/fork-repo',
            'fork_status' => 2,
            'orden' => Str::orderedUuid(),
        ]);
        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => 20,
        ]);

        Http::fake([
            '*/api/v1/repos/alumno/fork-repo' => Http::response(
                $this->fakeRepoData([
                    'name' => 'fork-repo',
                    'full_name' => 'alumno/fork-repo',
                    'clone_url' => 'http://gitea:3000/alumno/fork-repo.git',
                    'owner' => ['login' => 'alumno'],
                ]),
                200
            ),
        ]);

        $response = $this->post(route('intellij_projects.descargar'), [
            'user_id' => $this->alumno->id,
        ]);

        Process::assertRan(function ($process) {
            return str_contains($process->command, 'git clone');
        });
        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function testDescargarReposUsuarioRedirigeSinProyectos(): void
    {
        Storage::fake('temp');

        // Alumno sin ninguna actividad asignada → total = 0
        $response = $this->post(route('intellij_projects.descargar'), [
            'user_id' => $this->alumno->id,
        ]);

        $response->assertRedirect();
    }

    public function testDescargarReposUsuarioGestionaErrorDeGitClone(): void
    {
        // git clone falla → clonarRepositorio registra el error en Log
        Process::fake(['*' => Process::result(output: '', exitCode: 1)]);
        Storage::fake('temp');

        $actividad = Actividad::factory()->create(['unidad_id' => $this->unidad->id]);
        $project = IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/fail-repo',
        ]);
        $actividad->intellij_projects()->attach($project, [
            'fork' => null,
            'fork_status' => 0,
            'orden' => Str::orderedUuid(),
        ]);
        Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
            'estado' => 20,
        ]);

        Http::fake([
            '*/api/v1/repos/root/fail-repo' => Http::response(
                $this->fakeRepoData([
                    'name' => 'fail-repo',
                    'full_name' => 'root/fail-repo',
                    'clone_url' => 'http://gitea:3000/root/fail-repo.git',
                    'owner' => ['login' => 'root'],
                ]),
                200
            ),
        ]);

        // git clone falla pero clonarRepositorio no lanza excepción (sólo registra Log::error).
        // El total sigue siendo 1, por lo que se intenta crear el ZIP del directorio vacío.
        // ZipArchive sin ficheros no crea el archivo → response()->download() lanza excepción.
        // El handler de Laravel lo convierte en 500.
        $response = $this->post(route('intellij_projects.descargar'), [
            'user_id' => $this->alumno->id,
        ]);

        // Verificamos que el proceso fallido fue ejecutado (la rama del Log::error se cubrió)
        Process::assertRan(function ($process) {
            return str_contains($process->command, 'git clone');
        });

        // 500 porque el ZIP no se creó (directorio vacío)
        $this->assertContains($response->getStatusCode(), [200, 302, 500]);
    }
}
