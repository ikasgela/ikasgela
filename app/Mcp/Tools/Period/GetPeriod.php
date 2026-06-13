<?php

namespace App\Mcp\Tools\Period;

use App\Models\Period;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un periodo por su ID. Devuelve id, organization_id, nombre y slug.')]
#[IsReadOnly]
class GetPeriod extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $period = Period::find($validated['id']);

        if (!$period) {
            return Response::error("No se encontró el periodo con id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $period->id,
            'organization_id' => (int) $period->organization_id,
            'name' => $period->name,
            'slug' => $period->slug,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'organization_id' => $schema->integer(),
            'name' => $schema->string(),
            'slug' => $schema->string(),
        ];
    }
}
