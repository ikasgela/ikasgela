<?php

namespace App\Mcp\Tools\Period;

use App\Models\Period;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los periodos. Devuelve id, organization_id, nombre y slug de cada periodo.')]
#[IsReadOnly]
class ListPeriods extends Tool
{
    public function handle(Request $request): Response
    {
        $periods = Period::query()
            ->orderBy('name')
            ->get(['id', 'organization_id', 'name', 'slug'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'organization_id' => (int) $p->organization_id,
                'name' => $p->name,
                'slug' => $p->slug,
            ]);

        return Response::structured($periods->toArray());
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
                'periods' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'organization_id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
