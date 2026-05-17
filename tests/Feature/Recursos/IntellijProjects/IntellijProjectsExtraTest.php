<?php

namespace Tests\Feature\Recursos\IntellijProjects;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Facades\Agent;
use Override;
use Tests\TestCase;

class IntellijProjectsExtraTest extends TestCase
{
    use DatabaseTransactions;

    private Actividad $actividad;
    private IntellijProject $project;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $curso = Curso::factory()->create();

        $this->actingAs($this->profesor);
        setting_usuario(['curso_actual' => $curso->id]);

        $this->actividad = Actividad::factory()->create();
        $this->project = IntellijProject::factory()->create(['curso_id' => $curso->id]);

        // Attach with pivot data
        $this->actividad->intellij_projects()->attach($this->project, [
            'titulo_visible' => true,
            'descripcion_visible' => true,
            'incluir_siempre' => false,
        ]);
    }

    public function testToggleTituloVisible()
    {
        $this->actingAs($this->profesor);

        $response = $this->post(route('intellij_projects.toggle.titulo_visible', [
            $this->actividad, $this->project
        ]));

        $response->assertRedirect();

        // Check pivot was updated
        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertFalse((bool)$pivot->titulo_visible);
    }

    public function testToggleDescripcionVisible()
    {
        $this->actingAs($this->profesor);

        $response = $this->post(route('intellij_projects.toggle.descripcion_visible', [
            $this->actividad, $this->project
        ]));

        $response->assertRedirect();

        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertFalse((bool)$pivot->descripcion_visible);
    }

    public function testToggleIncluirSiempre()
    {
        $this->actingAs($this->profesor);

        $response = $this->post(route('intellij_projects.toggle.incluir_siempre', [
            $this->actividad, $this->project
        ]));

        $response->assertRedirect();

        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertTrue((bool)$pivot->incluir_siempre);
    }

    public function testLock()
    {
        $response = $this->post(route('intellij_projects.lock', [
            $this->project, $this->actividad
        ]));

        $response->assertRedirect();
    }

    public function testUnlock()
    {
        $response = $this->post(route('intellij_projects.unlock', [
            $this->project, $this->actividad
        ]));

        $response->assertRedirect();
    }

    public function testEditFork()
    {
        $response = $this->get(route('intellij_projects.edit_fork', [
            $this->project, $this->actividad
        ]));

        $response->assertSuccessful();
    }

    public function testUpdateFork()
    {
        $response = $this->put(route('intellij_projects.update_fork', [
            $this->project, $this->actividad
        ]), [
            'repositorio' => 'user/new-fork-name',
        ]);

        $response->assertRedirect();
    }

    public function testDuplicar()
    {
        $count = IntellijProject::count();

        $response = $this->post(route('intellij_projects.duplicar', $this->project));

        $response->assertRedirect(route('intellij_projects.index'));
        $this->assertEquals($count + 1, IntellijProject::count());
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
    // repository() — no_cache=true
    // ---------------------------------------------------------------------------

    public function testRepositoryNoCacheGitea(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
        ]);

        $repo = $this->project->repository(true);

        $this->assertEquals(42, $repo['id']);
        $this->assertEquals('test-repo', $repo['name']);
    }

    public function testRepositoryNoCacheHostDesconocido(): void
    {
        $project = IntellijProject::factory()->create([
            'repositorio' => 'root/unknown',
            'host' => 'gitlab',
        ]);

        $repo = $project->repository(true);

        $this->assertEquals('?', $repo['id']);
    }

    // ---------------------------------------------------------------------------
    // lock/unlock — con bloqueo real en Gitea
    // ---------------------------------------------------------------------------

    public function testLockConGiteaBloquea(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo' => Http::response([], 200),
        ]);

        $response = $this->post(route('intellij_projects.lock', [
            $this->project, $this->actividad,
        ]));

        $response->assertRedirect();

        Http::assertSent(function ($request) {
            return $request->method() === 'PATCH'
                && str_contains($request->url(), 'repos/root/test-repo');
        });
    }

    public function testUnlockConGiteaDesbloquea(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo' => Http::response([], 200),
        ]);

        $response = $this->post(route('intellij_projects.unlock', [
            $this->project, $this->actividad,
        ]));

        $response->assertRedirect();

        Http::assertSent(function ($request) {
            return $request->method() === 'PATCH'
                && str_contains($request->url(), 'repos/root/test-repo');
        });
    }

    // ---------------------------------------------------------------------------
    // setForkStatus()
    // ---------------------------------------------------------------------------

    public function testSetForkStatusSinFork(): void
    {
        $project = $this->actividad->intellij_projects()->find($this->project->id);

        $project->setForkStatus(1);

        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertEquals(1, $pivot->fork_status);
    }

    public function testSetForkStatusConFork(): void
    {
        $project = $this->actividad->intellij_projects()->find($this->project->id);

        $project->setForkStatus(2, 'root/fork-repo');

        $pivot = $this->actividad->intellij_projects()->find($this->project->id)->pivot;
        $this->assertEquals(2, $pivot->fork_status);
        $this->assertEquals('root/fork-repo', $pivot->fork);
    }

    // ---------------------------------------------------------------------------
    // gitkraken_deep_link()
    // ---------------------------------------------------------------------------

    public function testGitkrakenDeepLink(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/commits*' => Http::sequence()
                ->push([], 200, ['X-Total-Count' => 3])
                ->push([['sha' => 'abc123def456']], 200),
        ]);

        $link = $this->project->gitkraken_deep_link();

        $this->assertStringContainsString('gitkraken://repolink/abc123def456', $link);
        $this->assertStringContainsString('http://gitea:3000/root/test-repo.git', $link);
    }

    public function testGitkrakenDeepLinkSinRepoReal(): void
    {
        $link = $this->project->gitkraken_deep_link();

        $this->assertNull($link);
    }

    // ---------------------------------------------------------------------------
    // intellij_idea_deep_link()
    // ---------------------------------------------------------------------------

    public function testIntellijIdeaDeepLink(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
        ]);

        $link = $this->project->intellij_idea_deep_link();

        $this->assertStringContainsString('jetbrains://idea/checkout/git', $link);
        $this->assertStringContainsString('Git4Idea', $link);
    }

    public function testIntellijIdeaDeepLinkSafeExam(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
        ]);

        Agent::setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) SEB/ikasgela/1.0');

        $link = $this->project->intellij_idea_deep_link();

        Agent::setUserAgent('');

        $this->assertNotNull($link);
        $this->assertStringNotContainsString('jetbrains://idea', $link);
    }

    // ---------------------------------------------------------------------------
    // phpstorm_deep_link()
    // ---------------------------------------------------------------------------

    public function testPhpstormDeepLink(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
        ]);

        $link = $this->project->phpstorm_deep_link();

        $this->assertStringContainsString('jetbrains://php-storm/checkout/git', $link);
        $this->assertStringContainsString('Git4Idea', $link);
    }

    // ---------------------------------------------------------------------------
    // datagrip_deep_link()
    // ---------------------------------------------------------------------------

    public function testDatagripDeepLink(): void
    {
        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
        ]);

        $link = $this->project->datagrip_deep_link();

        $this->assertStringContainsString('jetbrains://dbe/checkout/git', $link);
        $this->assertStringContainsString('Git4Idea', $link);
    }

    // ---------------------------------------------------------------------------
    // duplicar() — con curso destino
    // ---------------------------------------------------------------------------

    public function testDuplicarConCursoDestino(): void
    {
        $cursoDestino = Curso::factory()->create(['gitea_organization' => 'org-destino']);

        $clonedData = $this->fakeRepoData([
            'id' => 99,
            'name' => 'test-repo-clon',
            'full_name' => 'org-destino/test-repo-clon',
            'description' => 'Test Repository',
        ]);

        Http::fake([
            '*/api/v1/repos/root/test' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/test-repo/generate' => Http::response($clonedData, 201),
        ]);

        $count = IntellijProject::count();

        $clon = $this->project->duplicar($cursoDestino);

        $this->assertEquals($count + 1, IntellijProject::count());
        $this->assertEquals($cursoDestino->id, $clon->curso_id);
        $this->assertEquals('org-destino/test-repo-clon', $clon->repositorio);
    }
}
