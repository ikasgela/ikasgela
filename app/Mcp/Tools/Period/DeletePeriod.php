<?php

namespace App\Mcp\Tools\Period;

use App\Models\Period;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar un periodo por su ID. Esta acción es irreversible y elimina también las categorías asociadas. Solo administradores pueden ejecutarla.')]
#[IsDestructive]
class DeletePeriod extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar periodos.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $period = Period::find($validated['id']);

        if (!$period) {
            return Response::error("No se encontró el periodo con id {$validated['id']}.");
        }

        $period->delete();

        return Response::structured([
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
