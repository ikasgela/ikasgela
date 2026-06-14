<?php

namespace App\Mcp\Tools\Recursos\IntellijProject;

use App\Models\IntellijProject;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los IntellijProjects. Devuelve id, titulo, descripcion, curso_id y repository.')]
#[IsReadOnly]
class ListIntellijProjects extends Tool
{
    public function handle(Request $request): Response
    {
        $projects = IntellijProject::query()
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'descripcion', 'curso_id', 'repositorio', 'host'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'titulo' => $p->titulo,
                'descripcion' => $p->descripcion,
                'curso_id' => (int) $p->curso_id,
                
                'repository' => $p->repositorio,
                'host' => $p->host,
            ]);

        return Response::json($projects->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'projects' => $schema->array(),
        ];
    }
}
