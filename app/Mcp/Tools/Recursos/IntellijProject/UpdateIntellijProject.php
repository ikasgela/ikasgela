<?php

namespace App\Mcp\Tools\Recursos\IntellijProject;

use App\Models\Curso;
use App\Models\IntellijProject;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Actualizar un IntellijProject existente por su ID. Campos opcionales: titulo, descripcion, repositorio (formato usuario/repositorio), host (por defecto gitea), curso_id. Devuelve los datos actualizados.')]
class UpdateIntellijProject extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar IntellijProjects.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'repositorio' => ['nullable', 'string', 'max:255'],
            'host' => ['nullable', 'string', 'max:100'],
            
            'curso_id' => ['nullable', 'integer'],
        ]);

        $project = IntellijProject::find($validated['id']);

        if (!$project) {
            return Response::error("No se encontró el IntellijProject con id {$validated['id']}.\n");
        }

        $updateData = [];

        if (isset($validated['titulo'])) {
            $updateData['titulo'] = $validated['titulo'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (isset($validated['repositorio'])) {
            $updateData['repositorio'] = $validated['repositorio'];
        }

        if (isset($validated['host'])) {
            $updateData['host'] = $validated['host'];
        }

        if (isset($validated['curso_id'])) {
            $cursoExists = Curso::where('id', $validated['curso_id'])->exists();

            if (!$cursoExists) {
                return Response::error("No se encontró el curso con id {$validated['curso_id']}.\n");
            }

            $updateData['curso_id'] = $validated['curso_id'];
        }

        $project->update($updateData);

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
            'id' => $schema->integer()->required(),
            'titulo' => $schema->string(),
            'descripcion' => $schema->string(),
            'repositorio' => $schema->string(),
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
