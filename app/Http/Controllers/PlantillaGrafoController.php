<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadGrafo;
use App\Models\RuleGroup;
use App\Models\Selector;
use App\Models\Unidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Vista de grafo de las plantillas de actividades.
 *
 * Alternativa visual a `actividades.plantillas` (tabla): cada plantilla es un nodo y
 * cada enlace representa el encadenado `siguiente_id` entre ellas.
 */
class PlantillaGrafoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // La gestión de plantillas de actividades es una funcionalidad de administración
        $this->middleware('role:admin');
    }

    public function index()
    {
        session(['ubicacion' => 'actividades.plantillas.grafo']);

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        return view('actividades.grafo_plantillas', [
            'unidades' => $unidades,
            'datos' => $this->datosGrafo(),
        ]);
    }

    /**
     * Datos del grafo en JSON (nodos, enlaces y textos de la interfaz).
     */
    public function datos(): JsonResponse
    {
        return response()->json($this->datosGrafo());
    }

    /**
     * Filtro por unidad (comparte clave de sesión con la vista de tabla).
     */
    public function filtro(Request $request)
    {
        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        return redirect()->route('actividades.plantillas.grafo');
    }

    /**
     * Guarda la posición (x, y) de los nodos en el canvas.
     */
    public function posiciones(Request $request): JsonResponse
    {
        $request->validate([
            'posiciones' => 'required|array|max:2000',
            'posiciones.*.actividad_id' => 'required|integer',
            'posiciones.*.x' => 'required|numeric',
            'posiciones.*.y' => 'required|numeric',
        ]);

        $ids = $this->plantillasQuery()->pluck('id')->toArray();

        foreach ($request->input('posiciones') as $posicion) {
            $actividad_id = (int) $posicion['actividad_id'];

            // Solo se guardan posiciones de plantillas del curso actual
            if (!in_array($actividad_id, $ids)) {
                continue;
            }

            ActividadGrafo::updateOrCreate(
                ['actividad_id' => $actividad_id],
                [
                    'posicion_x' => (float) $posicion['x'],
                    'posicion_y' => (float) $posicion['y'],
                ]
            );
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Guarda el encadenado entre plantillas (`siguiente_id`).
     *
     * Cada conexión recibida fija (o elimina, si `destino_id` es null) el `siguiente_id`
     * de la plantilla origen.
     */
    public function conexiones(Request $request): JsonResponse
    {
        $request->validate([
            'conexiones' => 'required|array|max:2000',
            'conexiones.*.origen_id' => 'required|integer',
            'conexiones.*.destino_id' => 'nullable|integer',
        ]);

        $ids = $this->plantillasQuery()->pluck('id')->toArray();

        // El destino puede ser una plantilla de otra unidad (no visible con el filtro actual)
        $ids_curso = $this->plantillasQuery(false)->pluck('id')->toArray();

        foreach ($request->input('conexiones') as $conexion) {
            $origen_id = (int) $conexion['origen_id'];
            $destino_id = isset($conexion['destino_id']) && $conexion['destino_id'] !== ''
                ? (int) $conexion['destino_id']
                : null;

            // Solo se conectan plantillas visibles del curso actual
            if (!in_array($origen_id, $ids)) {
                continue;
            }

            if (!is_null($destino_id) && (!in_array($destino_id, $ids_curso) || $destino_id === $origen_id)) {
                continue;
            }

            $actividad = Actividad::find($origen_id);

            $actividad->siguiente_overriden = $actividad->siguiente_id != $destino_id;
            $actividad->siguiente_id = $destino_id;
            $actividad->save();
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Guarda el resultado de las condiciones de los selectores.
     *
     * Cada conexión recibida fija el `resultado` de un grupo de reglas del selector
     * (la actividad que se muestra cuando la condición se cumple). Si `regla_id` es
     * null se crea un grupo de reglas nuevo y si `destino_id` es null se elimina el
     * grupo de reglas (y sus reglas, en cascada).
     */
    public function resultados(Request $request): JsonResponse
    {
        $request->validate([
            'conexiones' => 'required|array|max:2000',
            'conexiones.*.selector_id' => 'required|integer',
            'conexiones.*.regla_id' => 'nullable|integer',
            'conexiones.*.destino_id' => 'nullable|integer',
        ]);

        // Plantillas del curso actual (el destino puede estar oculto por el filtro)
        $plantillas = $this->plantillasQuery(false)->pluck('id')->toArray();

        // Selectores de las plantillas del curso actual
        $selectores = Selector::where('curso_id', setting_usuario('curso_actual'))
            ->whereHas('actividades', function ($query) use ($plantillas) {
                $query->where('actividades.plantilla', true)->whereIn('actividades.id', $plantillas);
            })->get()->keyBy('id');

        $afectados = [];

        foreach ($request->input('conexiones') as $conexion) {
            $selector_id = (int) $conexion['selector_id'];
            $regla_id = isset($conexion['regla_id']) && $conexion['regla_id'] !== ''
                ? (int) $conexion['regla_id']
                : null;
            $destino_id = isset($conexion['destino_id']) && $conexion['destino_id'] !== ''
                ? (int) $conexion['destino_id']
                : null;

            $selector = $selectores->get($selector_id);

            if (is_null($selector)) {
                continue;
            }

            $afectados[$selector_id] = $selector;

            if (is_null($regla_id)) {
                // Nueva condición del selector
                if (is_null($destino_id) || !in_array($destino_id, $plantillas)) {
                    continue;
                }

                RuleGroup::create([
                    'selector_id' => $selector->id,
                    'operador' => 'or',
                    'accion' => 'siguiente',
                    'resultado' => $destino_id,
                ]);

                continue;
            }

            $regla = $selector->rule_groups()->where('id', $regla_id)->first();

            if (is_null($regla)) {
                continue;
            }

            if (is_null($destino_id)) {
                // Se elimina la condición (y sus reglas en cascada)
                $regla->delete();

                continue;
            }

            if (!in_array($destino_id, $plantillas)) {
                continue;
            }

            $regla->update(['resultado' => $destino_id]);
        }

        $reglas = [];

        foreach ($afectados as $selector) {
            foreach ($selector->rule_groups()->get() as $regla) {
                $reglas[] = [
                    'selector_id' => $selector->id,
                    'regla_id' => $regla->id,
                    'destino_id' => $regla->resultado ? (int) $regla->resultado : null,
                ];
            }
        }

        return response()->json(['ok' => true, 'reglas' => $reglas]);
    }

    /**
     * Plantillas del curso actual con los filtros de sesión de la vista de tabla.
     */
    private function plantillasQuery(bool $filtrar = true)
    {
        $query = Actividad::cursoActual()->plantilla()->orderBy('orden')->orderBy('id');

        if ($filtrar && session('profesor_unidad_id_disponibles')) {
            $query = $query->where('unidad_id', session('profesor_unidad_id_disponibles'));
        }

        if ($filtrar && session('profesor_filtro_actividades_etiquetas')) {
            $query = $query->tags(session('tags_actividades', []));
        }

        return $query;
    }

    private function datosGrafo(): array
    {
        $curso_id = setting_usuario('curso_actual');

        $actividades = $this->plantillasQuery()
            ->with([
                'unidad',
                'youtube_videos', 'intellij_projects', 'markdown_texts',
                'file_resources', 'file_uploads', 'link_collections',
                'cuestionarios', 'rubrics', 'test_results',
                'selectors' => fn ($query) => $query->where('curso_id', $curso_id)->with('rule_groups'),
            ])
            ->get();

        $ids = $actividades->pluck('id')->toArray();

        $posiciones = ActividadGrafo::whereIn('actividad_id', $ids)->get()->keyBy('actividad_id');

        $nodos = [];
        $enlaces = [];
        $enlaces_selectores = [];

        foreach ($actividades as $actividad) {
            $posicion = $posiciones->get($actividad->id);

            $selectores = [];

            foreach ($actividad->selectors as $selector) {
                $reglas = [];

                foreach ($selector->rule_groups as $regla) {
                    $reglas[] = [
                        'id' => $regla->id,
                        'resultado_id' => $regla->resultado ? (string) $regla->resultado : null,
                    ];

                    // Solo se dibuja la condición si su resultado es visible
                    if ($regla->resultado && in_array((int) $regla->resultado, $ids)) {
                        $enlaces_selectores[] = [
                            'id' => 's' . $selector->id . '-' . $regla->id,
                            'source' => $this->idSelector($actividad->id, $selector->id),
                            'target' => (string) $regla->resultado,
                            'selector_id' => $selector->id,
                            'regla_id' => $regla->id,
                            'actividad_id' => $actividad->id,
                        ];
                    }
                }

                $selectores[] = [
                    'id' => $selector->id,
                    'titulo' => $selector->titulo,
                    'url_edit' => route('selectors.edit', $selector->id),
                    'reglas' => $reglas,
                ];
            }

            $nodos[] = [
                'id' => (string) $actividad->id,
                'nombre' => $actividad->nombre,
                'slug' => $actividad->slug,
                'unidad' => $actividad->unidad?->nombre,
                'unidad_id' => $actividad->unidad_id,
                'orden' => $actividad->orden,
                'puntuacion' => $actividad->puntuacion,
                'final' => (bool) $actividad->final,
                'auto_avance' => (bool) $actividad->auto_avance,
                'destacada' => (bool) $actividad->destacada,
                'recursos' => $actividad->recursos->count(),
                'etiquetas' => array_values(array_filter($actividad->etiquetas())),
                'siguiente_id' => $actividad->siguiente_id ? (string) $actividad->siguiente_id : null,
                'selectores' => $selectores,
                'url_preview' => route('actividades.preview', $actividad->id),
                'url_edit' => route('actividades.edit', $actividad->id),
                'posicion' => [
                    'x' => $posicion?->posicion_x,
                    'y' => $posicion?->posicion_y,
                ],
            ];

            if ($actividad->siguiente_id && in_array($actividad->siguiente_id, $ids)) {
                $enlaces[] = [
                    'id' => 'e' . $actividad->id . '-' . $actividad->siguiente_id,
                    'source' => (string) $actividad->id,
                    'target' => (string) $actividad->siguiente_id,
                ];
            }
        }

        return [
            'nodos' => $nodos,
            'enlaces' => $enlaces,
            'enlaces_selectores' => $enlaces_selectores,
            'textos' => [
                'organizar' => __('Organize automatically'),
                'recargar' => __('Reload'),
                'recargado' => __('Reloaded'),
                'guardando' => __('Saving...'),
                'guardado' => __('Saved'),
                'error' => __('Error saving changes'),
                'sin_plantillas' => __('There are no activity templates to show'),
                'arrastra' => __('Drag from the right handle to another node to chain them'),
                'reconectar' => __('Drag the end of a link to another node to change the connection'),
                'borrar_enlace' => __('Select a link and press Delete to remove it'),
                'resources' => __('Resources'),
                'final' => __('Final'),
                'auto' => __('Auto'),
                'preview' => __('Preview'),
                'edit' => __('Edit'),
                'sin_conexion' => __('Not connected'),
                'con_conexion' => __('Connected'),
                'selector' => __('Selector'),
                'condicional' => __('Drag from the circle to the activity to show when the condition is met'),
                'borrar_condicion' => __('Deleting a conditional link removes its rule group and its rules'),
                'ayuda' => __('Help'),
                'ocultar_ayuda' => __('Hide help'),
                'mostrar_ayuda' => __('Show help'),
                'minimapa' => __('Mini map of the graph'),
            ],
            'urls' => [
                'datos' => route('actividades.plantillas.grafo.datos'),
                'posiciones' => route('actividades.plantillas.grafo.posiciones'),
                'conexiones' => route('actividades.plantillas.grafo.conexiones'),
                'resultados' => route('actividades.plantillas.grafo.resultados'),
            ],
        ];
    }

    /**
     * Identificador del nodo (círculo) de un selector en el grafo.
     */
    private function idSelector(int $actividad_id, int $selector_id): string
    {
        return 'selector-' . $actividad_id . '-' . $selector_id;
    }
}
