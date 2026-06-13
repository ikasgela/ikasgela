<?php

namespace App\Mcp\Tools\Actividad;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar una actividad existente por su ID. Campos opcionales: nombre, descripcion, puntuacion, plantilla, slug, final, siguiente_id, auto_avance, qualification_id, orden, fecha_disponibilidad, fecha_entrega, fecha_limite. Solo administradores pueden ejecutar esta acción.')]
class UpdateActividad extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar actividades.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'puntuacion' => ['nullable', 'numeric'],
            'plantilla' => ['boolean'],
            'slug' => ['nullable', 'string', 'max:255'],
            'final' => ['boolean'],
            'siguiente_id' => ['nullable', 'integer'],
            'auto_avance' => ['boolean'],
            'qualification_id' => ['nullable', 'integer'],
            'orden' => ['nullable', 'integer'],
            'fecha_disponibilidad' => ['nullable', 'date'],
            'fecha_entrega' => ['nullable', 'date'],
            'fecha_limite' => ['nullable', 'date'],
        ]);

        $actividad = Actividad::find($validated['id']);

        if (!$actividad) {
            return Response::error("No se encontró la actividad con id {$validated['id']}.");
        }

        $updateData = [];

        if (isset($validated['nombre'])) {
            $updateData['nombre'] = $validated['nombre'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (isset($validated['puntuacion'])) {
            $updateData['puntuacion'] = $validated['puntuacion'];
        }

        if (isset($validated['plantilla'])) {
            $updateData['plantilla'] = (bool) $validated['plantilla'];
        }

        if (isset($validated['slug'])) {
            $updateData['slug'] = $validated['slug'];
        }

        if (isset($validated['final'])) {
            $updateData['final'] = (bool) $validated['final'];
        }

        if (isset($validated['siguiente_id'])) {
            $updateData['siguiente_id'] = $validated['siguiente_id'];
        }

        if (isset($validated['auto_avance'])) {
            $updateData['auto_avance'] = (bool) $validated['auto_avance'];
        }

        if (isset($validated['qualification_id'])) {
            $updateData['qualification_id'] = $validated['qualification_id'];
        }

        if (isset($validated['orden'])) {
            $updateData['orden'] = $validated['orden'];
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

        $actividad->update($updateData);

        return Response::json([
            'id' => $actividad->id,
            'unidad_id' => (int) $actividad->unidad_id,
            'nombre' => $actividad->nombre,
            'descripcion' => $actividad->descripcion,
            'puntuacion' => (float) $actividad->puntuacion,
            'plantilla' => (bool) $actividad->plantilla,
            'slug' => $actividad->slug,
            'final' => (bool) $actividad->final,
            'siguiente_id' => $actividad->siguiente_id ? (int) $actividad->siguiente_id : null,
            'auto_avance' => (bool) $actividad->auto_avance,
            'qualification_id' => $actividad->qualification_id ? (int) $actividad->qualification_id : null,
            'orden' => (int) $actividad->orden,
            'fecha_disponibilidad' => $actividad->fecha_disponibilidad ? $actividad->fecha_disponibilidad : null,
            'fecha_entrega' => $actividad->fecha_entrega ? $actividad->fecha_entrega : null,
            'fecha_limite' => $actividad->fecha_limite ? $actividad->fecha_limite : null,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required(),
            'nombre' => $schema->string(),
            'descripcion' => $schema->string(),
            'puntuacion' => $schema->number(),
            'plantilla' => $schema->boolean(),
            'slug' => $schema->string(),
            'final' => $schema->boolean(),
            'siguiente_id' => $schema->integer(),
            'auto_avance' => $schema->boolean(),
            'qualification_id' => $schema->integer(),
            'orden' => $schema->integer(),
            'fecha_disponibilidad' => $schema->string(),
            'fecha_entrega' => $schema->string(),
            'fecha_limite' => $schema->string(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'unidad_id' => $schema->integer(),
            'nombre' => $schema->string(),
            'descripcion' => $schema->string(),
            'puntuacion' => $schema->number(),
            'plantilla' => $schema->boolean(),
            'slug' => $schema->string(),
            'final' => $schema->boolean(),
            'siguiente_id' => $schema->integer(),
            'auto_avance' => $schema->boolean(),
            'qualification_id' => $schema->integer(),
            'orden' => $schema->integer(),
            'fecha_disponibilidad' => $schema->string(),
            'fecha_entrega' => $schema->string(),
            'fecha_limite' => $schema->string(),
        ];
    }
}
