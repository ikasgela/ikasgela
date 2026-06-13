<?php

namespace App\Mcp\Tools\Curso;

use App\Models\Curso;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un curso por su ID. Devuelve id, category_id, nombre, slug y matricula_abierta.')]
#[IsReadOnly]
class GetCurso extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $curso = Curso::find($validated['id']);

        if (!$curso) {
            return Response::error("No se encontró el curso con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $curso->id,
            'category_id' => (int) $curso->category_id,
            'nombre' => $curso->nombre,
            'slug' => $curso->slug,
            'matricula_abierta' => (bool) $curso->matricula_abierta,
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
            'category_id' => $schema->integer(),
            'nombre' => $schema->string(),
            'slug' => $schema->string(),
            'matricula_abierta' => $schema->boolean(),
        ];
    }
}
