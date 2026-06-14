<?php

namespace App\Mcp\Tools\Recursos\FileResource;

use App\Models\Curso;
use App\Models\FileResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo recurso de archivo. Requiere titulo y curso_id (ID del curso). Devuelve los datos del recurso creado.')]
class CreateFileResource extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear recursos de archivo.');
        }

        $validated = $request->validate([
            'curso_id' => ['required', 'integer'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
        ]);

        // Verify course exists
        $courseExists = Curso::where('id', $validated['curso_id'])->exists();

        if (!$courseExists) {
            return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
        }

        $fileResource = FileResource::create([
            'curso_id' => $validated['curso_id'],
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            
        ]);

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
            'curso_id' => $schema->integer()->required(),
            'titulo' => $schema->string()->required(),
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
