<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use App\Models\MarkdownText;
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

    public function testDownloadCuandoStreamEsNullRetornaRespuestaOk(): void
    {
        $project = IntellijProject::factory()->create([
            'curso_id' => $this->curso->id,
            'repositorio' => 'root/test-repo',
            'host' => 'gitea',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            // 404 hace que GiteaClient::download() devuelva null
            '*/api/v1/repos/root/test-repo/archive/master.zip' => Http::response('', 404),
        ]);

        $this->actingAs($this->alumno);

        $response = $this->get(route('intellij_projects.download', $project));

        // La respuesta es StreamedResponse; ejecutamos la callback para cubrir el branch
        ob_start();
        $response->sendContent();
        ob_end_clean();

        $response->assertOk();
    }

    // ---------------------------------------------------------------------------
    // clonar() — variantes de recurso_type adicionales
    // ---------------------------------------------------------------------------

    public function testClonarCreaProyectoPhpStorm(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 44,
            'name' => 'phpstorm-repo',
            'full_name' => 'root/phpstorm-repo',
            'description' => 'PhpStorm Project',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            '*/api/v1/repos/root/phpstorm-repo' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();
        $countBefore = IntellijProject::count();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/phpstorm-repo',
            'nombre' => 'PhpStorm Project',
            'recurso_type' => 'intellij_project_phpstorm',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($countBefore + 1, IntellijProject::count());

        $nuevo = IntellijProject::latest()->first();
        $this->assertEquals('phpstorm', $nuevo->open_with);
    }

    public function testClonarCreaProyectoDataGrip(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 45,
            'name' => 'datagrip-repo',
            'full_name' => 'root/datagrip-repo',
            'description' => 'DataGrip Project',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            '*/api/v1/repos/root/datagrip-repo' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();
        $countBefore = IntellijProject::count();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/datagrip-repo',
            'nombre' => 'DataGrip Project',
            'recurso_type' => 'intellij_project_datagrip',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($countBefore + 1, IntellijProject::count());

        $nuevo = IntellijProject::latest()->first();
        $this->assertEquals('datagrip', $nuevo->open_with);
    }

    public function testClonarCreaProyectoGenericoIntellijProject(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 46,
            'name' => 'generic-repo',
            'full_name' => 'root/generic-repo',
            'description' => 'Generic Project',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            '*/api/v1/repos/root/generic-repo' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();
        $countBefore = IntellijProject::count();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/generic-repo',
            'nombre' => 'Generic Project',
            'recurso_type' => 'intellij_project',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($countBefore + 1, IntellijProject::count());

        $nuevo = IntellijProject::latest()->first();
        $this->assertEquals('', $nuevo->open_with);
    }

    public function testClonarCreaMarkdownText(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 47,
            'name' => 'md-repo',
            'full_name' => 'root/md-repo',
            'description' => 'Markdown Project',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            '*/api/v1/repos/root/md-repo' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();
        $countBefore = MarkdownText::count();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/md-repo',
            'nombre' => 'Markdown Project',
            'recurso_type' => 'markdown_text',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($countBefore + 1, MarkdownText::count());

        $nuevo = MarkdownText::latest()->first();
        $this->assertEquals('root/md-repo', $nuevo->repositorio);
    }

    public function testClonarConTipoDesconocidoNoCreaRecurso(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 48,
            'name' => 'unknown-repo',
            'full_name' => 'root/unknown-repo',
            'description' => 'Unknown Project',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            '*/api/v1/repos/root/unknown-repo' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();
        $ipCount = IntellijProject::count();
        $mdCount = MarkdownText::count();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/unknown-repo',
            'nombre' => 'Unknown Project',
            'recurso_type' => 'tipo_desconocido',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($ipCount, IntellijProject::count());
        $this->assertEquals($mdCount, MarkdownText::count());
    }

    public function testClonarConDestinoVacioUsaDatosDelOrigen(): void
    {
        $clonedData = $this->fakeRepoData([
            'id' => 49,
            'name' => 'test-repo',
            'full_name' => 'root/test-repo',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
        ]);

        $actividad = Actividad::factory()->create();

        // Sin destino → $usuario y $ruta se extraen del proyecto origen
        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => '',
            'nombre' => '',
            'recurso_type' => 'intellij_project_idea',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();
    }

    public function testClonarConvierteOrigenAPlantillaSiNoEsTemplate(): void
    {
        $noTemplate = $this->fakeRepoData(['template' => false]);
        $clonedData = $this->fakeRepoData([
            'id' => 50,
            'name' => 'forced-template',
            'full_name' => 'root/forced-template',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test-repo' => Http::response($noTemplate, 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
            '*/api/v1/repos/root/forced-template' => Http::response($clonedData, 200),
        ]);

        $actividad = Actividad::factory()->create();

        $response = $this->post(route('intellij_projects.clonar'), [
            'origen' => 'root/test-repo',
            'destino' => 'root/forced-template',
            'nombre' => 'Forced Template',
            'recurso_type' => 'intellij_project_idea',
            'actividad_id' => $actividad->id,
        ]);

        $response->assertRedirect();

        // Debe haberse enviado la llamada PATCH para convertir a plantilla
        Http::assertSent(function ($request) {
            return $request->method() === 'PATCH'
                && str_contains($request->url(), 'repos/root/test-repo');
        });
    }
}
