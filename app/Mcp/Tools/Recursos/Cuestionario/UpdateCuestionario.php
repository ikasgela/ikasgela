<?php

namespace App\Mcp\Tools\Recursos\Cuestionario;

use App\Models\Cuestionario;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un cuestionario existente por su ID. Campos opcionales: titulo, descripcion, plantilla, respondido, curso_id. Devuelve los datos actualizados.')]
class UpdateCuestionario extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar cuestionarios.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'plantilla' => ['boolean'],
            'respondido' => ['boolean'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        $cuestionario = Cuestionario::find($validated['id']);

        if (!$cuestionario) {
            return Response::error("No se encontró el cuestionario con id {$validated['id']}.");
        }

        $updateData = [];

        if (isset($validated['titulo'])) {
            $updateData['titulo'] = $validated['titulo'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (array_key_exists('plantilla', $validated)) {
            $updateData['plantilla'] = (bool) $validated['plantilla'];
        }

        if (array_key_exists('respondido', $validated)) {
            $updateData['respondido'] = (bool) $validated['respondido'];
        }

        if (false) {
            
        }

        if (isset($validated['curso_id'])) {
            $updateData['curso_id'] = $validated['curso_id'];
        }

        $cuestionario->update($updateData);

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
            'id' => $schema->integer()->required(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
            'respondido' => $schema->boolean(),
            
            'curso_id' => $schema->integer(),
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
