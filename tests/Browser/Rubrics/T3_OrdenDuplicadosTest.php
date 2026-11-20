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
 * Regresión ADR-005 (browser): la remapa one-shot COMB→UUIDv7 no es inyectiva —
 * dos filas generadas en la misma ventana de 10 µs (seeders, `duplicar()`) se
 * re-codifican al mismo v7, dejando `orden` duplicados en la serie (BD de
 * producción: 387 series de `criteria_groups` y 869 de `criterias` afectadas).
 *
 * Con filas empatadas, los botones de mover del editor hacían saltar la fila DOS
 * posiciones (o a un sitio equivocado): `up`/`left` usaban `max(orden < X)` y
 * `where(orden, max)->first()`, que saltaba el empate y elegía arbitrariamente
 * entre sus miembros.
 *
 * Escenario reproducido aquí (igual que la BD de producción): tres filas A, B, C
 * donde B y C comparten `orden`. «Move up» en C (la segunda del empate) no debe
 * moverla: su vecino anterior en la serie `(orden, id)` es B, con la que
 * intercambia el mismo valor (no-op visible). El código anterior la
 * intercambiaba con A y saltaba dos posiciones hacia arriba.
 *
 * La sesión del navegador persiste entre métodos (patrón de la suite T1/T2): se
 * login al principio y se logout al final.
 */
class T3_OrdenDuplicadosTest extends DuskTestCase
{
    use BrowserUiHelpers;

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'profesor@ikasgela.com', '12345Abcde', 'profesor.index');
        });
    }

    // -------------------------------------------------------------------------
    // Grupos: «Move up» en la segunda del empate NO salta dos posiciones
    // -------------------------------------------------------------------------

    public function testMoveUpSegundoDelEmpateNoSaltaDosPosiciones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Empate Dusk']);

        $a = CriteriaGroup::factory()->create([
            'titulo' => 'Empate Grupo A', 'rubric_id' => $rubric->id, 'orden' => Str::uuid7(),
        ]);
        $ordenEmpate = Str::uuid7();
        $b = CriteriaGroup::factory()->create([
            'titulo' => 'Empate Grupo B', 'rubric_id' => $rubric->id, 'orden' => $ordenEmpate,
        ]);
        $c = CriteriaGroup::factory()->create([
            'titulo' => 'Empate Grupo C', 'rubric_id' => $rubric->id, 'orden' => $ordenEmpate,
        ]);

        $this->browse(function (Browser $browser) use ($rubric, $b, $c, $ordenEmpate) {
            $this->activarEditor($browser, $rubric);

            // Orden inicial: A, B, C (B y C empatadas; B con id menor va antes).
            $this->assertOrdenGrupos($browser, ['Empate Grupo A', 'Empate Grupo B', 'Empate Grupo C']);

            // «Move up» del último grupo (el tercero, C): la segunda del empate.
            $browser->elements('button[title="' . __('Move up') . '"]')[2]->click();
            $browser->pause(1200);

            // C intercambia con B (su vecino en `(orden, id)`, el mismo valor) →
            // no-op visible: la serie NO cambia. El código anterior la
            // intercambiaba con A y aparecía en la primera posición.
            $this->assertOrdenGrupos($browser, ['Empate Grupo A', 'Empate Grupo B', 'Empate Grupo C']);

            // Los `orden` de la BD no han cambiado (swap de valores iguales).
            $this->assertSame(strval($ordenEmpate), $b->fresh()->orden);
            $this->assertSame(strval($ordenEmpate), $c->fresh()->orden);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Criterios: «Move left» en la segunda del empate NO salta dos posiciones
    // -------------------------------------------------------------------------

    public function testMoveLeftSegundoDelEmpateNoSaltaDosPosiciones(): void
    {
        $rubric = Rubric::factory()->create(['titulo' => 'Rubric Empate Crit Dusk']);
        $grupo = CriteriaGroup::factory()->create([
            'titulo' => 'Grupo Empate Crit', 'rubric_id' => $rubric->id, 'orden' => Str::uuid7(),
        ]);

        $a = Criteria::factory()->create([
            'texto' => 'Empate Crit A', 'puntuacion' => 1, 'criteria_group_id' => $grupo->id, 'orden' => Str::uuid7(),
        ]);
        $ordenEmpate = Str::uuid7();
        $b = Criteria::factory()->create([
            'texto' => 'Empate Crit B', 'puntuacion' => 2, 'criteria_group_id' => $grupo->id, 'orden' => $ordenEmpate,
        ]);
        $c = Criteria::factory()->create([
            'texto' => 'Empate Crit C', 'puntuacion' => 3, 'criteria_group_id' => $grupo->id, 'orden' => $ordenEmpate,
        ]);

        $this->browse(function (Browser $browser) use ($rubric, $a, $b, $c, $ordenEmpate) {
            $this->activarEditor($browser, $rubric);

            // Orden inicial: A, B, C.
            $this->assertRowButtons($browser, 'Empate Crit A', [true, false]);
            $this->assertRowButtons($browser, 'Empate Crit C', [false, true]);

            // «Move left» del tercer criterio (el segundo del empate).
            $browser->elements('button[title="' . __('Move left') . '"]')[2]->click();
            $browser->pause(1200);

            // C intercambia con B (el mismo valor) → no-op visible: la serie NO
            // cambia. El código anterior la intercambiaba con A y aparecía en la
            // primera posición (salto de dos).
            $this->assertOrdenCriterios($browser, ['Empate Crit A', 'Empate Crit B', 'Empate Crit C']);

            // Los `orden` no han cambiado (swap de valores iguales).
            $this->assertSame(strval($ordenEmpate), $b->fresh()->orden);
            $this->assertSame(strval($ordenEmpate), $c->fresh()->orden);
            $this->assertNotSame(strval($ordenEmpate), $a->fresh()->orden);

            // Y el «Move left» del PRIMER criterio del empate tampoco debe hacer
            // saltos: B (primero del empate) «izquierda» con A (único anterior).
            $browser->elements('button[title="' . __('Move left') . '"]')[1]->click();
            $browser->pause(1200);

            $this->assertOrdenCriterios($browser, ['Empate Crit B', 'Empate Crit A', 'Empate Crit C']);

            $this->assertNoAppErrors($browser);
        });
    }

    // -------------------------------------------------------------------------
    // Limpieza y logout
    // -------------------------------------------------------------------------

    public function testLimpiarYLogout(): void
    {
        Rubric::where('titulo', 'like', '%Empate% Dusk')->get()->each->delete();

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
     * Comprueba que los grupos aparecen en el orden dado (por título) en la
     * página.
     */
    private function assertOrdenGrupos(Browser $browser, array $titulos): void
    {
        $posiciones = [];
        foreach ($titulos as $titulo) {
            $posiciones[] = "document.body.innerText.indexOf(" . json_encode($titulo) . ")";
        }
        $js = implode(
            ' !== -1 && ',
            $posiciones
        ) . ' && ' . implode(' < ', $posiciones);

        $this->assertTrue(
            (bool) $browser->driver->executeScript("return {$js};"),
            'Los grupos no están en el orden esperado: ' . implode(', ', $titulos)
        );
    }

    /**
     * Comprueba que los criterios de la (única) fila aparecen en el orden dado
     * (por texto).
     */
    private function assertOrdenCriterios(Browser $browser, array $textos): void
    {
        $posiciones = [];
        foreach ($textos as $texto) {
            $posiciones[] = "document.body.innerText.indexOf(" . json_encode($texto) . ")";
        }
        $js = implode(' !== -1 && ', $posiciones) . ' && ' . implode(' < ', $posiciones);

        $this->assertTrue(
            (bool) $browser->driver->executeScript("return {$js};"),
            'Los criterios no están en el orden esperado: ' . implode(', ', $textos)
        );
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
}
