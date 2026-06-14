<?php

namespace App\Mcp\Tools\Recursos\YoutubeVideo;

use App\Models\YoutubeVideo;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo video de YouTube. Requiere titulo y codigo (código del video). Devuelve los datos del video creado.')]
class CreateYoutubeVideo extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear videos de YouTube.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'codigo' => ['required', 'string', 'max:255'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        $youtube_video = YoutubeVideo::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'codigo' => $validated['codigo'],
            
            'curso_id' => $validated['curso_id'] ?? null,
        ]);

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
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'codigo' => $schema->string()->required(),
            
            'curso_id' => $schema->integer(),
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
