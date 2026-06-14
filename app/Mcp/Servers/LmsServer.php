<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class LmsServer extends Server
{
    public int $defaultPaginationLength = 500;

    protected array $tools = [
        // Organization tools (read)
        \App\Mcp\Tools\Organization\ListOrganizations::class,
        \App\Mcp\Tools\Organization\GetOrganization::class,

        // Organization tools (write)
        \App\Mcp\Tools\Organization\CreateOrganization::class,
        \App\Mcp\Tools\Organization\UpdateOrganization::class,
        \App\Mcp\Tools\Organization\DeleteOrganization::class,

        // Period tools (read)
        \App\Mcp\Tools\Period\ListPeriods::class,
        \App\Mcp\Tools\Period\GetPeriod::class,

        // Period tools (write)
        \App\Mcp\Tools\Period\CreatePeriod::class,
        \App\Mcp\Tools\Period\UpdatePeriod::class,
        \App\Mcp\Tools\Period\DeletePeriod::class,

        // Category tools (read)
        \App\Mcp\Tools\Category\ListCategories::class,
        \App\Mcp\Tools\Category\GetCategory::class,

        // Category tools (write)
        \App\Mcp\Tools\Category\CreateCategory::class,
        \App\Mcp\Tools\Category\UpdateCategory::class,
        \App\Mcp\Tools\Category\DeleteCategory::class,

        // Curso tools (read)
        \App\Mcp\Tools\Curso\ListCursos::class,
        \App\Mcp\Tools\Curso\GetCurso::class,

        // Curso tools (write)
        \App\Mcp\Tools\Curso\CreateCurso::class,
        \App\Mcp\Tools\Curso\UpdateCurso::class,
        \App\Mcp\Tools\Curso\DeleteCurso::class,

        // Unidad tools (read)
        \App\Mcp\Tools\Unidad\ListUnidades::class,
        \App\Mcp\Tools\Unidad\GetUnidad::class,

        // Unidad tools (write)
        \App\Mcp\Tools\Unidad\CreateUnidad::class,
        \App\Mcp\Tools\Unidad\UpdateUnidad::class,
        \App\Mcp\Tools\Unidad\DeleteUnidad::class,

        // Actividad tools (read)
        \App\Mcp\Tools\Actividad\ListActividades::class,
        \App\Mcp\Tools\Actividad\GetActividad::class,
        \App\Mcp\Tools\Actividad\ListActividadesByCurso::class,

        // Actividad tools (write)
        \App\Mcp\Tools\Actividad\CreateActividad::class,
        \App\Mcp\Tools\Actividad\UpdateActividad::class,
        \App\Mcp\Tools\Actividad\DeleteActividad::class,

        // ========== Recursos: Cuestionario (read) ==========
        \App\Mcp\Tools\Recursos\Cuestionario\ListCuestionarios::class,
        \App\Mcp\Tools\Recursos\Cuestionario\GetCuestionario::class,

        // Recursos: Cuestionario (write)
        \App\Mcp\Tools\Recursos\Cuestionario\CreateCuestionario::class,
        \App\Mcp\Tools\Recursos\Cuestionario\UpdateCuestionario::class,
        \App\Mcp\Tools\Recursos\Cuestionario\DeleteCuestionario::class,

        // Recursos: FileResource (read)
        \App\Mcp\Tools\Recursos\FileResource\ListFileResources::class,
        \App\Mcp\Tools\Recursos\FileResource\GetFileResource::class,

        // Recursos: FileResource (write)
        \App\Mcp\Tools\Recursos\FileResource\CreateFileResource::class,
        \App\Mcp\Tools\Recursos\FileResource\UpdateFileResource::class,
        \App\Mcp\Tools\Recursos\FileResource\DeleteFileResource::class,

        // Recursos: LinkCollection (read)
        \App\Mcp\Tools\Recursos\LinkCollection\ListLinkCollections::class,
        \App\Mcp\Tools\Recursos\LinkCollection\GetLinkCollection::class,

        // Recursos: LinkCollection (write)
        \App\Mcp\Tools\Recursos\LinkCollection\CreateLinkCollection::class,
        \App\Mcp\Tools\Recursos\LinkCollection\UpdateLinkCollection::class,
        \App\Mcp\Tools\Recursos\LinkCollection\DeleteLinkCollection::class,

        // Recursos: MarkdownText (read)
        \App\Mcp\Tools\Recursos\MarkdownText\ListMarkdownTexts::class,
        \App\Mcp\Tools\Recursos\MarkdownText\GetMarkdownText::class,

        // Recursos: MarkdownText (write)
        \App\Mcp\Tools\Recursos\MarkdownText\CreateMarkdownText::class,
        \App\Mcp\Tools\Recursos\MarkdownText\UpdateMarkdownText::class,
        \App\Mcp\Tools\Recursos\MarkdownText\DeleteMarkdownText::class,

        // Recursos: Rubric (read)
        \App\Mcp\Tools\Recursos\Rubric\ListRubrics::class,
        \App\Mcp\Tools\Recursos\Rubric\GetRubric::class,

        // Recursos: Rubric (write)
        \App\Mcp\Tools\Recursos\Rubric\CreateRubric::class,
        \App\Mcp\Tools\Recursos\Rubric\UpdateRubric::class,
        \App\Mcp\Tools\Recursos\Rubric\DeleteRubric::class,

        // Recursos: Selector (read)
        \App\Mcp\Tools\Recursos\Selector\ListSelectors::class,
        \App\Mcp\Tools\Recursos\Selector\GetSelector::class,

        // Recursos: Selector (write)
        \App\Mcp\Tools\Recursos\Selector\CreateSelector::class,
        \App\Mcp\Tools\Recursos\Selector\UpdateSelector::class,
        \App\Mcp\Tools\Recursos\Selector\DeleteSelector::class,

        // Recursos: TestResult (read)
        \App\Mcp\Tools\Recursos\TestResult\ListTestResults::class,
        \App\Mcp\Tools\Recursos\TestResult\GetTestResult::class,

        // Recursos: TestResult (write)
        \App\Mcp\Tools\Recursos\TestResult\CreateTestResult::class,
        \App\Mcp\Tools\Recursos\TestResult\UpdateTestResult::class,
        \App\Mcp\Tools\Recursos\TestResult\DeleteTestResult::class,

        // Recursos: YoutubeVideo (read)
        \App\Mcp\Tools\Recursos\YoutubeVideo\ListYoutubeVideos::class,
        \App\Mcp\Tools\Recursos\YoutubeVideo\GetYoutubeVideo::class,

        // Recursos: YoutubeVideo (write)
        \App\Mcp\Tools\Recursos\YoutubeVideo\CreateYoutubeVideo::class,
        \App\Mcp\Tools\Recursos\YoutubeVideo\UpdateYoutubeVideo::class,
        \App\Mcp\Tools\Recursos\YoutubeVideo\DeleteYoutubeVideo::class,

        // Recursos: IntellijProject (read)
        \App\Mcp\Tools\Recursos\IntellijProject\ListIntellijProjects::class,
        \App\Mcp\Tools\Recursos\IntellijProject\GetIntellijProject::class,

        // Recursos: IntellijProject (write)
        \App\Mcp\Tools\Recursos\IntellijProject\CreateIntellijProject::class,
        \App\Mcp\Tools\Recursos\IntellijProject\UpdateIntellijProject::class,
        \App\Mcp\Tools\Recursos\IntellijProject\DeleteIntellijProject::class,

        // ========== Recursos genéricos (asociación/desasociación) ==========
        \App\Mcp\Tools\Recursos\AsociarRecurso::class,
        \App\Mcp\Tools\Recursos\DesasociarRecurso::class,
        \App\Mcp\Tools\Recursos\ReordenarRecursos::class,
        \App\Mcp\Tools\Recursos\ActualizarVisibilidadRecurso::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
