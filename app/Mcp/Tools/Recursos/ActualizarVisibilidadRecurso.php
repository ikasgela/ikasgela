<?php

namespace App\Mcp\Tools\Recursos;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar la visibilidad de un recurso en una actividad. Requiere actividad_id, recurso_id, tipo_recurso y los flags de visibilidad. Devuelve la configuración actualizada.')]
class ActualizarVisibilidadRecurso extends Tool
{
    /**
     * Mapeo de tipo_recurso (string) a modelo, relación y tabla pivot.
     * Las tablas pivot siguen la convención de Laravel: actividad_{recurso_singular}.
     */
    private const TIPO_RECURSO_MAP = [
        'cuestionario'      => ['model' => \App\Models\Cuestionario::class, 'relation' => 'cuestionarios', 'pivot_table' => 'actividad_cuestionario', 'resource_column' => 'cuestionario_id'],
        'file_resource'     => ['model' => \App\Models\FileResource::class, 'relation' => 'file_resources', 'pivot_table' => 'actividad_file_resource', 'resource_column' => 'file_resource_id'],
        'link_collection'   => ['model' => \App\Models\LinkCollection::class, 'relation' => 'link_collections', 'pivot_table' => 'actividad_link_collection', 'resource_column' => 'link_collection_id'],
        'markdown_text'     => ['model' => \App\Models\MarkdownText::class, 'relation' => 'markdown_texts', 'pivot_table' => 'actividad_markdown_text', 'resource_column' => 'markdown_text_id'],
        'rubrica'           => ['model' => \App\Models\Rubric::class, 'relation' => 'rubrics', 'pivot_table' => 'actividad_rubric', 'resource_column' => 'rubric_id'],
        'selector'          => ['model' => \App\Models\Selector::class, 'relation' => 'selectors', 'pivot_table' => 'actividad_selector', 'resource_column' => 'selector_id'],
        'test_result'       => ['model' => \App\Models\TestResult::class, 'relation' => 'test_results', 'pivot_table' => 'actividad_test_result', 'resource_column' => 'test_result_id'],
        'youtube_video'     => ['model' => \App\Models\YoutubeVideo::class, 'relation' => 'youtube_videos', 'pivot_table' => 'actividad_youtube_video', 'resource_column' => 'youtube_video_id'],
        'intellij_project'  => ['model' => \App\Models\IntellijProject::class, 'relation' => 'intellij_projects', 'pivot_table' => 'actividad_intellij_project', 'resource_column' => 'intellij_project_id'],
    ];

    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar visibilidad.');
        }

        $validated = $request->validate([
            'actividad_id' => ['required', 'integer'],
            'recurso_id' => ['required', 'integer'],
            'tipo_recurso' => ['required', 'string'],
            'titulo_visible' => ['nullable', 'boolean'],
            'descripcion_visible' => ['nullable', 'boolean'],
        ]);

        // At least one visibility flag must be provided
        if (!isset($validated['titulo_visible']) && !isset($validated['descripcion_visible'])) {
            return Response::error('Se debe proporcionar al menos uno de: titulo_visible, descripcion_visible.');
        }

        // Validate tipo_recurso
        if (!array_key_exists($validated['tipo_recurso'], self::TIPO_RECURSO_MAP)) {
            return Response::error("Tipo de recurso no válido: {$validated['tipo_recurso']}. Tipos válidos: " . implode(', ', array_keys(self::TIPO_RECURSO_MAP)));
        }

        $mapping = self::TIPO_RECURSO_MAP[$validated['tipo_recurso']];
        $relationName = $mapping['relation'];
        $pivotTable = $mapping['pivot_table'];
        $resourceColumn = $mapping['resource_column'];

        // Verify actividad exists
        $actividadExists = Actividad::where('id', $validated['actividad_id'])->exists();

        if (!$actividadExists) {
            return Response::error("No se encontró la actividad con id {$validated['actividad_id']}.");
        }

        // Verify resource exists (only for types that have a model entry)
        if (array_key_exists('model', $mapping)) {
            $modelClass = $mapping['model'];
            $resourceExists = (new $modelClass())->where('id', $validated['recurso_id'])->exists();

            if (!$resourceExists) {
                return Response::error("No se encontró el recurso de tipo '{$validated['tipo_recurso']}' con id {$validated['recurso_id']}.");
            }
        }

        $actividad = Actividad::find($validated['actividad_id']);

        // Check if the resource is associated with this activity
        $pivotRecord = \DB::table($pivotTable)
            ->where('actividad_id', $validated['actividad_id'])
            ->where($resourceColumn, $validated['recurso_id'])
            ->first();

        if (!$pivotRecord) {
            return Response::error("El recurso de tipo '{$validated['tipo_recurso']}' con id {$validated['recurso_id']} no está asociado a la actividad {$validated['actividad_id']}.");
        }

        // Update pivot visibility settings
        $updateData = [];
        if (isset($validated['titulo_visible'])) {
            $updateData['titulo_visible'] = (bool) $validated['titulo_visible'];
        }
        if (isset($validated['descripcion_visible'])) {
            $updateData['descripcion_visible'] = (bool) $validated['descripcion_visible'];
        }

        \DB::table($pivotTable)
            ->where('actividad_id', $validated['actividad_id'])
            ->where($resourceColumn, $validated['recurso_id'])
            ->update($updateData);

        // Get updated pivot data
        $updatedPivot = \DB::table($pivotTable)
            ->where('actividad_id', $validated['actividad_id'])
            ->where($resourceColumn, $validated['recurso_id'])
            ->first();

        return Response::json([
            'actividad_id' => (int) $validated['actividad_id'],
            'recurso_id' => (int) $validated['recurso_id'],
            'tipo_recurso' => $validated['tipo_recurso'],
            'titulo_visible' => (bool) ($updatedPivot->titulo_visible ?? true),
            'descripcion_visible' => (bool) ($updatedPivot->descripcion_visible ?? true),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'actividad_id' => $schema->integer()->required(),
            'recurso_id' => $schema->integer()->required(),
            'tipo_recurso' => $schema->string()->required(),
            'titulo_visible' => $schema->boolean(),
            'descripcion_visible' => $schema->boolean(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'actividad_id' => $schema->integer(),
            'recurso_id' => $schema->integer(),
            'tipo_recurso' => $schema->string(),
            'titulo_visible' => $schema->boolean(),
            'descripcion_visible' => $schema->boolean(),
        ];
    }
}
