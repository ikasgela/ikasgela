<?php

namespace App\Mcp\Tools\Recursos\Rubric;

use App\Models\Rubric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de una rúbrica por su ID. Devuelve id, curso_id, titulo, descripcion y plantilla.')]
#[IsReadOnly]
class GetRubric extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $rubric = Rubric::find($validated['id']);

        if (!$rubric) {
            return Response::error("No se encontró la rúbrica con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $rubric->id,
            'curso_id' => (int) $rubric->curso_id,
            'titulo' => $rubric->titulo,
            'descripcion' => $rubric->descripcion,
            'plantilla' => (bool) $rubric->plantilla,
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
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
        ];
    }
}
