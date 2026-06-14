<?php

namespace App\Mcp\Tools\Recursos\Selector;

use App\Models\Selector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo selector. Requiere titulo y curso_id (ID del curso). Devuelve los datos del selector creado.')]
class CreateSelector extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear selectores.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
            'curso_id' => ['required', 'integer'],
        ]);

        $selector = Selector::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            
            'curso_id' => $validated['curso_id'],
        ]);

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
