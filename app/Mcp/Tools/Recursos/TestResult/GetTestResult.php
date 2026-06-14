<?php

namespace App\Mcp\Tools\Recursos\TestResult;

use App\Models\TestResult;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Obtener los detalles de un test result por su ID. Devuelve id, curso_id, titulo, descripcion .')]
#[IsReadOnly]
class GetTestResult extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $testResult = TestResult::find($validated['id']);

        if (!$testResult) {
            return Response::error("No se encontró el test result con id {$validated['id']}.");
        }

        return Response::json([
            'id' => $testResult->id,
            'curso_id' => (int) $testResult->curso_id,
            'titulo' => $testResult->titulo,
            'descripcion' => $testResult->descripcion,
            
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
