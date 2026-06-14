<?php

namespace App\Mcp\Tools\Recursos\Rubric;

use App\Models\Rubric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todas las rúbricas. Devuelve id, curso_id, titulo, descripcion y plantilla de cada rúbrica.')]
#[IsReadOnly]
class ListRubrics extends Tool
{
    public function handle(Request $request): Response
    {
        $rubrics = Rubric::query()
            ->orderBy('titulo')
            ->get(['id', 'curso_id', 'titulo', 'descripcion', 'plantilla'])
            ->map(fn($r) => [
                'id' => $r->id,
                'curso_id' => (int) $r->curso_id,
                'titulo' => $r->titulo,
                'descripcion' => $r->descripcion,
                'plantilla' => (bool) $r->plantilla,
            ]);

        return Response::json($rubrics->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'rubrics' => $schema->array(),
        ];
    }
}
