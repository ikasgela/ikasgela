<?php

namespace App\Mcp\Tools\Organization;

use App\Models\Organization;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de una organización por su ID. Devuelve id, nombre, slug, current_period_id, estado de registro y plazas disponibles.')]
#[IsReadOnly]
class GetOrganization extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $org = Organization::find($validated['id']);

        if (!$org) {
            return Response::error("No se encontró la organización con id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $org->id,
            'name' => $org->name,
            'slug' => $org->slug,
            'current_period_id' => (int) $org->current_period_id,
            'registration_open' => (bool) $org->registration_open,
            'seats' => (int) $org->seats,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
            'required' => ['id'],
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'current_period_id' => ['type' => 'integer'],
                'registration_open' => ['type' => 'boolean'],
                'seats' => ['type' => 'integer'],
            ],
        ];
    }
}
