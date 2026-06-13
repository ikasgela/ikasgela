<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class LmsServer extends Server
{
    public int $defaultPaginationLength = 50;

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
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
