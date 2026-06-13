<?php

namespace App\Mcp\Tools\Category;

use App\Models\Category;
use App\Models\Period;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear una nueva categoría. Requiere period_id (ID del periodo) y nombre. Devuelve los datos de la categoría creada.')]
class CreateCategory extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear categorías.');
        }

        $validated = $request->validate([
            'period_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        // Verify period exists
        $periodExists = Period::where('id', $validated['period_id'])->exists();

        if (!$periodExists) {
            return Response::error("No se encontró el periodo con id {$validated['period_id']}.");
        }

        $category = Category::create([
            'period_id' => $validated['period_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return Response::structured([
            'id' => $category->id,
            'period_id' => (int)$category->period_id,
            'name' => $category->name,
            'slug' => $category->slug,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'period_id' => $schema->integer()->required(),
            'name' => $schema->string()->required(),
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
