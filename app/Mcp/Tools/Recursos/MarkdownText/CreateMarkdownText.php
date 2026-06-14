<?php

namespace App\Mcp\Tools\Recursos\MarkdownText;

use App\Models\Curso;
use App\Models\MarkdownText;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo texto Markdown. Requiere titulo y curso_id (ID del curso). Devuelve los datos del texto creado.')]
class CreateMarkdownText extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear textos Markdown.');
        }

        $validated = $request->validate([
            'curso_id' => ['required', 'integer'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'repositorio' => ['required', 'string', 'max:255'],
            'rama' => ['nullable', 'string', 'max:255'],
            'archivo' => ['required', 'string', 'max:255'],
            
        ]);

        // Verify course exists
        $courseExists = Curso::where('id', $validated['curso_id'])->exists();

        if (!$courseExists) {
            return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
        }

        $markdownText = MarkdownText::create([
            'curso_id' => $validated['curso_id'],
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'repositorio' => $validated['repositorio'] ?? null,
            'rama' => $validated['rama'] ?? null,
            'archivo' => $validated['archivo'] ?? null,
            
        ]);

        return Response::json([
            'id' => $markdownText->id,
            'curso_id' => (int) $markdownText->curso_id,
            'titulo' => $markdownText->titulo,
            'descripcion' => $markdownText->descripcion,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'curso_id' => $schema->integer()->required(),
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'repositorio' => $schema->string(),
            'rama' => $schema->string(),
            'archivo' => $schema->string(),
            
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
        ];
    }
}
