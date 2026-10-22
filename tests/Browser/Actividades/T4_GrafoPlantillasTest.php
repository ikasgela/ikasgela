<?php

namespace Tests\Browser\Actividades;

use App\Models\Actividad;
use App\Models\ActividadGrafo;
use App\Models\Curso;
use App\Models\Unidad;
use App\Models\User;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

class T4_GrafoPlantillasTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private $plantilla1;
    private $plantilla2;
    private $plantilla3;
    private $unidad;

    public function testGrafoPlantillas()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $this->login($browser);

            // La vista de grafo se carga con los nodos visibles
            $browser->visit(route('actividades.plantillas.grafo'));
            $browser->assertRouteIs('actividades.plantillas.grafo');

            $browser->waitFor('.vue-flow__node-plantilla', 15);
            $browser->assertVisible('.vue-flow__node-plantilla');
            $browser->assertSee('Grafo plantilla uno');
            $browser->assertSee('Grafo plantilla dos');

            $this->assertNoAppErrors($browser);

            // La vista de tabla sigue disponible
            $browser->visit(route('actividades.plantillas'));
            $browser->assertRouteIs('actividades.plantillas');
            $browser->assertSee('Grafo plantilla uno');
        });
    }

    public function testMoverNodoGuardaPosicion()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $this->login($browser);

            $this->abrirGrafo($browser);

            $this->arrastrar(
                $browser,
                '.vue-flow__node[data-id="' . $this->plantilla1->id . '"] .nodo-plantilla__nombre',
                '.vue-flow__node[data-id="' . $this->plantilla2->id . '"] .nodo-plantilla__nombre',
                120,
                90
            );

            $browser->pause(1500);

            $this->assertNotNull(
                ActividadGrafo::where('actividad_id', $this->plantilla1->id)->first(),
                'La posición del nodo no se ha guardado'
            );
        });
    }

    public function testConectarNodosGuardaSiguiente()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $this->login($browser);

            $this->abrirGrafo($browser);

            $this->arrastrar(
                $browser,
                '.vue-flow__node[data-id="' . $this->plantilla1->id . '"] .vue-flow__handle-right',
                '.vue-flow__node[data-id="' . $this->plantilla2->id . '"] .vue-flow__handle-left'
            );

            $browser->waitFor('.vue-flow__edge', 10);
            $browser->pause(1500);

            $this->assertEquals(
                $this->plantilla2->id,
                $this->plantilla1->fresh()->siguiente_id,
                'La conexión entre plantillas no se ha guardado'
            );
        });
    }

    public function testReconectarEnlaceCambiaDestino()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(3);

            $this->login($browser);

            $this->abrirGrafo($browser);

            // Se encadena la primera plantilla con la segunda
            $this->arrastrar(
                $browser,
                '.vue-flow__node[data-id="' . $this->plantilla1->id . '"] .vue-flow__handle-right',
                '.vue-flow__node[data-id="' . $this->plantilla2->id . '"] .vue-flow__handle-left'
            );

            $browser->waitFor('.vue-flow__edgeupdater-target', 10);
            $browser->pause(1000);

            // Se arrastra el final del enlace hasta la tercera plantilla
            $this->arrastrar(
                $browser,
                '.vue-flow__edgeupdater-target',
                '.vue-flow__node[data-id="' . $this->plantilla3->id . '"] .vue-flow__handle-left'
            );

            $browser->pause(1500);

            $this->assertEquals(
                $this->plantilla3->id,
                $this->plantilla1->fresh()->siguiente_id,
                'La conexión no se ha reconectado a la otra plantilla'
            );
        });
    }

    public function testEliminarConexionBorraSiguiente()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $this->plantilla1->update(['siguiente_id' => $this->plantilla2->id]);

            $this->login($browser);

            $this->abrirGrafo($browser);

            $browser->waitFor('.vue-flow__edge', 15);

            // Se selecciona el enlace y se elimina con la tecla Supr
            $browser->script("
                var enlace = document.querySelector('.vue-flow__edge');
                enlace.dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true, view: window}));
            ");
            $browser->pause(500);
            $browser->driver->getKeyboard()->pressKey(WebDriverKeys::DELETE);
            $browser->pause(1500);

            $this->assertNull(
                $this->plantilla1->fresh()->siguiente_id,
                'La conexión entre plantillas no se ha eliminado'
            );
        });
    }

    public function testUsuarioNoAdminNoAccede()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();

            $this->loginAs($browser, 'profesor@ikasgela.com', '12345Abcde', 'profesor.index');

            $browser->visit(route('actividades.plantillas.grafo'));

            $browser->assertSee('403');
            $browser->assertDontSee('.vue-flow__node-plantilla');
        });
    }

    /**
     * Login como administrador.
     */
    private function login(Browser $browser): void
    {
        $browser->logout();

        $this->loginAs($browser, 'admin@ikasgela.com', '12345Abcde', 'admin.index');
    }

    /**
     * Abre el grafo filtrando por la unidad de las plantillas de prueba.
     */
    private function abrirGrafo(Browser $browser): void
    {
        $browser->visit(route('actividades.plantillas.grafo'));

        $browser->select('select[name="unidad_id_disponibles"]', (string) $this->unidad->id);
        $browser->press(__('Filter'));

        $browser->waitFor('.vue-flow__node-plantilla', 15);
        $browser->pause(1500);

        $this->assertNoAppErrors($browser);
    }

    /**
     * Crea plantillas de actividades en el curso actual del administrador.
     */
    private function prepararPlantillas(int $cuantas = 2): void
    {
        $usuario = User::where('email', 'admin@ikasgela.com')->first();
        $curso = Curso::find(setting_usuario('curso_actual', $usuario));

        $this->unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $this->plantilla1 = $this->crearPlantilla('Grafo plantilla uno');
        $this->plantilla2 = $this->crearPlantilla('Grafo plantilla dos');

        if ($cuantas > 2) {
            $this->plantilla3 = $this->crearPlantilla('Grafo plantilla tres');
        }
    }

    /**
     * Crea una plantilla de actividades en la unidad de pruebas.
     */
    private function crearPlantilla(string $nombre): Actividad
    {
        return Actividad::factory()->create([
            'nombre' => $nombre,
            'plantilla' => true,
            'unidad_id' => $this->unidad->id,
        ]);
    }

    /**
     * Arrastra un elemento a otro con eventos de puntero reales.
     */
    private function arrastrar(Browser $browser, string $origen, string $destino, int $dx = 0, int $dy = 0): void
    {
        $inicio = $this->centro($browser, $origen);
        $fin = $this->centro($browser, $destino);

        $fin['x'] += $dx;
        $fin['y'] += $dy;

        $acciones = [
            ['type' => 'pointerMove', 'duration' => 0, 'origin' => 'viewport', 'x' => $inicio['x'], 'y' => $inicio['y']],
            ['type' => 'pointerDown', 'button' => 0],
        ];

        $pasos = 12;

        for ($i = 1; $i <= $pasos; $i++) {
            $acciones[] = [
                'type' => 'pointerMove',
                'duration' => 30,
                'origin' => 'viewport',
                'x' => (int) round($inicio['x'] + ($fin['x'] - $inicio['x']) * $i / $pasos),
                'y' => (int) round($inicio['y'] + ($fin['y'] - $inicio['y']) * $i / $pasos),
            ];
        }

        $acciones[] = ['type' => 'pointerUp', 'button' => 0];

        $browser->driver->execute(DriverCommand::ACTIONS, [
            'actions' => [
                [
                    'type' => 'pointer',
                    'id' => 'mouse',
                    'parameters' => ['pointerType' => 'mouse'],
                    'actions' => $acciones,
                ],
            ],
        ]);

        $browser->pause(500);
    }

    /**
     * Centro del elemento en coordenadas de la ventana.
     */
    private function centro(Browser $browser, string $selector): array
    {
        $elemento = $browser->driver->findElement(WebDriverBy::cssSelector($selector));

        $posicion = $elemento->getLocation();
        $tamano = $elemento->getSize();

        $desplazamiento = $browser->driver->executeScript('return [window.scrollX, window.scrollY];');

        return [
            'x' => $posicion->getX() - $desplazamiento[0] + intdiv($tamano->getWidth(), 2),
            'y' => $posicion->getY() - $desplazamiento[1] + intdiv($tamano->getHeight(), 2),
        ];
    }
}
