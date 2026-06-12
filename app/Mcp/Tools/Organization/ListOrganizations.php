<?php

namespace App\Mcp\Tools\Organization;

use App\Models\Organization;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todas las organizaciones de la plataforma. Devuelve id, nombre, slug, estado de registro y plazas disponibles.')]
#[IsReadOnly]
class ListOrganizations extends Tool
{
    public function handle(Request $request): Response
    {
        $organizations = Organization::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'registration_open', 'seats'])
            ->map(fn ($org) => [
                'id' => $org->id,
                'name' => $org->name,
                'slug' => $org->slug,
                'registration_open' => (bool) $org->registration_open,
                'seats' => (int) $org->seats,
            ]);

        return Response::structured($organizations->toArray());
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
                'organizations' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'registration_open' => ['type' => 'boolean'],
                            'seats' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
