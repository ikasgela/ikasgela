<?php

namespace App\Mcp\Tools\Organization;

use App\Models\Organization;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear una nueva organización. Requiere nombre y plazas (seats). Devuelve los datos de la organización creada.')]
class CreateOrganization extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear organizaciones.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'seats' => ['required', 'integer', 'min:0'],
            'registration_open' => ['boolean'],
            'current_period_id' => ['nullable', 'integer'],
        ]);

        $org = Organization::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'seats' => $validated['seats'],
            'registration_open' => (bool) ($validated['registration_open'] ?? false),
            'current_period_id' => $validated['current_period_id'] ?? null,
        ]);

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
            'name' => $schema->string()->required(),
            'seats' => $schema->integer()->required(),
            'registration_open' => $schema->boolean(),
            'current_period_id' => $schema->integer(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'name' => $schema->string(),
            'slug' => $schema->string(),
            'current_period_id' => $schema->integer(),
            'registration_open' => $schema->boolean(),
            'seats' => $schema->integer(),
        ];
    }
}
