<?php

namespace Tests\Feature\Recursos\MarkdownTexts;

use Override;
use App\Models\Curso;
use App\Models\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarkdownTextsExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();
        $count = MarkdownText::count();

        // When
        $response = $this->post(route('markdown_texts.duplicar', $markdown_text));

        // Then
        $response->assertRedirect(route('markdown_texts.index'));
        $this->assertSame($count + 1, MarkdownText::count());
    }

    public function testBorrarCache()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.borrar_cache', $markdown_text));

        // Then
        $response->assertRedirect();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->post(route('markdown_texts.duplicar', $markdown_text));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotBorrarCache()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.borrar_cache', $markdown_text));

        // Then
        $response->assertForbidden();
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function fakeRepoData(array $overrides = []): array
    {
        return array_merge([
            'id' => 55,
            'name' => 'docs-repo',
            'description' => 'Docs Repository',
            'clone_url' => 'http://gitea:3000/root/docs-repo.git',
            'full_name' => 'root/docs-repo',
            'html_url' => 'http://gitea:3000/root/docs-repo',
            'template' => true,
            'owner' => ['login' => 'root'],
        ], $overrides);
    }

    // ---------------------------------------------------------------------------
    // markdown() — con Gitea habilitado
    // ---------------------------------------------------------------------------

    public function testMarkdownConGiteaHabilitado(): void
    {
        Config::set('ikasgela.gitea_enabled', true);

        $markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
            'rama' => 'master',
            'archivo' => 'README.md',
        ]);

        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/docs-repo/contents/README.md*' => Http::response([
                'content' => base64_encode("# Título\n\nContenido de prueba. [enlace relativo](doc.md)"),
            ], 200),
        ]);

        $html = $markdownText->markdown();

        $this->assertStringContainsString('<h1>', $html);
        $this->assertStringContainsString('Título', $html);
    }

    public function testMarkdownConGiteaError(): void
    {
        Config::set('ikasgela.gitea_enabled', true);

        $markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
            'rama' => 'master',
            'archivo' => 'README.md',
        ]);

        Http::fake([
            '*/api/v1/repos/root/docs-repo' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            },
        ]);

        $resultado = $markdownText->markdown();

        $this->assertStringContainsString('Error', $resultado);
    }

    // ---------------------------------------------------------------------------
    // raw() — con Gitea habilitado
    // ---------------------------------------------------------------------------

    public function testRawConGiteaHabilitado(): void
    {
        Config::set('ikasgela.gitea_enabled', true);

        $markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
            'rama' => 'master',
            'archivo' => 'README.md',
        ]);

        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/docs-repo/contents/README.md*' => Http::response([
                'content' => base64_encode("# Título\n\nContenido raw."),
            ], 200),
        ]);

        $resultado = $markdownText->raw();

        $this->assertStringContainsString('Título', $resultado);
    }

    public function testRawConGiteaError(): void
    {
        Config::set('ikasgela.gitea_enabled', true);

        $markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
            'rama' => 'master',
            'archivo' => 'README.md',
        ]);

        Http::fake([
            '*/api/v1/repos/root/docs-repo' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            },
        ]);

        $resultado = $markdownText->raw();

        $this->assertStringContainsString('Error', $resultado);
    }

    // ---------------------------------------------------------------------------
    // duplicar() — con curso destino
    // ---------------------------------------------------------------------------

    public function testDuplicarConCursoDestino(): void
    {
        $this->actingAs($this->profesor);

        $cursoDestino = Curso::factory()->create(['gitea_organization' => 'org-destino']);

        $markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
        ]);

        $clonedData = $this->fakeRepoData([
            'id' => 60,
            'name' => 'docs-repo-clon',
            'full_name' => 'org-destino/docs-repo-clon',
        ]);

        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response($this->fakeRepoData(), 200),
            '*/api/v1/repos/root/docs-repo/generate' => Http::response($clonedData, 201),
        ]);

        $count = MarkdownText::count();

        $clon = $markdownText->duplicar($cursoDestino);

        $this->assertEquals($count + 1, MarkdownText::count());
        $this->assertEquals($cursoDestino->id, $clon->curso_id);
        $this->assertEquals('org-destino/docs-repo-clon', $clon->repositorio);
    }

    // ---------------------------------------------------------------------------
    // duplicar_repositorio() — sin plantilla (convierte primero)
    // ---------------------------------------------------------------------------

    public function testDuplicarRepositorioSinTemplate(): void
    {
        $this->actingAs($this->profesor);

        $cursoDestino = Curso::factory()->create(['gitea_organization' => 'org-destino']);

        $markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
        ]);

        $noTemplate = $this->fakeRepoData(['template' => false]);
        $clonedData = $this->fakeRepoData([
            'id' => 61,
            'name' => 'docs-repo-clon',
            'full_name' => 'org-destino/docs-repo-clon',
        ]);

        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response($noTemplate, 200),
            '*/api/v1/repos/root/docs-repo/generate' => Http::response($clonedData, 201),
        ]);

        $resultado = $markdownText->duplicar_repositorio($cursoDestino);

        $this->assertEquals('org-destino/docs-repo-clon', $resultado['path_with_namespace']);

        Http::assertSent(function ($request) {
            return $request->method() === 'PATCH'
                && str_contains($request->url(), 'repos/root/docs-repo');
        });
    }
}
