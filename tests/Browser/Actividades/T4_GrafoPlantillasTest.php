<?php

namespace Tests\Browser\Actividades;

use App\Models\Actividad;
use App\Models\ActividadGrafo;
use App\Models\Curso;
use App\Models\Rule;
use App\Models\RuleGroup;
use App\Models\Selector;
use App\Models\Unidad;
use App\Models\User;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Support\Str;
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
    private $selector;
    private $regla;

    public function testGrafoPlantillas()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $this->login($browser);

            // La vista de grafo se carga con los nodos visibles
            $browser->visit(route('actividades.plantillas.grafo'));
            $browser->assertRouteIs('actividades.plantillas.grafo');

            $this->esperarGrafo($browser);
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

    public function testCondicionDelSelectorSeGuarda()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(3);

            // La primera plantilla sigue encadenada con la segunda
            $this->plantilla1->update(['siguiente_id' => $this->plantilla2->id]);

            $this->posicionarPlantillas();

            $this->crearSelector();

            $this->login($browser);

            $this->abrirGrafo($browser);

            // El círculo del selector aparece en el grafo
            $browser->waitForText('Selector de prueba', 20);
            $browser->assertVisible('.vue-flow__node-selector');
            $browser->assertSee('Selector de prueba');

            // El enlace normal de la actividad sigue visible
            $browser->waitFor('.vue-flow__edge', 15);
            $browser->assertPresent(
                '.vue-flow__edge[data-id="e' . $this->plantilla1->id . '-' . $this->plantilla2->id . '"]'
            );

            // Se arrastra el círculo del selector hasta la tercera plantilla
            $this->arrastrar(
                $browser,
                '.vue-flow__node-selector .vue-flow__handle-right',
                '.vue-flow__node[data-id="' . $this->plantilla3->id . '"] .vue-flow__handle-left'
            );

            $browser->pause(2000);

            $regla = RuleGroup::where('selector_id', $this->selector->id)->first();

            $this->assertNotNull(
                $regla,
                'No se ha creado la condición del selector'
            );

            $this->assertEquals(
                $this->plantilla3->id,
                $regla->resultado,
                'La condición del selector no apunta a la plantilla arrastrada'
            );

            // El enlace normal sigue estando junto al enlace condicionado
            $browser->assertScript("document.querySelectorAll('.vue-flow__edge').length", 2);
        });
    }

    public function testReconectarCondicionCambiaDestino()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(3);

            // La primera plantilla encadena con la segunda, donde está el círculo
            $this->plantilla1->update(['siguiente_id' => $this->plantilla2->id]);

            $this->posicionarPlantillas();

            $this->crearSelector(true);

            $this->login($browser);

            $this->abrirGrafo($browser);

            $enlace = '.vue-flow__edge[data-id="s' . $this->selector->id . '-' . $this->regla->id . '"]';

            $browser->waitForText('Selector de prueba', 20);
            $browser->waitFor($enlace, 15);

            // Se arrastra el extremo del enlace condicionado hasta la tercera plantilla
            $this->arrastrar(
                $browser,
                $enlace . ' .vue-flow__edgeupdater-target',
                '.vue-flow__node[data-id="' . $this->plantilla3->id . '"] .vue-flow__handle-left'
            );

            $browser->pause(2000);

            $this->assertEquals(
                $this->plantilla3->id,
                RuleGroup::find($this->regla->id)->resultado,
                'La condición del selector no se ha reconectado'
            );

            // Las reglas de la condición se conservan
            $this->assertEquals(
                1,
                Rule::where('rule_group_id', $this->regla->id)->count(),
                'Las reglas de la condición no se han conservado'
            );

            // No se ha creado ninguna condición nueva
            $this->assertEquals(
                1,
                RuleGroup::where('selector_id', $this->selector->id)->count(),
                'La condición se ha duplicado en lugar de reconectarse'
            );
        });
    }

    public function testEliminarCondicionBorraGrupoDeReglas()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(2);

            $this->posicionarPlantillas();

            $this->crearSelector(true);

            $this->login($browser);

            $this->abrirGrafo($browser);

            $browser->waitForText('Selector de prueba', 20);
            $browser->waitFor('.vue-flow__edge', 15);

            // Se selecciona el enlace condicionado y se elimina con la tecla Supr
            $browser->script("
                var enlace = document.querySelector('.vue-flow__edge[data-id^=\"s\"]');
                enlace.dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true, view: window}));
            ");
            $browser->pause(500);
            $browser->driver->getKeyboard()->pressKey(WebDriverKeys::DELETE);
            $browser->pause(2000);

            $this->assertNull(
                RuleGroup::where('selector_id', $this->selector->id)->first(),
                'La condición del selector no se ha eliminado'
            );

            $this->assertEquals(
                0,
                Rule::where('rule_group_id', $this->regla->id)->count(),
                'Las reglas de la condición no se han eliminado'
            );
        });
    }

    public function testElCirculoDelSelectorAbreLaEdicion()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(2);

            $this->posicionarPlantillas();

            $this->crearSelector(true);

            $this->login($browser);

            $this->abrirGrafo($browser);

            $browser->waitForText('Selector de prueba', 20);

            $original = $browser->driver->getWindowHandle();

            // Se pulsa el círculo con un evento de puntero real
            $this->clic($browser, '.vue-flow__node-selector .nodo-selector__enlace');

            $browser->pause(2000);

            $nuevas = array_values(array_diff($browser->driver->getWindowHandles(), [$original]));

            $this->assertNotEmpty(
                $nuevas,
                'El círculo del selector no ha abierto la edición del recurso'
            );

            $browser->driver->switchTo()->window($nuevas[0]);

            $browser->assertRouteIs('selectors.edit', [$this->selector->id]);
            $browser->assertSee(__('Edit selector'));
            $browser->assertInputValue('titulo', 'Selector de prueba');

            // El botón «Volver» regresa al grafo
            $browser->click('#boton_volver');
            $browser->pause(2000);

            $browser->assertRouteIs('actividades.plantillas.grafo');
            $browser->waitForText('Selector de prueba', 20);
        });
    }

    public function testElBotonVolverDesdeLaListaVuelveALaLista()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(2);

            $this->crearSelector(true);

            $this->login($browser);

            // Se edita el selector desde la lista de selectores
            $browser->visit(route('selectors.index'));
            $browser->assertRouteIs('selectors.index');

            $browser->click('a[href="' . route('selectors.edit', $this->selector->id) . '"]');
            $browser->pause(2000);

            $browser->assertRouteIs('selectors.edit', [$this->selector->id]);

            // El botón «Volver» regresa a la lista de selectores
            $browser->click('#boton_volver');
            $browser->pause(2000);

            $browser->assertRouteIs('selectors.index');
            $browser->assertSee('Selector de prueba');
        });
    }

    public function testElCuadroDeAyudaSePuedeMinimizar()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(2);

            $this->login($browser);

            $this->abrirGrafo($browser);

            $browser->waitForText('Grafo plantilla uno', 20);

            $selector = '.grafo-plantillas__ayuda-contenido';

            $inicial = $this->visible($browser, $selector);

            $browser->click('#grafo-ayuda');
            $browser->pause(500);

            $this->assertNotEquals(
                $inicial,
                $this->visible($browser, $selector),
                'El cuadro de ayuda no se ha minimizado'
            );

            $browser->click('#grafo-ayuda');
            $browser->pause(500);

            $this->assertEquals(
                $inicial,
                $this->visible($browser, $selector),
                'El cuadro de ayuda no se ha restaurado'
            );
        });
    }

    public function testElMinimapaUsaElTituloTraducido()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas(2);

            $this->login($browser);

            $this->abrirGrafo($browser);

            $browser->waitForText('Grafo plantilla uno', 20);

            $titulo = $browser->driver->findElement(
                WebDriverBy::cssSelector('.vue-flow__minimap title')
            );

            $this->assertEquals(
                __('Mini map of the graph'),
                $titulo->getDomProperty('textContent'),
                'El minimapa no muestra el título traducido'
            );
        });
    }

    /**
     * Indica si el elemento está presente y visible.
     */
    private function visible(Browser $browser, string $selector): bool
    {
        $elementos = $browser->driver->findElements(WebDriverBy::cssSelector($selector));

        if (!count($elementos)) {
            return false;
        }

        return $elementos[0]->isDisplayed();
    }

    public function testFiltroUnidadMuestraSoloLasPlantillasDeLaUnidad()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $usuario = User::where('email', 'admin@ikasgela.com')->first();
            $curso = Curso::find(setting_usuario('curso_actual', $usuario));

            $otraUnidad = Unidad::factory()->create(['curso_id' => $curso->id]);

            Actividad::factory()->create([
                'nombre' => 'Grafo plantilla fuera de filtro',
                'plantilla' => true,
                'unidad_id' => $otraUnidad->id,
            ]);

            $this->login($browser);

            $browser->visit(route('actividades.plantillas.grafo'));

            // Se quita cualquier filtro previo de la sesión
            $browser->select('select[name="unidad_id_disponibles"]', '');
            $browser->press(__('Filter'));
            $browser->pause(2000);

            $browser->waitForText('Grafo plantilla fuera de filtro', 20);

            // Al filtrar por la unidad solo quedan sus plantillas
            $browser->select('select[name="unidad_id_disponibles"]', (string) $this->unidad->id);
            $browser->press(__('Filter'));
            $browser->pause(2000);

            $this->esperarGrafo($browser);
            $browser->assertSee('Grafo plantilla uno');
            $browser->assertDontSee('Grafo plantilla fuera de filtro');

            $this->assertNoAppErrors($browser);
        });
    }

    public function testOrganizarAutomaticamenteGuardaLaDisposicion()
    {
        $this->browse(function (Browser $browser) {
            $this->prepararPlantillas();

            $this->login($browser);

            $this->abrirGrafo($browser);

            $original = ActividadGrafo::where('actividad_id', $this->plantilla1->id)->first();

            $this->assertNotNull($original, 'La disposición inicial no se ha guardado');

            // Se mueve el nodo y después se reorganiza todo el grafo
            $this->arrastrar(
                $browser,
                '.vue-flow__node[data-id="' . $this->plantilla1->id . '"] .nodo-plantilla__nombre',
                '.vue-flow__node[data-id="' . $this->plantilla2->id . '"] .nodo-plantilla__nombre',
                150,
                120
            );

            $browser->pause(1500);

            $movida = ActividadGrafo::where('actividad_id', $this->plantilla1->id)->first();

            $this->assertNotEquals(
                [$original->posicion_x, $original->posicion_y],
                [$movida->posicion_x, $movida->posicion_y],
                'El nodo no se ha movido'
            );

            $browser->click('#grafo-organizar');
            $browser->pause(2000);

            $organizada = ActividadGrafo::where('actividad_id', $this->plantilla1->id)->first();

            $this->assertEquals(
                [$original->posicion_x, $original->posicion_y],
                [$organizada->posicion_x, $organizada->posicion_y],
                'La organización automática no ha guardado la disposición'
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
     * Espera a que el grafo se haya dibujado en el lienzo.
     *
     * Vue Flow mantiene los nodos ocultos hasta que conoce sus dimensiones,
     * por lo que se espera a que sea visible el texto de una plantilla.
     */
    private function esperarGrafo(Browser $browser, int $segundos = 20): void
    {
        $browser->waitForText('Grafo plantilla uno', $segundos);
        $browser->waitFor('.vue-flow__node-plantilla', $segundos);
        $browser->pause(1000);
    }

    /**
     * Abre el grafo filtrando por la unidad de las plantillas de prueba.
     */
    private function abrirGrafo(Browser $browser): void
    {
        $browser->visit(route('actividades.plantillas.grafo'));

        $browser->select('select[name="unidad_id_disponibles"]', (string) $this->unidad->id);
        $browser->press(__('Filter'));

        $this->esperarGrafo($browser);
        $browser->pause(1000);

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
     * Coloca las plantillas en posiciones separadas para que los handles no se pisen.
     */
    private function posicionarPlantillas(): void
    {
        $posiciones = [
            [$this->plantilla1, 0],
            [$this->plantilla2, 600],
        ];

        if (!is_null($this->plantilla3)) {
            $posiciones[] = [$this->plantilla3, 1200];
        }

        foreach ($posiciones as [$plantilla, $x]) {
            ActividadGrafo::create([
                'actividad_id' => $plantilla->id,
                'posicion_x' => $x,
                'posicion_y' => 0,
            ]);
        }
    }

    /**
     * Crea un selector con sus reglas y lo adjunta a la primera plantilla.
     */
    private function crearSelector(bool $conReglas = false): void
    {
        $usuario = User::where('email', 'admin@ikasgela.com')->first();
        $curso = Curso::find(setting_usuario('curso_actual', $usuario));

        $this->selector = Selector::factory()->create([
            'titulo' => 'Selector de prueba',
            'curso_id' => $curso->id,
        ]);

        $this->plantilla1->selectors()->attach($this->selector, ['orden' => (string) Str::orderedUuid()]);

        if (!$conReglas) {
            return;
        }

        $regla = RuleGroup::factory()->create([
            'selector_id' => $this->selector->id,
            'operador' => 'and',
            'accion' => 'siguiente',
            'resultado' => $this->plantilla2->id,
        ]);

        $this->regla = $regla;

        Rule::factory()->create([
            'rule_group_id' => $regla->id,
            'propiedad' => 'puntuacion',
            'operador' => '<',
            'valor' => 80,
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
     * Pulsa un elemento con eventos de puntero reales.
     */
    private function clic(Browser $browser, string $selector): void
    {
        $centro = $this->centro($browser, $selector);

        $acciones = [
            ['type' => 'pointerMove', 'duration' => 0, 'origin' => 'viewport', 'x' => $centro['x'], 'y' => $centro['y']],
            ['type' => 'pointerDown', 'button' => 0],
            ['type' => 'pointerUp', 'button' => 0],
        ];

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
