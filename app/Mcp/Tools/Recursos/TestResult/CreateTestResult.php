<?php

namespace App\Mcp\Tools\Recursos\TestResult;

use App\Models\Curso;
use App\Models\TestResult;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo test result. Requiere curso_id (ID del curso) y titulo. Devuelve los datos del test result creado.')]
#[IsDestructive]
class CreateTestResult extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear test results.');
        }

        $validated = $request->validate([
            'curso_id' => ['required', 'integer'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            
        ]);

        // Verify course exists
        $courseExists = Curso::where('id', $validated['curso_id'])->exists();

        if (!$courseExists) {
            return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
        }

        $testResult = TestResult::create([
            'curso_id' => $validated['curso_id'],
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            
        ]);

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
            'curso_id' => $schema->integer()->required(),
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            
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
