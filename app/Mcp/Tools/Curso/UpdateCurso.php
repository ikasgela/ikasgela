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

#[Description('Actualizar un curso existente por su ID. Campos opcionales: category_id, nombre, descripcion, slug, gitea_organization, tags, matricula_abierta, qualification_id, max_simultaneas, plazo_actividad, fecha_inicio, fecha_fin, minimo_entregadas, minimo_competencias, minimo_examenes, minimo_examenes_finales, examenes_obligatorios, maximo_recuperable_examenes_finales, progreso_visible, silence_notifications, normalizar_nota, ajuste_proporcional_nota, mostrar_calificaciones. Devuelve los datos actualizados.')]
class UpdateCurso extends Tool
{
    public function handle(Request $request): Response
    {
        // Check authentication and admin role
        $user = $request->user();

        if (!$user) {
            return Response::error('No autenticado. Se requiere sesión activa.');
        }

        if (!$user->hasAnyRole(['admin'])) {
            return Response::error('Se requiere rol de administrador para actualizar cursos.');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255'],
            'gitea_organization' => ['nullable', 'string', 'max:40'],
            'tags' => ['nullable', 'string'],
            'matricula_abierta' => ['boolean'],
            'qualification_id' => ['nullable', 'integer'],
            'max_simultaneas' => ['nullable', 'integer'],
            'plazo_actividad' => ['nullable', 'integer'],
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

        $curso = Curso::find($validated['id']);

        if (!$curso) {
            return Response::error("No se encontró el curso con id {$validated['id']}.");
        }

        // Verify category if provided
        if (isset($validated['category_id'])) {
            $categoryExists = Category::where('id', $validated['category_id'])->exists();

            if (!$categoryExists) {
                return Response::error("No se encontró la categoría con id {$validated['category_id']}.");
            }
        }

        $updateData = [];

        if (isset($validated['category_id'])) {
            $updateData['category_id'] = $validated['category_id'];
        }

        if (isset($validated['nombre'])) {
            $updateData['nombre'] = $validated['nombre'];
        }

        if (isset($validated['descripcion'])) {
            $updateData['descripcion'] = $validated['descripcion'];
        }

        if (isset($validated['slug']) && strlen($validated['slug']) > 0) {
            $updateData['slug'] = Str::slug($validated['slug']);
        } elseif (isset($validated['nombre'])) {
            $updateData['slug'] = Str::slug($validated['nombre']);
        }

        if (isset($validated['gitea_organization']) && strlen($validated['gitea_organization']) > 0) {
            $updateData['gitea_organization'] = Str::slug($validated['gitea_organization']);
        } elseif (isset($validated['nombre'])) {
            $updateData['gitea_organization'] = Str::limit(Str::slug($validated['nombre']), 40, '');
        }

        if (isset($validated['tags'])) {
            $updateData['tags'] = $validated['tags'];
        }

        if (array_key_exists('matricula_abierta', $validated)) {
            $updateData['matricula_abierta'] = (bool) $validated['matricula_abierta'];
        }

        if (isset($validated['qualification_id'])) {
            $updateData['qualification_id'] = $validated['qualification_id'];
        }

        if (isset($validated['max_simultaneas'])) {
            $updateData['max_simultaneas'] = $validated['max_simultaneas'];
        }

        if (isset($validated['plazo_actividad'])) {
            $updateData['plazo_actividad'] = $validated['plazo_actividad'];
        }

        if (isset($validated['fecha_inicio'])) {
            $updateData['fecha_inicio'] = $validated['fecha_inicio'];
        }

        if (isset($validated['fecha_fin'])) {
            $updateData['fecha_fin'] = $validated['fecha_fin'];
        }

        if (isset($validated['minimo_entregadas'])) {
            $updateData['minimo_entregadas'] = $validated['minimo_entregadas'];
        }

        if (isset($validated['minimo_competencias'])) {
            $updateData['minimo_competencias'] = $validated['minimo_competencias'];
        }

        if (isset($validated['minimo_examenes'])) {
            $updateData['minimo_examenes'] = $validated['minimo_examenes'];
        }

        if (isset($validated['minimo_examenes_finales'])) {
            $updateData['minimo_examenes_finales'] = $validated['minimo_examenes_finales'];
        }

        if (array_key_exists('examenes_obligatorios', $validated)) {
            $updateData['examenes_obligatorios'] = (bool) $validated['examenes_obligatorios'];
        }

        if (isset($validated['maximo_recuperable_examenes_finales'])) {
            $updateData['maximo_recuperable_examenes_finales'] = $validated['maximo_recuperable_examenes_finales'];
        }

        if (array_key_exists('progreso_visible', $validated)) {
            $updateData['progreso_visible'] = (bool) $validated['progreso_visible'];
        }

        if (array_key_exists('silence_notifications', $validated)) {
            $updateData['silence_notifications'] = (bool) $validated['silence_notifications'];
        }

        if (array_key_exists('normalizar_nota', $validated)) {
            $updateData['normalizar_nota'] = (bool) $validated['normalizar_nota'];
        }

        if (isset($validated['ajuste_proporcional_nota'])) {
            $updateData['ajuste_proporcional_nota'] = $validated['ajuste_proporcional_nota'];
        }

        if (array_key_exists('mostrar_calificaciones', $validated)) {
            $updateData['mostrar_calificaciones'] = (bool) $validated['mostrar_calificaciones'];
        }

        $curso->update($updateData);

        return Response::structured([
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
            'category_id' => $schema->integer(),
            'nombre' => $schema->string(),
            'descripcion' => $schema->string(),
            'slug' => $schema->string(),
            'gitea_organization' => $schema->string(),
            'tags' => $schema->string(),
            'matricula_abierta' => $schema->boolean(),
            'qualification_id' => $schema->integer(),
            'max_simultaneas' => $schema->integer(),
            'plazo_actividad' => $schema->integer(),
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
