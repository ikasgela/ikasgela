<?php

namespace App\Mcp\Tools\Category;

use App\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de una categoría por su ID. Devuelve id, period_id, nombre y slug.')]
#[IsReadOnly]
class GetCategory extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $category = Category::find($validated['id']);

        if (!$category) {
            return Response::error("No se encontró la categoría con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $category->id,
            'period_id' => (int) $category->period_id,
            'name' => $category->name,
            'slug' => $category->slug,
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
            'period_id' => $schema->integer(),
            'name' => $schema->string(),
            'slug' => $schema->string(),
        ];
    }
}
