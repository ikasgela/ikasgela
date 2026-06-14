<?php

namespace App\Mcp\Tools\Recursos\IntellijProject;

use App\Models\IntellijProject;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar un IntellijProject por su ID. Esta acción es irreversible. Solo administradores pueden ejecutarla.')]
#[IsDestructive]
class DeleteIntellijProject extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar IntellijProjects.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $project = IntellijProject::find($validated['id']);

        if (!$project) {
            return Response::error("No se encontró el IntellijProject con id {$validated['id']}.\n");
        }

        $project->delete();

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
