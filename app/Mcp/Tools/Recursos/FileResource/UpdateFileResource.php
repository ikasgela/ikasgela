<?php

namespace App\Mcp\Tools\Recursos\FileResource;

use App\Models\Curso;
use App\Models\FileResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un recurso de archivo existente por su ID. Campos opcionales: curso_id, titulo, descripcion. Devuelve los datos actualizados.')]
class UpdateFileResource extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar recursos de archivo.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'curso_id' => ['nullable', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
        ]);

        $fileResource = FileResource::find($validated['id']);

        if (!$fileResource) {
            return Response::error("No se encontró el recurso de archivo con id {$validated['id']}.");
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


        $fileResource->update($updateData);

        return Response::json([
            'id' => $fileResource->id,
            'curso_id' => (int) $fileResource->curso_id,
            'titulo' => $fileResource->titulo,
            'descripcion' => $fileResource->descripcion,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required(),
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
        ];
    }
}
