<?php

namespace App\Mcp\Tools\Recursos\Rubric;

use App\Models\Curso;
use App\Models\Rubric;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Crear una nueva rúbrica. Requiere curso_id (ID del curso) y titulo. Devuelve los datos de la rúbrica creada.')]
class CreateRubric extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear rúbricas.');
        }

        $validated = $request->validate([
            'curso_id' => ['required', 'integer'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'plantilla' => ['boolean'],
        ]);

        // Verify course exists
        $courseExists = Curso::where('id', $validated['curso_id'])->exists();

        if (!$courseExists) {
            return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
        }

        $rubric = Rubric::create([
            'curso_id' => $validated['curso_id'],
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'plantilla' => (bool) ($validated['plantilla'] ?? false),
        ]);

        return Response::json([
            'id' => $rubric->id,
            'curso_id' => (int) $rubric->curso_id,
            'titulo' => $rubric->titulo,
            'descripcion' => $rubric->descripcion,
            'plantilla' => (bool) $rubric->plantilla,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'curso_id' => $schema->integer()->required(),
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
        ];
    }
}
