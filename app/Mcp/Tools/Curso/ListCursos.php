<?php

namespace App\Mcp\Tools\Curso;

use App\Models\Curso;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Listar todos los cursos. Devuelve id, category_id, nombre, slug y matricula_abierta de cada curso.')]
#[IsReadOnly]
class ListCursos extends Tool
{
    public function handle(Request $request): Response
    {
        $cursos = Curso::query()
            ->orderBy('nombre')
            ->get(['id', 'category_id', 'nombre', 'slug', 'matricula_abierta'])
            ->map(fn($c) => [
                'id' => $c->id,
                'category_id' => (int)$c->category_id,
                'nombre' => $c->nombre,
                'slug' => $c->slug,
                'matricula_abierta' => (bool)$c->matricula_abierta,
            ]);

        return Response::json($cursos->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'cursos' => $schema->array(),
        ];
    }
}
