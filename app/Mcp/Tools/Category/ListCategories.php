<?php

namespace App\Mcp\Tools\Category;

use App\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todas las categorías. Devuelve id, period_id, nombre y slug de cada categoría.')]
#[IsReadOnly]
class ListCategories extends Tool
{
    public function handle(Request $request): Response
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'period_id', 'name', 'slug'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'period_id' => (int) $c->period_id,
                'name' => $c->name,
                'slug' => $c->slug,
            ]);

        return Response::json($categories->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'categories' => $schema->array(),
        ];
    }
}
