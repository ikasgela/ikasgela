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

#[Description('Actualizar una categoría existente por su ID. Campos opcionales: period_id, name, slug. Devuelve los datos actualizados.')]
class UpdateCategory extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar categorías.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'period_id' => ['nullable', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $category = Category::find($validated['id']);

        if (!$category) {
            return Response::error("No se encontró la categoría con id {$validated['id']}.");
        }

        $updateData = [];

        if (isset($validated['period_id'])) {
            $periodExists = Period::where('id', $validated['period_id'])->exists();

            if (!$periodExists) {
                return Response::error("No se encontró el periodo con id {$validated['period_id']}.");
            }

            $updateData['period_id'] = $validated['period_id'];
        }

        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }

        if (isset($validated['slug']) && strlen($validated['slug']) > 0) {
            $updateData['slug'] = Str::slug($validated['slug']);
        } elseif (isset($validated['name'])) {
            $updateData['slug'] = Str::slug($validated['name']);
        }

        $category->update($updateData);

        return Response::structured([
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
            'period_id' => $schema->integer(),
            'name' => $schema->string(),
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
