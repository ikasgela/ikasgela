<?php

namespace App\Mcp\Tools\Unidad;

use App\Models\Unidad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Obtener los detalles de una unidad por su ID. Devuelve id, curso_id, codigo, nombre y descripcion.')]
#[IsReadOnly]
class GetUnidad extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $unidad = Unidad::find($validated['id']);

        if (!$unidad) {
            return Response::error("No se encontró la unidad con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $unidad->id,
            'curso_id' => (int) $unidad->curso_id,
            'codigo' => $unidad->codigo,
            'nombre' => $unidad->nombre,
            'descripcion' => $unidad->descripcion,
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
            'codigo' => $schema->string(),
            'nombre' => $schema->string(),
            'descripcion' => $schema->string(),
        ];
    }
}
