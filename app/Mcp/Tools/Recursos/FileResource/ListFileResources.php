<?php

namespace App\Mcp\Tools\Recursos\FileResource;

use App\Models\FileResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los recursos de archivos. Devuelve id, curso_id, titulo y descripcion de cada recurso.')]
#[IsReadOnly]
class ListFileResources extends Tool
{
    public function handle(Request $request): Response
    {
        $fileResources = FileResource::query()
            ->orderBy('titulo')
            ->get(['id', 'curso_id', 'titulo', 'descripcion'])
            ->map(fn($r) => [
                'id' => $r->id,
                'curso_id' => (int) $r->curso_id,
                'titulo' => $r->titulo,
                'descripcion' => $r->descripcion,
            ]);

        return Response::json($fileResources->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'file_resources' => $schema->array(),
        ];
    }
}
