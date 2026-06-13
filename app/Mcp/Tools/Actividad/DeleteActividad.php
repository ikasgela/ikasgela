<?php

namespace App\Mcp\Tools\Actividad;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar una actividad por su ID (soft delete). Solo administradores pueden ejecutar esta acción.')]
class DeleteActividad extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar actividades.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $actividad = Actividad::find($validated['id']);

        if (!$actividad) {
            return Response::error("No se encontró la actividad con id {$validated['id']}.");
        }

        $actividad->delete();

        return Response::json([
            'deleted' => true,
            'id' => $validated['id'],
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
            'deleted' => $schema->boolean(),
            'id' => $schema->integer(),
        ];
    }
}
