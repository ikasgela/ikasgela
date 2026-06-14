<?php

namespace App\Mcp\Tools\Recursos\Cuestionario;

use App\Models\Cuestionario;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo cuestionario. Requiere titulo y curso_id (ID del curso). Devuelve los datos del cuestionario creado.')]
class CreateCuestionario extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear cuestionarios.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'plantilla' => ['boolean'],
            'respondido' => ['boolean'],
            
            'curso_id' => ['required', 'integer'],
        ]);

        $cuestionario = Cuestionario::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'plantilla' => (bool) ($validated['plantilla'] ?? false),
            'respondido' => (bool) ($validated['respondido'] ?? false),
            
            'curso_id' => $validated['curso_id'],
        ]);

        return Response::json([
            'id' => $cuestionario->id,
            'titulo' => $cuestionario->titulo,
            'descripcion' => $cuestionario->descripcion,
            'plantilla' => (bool) $cuestionario->plantilla,
            'respondido' => (bool) $cuestionario->respondido,
            'curso_id' => (int) $cuestionario->curso_id,
            
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
            'respondido' => $schema->boolean(),
            
            'curso_id' => $schema->integer()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
            'respondido' => $schema->boolean(),
            'curso_id' => $schema->integer(),
            
        ];
    }
}
