<?php

namespace App\Mcp\Tools\Unidad;

use App\Models\Unidad;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Listar todas las unidades de un curso. Devuelve id, curso_id, codigo, nombre y descripcion de cada unidad.')]
#[IsReadOnly]
class ListUnidades extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'curso_id' => ['required', 'integer'],
        ]);

        $unidades = Unidad::query()
            ->where('curso_id', $validated['curso_id'])
            ->orderBy('orden')
            ->get(['id', 'curso_id', 'codigo', 'nombre', 'descripcion'])
            ->map(fn($u) => [
                'id' => $u->id,
                'curso_id' => (int) $u->curso_id,
                'codigo' => $u->codigo,
                'nombre' => $u->nombre,
                'descripcion' => $u->descripcion,
            ]);

        return Response::json($unidades->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'curso_id' => $schema->integer()->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'unidades' => $schema->array(),
        ];
    }
}
