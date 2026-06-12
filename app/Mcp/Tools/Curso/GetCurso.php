<?php

namespace App\Mcp\Tools\Curso;

use App\Models\Curso;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un curso por su ID. Devuelve todos los campos principales: id, category_id, nombre, descripcion, slug, fechas, configuración de matrícula y calificaciones.')]
#[IsReadOnly]
class GetCurso extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $curso = Curso::find($validated['id']);

        if (!$curso) {
            return Response::error("No se encontró el curso con id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $curso->id,
            'category_id' => (int) $curso->category_id,
            'nombre' => $curso->nombre,
            'descripcion' => $curso->descripcion,
            'slug' => $curso->slug,
            'gitea_organization' => $curso->gitea_organization,
            'tags' => $curso->tags,
            'matricula_abierta' => (bool) $curso->matricula_abierta,
            'qualification_id' => (int) ($curso->qualification_id ?? 0),
            'max_simultaneas' => (int) ($curso->max_simultaneas ?? 0),
            'plazo_actividad' => (int) ($curso->plazo_actividad ?? 0),
            'fecha_inicio' => $curso->fecha_inicio?->toDateTimeString(),
            'fecha_fin' => $curso->fecha_fin?->toDateTimeString(),
            'minimo_entregadas' => (int) ($curso->minimo_entregadas ?? 0),
            'minimo_competencias' => (int) ($curso->minimo_competencias ?? 0),
            'minimo_examenes' => (int) ($curso->minimo_examenes ?? 0),
            'minimo_examenes_finales' => (int) ($curso->minimo_examenes_finales ?? 0),
            'examenes_obligatorios' => (bool) $curso->examenes_obligatorios,
            'maximo_recuperable_examenes_finales' => (int) ($curso->maximo_recuperable_examenes_finales ?? 0),
            'progreso_visible' => (bool) $curso->progreso_visible,
            'silence_notifications' => (bool) $curso->silence_notifications,
            'normalizar_nota' => (bool) $curso->normalizar_nota,
            'ajuste_proporcional_nota' => (float) ($curso->ajuste_proporcional_nota ?? 0.0),
            'mostrar_calificaciones' => (bool) $curso->mostrar_calificaciones,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
            'required' => ['id'],
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
                'category_id' => ['type' => 'integer'],
                'nombre' => ['type' => 'string'],
                'descripcion' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'gitea_organization' => ['type' => 'string'],
                'tags' => ['type' => 'string'],
                'matricula_abierta' => ['type' => 'boolean'],
                'qualification_id' => ['type' => 'integer'],
                'max_simultaneas' => ['type' => 'integer'],
                'plazo_actividad' => ['type' => 'integer'],
                'fecha_inicio' => ['type' => 'string', 'format' => 'date-time'],
                'fecha_fin' => ['type' => 'string', 'format' => 'date-time'],
                'minimo_entregadas' => ['type' => 'integer'],
                'minimo_competencias' => ['type' => 'integer'],
                'minimo_examenes' => ['type' => 'integer'],
                'minimo_examenes_finales' => ['type' => 'integer'],
                'examenes_obligatorios' => ['type' => 'boolean'],
                'maximo_recuperable_examenes_finales' => ['type' => 'integer'],
                'progreso_visible' => ['type' => 'boolean'],
                'silence_notifications' => ['type' => 'boolean'],
                'normalizar_nota' => ['type' => 'boolean'],
                'ajuste_proporcional_nota' => ['type' => 'number'],
                'mostrar_calificaciones' => ['type' => 'boolean'],
            ],
        ];
    }
}
