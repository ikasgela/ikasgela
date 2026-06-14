<?php

namespace App\Mcp\Tools\Recursos\MarkdownText;

use App\Models\MarkdownText;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los textos Markdown. Devuelve id, curso_id, titulo y descripcion de cada texto.')]
#[IsReadOnly]
class ListMarkdownTexts extends Tool
{
    public function handle(Request $request): Response
    {
        $markdownTexts = MarkdownText::query()
            ->orderBy('titulo')
            ->get(['id', 'curso_id', 'titulo', 'descripcion'])
            ->map(fn($r) => [
                'id' => $r->id,
                'curso_id' => (int) $r->curso_id,
                'titulo' => $r->titulo,
                'descripcion' => $r->descripcion,
            ]);

        return Response::json($markdownTexts->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'markdown_texts' => $schema->array(),
        ];
    }
}
