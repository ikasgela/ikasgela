<?php

namespace App\Mcp\Tools\Category;

use App\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar una categoría por su ID. Esta acción es irreversible y elimina también los cursos asociados. Solo administradores pueden ejecutarla.')]
#[IsDestructive]
class DeleteCategory extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar categorías.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $category = Category::find($validated['id']);

        if (!$category) {
            return Response::error("No se encontró la categoría con id {$validated['id']}.");
        }

        $category->delete();

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
