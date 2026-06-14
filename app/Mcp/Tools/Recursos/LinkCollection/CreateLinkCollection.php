<?php

namespace App\Mcp\Tools\Recursos\LinkCollection;

use App\Models\LinkCollection;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear una nueva colección de enlaces. Requiere titulo y curso_id (ID del curso). Devuelve los datos de la colección creada.')]
class CreateLinkCollection extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear colecciones de enlaces.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
            'curso_id' => ['required', 'integer'],
        ]);

        $linkCollection = LinkCollection::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            
            'curso_id' => $validated['curso_id'],
        ]);

        return Response::json([
            'id' => $linkCollection->id,
            'titulo' => $linkCollection->titulo,
            'descripcion' => $linkCollection->descripcion,
            'curso_id' => (int) $linkCollection->curso_id,
            
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            
            'curso_id' => $schema->integer()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'curso_id' => $schema->integer(),
            
        ];
    }
}
