<?php

namespace App\Mcp\Tools\Recursos\TestResult;

use App\Models\TestResult;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Listar todos los test results. Devuelve id, curso_id, titulo, descripcion cada uno.')]
#[IsReadOnly]
class ListTestResults extends Tool
{
    public function handle(Request $request): Response
    {
        $testResults = TestResult::query()
            ->orderBy('titulo')
            ->get(['id', 'curso_id', 'titulo', 'descripcion'])
            ->map(fn($t) => [
                'id' => $t->id,
                'curso_id' => (int) $t->curso_id,
                'titulo' => $t->titulo,
                'descripcion' => $t->descripcion,
                
            ]);

        return Response::json($testResults->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'test_results' => $schema->array(),
        ];
    }
}
