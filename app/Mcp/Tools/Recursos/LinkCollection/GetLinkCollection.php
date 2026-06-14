<?php

namespace App\Mcp\Tools\Recursos\LinkCollection;

use App\Models\LinkCollection;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de una colección de enlaces por su ID. Devuelve id, titulo, descripcion, curso_id .')]
#[IsReadOnly]
class GetLinkCollection extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $linkCollection = LinkCollection::find($validated['id']);

        if (!$linkCollection) {
            return Response::error("No se encontró la colección de enlaces con id {$validated['id']}");
        }

        return Response::json([
            'id' => $linkCollection->id,
            'titulo' => $linkCollection->titulo,
            'descripcion' => $linkCollection->descripcion,
            'curso_id' => (int) $linkCollection->curso_id,
            
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
