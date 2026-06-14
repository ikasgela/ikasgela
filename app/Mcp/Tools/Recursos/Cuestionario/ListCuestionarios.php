<?php

namespace App\Mcp\Tools\Recursos\Cuestionario;

use App\Models\Cuestionario;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los cuestionarios. Devuelve id, titulo, descripcion, plantilla, respondido, curso_id cada cuestionario.')]
#[IsReadOnly]
class ListCuestionarios extends Tool
{
    public function handle(Request $request): Response
    {
        $cuestionarios = Cuestionario::query()
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'descripcion', 'plantilla', 'respondido', 'curso_id'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'descripcion' => $c->descripcion,
                'plantilla' => (bool) $c->plantilla,
                'respondido' => (bool) $c->respondido,
                'curso_id' => (int) $c->curso_id,
                
            ]);

        return Response::json($cuestionarios->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'cuestionarios' => $schema->array(),
        ];
    }
}
