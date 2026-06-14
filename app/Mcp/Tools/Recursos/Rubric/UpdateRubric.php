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

#[Description('Actualizar una rúbrica existente por su ID. Campos opcionales: curso_id, titulo, descripcion, plantilla. Devuelve los datos actualizados.')]
#[IsDestructive]
class UpdateRubric extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar rúbricas.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'curso_id' => ['nullable', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'plantilla' => ['boolean'],
        ]);

        $rubric = Rubric::find($validated['id']);

        if (!$rubric) {
            return Response::error("No se encontró la rúbrica con id {$validated['id']}.");
        }

        // Verify course if provided
        if (isset($validated['curso_id'])) {
            $courseExists = Curso::where('id', $validated['curso_id'])->exists();

            if (!$courseExists) {
                return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
            }
        }

        $updateData = [];

        if (isset($validated['curso_id'])) {
            $updateData['curso_id'] = $validated['curso_id'];
        }

        if (isset($validated['titulo'])) {
            $updateData['titulo'] = $validated['titulo'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (array_key_exists('plantilla', $validated)) {
            $updateData['plantilla'] = (bool) $validated['plantilla'];
        }

        $rubric->update($updateData);

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
            'id' => $schema->integer()->required(),
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
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
