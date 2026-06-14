<?php

namespace App\Mcp\Tools\Recursos\Selector;

use App\Models\Selector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un selector por su ID. Devuelve id, titulo, descripcion, curso_id .')]
#[IsReadOnly]
class GetSelector extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $selector = Selector::find($validated['id']);

        if (!$selector) {
            return Response::error("No se encontró el selector con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $selector->id,
            'titulo' => $selector->titulo,
            'descripcion' => $selector->descripcion,
            'curso_id' => (int) $selector->curso_id,
            
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
            
        ];
    }
}
