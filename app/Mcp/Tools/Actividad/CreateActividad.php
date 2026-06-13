<?php

namespace App\Mcp\Tools\Actividad;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear una nueva actividad. Requiere unidad_id, nombre y orden. Campos opcionales: descripcion, puntuacion, plantilla, slug, final, siguiente_id, auto_avance, qualification_id, fecha_disponibilidad, fecha_entrega, fecha_limite. Solo administradores pueden ejecutar esta acción.')]
class CreateActividad extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear actividades.');
        }

        $validated = $request->validate([
            'unidad_id' => ['required', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
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

        $actividad = Actividad::create([
            'unidad_id' => $validated['unidad_id'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'puntuacion' => $validated['puntuacion'] ?? 0,
            'plantilla' => (bool) ($validated['plantilla'] ?? false),
            'slug' => $validated['slug'] ?? null,
            'final' => (bool) ($validated['final'] ?? false),
            'siguiente_id' => $validated['siguiente_id'] ?? null,
            'auto_avance' => (bool) ($validated['auto_avance'] ?? false),
            'qualification_id' => $validated['qualification_id'] ?? null,
            'orden' => $validated['orden'] ?? 0,
            'fecha_disponibilidad' => $validated['fecha_disponibilidad'] ?? null,
            'fecha_entrega' => $validated['fecha_entrega'] ?? null,
            'fecha_limite' => $validated['fecha_limite'] ?? null,
        ]);

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
            'unidad_id' => $schema->integer()->required(),
            'nombre' => $schema->string()->required(),
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
