<?php

namespace App\Mcp\Tools\Curso;

use App\Models\Curso;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los cursos. Devuelve id, category_id, nombre, slug, fechas de inicio/fin y estado de matrícula abierta.')]
#[IsReadOnly]
class ListCursos extends Tool
{
    public function handle(Request $request): Response
    {
        $cursos = Curso::query()
            ->orderBy('nombre')
            ->get(['id', 'category_id', 'nombre', 'slug', 'fecha_inicio', 'fecha_fin', 'matricula_abierta'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'category_id' => (int) $c->category_id,
                'nombre' => $c->nombre,
                'slug' => $c->slug,
                'fecha_inicio' => $c->fecha_inicio?->toDateTimeString(),
                'fecha_fin' => $c->fecha_fin?->toDateTimeString(),
                'matricula_abierta' => (bool) $c->matricula_abierta,
            ]);

        return Response::structured($cursos->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'cursos' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'category_id' => ['type' => 'integer'],
                            'nombre' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'fecha_inicio' => ['type' => 'string', 'format' => 'date-time'],
                            'fecha_fin' => ['type' => 'string', 'format' => 'date-time'],
                            'matricula_abierta' => ['type' => 'boolean'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
