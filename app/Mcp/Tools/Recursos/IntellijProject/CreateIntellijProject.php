<?php

namespace App\Mcp\Tools\Recursos\IntellijProject;

use App\Models\Curso;
use App\Models\IntellijProject;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo IntellijProject. Requiere titulo y repositorio (formato usuario/repositorio, no la URL completa del repositorio). El host por defecto es gitea. Devuelve los datos del proyecto creado.')]
class CreateIntellijProject extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear IntellijProjects.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'repositorio' => ['required', 'string', 'max:255'],
            'host' => ['nullable', 'string', 'max:100'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        // Verify curso exists if provided
        if (isset($validated['curso_id'])) {
            $cursoExists = Curso::where('id', $validated['curso_id'])->exists();

            if (!$cursoExists) {
                return Response::error("No se encontró el curso con id {$validated['curso_id']}.\n");
            }
        }

        $project = IntellijProject::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'repositorio' => $validated['repositorio'],
            'host' => $validated['host'] ?? 'gitea',
            
            'curso_id' => $validated['curso_id'] ?? null,
        ]);

        return Response::json([
            'id' => $project->id,
            'titulo' => $project->titulo,
            'descripcion' => $project->descripcion,
            'curso_id' => (int) $project->curso_id,
            
            'repository' => $project->repositorio,
            'host' => $project->host,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'titulo' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'repositorio' => $schema->string()->required()->description('Formato: usuario/repositorio (no la URL completa del repositorio)'),
            'host' => $schema->string(),
            
            'curso_id' => $schema->integer(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'curso_id' => $schema->integer(),
            
            'repository' => $schema->string(),
            'host' => $schema->string()->nullable(),
        ];
    }
}
