<?php

namespace App\Mcp\Tools\Recursos\MarkdownText;

use App\Models\Curso;
use App\Models\MarkdownText;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un texto Markdown existente por su ID. Campos opcionales: curso_id, titulo, descripcion, repositorio y archivo. La rama por defecto es master; si se proporciona sin valor explícito, se asume master. Devuelve los datos actualizados.')]
class UpdateMarkdownText extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar textos Markdown.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'curso_id' => ['nullable', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'repositorio' => ['nullable', 'string', 'max:255'],
            'rama' => ['nullable', 'string', 'max:255'],
            'archivo' => ['nullable', 'string', 'max:255'],
            
        ]);

        $markdownText = MarkdownText::find($validated['id']);

        if (!$markdownText) {
            return Response::error("No se encontró el texto Markdown con id {$validated['id']}.");
        }

        // Verify course if provided
        if (isset($validated['curso_id'])) {
            $courseExists = Curso::where('id', $validated['curso_id'])->exists();

            if (!$courseExists) {
                return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
            }
        }

        $updateData = [];

        if (isset($validated['curso_id'])) {
            $updateData['curso_id'] = $validated['curso_id'];
        }

        if (isset($validated['titulo'])) {
            $updateData['titulo'] = $validated['titulo'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (isset($validated['repositorio'])) {
            $updateData['repositorio'] = $validated['repositorio'];
        }

        if (isset($validated['rama'])) {
            $updateData['rama'] = $validated['rama'] ?? 'master';
        }

        if (isset($validated['archivo'])) {
            $updateData['archivo'] = $validated['archivo'];
        }

        $markdownText->update($updateData);

        return Response::json([
            'id' => $markdownText->id,
            'curso_id' => (int) $markdownText->curso_id,
            'titulo' => $markdownText->titulo,
            'descripcion' => $markdownText->descripcion,
            'rama' => $markdownText->rama,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required(),
            'curso_id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'repositorio' => $schema->string(),
            'rama' => $schema->string()->description('Rama del repositorio. Por defecto es master; si el archivo se encuentra en otra rama, especificarla aquí.'),
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
            'rama' => $schema->string(),
        ];
    }
}
