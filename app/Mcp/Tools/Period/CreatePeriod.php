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

#[Description('Crear un nuevo periodo. Requiere organization_id (ID de la organización) y nombre. Devuelve los datos del periodo creado.')]
class CreatePeriod extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear periodos.');
        }

        $validated = $request->validate([
            'organization_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        // Verify organization exists
        $orgExists = Organization::where('id', $validated['organization_id'])->exists();

        if (!$orgExists) {
            return Response::error("No se encontró la organización con id {$validated['organization_id']}.");
        }

        $period = Period::create([
            'organization_id' => $validated['organization_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

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
            'organization_id' => $schema->integer()->required(),
            'name' => $schema->string()->required(),
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
