<?php

namespace Tests\Browser\Rubrics;

use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Pruebas de browser para los botones de mover (criterios izquierda/derecha,
 * grupos arriba/abajo) del editor de rúbricas.
 *
 * Regresión (ADR-013): al mover/añadir/borrar una fila, el padre re-interroga la
 * relación y pasa a los hijos modelos nuevos y limpios; Livewire 4 omite el
 * re-renderizado de un hijo cuyas props `#[Reactive]` no «cambian» (la
 * comparación de modelos no detecta la reordenación), y el estado de los botones
 * (`#[Computed]` que re-leen la BD) quedaba obsoleto: p. ej., el criterio que
 * dejaba de ser el último seguía mostrando el botón hacia la derecha desactivado,
 * el nuevo último tras borrar el anterior no lo desactivaba, y el nuevo primero
 * tras borrar el anterior no desactivaba el de la izquierda.
 * La prop `#[Reactive] orden_vecinos` (hash de los `orden` de las filas
 * adyacentes) cambia exactamente en las filas afectadas por cualquier operación
 * y fuerza el re-renderizado.
 *
 * La sesión del navegador persiste entre métodos (patrón de la suite T1): se
 * login al principio y se logout al final.
 */
class T2_CriteriaMoveButtonsTest extends DuskTestCase
{
    use BrowserUiHelpers;

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'profesor@ikasgela.com', '12345Abcde', 'profesor.index');
        });
    }

    // -------------------------------------------------------------------------
    // Criterios: mover el último a la izquierda (el escenario reportado)
    // -------------------------------------------------------------------------

    public function testMoverUltimoCriteriaAIzquierdaActualizaLosBotones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones Move Dusk']);
        $group = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo Botones',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);

        Criteria::factory()->create([
            'texto'             => 'Botones Criterio A',
            'puntuacion'        => 1,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'Botones Criterio B',
            'puntuacion'        => 2,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'Botones Criterio C',
            'puntuacion'        => 3,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: A (primero) → izq. inactivo, der. activo.
            // C (último) → izq. activo, der. inactivo.
            $this->assertRowButtons($browser, 'Botones Criterio A', [true, false]);
            $this->assertRowButtons($browser, 'Botones Criterio C', [false, true]);

            // Mover C (la última fila) a la izquierda: el tercer botón "Move left".
            $browser->elements('button[title="' . __('Move left') . '"]')[2]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Botones Criterio A') !== -1 && document.body.innerText.indexOf('Botones Criterio C') !== -1 && document.body.innerText.indexOf('Botones Criterio B') !== -1 && document.body.innerText.indexOf('Botones Criterio A') < document.body.innerText.indexOf('Botones Criterio C') && document.body.innerText.indexOf('Botones Criterio C') < document.body.innerText.indexOf('Botones Criterio B')"
            );
            $browser->pause(800);

            // C ya está en el medio → ambos botones activos.
            $this->assertRowButtons($browser, 'Botones Criterio C', [false, false]);
            // B ahora es el último → izq. activo, der. inactivo.
            $this->assertRowButtons($browser, 'Botones Criterio B', [false, true]);
            // A sigue siendo el primero → izq. inactivo, der. activo.
            $this->assertRowButtons($browser, 'Botones Criterio A', [true, false]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Criterios: mover el primero a la derecha
    // -------------------------------------------------------------------------

    public function testMoverPrimeroCriteriaADerechaActualizaLosBotones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones Move 2 Dusk']);
        $group = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo Botones 2',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);

        Criteria::factory()->create([
            'texto'             => 'Botones2 Criterio A',
            'puntuacion'        => 1,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'Botones2 Criterio B',
            'puntuacion'        => 2,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'Botones2 Criterio C',
            'puntuacion'        => 3,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Mover A (la primera fila) a la derecha: el primer botón "Move right".
            $browser->elements('button[title="' . __('Move right') . '"]')[0]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Botones2 Criterio B') !== -1 && document.body.innerText.indexOf('Botones2 Criterio A') !== -1 && document.body.innerText.indexOf('Botones2 Criterio B') < document.body.innerText.indexOf('Botones2 Criterio A')"
            );
            $browser->pause(800);

            // B ahora es el primero → izq. inactivo, der. activo.
            $this->assertRowButtons($browser, 'Botones2 Criterio B', [true, false]);
            // A está en el medio → ambos botones activos.
            $this->assertRowButtons($browser, 'Botones2 Criterio A', [false, false]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Grupos: mover el último arriba (mismo mecanismo: ADR-013)
    // -------------------------------------------------------------------------

    public function testMoverUltimoGrupoArribaActualizaLosBotones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones Grupo Dusk']);
        CriteriaGroup::factory()->create(['titulo' => 'Grupos Boton A', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);
        CriteriaGroup::factory()->create(['titulo' => 'Grupos Boton B', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);
        CriteriaGroup::factory()->create(['titulo' => 'Grupos Boton C', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: C (último) → arriba activo, abajo inactivo.
            $this->assertGroupButtons($browser, 'Grupos Boton C', [false, true]);

            // Mover C (el último grupo) arriba: el tercer botón "Move up".
            $browser->elements('button[title="' . __('Move up') . '"]')[2]->click();
            $browser->waitUntil(
                "document.body.innerText.indexOf('Grupos Boton A') !== -1 && document.body.innerText.indexOf('Grupos Boton C') !== -1 && document.body.innerText.indexOf('Grupos Boton B') !== -1 && document.body.innerText.indexOf('Grupos Boton A') < document.body.innerText.indexOf('Grupos Boton C') && document.body.innerText.indexOf('Grupos Boton C') < document.body.innerText.indexOf('Grupos Boton B')"
            );
            $browser->pause(800);

            // C ya está en el medio → ambos botones activos.
            $this->assertGroupButtons($browser, 'Grupos Boton C', [false, false]);
            // B ahora es el último → arriba activo, abajo inactivo.
            $this->assertGroupButtons($browser, 'Grupos Boton B', [false, true]);
            // A sigue siendo el primero → arriba inactivo, abajo activo.
            $this->assertGroupButtons($browser, 'Grupos Boton A', [true, false]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Criterios: añadir → la que era la última debe activar el botón derecho
    // -------------------------------------------------------------------------

    public function testAnadirCriteriaActualizaLosBotones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones Add Dusk']);
        $group = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo Botones Add',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);

        Criteria::factory()->create([
            'texto'             => 'BotonesA Criterio A',
            'puntuacion'        => 1,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'BotonesA Criterio B',
            'puntuacion'        => 2,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'BotonesA Criterio C',
            'puntuacion'        => 3,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);

        $textoNuevo = (string) __('Write your criteria here');

        $this->browse(function (Browser $browser) use ($rubric, $textoNuevo) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: C (último) → izq. activo, der. inactivo.
            $this->assertRowButtons($browser, 'BotonesA Criterio C', [false, true]);

            // Añadir un criterio (botón "+" del grupo; hay un solo grupo).
            $browser->click('button[title="' . __('Add criteria') . '"]');
            $browser->waitUntil(
                "document.body.innerText.indexOf(" . json_encode($textoNuevo) . ") !== -1"
            );
            $browser->pause(800);

            // C ya no es el último → der. se activa.
            $this->assertRowButtons($browser, 'BotonesA Criterio C', [false, false]);
            // A sigue siendo el primero → izq. inactivo, der. activo.
            $this->assertRowButtons($browser, 'BotonesA Criterio A', [true, false]);
            // El nuevo criterio es el último → izq. activo, der. inactivo.
            $this->assertRowButtons($browser, $textoNuevo, [false, true]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Criterios: borrar el último → el nuevo último debe desactivar el derecho
    // -------------------------------------------------------------------------

    public function testBorrarUltimoCriteriaDesactivaBotonDerecha(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones Del Dusk']);
        $group = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo Botones Del',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);

        Criteria::factory()->create([
            'texto'             => 'BotonesB Criterio A',
            'puntuacion'        => 1,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'BotonesB Criterio B',
            'puntuacion'        => 2,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'BotonesB Criterio C',
            'puntuacion'        => 3,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: B (medio) → ambos activos; C (último) → der. inactivo.
            $this->assertRowButtons($browser, 'BotonesB Criterio B', [false, false]);
            $this->assertRowButtons($browser, 'BotonesB Criterio C', [false, true]);

            // Borrar C (papelera de la tercera fila).
            $this->clickRowDelete($browser, 'BotonesB Criterio C');
            $browser->waitUntil("document.body.innerText.indexOf('BotonesB Criterio C') === -1");
            $browser->pause(800);

            // B ahora es el último → der. se desactiva.
            $this->assertRowButtons($browser, 'BotonesB Criterio B', [false, true]);
            // A sigue siendo el primero → izq. inactivo, der. activo.
            $this->assertRowButtons($browser, 'BotonesB Criterio A', [true, false]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Criterios: borrar el primero → el nuevo primero debe desactivar el izquierdo
    // -------------------------------------------------------------------------

    public function testBorrarPrimeroCriteriaDesactivaBotonIzquierda(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones Del 2 Dusk']);
        $group = CriteriaGroup::factory()->create([
            'titulo'    => 'Grupo Botones Del 2',
            'rubric_id' => $rubric->id,
            'orden'     => Str::orderedUuid(),
        ]);

        Criteria::factory()->create([
            'texto'             => 'BotonesC Criterio A',
            'puntuacion'        => 1,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'BotonesC Criterio B',
            'puntuacion'        => 2,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);
        Criteria::factory()->create([
            'texto'             => 'BotonesC Criterio C',
            'puntuacion'        => 3,
            'criteria_group_id' => $group->id,
            'orden'             => Str::orderedUuid(),
        ]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: A (primero) → izq. inactivo, der. activo.
            $this->assertRowButtons($browser, 'BotonesC Criterio A', [true, false]);

            // Borrar A (papelera de la primera fila).
            $this->clickRowDelete($browser, 'BotonesC Criterio A');
            $browser->waitUntil("document.body.innerText.indexOf('BotonesC Criterio A') === -1");
            $browser->pause(800);

            // B ahora es el primero → izq. se desactiva.
            $this->assertRowButtons($browser, 'BotonesC Criterio B', [true, false]);
            // C sigue siendo el último → izq. activo, der. inactivo.
            $this->assertRowButtons($browser, 'BotonesC Criterio C', [false, true]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Grupos: añadir/borrar → mismo mecanismo (ADR-013)
    // -------------------------------------------------------------------------

    public function testAnadirGrupoActualizaLosBotones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones GAdd Dusk']);
        CriteriaGroup::factory()->create(['titulo' => 'GruposGAdd A', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);
        CriteriaGroup::factory()->create(['titulo' => 'GruposGAdd B', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);
        CriteriaGroup::factory()->create(['titulo' => 'GruposGAdd C', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: C (último) → arriba activo, abajo inactivo.
            $this->assertGroupButtons($browser, 'GruposGAdd C', [false, true]);

            // Añadir un grupo (botón "+" final de la rúbrica).
            $browser->click('button[title="' . __('Add criteria group') . '"]');
            $browser->waitUntil(
                "document.querySelectorAll('button[title=" . json_encode(__('Move up')) . "]').length === 4"
            );
            $browser->pause(800);

            // C ya no es el último → abajo se activa.
            $this->assertGroupButtons($browser, 'GruposGAdd C', [false, false]);
            // A sigue siendo el primero → arriba inactivo, abajo activo.
            $this->assertGroupButtons($browser, 'GruposGAdd A', [true, false]);

            $this->assertNoAppErrors($browser);
        });
    }

    public function testBorrarPrimerGrupoDesactivaBotonArriba(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Botones GDel Dusk']);
        CriteriaGroup::factory()->create(['titulo' => 'GruposGDel A', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);
        CriteriaGroup::factory()->create(['titulo' => 'GruposGDel B', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);
        CriteriaGroup::factory()->create(['titulo' => 'GruposGDel C', 'rubric_id' => $rubric->id, 'orden' => Str::orderedUuid()]);

        $this->browse(function (Browser $browser) use ($rubric) {
            $this->activarEditor($browser, $rubric);

            // Estado inicial: A (primero) → arriba inactivo, abajo activo.
            $this->assertGroupButtons($browser, 'GruposGDel A', [true, false]);

            // Borrar A (papelera del primer grupo; sin criterios no hay otras papeleras).
            $browser->elements('button[title="' . __('Delete') . '"][wire\\:click*="delete_criteria_group("]')[0]->click();
            $browser->waitUntil("document.body.innerText.indexOf('GruposGDel A') === -1");
            $browser->pause(800);

            // B ahora es el primero → arriba se desactiva.
            $this->assertGroupButtons($browser, 'GruposGDel B', [true, false]);
            // C sigue siendo el último → arriba activo, abajo inactivo.
            $this->assertGroupButtons($browser, 'GruposGDel C', [false, true]);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Limpieza y logout
    // -------------------------------------------------------------------------

    public function testLimpiarYLogout(): void
    {
        Rubric::where('titulo', 'like', '%Botones% Dusk')->get()->each->delete();

        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->assertGuest()
                ->assertDontSee('Ignition');
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Navega a la página de la rúbrica y activa el modo edición
     * (asume sesión iniciada: ver testLogin).
     */
    private function activarEditor(Browser $browser, Rubric $rubric): void
    {
        $browser->visit(route('rubrics.show', $rubric))
            ->click('a[title="' . __('Edit') . '"]')
            ->waitFor('button[title="' . __('Add criteria group') . '"]');
    }

    /**
     * Comprueba el estado (activado/deshabilitado) de los botones
     * "Move left"/"Move right" de la fila que contiene $texto.
     *
     * @param array{0: bool, 1: bool} $esperado [izquierdaDesactivado, derechaDesactivado]
     */
    private function assertRowButtons(Browser $browser, string $texto, array $esperado): void
    {
        $titulos = json_encode([__('Move left'), __('Move right')]);
        $textoJs = json_encode($texto);

        $estado = $browser->driver->executeScript(<<<JS
            var filas = document.querySelectorAll('div.col-auto');
            for (var i = 0; i < filas.length; i++) {
                if (filas[i].innerText.indexOf($textoJs) !== -1) {
                    var titulos = $titulos;
                    var left = filas[i].querySelector('button[title="' + titulos[0] + '"]');
                    var right = filas[i].querySelector('button[title="' + titulos[1] + '"]');
                    return [
                        left ? left.classList.contains('disabled') : null,
                        right ? right.classList.contains('disabled') : null
                    ];
                }
            }
            return null;
        JS);

        $this->assertNotNull($estado, "No se encontró la fila del criterio '{$texto}'");

        [$izq, $der] = $estado;

        $this->assertSame(
            $esperado[0],
            $izq,
            "Fila '{$texto}': botón 'Move left' no está en el estado esperado (desactivado=" . ($esperado[0] ? 'sí' : 'no') . ')'
        );
        $this->assertSame(
            $esperado[1],
            $der,
            "Fila '{$texto}': botón 'Move right' no está en el estado esperado (desactivado=" . ($esperado[1] ? 'sí' : 'no') . ')'
        );
    }

    /**
     * Pulsa el botón "Delete" (papelera) de la fila que contiene $texto.
     */
    private function clickRowDelete(Browser $browser, string $texto): void
    {
        $textoJs = json_encode($texto);
        $tituloJs = json_encode(__('Delete'));

        $encontrado = $browser->driver->executeScript(<<<JS
            var filas = document.querySelectorAll('div.col-auto');
            for (var i = 0; i < filas.length; i++) {
                if (filas[i].innerText.indexOf($textoJs) !== -1) {
                    var btn = filas[i].querySelector('button[title="' + $tituloJs + '"]');
                    if (btn) { btn.click(); return true; }
                }
            }
            return false;
        JS);

        $this->assertTrue($encontrado, "No se encontró el botón 'Delete' de la fila '{$texto}'");
    }

    /**
     * Comprueba el estado (activado/deshabilitado) de los botones
     * "Move up"/"Move down" del grupo cuyo título es $grupoTitulo.
     *
     * @param array{0: bool, 1: bool} $esperado [arribaDesactivado, abajoDesactivado]
     */
    private function assertGroupButtons(Browser $browser, string $grupoTitulo, array $esperado): void
    {
        $titulos = json_encode([__('Move up'), __('Move down')]);
        $tituloJs = json_encode($grupoTitulo);

        $estado = $browser->driver->executeScript(<<<JS
            var raices = document.querySelectorAll('[wire\\\\:id]');
            var mejor = null;
            for (var i = 0; i < raices.length; i++) {
                if (raices[i].innerText.indexOf($tituloJs) !== -1) {
                    if (!mejor || raices[i].innerText.length < mejor.innerText.length) {
                        mejor = raices[i];
                    }
                }
            }
            if (!mejor) return null;
            var titulos = $titulos;
            var up = mejor.querySelector('button[title="' + titulos[0] + '"]');
            if (!up) return null;
            var down = mejor.querySelector('button[title="' + titulos[1] + '"]');
            return [
                up.classList.contains('disabled'),
                down ? down.classList.contains('disabled') : null
            ];
        JS);

        $this->assertNotNull($estado, "No se encontró el grupo '{$grupoTitulo}'");

        [$arriba, $abajo] = $estado;

        $this->assertSame(
            $esperado[0],
            $arriba,
            "Grupo '{$grupoTitulo}': botón 'Move up' no está en el estado esperado (desactivado=" . ($esperado[0] ? 'sí' : 'no') . ')'
        );
        $this->assertSame(
            $esperado[1],
            $abajo,
            "Grupo '{$grupoTitulo}': botón 'Move down' no está en el estado esperado (desactivado=" . ($esperado[1] ? 'sí' : 'no') . ')'
        );
    }
}
