<?php

namespace App\Mcp\Tools\Recursos;

use App\Models\Actividad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Desasociar un recurso de una actividad. Requiere actividad_id, recurso_id y tipo_recurso. Devuelve confirmación de la desasociación.')]
class DesasociarRecurso extends Tool
{
    /**
     * Mapeo de tipo_recurso (string) a modelo, relación y tabla pivot.
     * Las tablas pivot siguen la convención de Laravel: actividad_{recurso_singular}.
     */
    private const TIPO_RECURSO_MAP = [
        'cuestionario'      => ['model' => \App\Models\Cuestionario::class, 'relation' => 'cuestionarios', 'pivot_table' => 'actividad_cuestionario'],
        'file_resource'     => ['model' => \App\Models\FileResource::class, 'relation' => 'file_resources', 'pivot_table' => 'actividad_file_resource'],
        'link_collection'   => ['model' => \App\Models\LinkCollection::class, 'relation' => 'link_collections', 'pivot_table' => 'actividad_link_collection'],
        'markdown_text'     => ['model' => \App\Models\MarkdownText::class, 'relation' => 'markdown_texts', 'pivot_table' => 'actividad_markdown_text'],
        'rubrica'           => ['model' => \App\Models\Rubric::class, 'relation' => 'rubrics', 'pivot_table' => 'actividad_rubric'],
        'selector'          => ['model' => \App\Models\Selector::class, 'relation' => 'selectors', 'pivot_table' => 'actividad_selector'],
        'test_result'       => ['model' => \App\Models\TestResult::class, 'relation' => 'test_results', 'pivot_table' => 'actividad_test_result'],
        'youtube_video'     => ['model' => \App\Models\YoutubeVideo::class, 'relation' => 'youtube_videos', 'pivot_table' => 'actividad_youtube_video'],
        'intellij_project'  => ['model' => \App\Models\IntellijProject::class, 'relation' => 'intellij_projects', 'pivot_table' => 'actividad_intellij_project'],
    ];

    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para desasociar recursos.');
        }

        $validated = $request->validate([
            'actividad_id' => ['required', 'integer'],
            'recurso_id' => ['required', 'integer'],
            'tipo_recurso' => ['required', 'string'],
        ]);

        // Validate tipo_recurso
        if (!array_key_exists($validated['tipo_recurso'], self::TIPO_RECURSO_MAP)) {
            return Response::error("Tipo de recurso no válido: {$validated['tipo_recurso']}. Tipos válidos: " . implode(', ', array_keys(self::TIPO_RECURSO_MAP)));
        }

        $mapping = self::TIPO_RECURSO_MAP[$validated['tipo_recurso']];
        $modelClass = $mapping['model'];

        // Verify actividad exists
        $actividadExists = Actividad::where('id', $validated['actividad_id'])->exists();

        if (!$actividadExists) {
            return Response::error("No se encontró la actividad con id {$validated['actividad_id']}.");
        }

        // Verify resource exists
        $resourceExists = (new $modelClass())->where('id', $validated['recurso_id'])->exists();

        if (!$resourceExists) {
            return Response::error("No se encontró el recurso de tipo '{$validated['tipo_recurso']}' con id {$validated['recurso_id']}.");
        }

        $actividad = Actividad::find($validated['actividad_id']);
        $relationName = $mapping['relation'];

        // Detach resource from activity
        $actividad->$relationName()->detach($validated['recurso_id']);

        return Response::json([
            'actividad_id' => (int) $validated['actividad_id'],
            'recurso_id' => (int) $validated['recurso_id'],
            'tipo_recurso' => $validated['tipo_recurso'],
            'desasociado' => true,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'actividad_id' => $schema->integer()->required(),
            'recurso_id' => $schema->integer()->required(),
            'tipo_recurso' => $schema->string()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'actividad_id' => $schema->integer(),
            'recurso_id' => $schema->integer(),
            'tipo_recurso' => $schema->string(),
            'desasociado' => $schema->boolean(),
        ];
    }
}
