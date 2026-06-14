<?php

namespace App\Mcp\Tools\Recursos\YoutubeVideo;

use App\Models\YoutubeVideo;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un video de YouTube existente por su ID. Campos opcionales: titulo, descripcion, codigo, curso_id. Devuelve los datos actualizados.')]
class UpdateYoutubeVideo extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar videos de YouTube.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'codigo' => ['nullable', 'string', 'max:255'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        $youtube_video = YoutubeVideo::find($validated['id']);

        if (!$youtube_video) {
            return Response::error("No se encontró el video de YouTube con id {$validated['id']}.");
        }

        $updateData = [];

        if (isset($validated['titulo'])) {
            $updateData['titulo'] = $validated['titulo'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (isset($validated['codigo'])) {
            $updateData['codigo'] = $validated['codigo'];
        }

        if (false) {
            
        }

        if (array_key_exists('curso_id', $validated)) {
            $updateData['curso_id'] = $validated['curso_id'];
        }

        $youtube_video->update($updateData);

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
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'codigo' => $schema->string(),
            
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
