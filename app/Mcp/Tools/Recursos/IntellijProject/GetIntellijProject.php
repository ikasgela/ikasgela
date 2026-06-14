<?php

namespace App\Mcp\Tools\Recursos\IntellijProject;

use App\Models\IntellijProject;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un IntellijProject por su ID. Devuelve id, titulo, descripcion, curso_id y repository.')]
#[IsReadOnly]
class GetIntellijProject extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $project = IntellijProject::find($validated['id']);

        if (!$project) {
            return Response::error("No se encontró el IntellijProject con id {$validated['id']}.\n");
        }

        return Response::json([
            'id' => $project->id,
            'titulo' => $project->titulo,
            'descripcion' => $project->descripcion,
            'curso_id' => (int) $project->curso_id,
            
            'repository' => $project->repositorio,
            'host' => $project->host,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'curso_id' => $schema->integer(),
            
            'repository' => $schema->string(),
            'host' => $schema->string()->nullable(),
        ];
    }
}
