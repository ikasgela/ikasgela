<?php

namespace App\Mcp\Tools\Curso;

use App\Models\Curso;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Eliminar un curso por su ID. Esta acción es irreversible y elimina todas las unidades, actividades y recursos asociados. Solo administradores pueden ejecutarla.')]
#[IsDestructive]
class DeleteCurso extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para eliminar cursos.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $curso = Curso::find($validated['id']);

        if (!$curso) {
            return Response::error("No se encontró el curso con id {$validated['id']}.");
        }

        $curso->delete();

        return Response::structured([
            'deleted' => true,
            'id' => $validated['id'],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
            'required' => ['id'],
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'deleted' => ['type' => 'boolean'],
                'id' => ['type' => 'integer'],
            ],
        ];
    }
}
