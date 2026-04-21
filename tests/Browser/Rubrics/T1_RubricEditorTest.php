<?php

namespace Tests\Browser\Rubrics;

use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Pruebas de browser para el editor de rúbricas Livewire.
 *
 * Cubre:
 *  - Activar/desactivar modo edición
 *  - Mover CriteriaGroups arriba y abajo
 *  - Mover Criterias izquierda y derecha
 *  - Editar una Criteria a través del modal (texto + puntuación)
 *  - Editar el título de un CriteriaGroup inline
 *  - Añadir un nuevo CriteriaGroup
 *  - Duplicar un CriteriaGroup (verificando que sus criterias reciben nuevos orden)
 *  - Eliminar un CriteriaGroup
 */
class T1_RubricEditorTest extends DuskTestCase
{
    /** ID de la rúbrica creada para esta suite, compartido entre métodos. */
    private static ?int $rubricId = null;

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'))
                ->type('email', 'profesor@ikasgela.com')
                ->type('password', '12345Abcde')
                ->check('remember')
                ->press(__('Login'))
                ->assertRouteIs('profesor.index')
                ->assertDontSee('Ignition');
        });
    }

    // -------------------------------------------------------------------------
    // Crear datos de prueba y activar el editor
    // -------------------------------------------------------------------------

    public function testActivarModoEdicion(): void
    {
        // Crear rúbrica con 3 grupos y 2 criterias por grupo
        $rubric = Rubric::factory()->create(['titulo' => 'Rúbrica Dusk Test']);

        $groupA = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo A',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);
        $groupB = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo B',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);
        $groupC = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo C',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);

        foreach ([$groupA, $groupB, $groupC] as $group) {
            Criteria::factory()->create([
                'texto'             => "Criterio 1 de {$group->titulo}",
                'puntuacion'        => 1,
                'criteria_group_id' => $group->id,
                'orden'             => Str::orderedUuid(),
            ]);
            Criteria::factory()->create([
                'texto'             => "Criterio 2 de {$group->titulo}",
                'puntuacion'        => 2,
                'criteria_group_id' => $group->id,
                'orden'             => Str::orderedUuid(),
            ]);
        }

        static::$rubricId = $rubric->id;

        $this->browse(function (Browser $browser) use ($rubric) {
            $browser->visit(route('rubrics.show', $rubric))
                ->assertSee('Grupo A')
                ->assertSee('Grupo B')
                ->assertSee('Grupo C')
                // El modo edición está desactivado: los botones de mover no deben aparecer
                ->assertMissing('button[title="' . __('Move down') . '"]')
                // Activar modo edición
                ->click('a[title="' . __('Edit') . '"]')
                ->waitFor('button[title="' . __('Add criteria group') . '"]')
                ->assertPresent('button[title="' . __('Move down') . '"]');
        });
    }

    // -------------------------------------------------------------------------
    // Mover CriteriaGroups
    // -------------------------------------------------------------------------

    public function testMoverGrupoAbajo(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $this->activarEditor($browser, $rubric);

            // Orden inicial: Grupo A → Grupo B → Grupo C
            $this->assertOrdenTexto($browser, 'Grupo A', 'Grupo B');
            $this->assertOrdenTexto($browser, 'Grupo B', 'Grupo C');

            // Mover Grupo A hacia abajo (primer botón "Move down")
            $browser->elements('button[title="' . __('Move down') . '"]')[0]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Grupo B') > -1 && document.body.innerText.indexOf('Grupo A') > -1 && document.body.innerText.indexOf('Grupo B') < document.body.innerText.indexOf('Grupo A')"
            );

            // Nuevo orden: Grupo B → Grupo A → Grupo C
            $this->assertOrdenTexto($browser, 'Grupo B', 'Grupo A');
            $this->assertOrdenTexto($browser, 'Grupo A', 'Grupo C');
        });
    }

    public function testMoverGrupoArriba(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $this->activarEditor($browser, $rubric);

            // Estado previo: Grupo B → Grupo A → Grupo C
            // Mover Grupo A hacia arriba (es el segundo grupo, usar el segundo botón "Move up")
            $browser->waitFor('button[title="' . __('Move up') . '"]');
            $browser->elements('button[title="' . __('Move up') . '"]')[1]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Grupo A') > -1 && document.body.innerText.indexOf('Grupo B') > -1 && document.body.innerText.indexOf('Grupo A') < document.body.innerText.indexOf('Grupo B')"
            );

            // Orden restaurado: Grupo A → Grupo B → Grupo C
            $this->assertOrdenTexto($browser, 'Grupo A', 'Grupo B');
            $this->assertOrdenTexto($browser, 'Grupo B', 'Grupo C');
        });
    }

    // -------------------------------------------------------------------------
    // Mover Criterias dentro de un grupo
    // -------------------------------------------------------------------------

    public function testMoverCriteriaADerecha(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $this->activarEditor($browser, $rubric);

            // Grupo A: Criterio 1 → Criterio 2 (orden inicial)
            $this->assertOrdenTexto($browser, 'Criterio 1 de Grupo A', 'Criterio 2 de Grupo A');

            // Mover Criterio 1 a la derecha (primer botón "Move right")
            $browser->elements('button[title="' . __('Move right') . '"]')[0]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Criterio 2 de Grupo A') > -1 && document.body.innerText.indexOf('Criterio 1 de Grupo A') > -1 && document.body.innerText.indexOf('Criterio 2 de Grupo A') < document.body.innerText.indexOf('Criterio 1 de Grupo A')"
            );

            // Nuevo orden: Criterio 2 → Criterio 1
            $this->assertOrdenTexto($browser, 'Criterio 2 de Grupo A', 'Criterio 1 de Grupo A');
        });
    }

    public function testMoverCriteriaAIzquierda(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $this->activarEditor($browser, $rubric);

            // Estado previo: Criterio 2 → Criterio 1 en Grupo A
            // Criterio 1 está en segunda posición → segundo botón "Move left"
            $browser->elements('button[title="' . __('Move left') . '"]')[1]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Criterio 1 de Grupo A') > -1 && document.body.innerText.indexOf('Criterio 2 de Grupo A') > -1 && document.body.innerText.indexOf('Criterio 1 de Grupo A') < document.body.innerText.indexOf('Criterio 2 de Grupo A')"
            );

            // Orden restaurado: Criterio 1 → Criterio 2
            $this->assertOrdenTexto($browser, 'Criterio 1 de Grupo A', 'Criterio 2 de Grupo A');
        });
    }

    // -------------------------------------------------------------------------
    // Editar Criteria via modal
    // -------------------------------------------------------------------------

    public function testEditarCriteria(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $this->activarEditor($browser, $rubric);

            // Abrir modal haciendo clic en el botón de texto del primer criterio del Grupo A
            $browser->elements('button.btn-primary.p-3')[0]->click();

            // Esperar a que el modal Bootstrap aparezca
            $browser->waitFor('#livewire-bootstrap-modal.show')
                ->assertSee(__('Edit criteria'));

            // Escribir nuevo texto — Tab tras escribir dispara el evento blur y sincroniza wire:model
            $browser->type('#livewire-bootstrap-modal textarea', 'Criterio editado Dusk')
                ->keys('#livewire-bootstrap-modal textarea', ['{tab}'])
                ->pause(300);

            $browser->type('#livewire-bootstrap-modal input[type=text]', '10')
                ->keys('#livewire-bootstrap-modal input[type=text]', ['{tab}'])
                ->pause(300);

            // Guardar y cerrar
            $browser->press(__('Save & Close'))
                ->pause(800)
                ->assertSee('Criterio editado Dusk');
        });
    }

    // -------------------------------------------------------------------------
    // Editar título de CriteriaGroup inline
    // -------------------------------------------------------------------------

    public function testEditarTituloCriteriaGroup(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $this->activarEditor($browser, $rubric);

            // Clicar sobre el título del Grupo B para activar la edición inline
            $browser->clickLink('Grupo B')
                ->waitFor('input.form-control.mb-2');

            // Reemplazar el título y confirmar con Enter
            $grupoInputs = $browser->elements('input.form-control.mb-2');
            $grupoInputs[0]->clear();
            $grupoInputs[0]->sendKeys('Grupo B Editado');
            $grupoInputs[0]->sendKeys(\Facebook\WebDriver\WebDriverKeys::ENTER);

            $browser->pause(600)
                ->assertSee('Grupo B Editado');
        });
    }

    // -------------------------------------------------------------------------
    // Añadir CriteriaGroup
    // -------------------------------------------------------------------------

    public function testAnadirCriteriaGroup(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $conteoInicial = $rubric->criteria_groups()->count();

            $this->activarEditor($browser, $rubric);
            $browser->click('button[title="' . __('Add criteria group') . '"]')
                ->pause(600);

            $this->assertSame($conteoInicial + 1, $rubric->refresh()->criteria_groups()->count());
        });
    }

    // -------------------------------------------------------------------------
    // Duplicar CriteriaGroup (verifica también que los criterias reciben nuevos
    // valores de orden únicos, corrigiendo el bug de duplicación)
    // -------------------------------------------------------------------------

    public function testDuplicarCriteriaGroup(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $conteoInicial = $rubric->criteria_groups()->count();

            $this->activarEditor($browser, $rubric);
            $browser->waitFor('button[title="' . __('Duplicate') . '"]');
            $browser->elements('button[title="' . __('Duplicate') . '"]')[0]->click();
            $browser->pause(600);

            $rubric->refresh();
            $this->assertSame($conteoInicial + 1, $rubric->criteria_groups()->count());

            // Verificar que los valores de 'orden' de las criterias son únicos en toda la tabla
            // (si no lo fueran, indicaría que la duplicación copió los mismos UUIDs)
            $totalOrdenes   = Criteria::withTrashed()->pluck('orden')->count();
            $ordenesUnicos  = Criteria::withTrashed()->pluck('orden')->unique()->count();
            $this->assertSame($totalOrdenes, $ordenesUnicos, 'Los valores de orden de las criterias no son únicos tras duplicar');
        });
    }

    // -------------------------------------------------------------------------
    // Eliminar CriteriaGroup
    // -------------------------------------------------------------------------

    public function testEliminarCriteriaGroup(): void
    {
        $this->browse(function (Browser $browser) {
            $rubric = Rubric::findOrFail(static::$rubricId);
            $conteoInicial = $rubric->criteria_groups()->count();

            $this->activarEditor($browser, $rubric);
            // Usar wire:click para seleccionar sólo los botones de eliminar GRUPO
            // (los de criterias tienen wire:click="$parent.delete_criteria(...)
            $browser->waitFor('button[title="' . __('Delete') . '"]');
            $deleteGroupButtons = $browser->elements('button[wire\:click*="delete_criteria_group"]');
            $deleteGroupButtons[0]->click();
            $browser->pause(600);

            $rubric->refresh();
            $this->assertSame($conteoInicial - 1, $rubric->criteria_groups()->count());
        });
    }

    // -------------------------------------------------------------------------
    // Limpieza y logout
    // -------------------------------------------------------------------------

    public function testLimpiarYLogout(): void
    {
        if (static::$rubricId !== null) {
            $rubric = Rubric::find(static::$rubricId);
            $rubric?->delete();
            static::$rubricId = null;
        }

        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit(route('portada'))
                ->assertRouteIs('portada')
                ->assertDontSee('Ignition');
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Navega a la página de la rúbrica y activa el modo edición,
     * esperando a que los controles del editor aparezcan en el DOM.
     */
    private function activarEditor(Browser $browser, Rubric $rubric): void
    {
        $browser->visit(route('rubrics.show', $rubric))
            ->click('a[title="' . __('Edit') . '"]')
            ->waitFor('button[title="' . __('Add criteria group') . '"]');
    }

    /**
     * Verifica que $textoAntes aparece antes que $textoDespues en el HTML de la página.
     */
    private function assertOrdenTexto(Browser $browser, string $textoAntes, string $textoDespues): void
    {
        $posAntes   = $browser->driver->executeScript("return document.body.innerText.indexOf(" . json_encode($textoAntes) . ");");
        $posDespues = $browser->driver->executeScript("return document.body.innerText.indexOf(" . json_encode($textoDespues) . ");");

        $this->assertGreaterThan(-1, $posAntes, "No se encontró el texto '{$textoAntes}' en la página");
        $this->assertGreaterThan(-1, $posDespues, "No se encontró el texto '{$textoDespues}' en la página");
        $this->assertLessThan($posDespues, $posAntes, "Se esperaba '{$textoAntes}' antes de '{$textoDespues}'");
    }
}
