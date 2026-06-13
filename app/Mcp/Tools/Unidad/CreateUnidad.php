<?php

namespace App\Mcp\Tools\Unidad;

use App\Models\Curso;
use App\Models\Unidad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear una nueva unidad. Requiere curso_id, codigo y nombre. Devuelve los datos de la unidad creada.')]
class CreateUnidad extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear unidades.');
        }

        $validated = $request->validate([
            'curso_id' => ['required', 'integer'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer'],
            'visible' => ['boolean'],
            'fecha_disponibilidad' => ['nullable', 'date'],
            'fecha_entrega' => ['nullable', 'date'],
            'fecha_limite' => ['nullable', 'date'],
            'minimo_entregadas' => ['nullable', 'integer'],
        ]);

        // Verify course exists
        $courseExists = Curso::where('id', $validated['curso_id'])->exists();

        if (!$courseExists) {
            return Response::error("No se encontró el curso con id {$validated['curso_id']}.");
        }

        $unidad = Unidad::create([
            'curso_id' => $validated['curso_id'],
            'codigo' => $validated['codigo'] ?? null,
            'nombre' => $validated['nombre'],
            'slug' => Str::slug($validated['nombre']),
            'descripcion' => $validated['descripcion'] ?? null,
            'orden' => $validated['orden'] ?? null,
            'visible' => (bool) ($validated['visible'] ?? false),
            'fecha_disponibilidad' => $validated['fecha_disponibilidad'] ?? null,
            'fecha_entrega' => $validated['fecha_entrega'] ?? null,
            'fecha_limite' => $validated['fecha_limite'] ?? null,
            'minimo_entregadas' => $validated['minimo_entregadas'] ?? null,
        ]);

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
            'curso_id' => $schema->integer()->required(),
            'codigo' => $schema->string(),
            'nombre' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'orden' => $schema->integer(),
            'visible' => $schema->boolean(),
            'fecha_disponibilidad' => $schema->string(),
            'fecha_entrega' => $schema->string(),
            'fecha_limite' => $schema->string(),
            'minimo_entregadas' => $schema->integer(),
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
