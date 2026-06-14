<?php

namespace App\Mcp\Tools\Recursos\Selector;

use App\Models\Selector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un selector existente por su ID. Campos opcionales: titulo, descripcion, curso_id. Devuelve los datos actualizados.')]
class UpdateSelector extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar selectores.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        $selector = Selector::find($validated['id']);

        if (!$selector) {
            return Response::error("No se encontró el selector con id {$validated['id']}.");
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

        $selector->update($updateData);

        return Response::json([
            'id' => $selector->id,
            'titulo' => $selector->titulo,
            'descripcion' => $selector->descripcion,
            'curso_id' => (int) $selector->curso_id,
            
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
