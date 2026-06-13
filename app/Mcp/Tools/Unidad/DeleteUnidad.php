<?php

namespace App\Mcp\Tools\Unidad;

use App\Models\Unidad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar una unidad por su ID. Esta acción es irreversible y elimina todas las actividades asociadas. Solo administradores pueden ejecutarla.')]
#[IsDestructive]
class DeleteUnidad extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar unidades.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $unidad = Unidad::find($validated['id']);

        if (!$unidad) {
            return Response::error("No se encontró la unidad con id {$validated['id']}.");
        }

        $unidad->delete();

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
