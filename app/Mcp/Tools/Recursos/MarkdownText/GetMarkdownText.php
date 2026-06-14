<?php

namespace App\Mcp\Tools\Recursos\MarkdownText;

use App\Models\MarkdownText;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un texto Markdown por su ID. Devuelve id, curso_id, titulo y descripcion.')]
#[IsReadOnly]
class GetMarkdownText extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $markdownText = MarkdownText::find($validated['id']);

        if (!$markdownText) {
            return Response::error("No se encontró el texto Markdown con id {$validated['id']}.");
        }

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
            'id' => $schema->integer()->required(),
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
