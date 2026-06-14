<?php

namespace App\Mcp\Tools\Recursos\LinkCollection;

use App\Models\LinkCollection;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar una colección de enlaces por su ID. Esta acción es irreversible y elimina también los enlaces asociados. Solo administradores pueden ejecutarla.')]
#[IsDestructive]
class DeleteLinkCollection extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar colecciones de enlaces.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $linkCollection = LinkCollection::find($validated['id']);

        if (!$linkCollection) {
            return Response::error("No se encontró la colección de enlaces con id {$validated['id']}");
        }

        $linkCollection->delete();

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
