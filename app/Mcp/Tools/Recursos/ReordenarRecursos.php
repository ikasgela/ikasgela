<?php

namespace App\Mcp\Tools\Recursos;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Reordenar recursos de una actividad. Requiere actividad_id, tipo_recurso y un array de recursos ordenados por id. Devuelve los nuevos órdenes.')]
class ReordenarRecursos extends Tool
{
    /**
     * Mapeo de tipo_recurso (string) a relación y tabla pivot.
     * Las tablas pivot siguen la convención de Laravel: actividad_{recurso_singular}.
     */
    private const TIPO_RECURSO_MAP = [
        'cuestionario'      => ['relation' => 'cuestionarios', 'pivot_table' => 'actividad_cuestionario', 'resource_column' => 'cuestionario_id'],
        'file_resource'     => ['relation' => 'file_resources', 'pivot_table' => 'actividad_file_resource', 'resource_column' => 'file_resource_id'],
        'link_collection'   => ['relation' => 'link_collections', 'pivot_table' => 'actividad_link_collection', 'resource_column' => 'link_collection_id'],
        'markdown_text'     => ['relation' => 'markdown_texts', 'pivot_table' => 'actividad_markdown_text', 'resource_column' => 'markdown_text_id'],
        'rubrica'           => ['relation' => 'rubrics', 'pivot_table' => 'actividad_rubric', 'resource_column' => 'rubric_id'],
        'selector'          => ['relation' => 'selectors', 'pivot_table' => 'actividad_selector', 'resource_column' => 'selector_id'],
        'test_result'       => ['relation' => 'test_results', 'pivot_table' => 'actividad_test_result', 'resource_column' => 'test_result_id'],
        'youtube_video'     => ['relation' => 'youtube_videos', 'pivot_table' => 'actividad_youtube_video', 'resource_column' => 'youtube_video_id'],
        'intellij_project'  => ['relation' => 'intellij_projects', 'pivot_table' => 'actividad_intellij_project', 'resource_column' => 'intellij_project_id'],
    ];

    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para reordenar recursos.');
        }

        $validated = $request->validate([
            'actividad_id' => ['required', 'integer'],
            'tipo_recurso' => ['required', 'string'],
            'recursos_ordenados' => ['required', 'array'],
            'recursos_ordenados.*.id' => ['required', 'integer'],
        ]);

        // Validate tipo_recurso
        if (!array_key_exists($validated['tipo_recurso'], self::TIPO_RECURSO_MAP)) {
            return Response::error("Tipo de recurso no válido: {$validated['tipo_recurso']}. Tipos válidos: " . implode(', ', array_keys(self::TIPO_RECURSO_MAP)));
        }

        $mapping = self::TIPO_RECURSO_MAP[$validated['tipo_recurso']];
        $relationName = $mapping['relation'];
        $resourceColumn = $mapping['resource_column'];

        // Verify actividad exists
        $actividadExists = Actividad::where('id', $validated['actividad_id'])->exists();

        if (!$actividadExists) {
            return Response::error("No se encontró la actividad con id {$validated['actividad_id']}.");
        }

        $actividad = Actividad::find($validated['actividad_id']);
        $pivotTable = $mapping['pivot_table'];

        // Get current resources for this tipo_recurso in this actividad
        $currentResources = \DB::table($pivotTable)
            ->where('actividad_id', $validated['actividad_id'])
            ->get();

        // Build a map of current resources by resource_id for preserving visibility settings
        $currentMap = [];
        foreach ($currentResources as $pivot) {
            $currentMap[$pivot->{$resourceColumn}] = [
                'titulo_visible' => (bool) $pivot->titulo_visible,
                'descripcion_visible' => (bool) $pivot->descripcion_visible,
            ];
        }

        // Detach all resources of this tipo for this actividad
        $actividad->$relationName()->detach();

        // Re-attach with new order, preserving visibility settings
        $nuevosOrdenes = [];
        foreach ($validated['recursos_ordenados'] as $index => $recurso) {
            $resourceId = (int) $recurso['id'];

            // Preserve visibility settings from current pivot
            $tituloVisible = isset($currentMap[$resourceId]) ? (bool) $currentMap[$resourceId]['titulo_visible'] : true;
            $descripcionVisible = isset($currentMap[$resourceId]) ? (bool) $currentMap[$resourceId]['descripcion_visible'] : true;

            $actividad->$relationName()->attach($resourceId, [
                'orden' => $index + 1,
                'titulo_visible' => $tituloVisible,
                'descripcion_visible' => $descripcionVisible,
            ]);

            $nuevosOrdenes[] = [
                'recurso_id' => $resourceId,
                'orden' => $index + 1,
            ];
        }

        return Response::json([
            'actividad_id' => (int) $validated['actividad_id'],
            'tipo_recurso' => $validated['tipo_recurso'],
            'recursos_ordenados' => $nuevosOrdenes,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'actividad_id' => $schema->integer()->required(),
            'tipo_recurso' => $schema->string()->required(),
            'recursos_ordenados' => $schema->array(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'actividad_id' => $schema->integer(),
            'tipo_recurso' => $schema->string(),
            'recursos_ordenados' => $schema->array(),
        ];
    }
}
