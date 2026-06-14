<?php

namespace App\Mcp\Tools\Recursos\Selector;

use App\Models\Selector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los selectores. Devuelve id, titulo, descripcion, curso_id cada selector.')]
#[IsReadOnly]
class ListSelectors extends Tool
{
    public function handle(Request $request): Response
    {
        $selectores = Selector::query()
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'descripcion', 'curso_id'])
            ->map(fn ($s) => [
                'id' => $s->id,
                'titulo' => $s->titulo,
                'descripcion' => $s->descripcion,
                'curso_id' => (int) $s->curso_id,
                
            ]);

        return Response::json($selectores->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'selectores' => $schema->array(),
        ];
    }
}
