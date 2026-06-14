<?php

namespace App\Mcp\Tools\Recursos\YoutubeVideo;

use App\Models\YoutubeVideo;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un video de YouTube por su ID. Devuelve id, titulo, descripcion, codigo, curso_id .')]
#[IsReadOnly]
class GetYoutubeVideo extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $youtube_video = YoutubeVideo::find($validated['id']);

        if (!$youtube_video) {
            return Response::error("No se encontró el video de YouTube con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $youtube_video->id,
            'titulo' => $youtube_video->titulo,
            'descripcion' => $youtube_video->descripcion,
            'codigo' => $youtube_video->codigo,
            'curso_id' => (int) $youtube_video->curso_id,
            
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
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'codigo' => $schema->string(),
            'curso_id' => $schema->integer(),
            
        ];
    }
}
