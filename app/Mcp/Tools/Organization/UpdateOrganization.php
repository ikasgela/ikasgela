<?php

namespace App\Mcp\Tools\Organization;

use App\Models\Organization;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar una organización existente por su ID. Campos opcionales: name, slug, seats, registration_open, current_period_id. Devuelve los datos actualizados.')]
class UpdateOrganization extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar organizaciones.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'seats' => ['nullable', 'integer', 'min:0'],
            'registration_open' => ['boolean'],
            'current_period_id' => ['nullable', 'integer'],
        ]);

        $org = Organization::find($validated['id']);

        if (!$org) {
            return Response::error("No se encontró la organización con id {$validated['id']}.");
        }

        $updateData = [];

        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }

        if (isset($validated['slug']) && strlen($validated['slug']) > 0) {
            $updateData['slug'] = Str::slug($validated['slug']);
        } elseif (isset($validated['name'])) {
            $updateData['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['seats'])) {
            $updateData['seats'] = $validated['seats'];
        }

        if (isset($validated['registration_open'])) {
            $updateData['registration_open'] = (bool) $validated['registration_open'];
        }

        if (isset($validated['current_period_id'])) {
            $updateData['current_period_id'] = $validated['current_period_id'];
        }

        $org->update($updateData);

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
            'id' => $schema->integer()->required(),
            'name' => $schema->string(),
            'slug' => $schema->string(),
            'seats' => $schema->integer(),
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
