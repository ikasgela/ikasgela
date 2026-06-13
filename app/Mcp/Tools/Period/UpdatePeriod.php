<?php

namespace App\Mcp\Tools\Period;

use App\Models\Organization;
use App\Models\Period;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un periodo existente por su ID. Campos opcionales: organization_id, name, slug. Devuelve los datos actualizados.')]
class UpdatePeriod extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar periodos.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'organization_id' => ['nullable', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $period = Period::find($validated['id']);

        if (!$period) {
            return Response::error("No se encontró el periodo con id {$validated['id']}.");
        }

        $updateData = [];

        if (isset($validated['organization_id'])) {
            $orgExists = Organization::where('id', $validated['organization_id'])->exists();

            if (!$orgExists) {
                return Response::error("No se encontró la organización con id {$validated['organization_id']}.");
            }

            $updateData['organization_id'] = $validated['organization_id'];
        }

        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }

        if (isset($validated['slug']) && strlen($validated['slug']) > 0) {
            $updateData['slug'] = Str::slug($validated['slug']);
        } elseif (isset($validated['name'])) {
            $updateData['slug'] = Str::slug($validated['name']);
        }

        $period->update($updateData);

        return Response::json([
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
            'organization_id' => $schema->integer(),
            'name' => $schema->string(),
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
