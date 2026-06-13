<?php

namespace App\Mcp\Tools\Actividad;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de una actividad por su ID. Devuelve id, unidad_id, nombre, descripcion, puntuacion, plantilla, slug, final, siguiente_id, auto_avance, qualification_id, orden, fecha_disponibilidad, fecha_entrega y fecha_limite.')]
#[IsReadOnly]
class GetActividad extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $actividad = Actividad::find($validated['id']);

        if (!$actividad) {
            return Response::error("No se encontró la actividad con id {$validated['id']}.");
        }

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
