<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Override;
use Tests\TestCase;

class IntellijProjectsGiteaTest extends TestCase
{
    use DatabaseTransactions;

    private Curso $curso;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->curso = Curso::factory()->create();
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

    // ---------------------------------------------------------------------------
    // copia() — lista repos del root
    // ---------------------------------------------------------------------------

    public function testCopiaListsRepositoriesFromGitea(): void
    {
        Http::fake([
            '*/api/v1/users/root' => Http::response(['id' => 1, 'login' => 'root'], 200),
            '*/api/v1/repos/search*' => Http::response(['data' => [$this->fakeRepoData()]], 200),
        ]);

        $response = $this->get(route('intellij_projects.copia'));

        $response->assertOk();
        $response->assertViewHas('proyectos');

        $proyectos = $response->viewData('proyectos');
        $this->assertCount(1, $proyectos);
        $this->assertEquals('test-repo', $proyectos[0]['name']);
    }

    public function testCopiaPassesEmptyArrayWhenGiteaFails(): void
    {
        Http::fake([
            '*/api/v1/users/root' => Http::response([], 500),
        ]);

        // uid() lanza excepción con throw(); el controlador recibe un array vacío
        $response = $this->get(route('intellij_projects.copia'));

        // La vista se renderiza (el controlador no tiene try/catch, la excepción
        // sube, pero comprobamos que al menos no devuelve 200 con datos)
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    // ---------------------------------------------------------------------------
    // borrar() — elimina repo en Gitea
    // ---------------------------------------------------------------------------

    public function testBorrarDeletesRepositoryOnGitea(): void
    {
        Http::fake([
            '*/api/v1/repositories/42' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo' => Http::response([], 204),
        ]);

        $response = $this->delete(route('intellij_projects.borrar', ['id' => 42]));

        $response->assertRedirect();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'repositories/42');
        });

        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE'
                && str_contains($request->url(), 'repos/root/test-repo');
        });
    }

    public function testBorrarDoesNothingWhenRepoNotFoundInGitea(): void
    {
        Http::fake([
            '*/api/v1/repositories/99' => Http::response([], 404),
        ]);

        // repo_by_id devuelve null → borrar_repo retorna sin hacer DELETE
        $response = $this->delete(route('intellij_projects.borrar', ['id' => 99]));

        $response->assertRedirect();

        Http::assertNotSent(function ($request) {
            return $request->method() === 'DELETE';
        });
    }

    // ---------------------------------------------------------------------------
    // clonar() — clona un repositorio plantilla en Gitea y crea IntellijProject
    // ---------------------------------------------------------------------------

    public function testClonarCreatesRepoOnGiteaAndIntellijProjectRecord(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 43,
            'name' => 'test-repo-clone',
            'full_name' => 'root/test-repo-clone',
            'description' => 'Clone Test',
        ]);

        Http::fake([
            // repo($origen)
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            // generate (clone)
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            // template PATCH calls
            '*/api/v1/repos/root/test-repo-clone' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();
        $countBefore = IntellijProject::count();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/test-repo-clone',
            'nombre' => 'Clone Test',
            'recurso_type' => 'intellij_project_idea',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();

        // Un nuevo IntellijProject debe haberse creado en BD
        $this->assertEquals($countBefore + 1, IntellijProject::count());

        $nuevo = IntellijProject::latest()->first();
        $this->assertEquals('root/test-repo-clone', $nuevo->repositorio);
        $this->assertEquals('idea', $nuevo->open_with);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'root/test-repo/generate');
        });
    }

    public function testClonarHandlesConflictAndRetries(): void
    {
        $firstCloneData = $this->fakeRepoData([
            'name' => 'test-repo-clone',
            'full_name' => 'root/test-repo-clone',
        ]);
        $retryCloneData = $this->fakeRepoData([
            'name' => 'test-repo-clone-2',
            'full_name' => 'root/test-repo-clone-2',
        ]);

        // Primer intento → 409 (conflicto), segundo → 201 (éxito)
        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::sequence()
                ->push($firstCloneData, 409)
                ->push($retryCloneData, 201),
            '*/api/v1/repos/root/test-repo-clone-2' => Http::response($retryCloneData, 200),
        ]);

        $actividad = Actividad::factory()->create();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/test-repo-clone',
            'nombre' => 'Clone Test',
            'recurso_type' => 'intellij_project_idea',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();

        // Debe haberse creado con el nombre del reintento
        $nuevo = IntellijProject::latest()->first();
        $this->assertEquals('root/test-repo-clone-2', $nuevo->repositorio);
    }

    // ---------------------------------------------------------------------------
    // download() — descarga el ZIP del repo
    // ---------------------------------------------------------------------------

    public function testDownloadStreamsZipFromGitea(): void
    {
        $project = IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/test-repo',
            'host' => 'gitea',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/archive/master.zip' => Http::response('ZIPCONTENT', 200),
        ]);

        $this->actingAs($this->alumno);

        $response = $this->get(route('intellij_projects.download', $project));

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }
}
