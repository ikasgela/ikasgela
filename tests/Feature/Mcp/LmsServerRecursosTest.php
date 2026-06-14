<?php

namespace Tests\Feature\Mcp;

use App\Models\Cuestionario;
use App\Models\FileResource;
use App\Models\IntellijProject;
use App\Models\LinkCollection;
use App\Models\MarkdownText;
use App\Models\Rubric;
use App\Models\Selector;
use App\Models\TestResult;
use App\Models\Unidad;
use App\Models\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Mcp\Server as McpServer;
use Laravel\Mcp\Server\Testing\PendingTestResponse;
use Tests\TestCase;

class LmsServerRecursosTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    protected function mcp(string $serverClass): PendingTestResponse
    {
        return new PendingTestResponse(
            \Illuminate\Container\Container::getInstance(),
            $serverClass,
        );
    }

    // ========== Helper: crear estructura base (category + curso + unidad + actividad) ==========

    private function crearEstructuraBase(): array
    {
        $category = \App\Models\Category::factory()->create();
        $curso = \App\Models\Curso::factory()->create(['category_id' => $category->id]);
        $unidad = \App\Models\Unidad::factory()->create(['curso_id' => $curso->id]);
        $actividad = \App\Models\Actividad::factory()->create(['unidad_id' => $unidad->id]);

        return ['category' => $category, 'curso' => $curso, 'unidad' => $unidad, 'actividad' => $actividad];
    }

    // ========== Cuestionario Tools ==========

    public function testListCuestionarios()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        Cuestionario::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\ListCuestionarios::class, ['curso_id' => $base['curso']->id]);

        $response->dump();
        $response->assertOk();
    }

    public function testGetCuestionario()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\GetCuestionario::class, ['id' => $cuestionario->id]);

        $response->assertOk()->assertSee($cuestionario->titulo);
    }

    public function testCreateCuestionario()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\CreateCuestionario::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test Cuestionario',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('cuestionarios', ['titulo' => 'Test Cuestionario']);
    }

    public function testUpdateCuestionario()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\UpdateCuestionario::class, [
                'id' => $cuestionario->id,
                'titulo' => 'Updated Cuestionario',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('cuestionarios', ['id' => $cuestionario->id, 'titulo' => 'Updated Cuestionario']);
    }

    public function testDeleteCuestionario()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\DeleteCuestionario::class, ['id' => $cuestionario->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('cuestionarios', ['id' => $cuestionario->id]);
    }

    // ========== FileResource Tools ==========

    public function testListFileResources()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        FileResource::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\FileResource\ListFileResources::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetFileResource()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $fileResource = FileResource::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\FileResource\GetFileResource::class, ['id' => $fileResource->id]);

        $response->assertOk()->assertSee($fileResource->titulo);
    }

    public function testCreateFileResource()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\FileResource\CreateFileResource::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test FileResource',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('file_resources', ['titulo' => 'Test FileResource']);
    }

    public function testUpdateFileResource()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $fileResource = FileResource::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\FileResource\UpdateFileResource::class, [
                'id' => $fileResource->id,
                'titulo' => 'Updated FileResource',
            ]);

        $response->dump();
        $response->assertOk();
        $this->assertDatabaseHas('file_resources', ['id' => $fileResource->id, 'titulo' => 'Updated FileResource']);
    }

    public function testDeleteFileResource()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $fileResource = FileResource::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\FileResource\DeleteFileResource::class, ['id' => $fileResource->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('file_resources', ['id' => $fileResource->id]);
    }

    // ========== LinkCollection Tools ==========

    public function testListLinkCollections()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        LinkCollection::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\LinkCollection\ListLinkCollections::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetLinkCollection()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $linkCollection = LinkCollection::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\LinkCollection\GetLinkCollection::class, ['id' => $linkCollection->id]);

        $response->assertOk()->assertSee($linkCollection->titulo);
    }

    public function testCreateLinkCollection()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\LinkCollection\CreateLinkCollection::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test LinkCollection',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('link_collections', ['titulo' => 'Test LinkCollection']);
    }

    public function testUpdateLinkCollection()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $linkCollection = LinkCollection::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\LinkCollection\UpdateLinkCollection::class, [
                'id' => $linkCollection->id,
                'titulo' => 'Updated LinkCollection',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('link_collections', ['id' => $linkCollection->id, 'titulo' => 'Updated LinkCollection']);
    }

    public function testDeleteLinkCollection()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $linkCollection = LinkCollection::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\LinkCollection\DeleteLinkCollection::class, ['id' => $linkCollection->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('link_collections', ['id' => $linkCollection->id]);
    }

    // ========== MarkdownText Tools ==========

    public function testListMarkdownTexts()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        MarkdownText::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\MarkdownText\ListMarkdownTexts::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetMarkdownText()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $markdownText = MarkdownText::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\MarkdownText\GetMarkdownText::class, ['id' => $markdownText->id]);

        $response->assertOk()->assertSee($markdownText->titulo);
    }

    public function testCreateMarkdownText()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\MarkdownText\CreateMarkdownText::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test MarkdownText',
                'repositorio' => 'test-repo',
                'archivo' => 'test.md',
            ]);

        $response->dump();
        $response->assertOk();
        $this->assertDatabaseHas('markdown_texts', ['titulo' => 'Test MarkdownText']);
    }

    public function testUpdateMarkdownText()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $markdownText = MarkdownText::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\MarkdownText\UpdateMarkdownText::class, [
                'id' => $markdownText->id,
                'titulo' => 'Updated MarkdownText',
                'repositorio' => $markdownText->repositorio,
                'archivo' => $markdownText->archivo,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('markdown_texts', ['id' => $markdownText->id, 'titulo' => 'Updated MarkdownText']);
    }

    public function testDeleteMarkdownText()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $markdownText = MarkdownText::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\MarkdownText\DeleteMarkdownText::class, ['id' => $markdownText->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('markdown_texts', ['id' => $markdownText->id]);
    }

    // ========== Rubric Tools ==========

    public function testListRubrics()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        Rubric::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Rubric\ListRubrics::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetRubric()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $rubric = Rubric::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Rubric\GetRubric::class, ['id' => $rubric->id]);

        $response->assertOk()->assertSee($rubric->titulo);
    }

    public function testCreateRubric()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Rubric\CreateRubric::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test Rubric',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('rubrics', ['titulo' => 'Test Rubric']);
    }

    public function testUpdateRubric()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $rubric = Rubric::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Rubric\UpdateRubric::class, [
                'id' => $rubric->id,
                'titulo' => 'Updated Rubric',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('rubrics', ['id' => $rubric->id, 'titulo' => 'Updated Rubric']);
    }

    public function testDeleteRubric()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $rubric = Rubric::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Rubric\DeleteRubric::class, ['id' => $rubric->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('rubrics', ['id' => $rubric->id]);
    }

    // ========== Selector Tools ==========

    public function testListSelectors()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        Selector::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Selector\ListSelectors::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetSelector()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $selector = Selector::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Selector\GetSelector::class, ['id' => $selector->id]);

        $response->assertOk()->assertSee($selector->titulo);
    }

    public function testCreateSelector()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Selector\CreateSelector::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test Selector',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('selectors', ['titulo' => 'Test Selector']);
    }

    public function testUpdateSelector()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $selector = Selector::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Selector\UpdateSelector::class, [
                'id' => $selector->id,
                'titulo' => 'Updated Selector',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('selectors', ['id' => $selector->id, 'titulo' => 'Updated Selector']);
    }

    public function testDeleteSelector()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $selector = Selector::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Selector\DeleteSelector::class, ['id' => $selector->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('selectors', ['id' => $selector->id]);
    }

    // ========== TestResult Tools ==========

    public function testListTestResults()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        TestResult::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\TestResult\ListTestResults::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetTestResult()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $testResult = TestResult::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\TestResult\GetTestResult::class, ['id' => $testResult->id]);

        $response->assertOk()->assertSee($testResult->titulo);
    }

    public function testCreateTestResult()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\TestResult\CreateTestResult::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test TestResult',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('test_results', ['titulo' => 'Test TestResult']);
    }

    public function testUpdateTestResult()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $testResult = TestResult::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\TestResult\UpdateTestResult::class, [
                'id' => $testResult->id,
                'titulo' => 'Updated TestResult',
            ]);

        $response->dump();
        $this->assertDatabaseHas('test_results', ['id' => $testResult->id, 'titulo' => 'Updated TestResult']);
    }

    public function testDeleteTestResult()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $testResult = TestResult::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\TestResult\DeleteTestResult::class, ['id' => $testResult->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('test_results', ['id' => $testResult->id]);
    }

    // ========== YoutubeVideo Tools ==========

    public function testListYoutubeVideos()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        YoutubeVideo::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\YoutubeVideo\ListYoutubeVideos::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetYoutubeVideo()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $youtubeVideo = YoutubeVideo::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\YoutubeVideo\GetYoutubeVideo::class, ['id' => $youtubeVideo->id]);

        $response->assertOk()->assertSee($youtubeVideo->titulo);
    }

    public function testCreateYoutubeVideo()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\YoutubeVideo\CreateYoutubeVideo::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test YoutubeVideo',
                'codigo' => 'dQw4w9WgXcQ',
            ]);

        $response->dump();
        $this->assertDatabaseHas('youtube_videos', ['titulo' => 'Test YoutubeVideo']);
    }

    public function testUpdateYoutubeVideo()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $youtubeVideo = YoutubeVideo::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\YoutubeVideo\UpdateYoutubeVideo::class, [
                'id' => $youtubeVideo->id,
                'titulo' => 'Updated YoutubeVideo',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('youtube_videos', ['id' => $youtubeVideo->id, 'titulo' => 'Updated YoutubeVideo']);
    }

    public function testDeleteYoutubeVideo()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $youtubeVideo = YoutubeVideo::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\YoutubeVideo\DeleteYoutubeVideo::class, ['id' => $youtubeVideo->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('youtube_videos', ['id' => $youtubeVideo->id]);
    }

    // ========== IntellijProject Tools ==========

    public function testListIntellijProjects()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        IntellijProject::factory()->count(3)->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\IntellijProject\ListIntellijProjects::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetIntellijProject()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $intellijProject = IntellijProject::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\IntellijProject\GetIntellijProject::class, ['id' => $intellijProject->id]);

        $response->assertOk()->assertSee($intellijProject->titulo);
    }

    public function testCreateIntellijProject()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\IntellijProject\CreateIntellijProject::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Test IntellijProject',
                'repositorio' => 'https://github.com/example/project',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('intellij_projects', ['titulo' => 'Test IntellijProject']);
    }

    public function testUpdateIntellijProject()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $intellijProject = IntellijProject::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\IntellijProject\UpdateIntellijProject::class, [
                'id' => $intellijProject->id,
                'titulo' => 'Updated IntellijProject',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('intellij_projects', ['id' => $intellijProject->id, 'titulo' => 'Updated IntellijProject']);
    }

    public function testDeleteIntellijProject()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $intellijProject = IntellijProject::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\IntellijProject\DeleteIntellijProject::class, ['id' => $intellijProject->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('intellij_projects', ['id' => $intellijProject->id]);
    }

    // ========== Association Tools (generic) ==========

    public function testAsociarRecursoAActividad()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\AsociarRecurso::class, [
                'actividad_id' => $base['actividad']->id,
                'recurso_id' => $cuestionario->id,
                'tipo_recurso' => 'cuestionario',
            ]);

        $response->assertOk();
    }

    public function testDesasociarRecursoDeActividad()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        // Primero asociar
        $cuestionario->actividades()->attach($base['actividad']->id, ['orden' => 1]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\DesasociarRecurso::class, [
                'actividad_id' => $base['actividad']->id,
                'recurso_id' => $cuestionario->id,
                'tipo_recurso' => 'cuestionario',
            ]);

        $response->assertOk();
    }

    public function testReordenarRecursos()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario1 = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);
        $cuestionario2 = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        // Asociar con orden inicial
        $cuestionario1->actividades()->attach($base['actividad']->id, ['orden' => 2]);
        $cuestionario2->actividades()->attach($base['actividad']->id, ['orden' => 1]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\ReordenarRecursos::class, [
                'actividad_id' => $base['actividad']->id,
                'tipo_recurso' => 'cuestionario',
                'recursos_ordenados' => [
                    ['id' => $cuestionario2->id],
                    ['id' => $cuestionario1->id],
                ],
            ]);

        $response->assertOk();
    }

    public function testActualizarVisibilidadRecurso()
    {
        $this->actingAs($this->admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        // Asociar con visibilidad por defecto
        $cuestionario->actividades()->attach($base['actividad']->id, ['orden' => 1]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\ActualizarVisibilidadRecurso::class, [
                'actividad_id' => $base['actividad']->id,
                'recurso_id' => $cuestionario->id,
                'tipo_recurso' => 'cuestionario',
                'titulo_visible' => false,
                'descripcion_visible' => false,
            ]);

        $response->assertOk();
    }

    // ========== Authorization: non-admin cannot write ==========

    public function testNonAdminCannotCreateCuestionario()
    {
        $this->actingAs($this->not_admin);

        $base = $this->crearEstructuraBase();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\CreateCuestionario::class, [
                'curso_id' => $base['curso']->id,
                'titulo' => 'Unauthorized Cuestionario',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotAsociarRecurso()
    {
        $this->actingAs($this->not_admin);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\AsociarRecurso::class, [
                'actividad_id' => $base['actividad']->id,
                'recurso_id' => $cuestionario->id,
                'tipo_recurso' => 'cuestionario',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    // ========== Read tools accessible to all authenticated users ==========

    public function testListCuestionariosAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        $base = $this->crearEstructuraBase();
        Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\ListCuestionarios::class, ['curso_id' => $base['curso']->id]);

        $response->assertOk();
    }

    public function testGetCuestionarioAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        $base = $this->crearEstructuraBase();
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $base['curso']->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Recursos\Cuestionario\GetCuestionario::class, ['id' => $cuestionario->id]);

        $response->assertOk();
    }
}
