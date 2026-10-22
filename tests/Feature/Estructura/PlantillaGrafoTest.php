<?php

namespace Tests\Feature\Estructura;

use App\Models\Actividad;
use App\Models\ActividadGrafo;
use App\Models\RuleGroup;
use App\Models\Selector;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class PlantillaGrafoTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testGrafoPlantillas()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $plantilla = $this->crearPlantilla();

        setting_usuario(['curso_actual' => $plantilla->unidad->curso_id]);

        // When
        $response = $this->get(route('actividades.plantillas.grafo'));

        // Then
        $response->assertSuccessful();
        $response->assertViewIs('actividades.grafo_plantillas');
        $response->assertSee($plantilla->nombre);
    }

    public function testNotAdminNotGrafoPlantillas()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->get(route('actividades.plantillas.grafo'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotGrafoPlantillas()
    {
        // When
        $response = $this->get(route('actividades.plantillas.grafo'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDatosGrafoDevuelveNodosYEnlaces()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $destino = $this->crearPlantilla($origen->unidad_id);
        $suelta = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $origen->update(['siguiente_id' => $destino->id]);

        // When
        $response = $this->get(route('actividades.plantillas.grafo.datos'));

        // Then
        $response->assertSuccessful();

        $datos = $response->json();

        $this->assertCount(3, $datos['nodos']);
        $this->assertCount(1, $datos['enlaces']);

        $nodos = collect($datos['nodos'])->keyBy('id');

        $this->assertEquals((string) $destino->id, $nodos[(string) $origen->id]['siguiente_id']);
        $this->assertNull($nodos[(string) $suelta->id]['siguiente_id']);
        $this->assertEquals(
            (string) $destino->id,
            $datos['enlaces'][0]['target']
        );
    }

    public function testGuardarPosiciones()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $plantilla = $this->crearPlantilla();
        setting_usuario(['curso_actual' => $plantilla->unidad->curso_id]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.posiciones'), [
            'posiciones' => [
                ['actividad_id' => $plantilla->id, 'x' => 120.5, 'y' => -40.25],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertDatabaseHas('actividad_grafos', [
            'actividad_id' => $plantilla->id,
            'posicion_x' => 120.5,
            'posicion_y' => -40.25,
        ]);
    }

    public function testGuardarPosicionesDeActividadDeOtroCurso()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $plantilla = $this->crearPlantilla();
        $otra = $this->crearPlantilla();

        setting_usuario(['curso_actual' => $plantilla->unidad->curso_id]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.posiciones'), [
            'posiciones' => [
                ['actividad_id' => $otra->id, 'x' => 10, 'y' => 10],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertDatabaseMissing('actividad_grafos', [
            'actividad_id' => $otra->id,
        ]);
        $this->assertDatabaseMissing('actividad_grafos', [
            'actividad_id' => $plantilla->id,
        ]);
    }

    public function testGuardarConexiones()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $destino = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.conexiones'), [
            'conexiones' => [
                ['origen_id' => $origen->id, 'destino_id' => $destino->id],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertEquals($destino->id, $origen->fresh()->siguiente_id);
    }

    public function testEliminarConexiones()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $destino = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $origen->update(['siguiente_id' => $destino->id]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.conexiones'), [
            'conexiones' => [
                ['origen_id' => $origen->id, 'destino_id' => null],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertNull($origen->fresh()->siguiente_id);
    }

    public function testNoGuardarConexionesConPlantillasDeOtroCurso()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $otra = $this->crearPlantilla();

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.conexiones'), [
            'conexiones' => [
                ['origen_id' => $origen->id, 'destino_id' => $otra->id],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertNull($origen->fresh()->siguiente_id);
    }

    public function testFiltroPorUnidad()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $unidad = Unidad::factory()->create();
        $this->crearPlantilla($unidad->id);

        setting_usuario(['curso_actual' => $unidad->curso_id]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.filtro'), [
            'unidad_id_disponibles' => $unidad->id,
        ]);

        // Then
        $response->assertRedirect(route('actividades.plantillas.grafo'));

        $this->assertEquals($unidad->id, session('profesor_unidad_id_disponibles'));
    }

    public function testRelacionGrafoEnActividad()
    {
        // Given
        $plantilla = $this->crearPlantilla();

        ActividadGrafo::create([
            'actividad_id' => $plantilla->id,
            'posicion_x' => 15,
            'posicion_y' => 25,
        ]);

        // Then
        $this->assertEquals($plantilla->id, $plantilla->grafo->actividad_id);
        $this->assertEquals(15.0, $plantilla->grafo->posicion_x);
    }

    public function testDatosGrafoIncluyenSelectoresYCondiciones()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $destino = $this->crearPlantilla($origen->unidad_id);
        $resultado = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $origen->update(['siguiente_id' => $destino->id]);

        $selector = $this->crearSelector($origen, $resultado);

        // When
        $response = $this->get(route('actividades.plantillas.grafo.datos'));

        // Then
        $response->assertSuccessful();

        $datos = $response->json();

        $nodos = collect($datos['nodos'])->keyBy('id');
        $selectores = $nodos[(string) $origen->id]['selectores'];

        $this->assertCount(1, $selectores);
        $this->assertEquals($selector->id, $selectores[0]['id']);
        $this->assertCount(1, $selectores[0]['reglas']);
        $this->assertEquals((string) $resultado->id, $selectores[0]['reglas'][0]['resultado_id']);

        // La conexión normal sigue existiendo
        $this->assertCount(1, $datos['enlaces']);
        $this->assertEquals((string) $destino->id, $datos['enlaces'][0]['target']);

        // Y se añade la conexión condicional
        $this->assertCount(1, $datos['enlaces_selectores']);
        $this->assertEquals(
            'selector-' . $origen->id . '-' . $selector->id,
            $datos['enlaces_selectores'][0]['source']
        );
        $this->assertEquals((string) $resultado->id, $datos['enlaces_selectores'][0]['target']);
        $this->assertEquals($selector->id, $datos['enlaces_selectores'][0]['selector_id']);
    }

    public function testGuardarResultadoDeCondicion()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $resultado = $this->crearPlantilla($origen->unidad_id);
        $nuevo = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $selector = $this->crearSelector($origen, $resultado);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.resultados'), [
            'conexiones' => [
                [
                    'selector_id' => $selector->id,
                    'regla_id' => $selector->rule_groups()->first()->id,
                    'destino_id' => $nuevo->id,
                ],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertEquals($nuevo->id, (int) $selector->rule_groups()->first()->resultado);
    }

    public function testCrearCondicionNueva()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $resultado = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $selector = Selector::factory()->create(['curso_id' => $origen->unidad->curso_id]);
        $origen->selectors()->attach($selector->id);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.resultados'), [
            'conexiones' => [
                [
                    'selector_id' => $selector->id,
                    'regla_id' => null,
                    'destino_id' => $resultado->id,
                ],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertCount(1, $selector->rule_groups()->get());
        $this->assertEquals(
            $resultado->id,
            (int) $selector->rule_groups()->first()->resultado
        );
    }

    public function testEliminarCondicionBorraGrupoDeReglas()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $resultado = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $selector = $this->crearSelector($origen, $resultado);
        $regla = $selector->rule_groups()->first();

        // When
        $response = $this->post(route('actividades.plantillas.grafo.resultados'), [
            'conexiones' => [
                [
                    'selector_id' => $selector->id,
                    'regla_id' => $regla->id,
                    'destino_id' => null,
                ],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertNull(RuleGroup::find($regla->id));
    }

    public function testNoGuardarCondicionDeSelectorDeOtroCurso()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $resultado = $this->crearPlantilla($origen->unidad_id);

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $selector = Selector::factory()->create();
        $selector->actividades()->attach($origen->id);
        $regla = RuleGroup::factory()->create([
            'selector_id' => $selector->id,
            'resultado' => $resultado->id,
        ]);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.resultados'), [
            'conexiones' => [
                [
                    'selector_id' => $selector->id,
                    'regla_id' => $regla->id,
                    'destino_id' => null,
                ],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertNotNull(RuleGroup::find($regla->id));
    }

    public function testNoGuardarCondicionConDestinoDeOtroCurso()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $origen = $this->crearPlantilla();
        $resultado = $this->crearPlantilla($origen->unidad_id);
        $otra = $this->crearPlantilla();

        setting_usuario(['curso_actual' => $origen->unidad->curso_id]);

        $selector = $this->crearSelector($origen, $resultado);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.resultados'), [
            'conexiones' => [
                [
                    'selector_id' => $selector->id,
                    'regla_id' => $selector->rule_groups()->first()->id,
                    'destino_id' => $otra->id,
                ],
            ],
        ]);

        // Then
        $response->assertSuccessful();

        $this->assertEquals(
            $resultado->id,
            (int) $selector->rule_groups()->first()->resultado
        );
    }

    public function testNotAdminNotGuardarResultados()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->post(route('actividades.plantillas.grafo.resultados'), [
            'conexiones' => [],
        ]);

        // Then
        $response->assertForbidden();
    }

    private function crearSelector(Actividad $actividad, Actividad $resultado)
    {
        $selector = Selector::factory()->create([
            'curso_id' => $actividad->unidad->curso_id,
        ]);

        $actividad->selectors()->attach($selector->id);

        RuleGroup::factory()->create([
            'selector_id' => $selector->id,
            'resultado' => $resultado->id,
        ]);

        return $selector;
    }

    private function crearPlantilla($unidad_id = null)
    {
        return Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => $unidad_id ?? Unidad::factory()->create()->id,
        ]);
    }
}
