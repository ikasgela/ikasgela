<?php

namespace App\Mcp\Tools\Recursos\FileResource;

use App\Models\FileResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un recurso de archivo por su ID. Devuelve id, curso_id, titulo y descripcion.')]
#[IsReadOnly]
class GetFileResource extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $fileResource = FileResource::find($validated['id']);

        if (!$fileResource) {
            return Response::error("No se encontró el recurso de archivo con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $fileResource->id,
            'curso_id' => (int) $fileResource->curso_id,
            'titulo' => $fileResource->titulo,
            'descripcion' => $fileResource->descripcion,
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
        ];
    }
}
