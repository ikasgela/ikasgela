<?php

namespace App\Mcp\Tools\Recursos\Cuestionario;

use App\Models\Cuestionario;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un cuestionario por su ID. Devuelve id, titulo, descripcion, plantilla, respondido, curso_id .')]
#[IsReadOnly]
class GetCuestionario extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $cuestionario = Cuestionario::find($validated['id']);

        if (!$cuestionario) {
            return Response::error("No se encontró el cuestionario con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $cuestionario->id,
            'titulo' => $cuestionario->titulo,
            'descripcion' => $cuestionario->descripcion,
            'plantilla' => (bool) $cuestionario->plantilla,
            'respondido' => (bool) $cuestionario->respondido,
            'curso_id' => (int) $cuestionario->curso_id,
            
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
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'plantilla' => $schema->boolean(),
            'respondido' => $schema->boolean(),
            'curso_id' => $schema->integer(),
            
        ];
    }
}
