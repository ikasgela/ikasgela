<?php

namespace App\Mcp\Tools\Unidad;

use App\Models\Curso;
use App\Models\Unidad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar una unidad existente por su ID. Campos opcionales: curso_id, codigo, nombre, descripcion, orden, visible, fecha_disponibilidad, fecha_entrega, fecha_limite, minimo_entregadas. Devuelve los datos actualizados.')]
class UpdateUnidad extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar unidades.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'curso_id' => ['nullable', 'integer'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
            'visible' => ['boolean'],
            'fecha_disponibilidad' => ['nullable', 'date'],
            'fecha_entrega' => ['nullable', 'date'],
            'fecha_limite' => ['nullable', 'date'],
            'minimo_entregadas' => ['nullable', 'integer'],
        ]);

        $unidad = Unidad::find($validated['id']);

        if (!$unidad) {
            return Response::error("No se encontró la unidad con id {$validated['id']}.");
        }

        // Verify course exists if provided
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

        if (isset($validated['codigo'])) {
            $updateData['codigo'] = $validated['codigo'];
        }

        if (isset($validated['nombre'])) {
            $updateData['nombre'] = $validated['nombre'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (isset($validated['orden'])) {
            $updateData['orden'] = $validated['orden'];
        }

        if (array_key_exists('visible', $validated)) {
            $updateData['visible'] = (bool) $validated['visible'];
        }

        if (isset($validated['fecha_disponibilidad'])) {
            $updateData['fecha_disponibilidad'] = $validated['fecha_disponibilidad'];
        }

        if (isset($validated['fecha_entrega'])) {
            $updateData['fecha_entrega'] = $validated['fecha_entrega'];
        }

        if (isset($validated['fecha_limite'])) {
            $updateData['fecha_limite'] = $validated['fecha_limite'];
        }

        if (isset($validated['minimo_entregadas'])) {
            $updateData['minimo_entregadas'] = $validated['minimo_entregadas'];
        }

        $unidad->update($updateData);

        return Response::json([
            'id' => $unidad->id,
            'curso_id' => (int) $unidad->curso_id,
            'codigo' => $unidad->codigo,
            'nombre' => $unidad->nombre,
            'descripcion' => $unidad->descripcion,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required(),
            'curso_id' => $schema->integer(),
            'codigo' => $schema->string(),
            'nombre' => $schema->string(),
            'descripcion' => $schema->string(),
            'orden' => $schema->integer(),
            'visible' => $schema->boolean(),
            'fecha_disponibilidad' => $schema->string(),
            'fecha_entrega' => $schema->string(),
            'fecha_limite' => $schema->string(),
            'minimo_entregadas' => $schema->integer(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'curso_id' => $schema->integer(),
            'codigo' => $schema->string(),
            'nombre' => $schema->string(),
            'descripcion' => $schema->string(),
        ];
    }
}
