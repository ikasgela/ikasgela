<?php

namespace Tests\Feature\Recursos\MarkdownTexts;

use App\Models\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Override;
use Tests\TestCase;

class MarkdownTextsGiteaTest extends TestCase
{
    use DatabaseTransactions;

    private MarkdownText $markdownText;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        $this->markdownText = MarkdownText::factory()->create([
            'repositorio' => 'root/docs-repo',
        ]);
    }

    private function fakeRepoData(array $overrides = []): array
    {
        return array_merge([
            'id' => 55,
            'name' => 'docs-repo',
            'description' => 'Docs Repository',
            'clone_url' => 'http://gitea:3000/root/docs-repo.git',
            'full_name' => 'root/docs-repo',
            'html_url' => 'http://gitea:3000/root/docs-repo',
            'template' => false,
            'owner' => ['login' => 'root'],
        ], $overrides);
    }

    // ---------------------------------------------------------------------------
    // edit() — muestra el formulario con datos del repositorio Gitea
    // ---------------------------------------------------------------------------

    public function testEditShowsRepositoryDataFromGitea(): void
    {
        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response($this->fakeRepoData(), 200),
        ]);

        $this->actingAs($this->profesor);

        $response = $this->get(route('markdown_texts.edit', $this->markdownText));

        $response->assertOk();
        $response->assertViewHas('repositorio');

        $repo = $response->viewData('repositorio');
        $this->assertNotNull($repo);
        $this->assertEquals('docs-repo', $repo['name']);
        $this->assertEquals('root', $repo['owner']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'repos/root/docs-repo');
        });
    }

    public function testEditSetsRepositorioNullWhenGiteaReturnsError(): void
    {
        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response([], 500),
        ]);

        $this->actingAs($this->profesor);

        $response = $this->get(route('markdown_texts.edit', $this->markdownText));

        $response->assertOk();
        $response->assertViewHas('repositorio', null);
    }

    public function testEditSetsRepositorioNullWhenGiteaReturns404(): void
    {
        Http::fake([
            '*/api/v1/repos/root/docs-repo' => Http::response([], 404),
        ]);

        $this->actingAs($this->profesor);

        $response = $this->get(route('markdown_texts.edit', $this->markdownText));

        $response->assertOk();
        $response->assertViewHas('repositorio', null);
    }

    public function testEditSetsRepositorioNullWhenGiteaIsUnreachable(): void
    {
        Http::fake([
            '*/api/v1/repos/root/docs-repo' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            },
        ]);

        $this->actingAs($this->profesor);

        $response = $this->get(route('markdown_texts.edit', $this->markdownText));

        $response->assertOk();
        $response->assertViewHas('repositorio', null);
    }
}
