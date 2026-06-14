<?php

namespace App\Mcp\Tools\Recursos\YoutubeVideo;

use App\Models\YoutubeVideo;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los videos de YouTube. Devuelve id, titulo, descripcion, codigo, curso_id cada video.')]
#[IsReadOnly]
class ListYoutubeVideos extends Tool
{
    public function handle(Request $request): Response
    {
        $youtube_videos = YoutubeVideo::query()
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'descripcion', 'codigo', 'curso_id'])
            ->map(fn ($v) => [
                'id' => $v->id,
                'titulo' => $v->titulo,
                'descripcion' => $v->descripcion,
                'codigo' => $v->codigo,
                'curso_id' => (int) $v->curso_id,
                
            ]);

        return Response::json($youtube_videos->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'youtube_videos' => $schema->array(),
        ];
    }
}
