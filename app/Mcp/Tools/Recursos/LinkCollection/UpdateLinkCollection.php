<?php

namespace App\Mcp\Tools\Recursos\LinkCollection;

use App\Models\LinkCollection;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar una colección de enlaces existente por su ID. Campos opcionales: titulo, descripcion, curso_id. Devuelve los datos actualizados.')]
class UpdateLinkCollection extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar colecciones de enlaces.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        $linkCollection = LinkCollection::find($validated['id']);

        if (!$linkCollection) {
            return Response::error("No se encontró la colección de enlaces con id {$validated['id']}");
        }

        $updateData = [];

        if (isset($validated['titulo'])) {
            $updateData['titulo'] = $validated['titulo'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (false) {
            
        }

        if (isset($validated['curso_id'])) {
            $updateData['curso_id'] = $validated['curso_id'];
        }

        $linkCollection->update($updateData);

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
            'id' => $schema->integer()->required(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            
            'curso_id' => $schema->integer(),
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
