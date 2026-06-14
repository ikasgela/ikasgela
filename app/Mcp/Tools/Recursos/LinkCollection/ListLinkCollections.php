<?php

namespace App\Mcp\Tools\Recursos\LinkCollection;

use App\Models\LinkCollection;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todas las colecciones de enlaces. Devuelve id, titulo, descripcion, curso_id cada colección.')]
#[IsReadOnly]
class ListLinkCollections extends Tool
{
    public function handle(Request $request): Response
    {
        $linkCollections = LinkCollection::query()
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'descripcion', 'curso_id'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'descripcion' => $c->descripcion,
                'curso_id' => (int) $c->curso_id,
                
            ]);

        return Response::json($linkCollections->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'linkCollections' => $schema->array(),
        ];
    }
}
