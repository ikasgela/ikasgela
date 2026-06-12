<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class LmsServer extends Server
{
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
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
