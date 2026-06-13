<?php

namespace App\Mcp\Tools\Curso;

use App\Models\Category;
use App\Models\Curso;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Crear un nuevo curso. Requiere category_id (ID de la categoría) y nombre. Devuelve los datos del curso creado.')]
class CreateCurso extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para crear cursos.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'plazo_actividad' => ['required', 'integer'],
            'gitea_organization' => ['nullable', 'string', 'max:40'],
            'tags' => ['nullable', 'string'],
            'matricula_abierta' => ['boolean'],
            'qualification_id' => ['nullable', 'integer'],
            'max_simultaneas' => ['nullable', 'integer'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date'],
            'minimo_entregadas' => ['nullable', 'integer'],
            'minimo_competencias' => ['nullable', 'integer'],
            'minimo_examenes' => ['nullable', 'integer'],
            'minimo_examenes_finales' => ['nullable', 'integer'],
            'examenes_obligatorios' => ['boolean'],
            'maximo_recuperable_examenes_finales' => ['nullable', 'integer'],
            'progreso_visible' => ['boolean'],
            'silence_notifications' => ['boolean'],
            'normalizar_nota' => ['boolean'],
            'ajuste_proporcional_nota' => ['nullable', 'number'],
            'mostrar_calificaciones' => ['boolean'],
        ]);

        // Verify category exists
        $categoryExists = Category::where('id', $validated['category_id'])->exists();

        if (!$categoryExists) {
            return Response::error("No se encontró la categoría con id {$validated['category_id']}.");
        }

        $curso = Curso::create([
            'category_id' => $validated['category_id'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'slug' => Str::length($validated['slug'] ?? '') > 0
                ? Str::slug($validated['slug'])
                : Str::slug($validated['nombre']),
            'gitea_organization' => Str::length($validated['gitea_organization'] ?? '') > 0
                ? Str::slug($validated['gitea_organization'])
                : Str::limit(Str::slug($validated['nombre']), 40, ''),
            'tags' => $validated['tags'] ?? null,
            'matricula_abierta' => (bool) ($validated['matricula_abierta'] ?? false),
            'qualification_id' => $validated['qualification_id'] ?? null,
            'max_simultaneas' => $validated['max_simultaneas'] ?? null,
            'plazo_actividad' => $validated['plazo_actividad'],
            'fecha_inicio' => $validated['fecha_inicio'] ?? null,
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'minimo_entregadas' => $validated['minimo_entregadas'] ?? null,
            'minimo_competencias' => $validated['minimo_competencias'] ?? null,
            'minimo_examenes' => $validated['minimo_examenes'] ?? null,
            'minimo_examenes_finales' => $validated['minimo_examenes_finales'] ?? null,
            'examenes_obligatorios' => (bool) ($validated['examenes_obligatorios'] ?? false),
            'maximo_recuperable_examenes_finales' => $validated['maximo_recuperable_examenes_finales'] ?? null,
            'progreso_visible' => (bool) ($validated['progreso_visible'] ?? false),
            'silence_notifications' => (bool) ($validated['silence_notifications'] ?? false),
            'normalizar_nota' => (bool) ($validated['normalizar_nota'] ?? false),
            'ajuste_proporcional_nota' => $validated['ajuste_proporcional_nota'] ?? null,
            'mostrar_calificaciones' => (bool) ($validated['mostrar_calificaciones'] ?? false),
        ]);

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
            'category_id' => $schema->integer()->required(),
            'nombre' => $schema->string()->required(),
            'descripcion' => $schema->string(),
            'slug' => $schema->string(),
            'gitea_organization' => $schema->string(),
            'tags' => $schema->string(),
            'matricula_abierta' => $schema->boolean(),
            'qualification_id' => $schema->integer(),
            'max_simultaneas' => $schema->integer(),
            'plazo_actividad' => $schema->integer()->required(),
            'fecha_inicio' => $schema->string(),
            'fecha_fin' => $schema->string(),
            'minimo_entregadas' => $schema->integer(),
            'minimo_competencias' => $schema->integer(),
            'minimo_examenes' => $schema->integer(),
            'minimo_examenes_finales' => $schema->integer(),
            'examenes_obligatorios' => $schema->boolean(),
            'maximo_recuperable_examenes_finales' => $schema->integer(),
            'progreso_visible' => $schema->boolean(),
            'silence_notifications' => $schema->boolean(),
            'normalizar_nota' => $schema->boolean(),
            'ajuste_proporcional_nota' => $schema->number(),
            'mostrar_calificaciones' => $schema->boolean(),
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
