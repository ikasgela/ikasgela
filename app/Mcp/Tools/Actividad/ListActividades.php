<?php

namespace App\Mcp\Tools\Actividad;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar actividades de una unidad por su ID. Devuelve id, nombre, descripcion, puntuacion, plantilla, orden y fecha_disponibilidad de cada actividad.')]
#[IsReadOnly]
class ListActividades extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'unidad_id' => ['required', 'integer'],
        ]);

        $actividades = Actividad::query()
            ->where('unidad_id', $validated['unidad_id'])
            ->orderBy('orden')
            ->get(['id', 'unidad_id', 'nombre', 'descripcion', 'puntuacion', 'plantilla', 'slug', 'final', 'siguiente_id', 'auto_avance', 'qualification_id', 'orden', 'fecha_disponibilidad', 'fecha_entrega', 'fecha_limite'])
            ->map(fn ($a) => [
                'id' => $a->id,
                'unidad_id' => (int) $a->unidad_id,
                'nombre' => $a->nombre,
                'descripcion' => $a->descripcion,
                'puntuacion' => (float) $a->puntuacion,
                'plantilla' => (bool) $a->plantilla,
                'slug' => $a->slug,
                'final' => (bool) $a->final,
                'siguiente_id' => $a->siguiente_id ? (int) $a->siguiente_id : null,
                'auto_avance' => (bool) $a->auto_avance,
                'qualification_id' => $a->qualification_id ? (int) $a->qualification_id : null,
                'orden' => (int) $a->orden,
                'fecha_disponibilidad' => $a->fecha_disponibilidad ? $a->fecha_disponibilidad : null,
                'fecha_entrega' => $a->fecha_entrega ? $a->fecha_entrega : null,
                'fecha_limite' => $a->fecha_limite ? $a->fecha_limite : null,
            ]);

        return Response::json($actividades->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'unidad_id' => $schema->integer()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'actividades' => $schema->array(),
        ];
    }
}
