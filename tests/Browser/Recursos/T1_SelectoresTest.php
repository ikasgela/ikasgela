<?php

namespace Tests\Browser\Recursos;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Rule;
use App\Models\RuleGroup;
use App\Models\Selector;
use App\Models\Unidad;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Pruebas de browser de los selectores condicionales.
 *
 * Cubre:
 *  - El botón «Volver» de la edición de un grupo de reglas regresa a la
 *    ficha del selector cuando se llega desde ella.
 *  - El botón «Volver» de la edición de un grupo de reglas regresa a la
 *    edición del selector cuando se llega desde ella.
 */
class T1_SelectoresTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private $selector;
    private $regla;
    private $plantilla;

    public function testVolverDesdeLaFichaDelSelector(): void
    {
        $this->preparar();

        $this->browse(function (Browser $browser) {
            $this->login($browser);

            // Se llega a la ficha del selector y se edita un grupo de reglas
            $browser->visit(route('selectors.show', $this->selector->id));
            $browser->assertRouteIs('selectors.show', [$this->selector->id]);

            $this->editarGrupoReglas($browser);

            $browser->assertRouteIs('rule_groups.edit', [$this->regla->id]);

            // El botón «Volver» regresa a la ficha del selector
            $this->assertEnlaceVolver($browser, '/selectors/' . $this->selector->id);

            $browser->click('#boton_volver');
            $browser->pause(1500);

            $browser->assertRouteIs('selectors.show', [$this->selector->id]);
            $browser->assertSee('Selector de prueba');
        });
    }

    public function testVolverDesdeLaEdicionDelSelector(): void
    {
        $this->preparar();

        $this->browse(function (Browser $browser) {
            $this->login($browser);

            // Se llega a la edición del selector y se edita un grupo de reglas
            $browser->visit(route('selectors.edit', $this->selector->id));
            $browser->assertRouteIs('selectors.edit', [$this->selector->id]);

            $this->editarGrupoReglas($browser);

            $browser->assertRouteIs('rule_groups.edit', [$this->regla->id]);

            // El botón «Volver» regresa a la edición del selector
            $this->assertEnlaceVolver($browser, '/selectors/' . $this->selector->id . '/edit');

            $browser->click('#boton_volver');
            $browser->pause(1500);

            $browser->assertRouteIs('selectors.edit', [$this->selector->id]);
            $browser->assertInputValue('titulo', 'Selector de prueba');
        });
    }

    public function testVolverSinOrigenRegresaALaFichaDelSelector(): void
    {
        $this->preparar();

        $this->browse(function (Browser $browser) {
            $this->login($browser);

            // Se llega directamente a la edición del grupo de reglas
            $browser->visit(route('rule_groups.edit', $this->regla->id));
            $browser->assertRouteIs('rule_groups.edit', [$this->regla->id]);

            // Al no saber de dónde se viene, se vuelve a la ficha del selector
            $browser->click('#boton_volver');
            $browser->pause(1500);

            $browser->assertRouteIs('selectors.show', [$this->selector->id]);
        });
    }

    /**
     * Comprueba que el botón «Volver» apunta a la ruta indicada.
     */
    private function assertEnlaceVolver(Browser $browser, string $ruta): void
    {
        $enlace = $browser->driver->findElement(WebDriverBy::cssSelector('#boton_volver'));

        $this->assertStringEndsWith(
            $ruta,
            (string) parse_url($enlace->getAttribute('href'), PHP_URL_PATH),
            'El botón «Volver» no apunta al selector correspondiente'
        );
    }

    /**
     * Entra en la edición del grupo de reglas desde la página actual.
     */
    private function editarGrupoReglas(Browser $browser): void
    {
        $browser->click('a[href^="' . route('rule_groups.edit', $this->regla->id) . '"]');
        $browser->pause(1500);

        $browser->assertSee(__('Edit rule group'));
    }

    /**
     * Crea un selector con un grupo de reglas en el curso actual.
     */
    private function preparar(): void
    {
        $usuario = User::where('email', 'admin@ikasgela.com')->first();
        $curso = Curso::find(setting_usuario('curso_actual', $usuario));

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $this->plantilla = Actividad::factory()->create([
            'nombre' => 'Plantilla resultado',
            'plantilla' => true,
            'unidad_id' => $unidad->id,
        ]);

        $this->selector = Selector::factory()->create([
            'titulo' => 'Selector de prueba',
            'curso_id' => $curso->id,
        ]);

        $this->regla = RuleGroup::factory()->create([
            'selector_id' => $this->selector->id,
            'operador' => 'and',
            'accion' => 'siguiente',
            'resultado' => $this->plantilla->id,
        ]);

        Rule::factory()->create([
            'rule_group_id' => $this->regla->id,
            'propiedad' => 'puntuacion',
            'operador' => '<',
            'valor' => 80,
        ]);
    }

    /**
     * Inicia sesión como administrador.
     */
    private function login(Browser $browser): void
    {
        $browser->logout();

        $this->loginAs($browser, 'admin@ikasgela.com', '12345Abcde', 'admin.index');
    }
}
